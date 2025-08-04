<?php namespace App\Models;

use CodeIgniter\Model;

class RssModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('rss_feeds');
    }

    //input values from POST
    public function inputValues()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'feed_name' => inputPost('feed_name'),
            'feed_url' => inputPost('feed_url'),
            'post_limit' => inputPost('post_limit'),
            'category_id' => inputPost('category_id'),
            'image_saving_method' => inputPost('image_saving_method'),
            'auto_update' => inputPost('auto_update'),
            'generate_keywords_from_title' => inputPost('generate_keywords_from_title'),
            'read_more_button' => inputPost('read_more_button'),
            'read_more_button_text' => inputPost('read_more_button_text'),
            'add_posts_as_draft' => inputPost('add_posts_as_draft'),
        ];
    }

    //add feed to db
    public function addFeed()
    {
        $data = $this->inputValues();
        $subcategoryId = inputPost('subcategory_id');
        if (!empty($subcategoryId)) {
            $data['category_id'] = $subcategoryId;
        }
        $data['user_id'] = user()->id;
        $data['is_cron_updated'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //edit existing feed
    public function editFeed($feed)
    {
        if (!empty($feed)) {
            $data = $this->inputValues();
            $subcategoryId = inputPost('subcategory_id');
            if (!empty($subcategoryId)) {
                $data['category_id'] = $subcategoryId;
            }
            return $this->builder->where('id', $feed->id)->update($data);
        }
        return false;
    }

    //update posts' show_post_url field based on feed setting
    public function updateFeedPostsButton($feedId)
    {
        $feed = $this->getFeed($feedId);
        if (!empty($feed)) {
            $posts = $this->db->table('posts')->where('feed_id', $feed->id)->get()->getResult();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $this->db->table('posts')->where('id', $post->id)->update(['show_post_url' => $feed->read_more_button]);
                }
            }
        }
    }

    //get single feed by id
    public function getFeed($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get all feeds
    public function getFeeds()
    {
        return $this->builder->select('rss_feeds.*, (SELECT COUNT(*) FROM posts WHERE posts.feed_id = rss_feeds.id) AS num_posts')->get()->getResult();
    }

    //count filtered feeds
    public function getFeedsCount()
    {
        $this->filterFeeds();
        return $this->builder->countAllResults();
    }

    //get paginated filtered feeds
    public function getFeedsPaginated($perPage, $offset)
    {
        $this->filterFeeds();
        return $this->builder->select('rss_feeds.*, (SELECT COUNT(*) FROM posts WHERE posts.feed_id = rss_feeds.id) AS num_posts')
            ->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //apply filters on feeds query
    public function filterFeeds()
    {
        $langId = clrNum(inputGet('lang_id'));
        $q = inputGet('q');
        if (!empty($langId)) {
            $this->builder->where('lang_id', clrNum($langId));
        }
        if (!isSuperAdmin()) {
            $this->builder->where('user_id', user()->id);
        }
        if (!empty($q)) {
            $this->builder->like('feed_name', cleanStr($q));
        }
    }

    //get feeds by user
    public function getFeedsByUser($userId)
    {
        return $this->builder->where('user_id', clrNum($userId))->get()->getResult();
    }

    //get feeds for cron job (limit 3)
    public function getFeedsCron()
    {
        return $this->builder->where('auto_update', 1)->orderBy('is_cron_updated, id')->get(3)->getResult();
    }

    //get feeds not updated by cron yet
    public function getFeedsNotUpdated()
    {
        return $this->builder->where('is_cron_updated', 0)->get()->getResult();
    }

    //reset cron checked flag for all feeds
    public function resetFeedsCronChecked()
    {
        $this->builder->update(['is_cron_updated' => 0]);
    }

    //mark feed as checked by cron
    public function setFeedCronChecked($feedId)
    {
        $this->builder->where('id', clrNum($feedId))->update(['is_cron_updated' => 1]);
    }

    //delete feed with permission check
    public function deleteFeed($id)
    {
        $feed = $this->getFeed($id);
        if (!empty($feed)) {
            if (!hasPermission('rss_feeds') && user()->id != $feed->user_id) {
                return false;
            }
            return $this->builder->where('id', $feed->id)->delete();
        }
        return false;
    }

    // Main function: add RSS feed posts with full content fetching
    public function addFeedPosts($feedId)
    {
        loadLibrary('RssParser');
        $rssParser = new \RssParser();
        $feed = $this->getFeed($feedId);

        if (empty($feed)) {
            return false;
        }

        $response = $rssParser->getFeeds($feed->feed_url);
        if (empty($response)) {
            return false;
        }

        $i = 0;
        foreach ($response as $item) {
            if ($feed->post_limit == $i) {
                break;
            }

            $title = $this->characterConvert($item->get_title());
            $description = $this->characterConvert($item->get_description());
            $link = $item->get_link();

            // Check duplicates by title or hash
            $titleHash = md5($title ?? '');
            $numRows = $this->db->table('posts')->where('title', cleanStr($title))->orWhere('title_hash', cleanStr($titleHash))->countAllResults();
            if ($numRows > 0) {
                $i++;
                continue;
            }

            // Get full content by scraping the link page
            $fullContent = $this->getFullContentFromUrl($link);

            // If scraping failed, fallback to description/content
            if (empty($fullContent)) {
                $fullContent = $this->characterConvert($item->get_content());
            }

            $data = [];
            $data['lang_id'] = $feed->lang_id;
            $data['title'] = $title;
            $data['slug'] = strSlug($title);
            $data['title_hash'] = $titleHash;

            $data['keywords'] = '';
            if ($feed->generate_keywords_from_title == 1) {
                $data['keywords'] = generateKeywords($title);
            }

            $data['summary'] = !empty($description) ? strip_tags($description) : '';
            if (empty($data['summary'])) {
                $summary = !empty($fullContent) ? strTrim(strip_tags($fullContent)) : '';
                $data['summary'] = characterLimiter($summary, 240, '...');
            }

            $data['content'] = $fullContent;
            $data['category_id'] = $feed->category_id;
            $data['optional_url'] = '';
            $data['need_auth'] = 0;
            $data['slider_order'] = 1;
            $data['featured_order'] = 1;
            $data['is_scheduled'] = 0;
            $data['visibility'] = 1;
            $data['post_type'] = "article";
            $data['video_path'] = '';
            $data['video_embed_code'] = '';
            $data['user_id'] = $feed->user_id;
            $data['feed_id'] = $feed->id;
            $data['post_url'] = $link;
            $data['show_post_url'] = $feed->read_more_button;
            $data['image_description'] = '';
            $data['created_at'] = date('Y-m-d H:i:s');

            $data['status'] = $feed->add_posts_as_draft == 1 ? 0 : 1;

            // Image handling
            if ($feed->image_saving_method == 'download') {
                $dataImage = $rssParser->getImage($item, true);
                if (!empty($dataImage) && is_array($dataImage)) {
                    $dataImage['file_name'] = $data['slug'];
                    $dataImage['user_id'] = $feed->user_id;
                    $db = \Config\Database::connect(null, false);
                    if ($db->table('images')->insert($dataImage)) {
                        $data['image_id'] = $db->insertID();
                    }
                    $db->close();
                }
            } else {
                $data['image_url'] = $rssParser->getImage($item, false);
            }

            if ($this->db->table('posts')->insert($data)) {
                $postId = $this->db->insertID();
                updateSlug('posts', $postId);
            }

            $i++;
        }

        // Delete duplicated posts by title_hash, keep latest
        $postTitleHashs = $this->db->table('posts')->select('title_hash')->groupBy('title_hash')->having('COUNT(title_hash) > 1')->get()->getResult();
        if (!empty($postTitleHashs)) {
            foreach ($postTitleHashs as $titleHash) {
                if (!empty($titleHash)) {
                    // Delete all but latest
                    $postsToDelete = $this->db->table('posts')->where('title_hash', $titleHash->title_hash)->orderBy('id DESC')->offset(1)->get()->getResult();
                    foreach ($postsToDelete as $postToDelete) {
                        $this->db->table('posts')->where('id', $postToDelete->id)->delete();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Scrape full article content from given URL using DOMDocument and XPath.
     * You may need to adjust the selectors for your target sites.
     * 
     * @param string $url
     * @return string|null Full content HTML or null on failure
     */
    private function getFullContentFromUrl(string $url): ?string
    {
        $content = null;

        // Basic sanity check for URL
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Use cURL for robust fetching with timeout
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; RssBot/1.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        curl_close($ch);

        if ($html === false || empty($html)) {
            return null;
        }

        // Load HTML into DOMDocument
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if (!$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'))) {
            libxml_clear_errors();
            return null;
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        // Try common containers one by one, fallback if needed:
        $containers = [
            '//article',
            '//*[contains(@class,"content")]',
            '//*[contains(@class,"post-body")]',
            '//*[contains(@class,"article-content")]',
            '//*[contains(@id,"content")]',
            '//*[contains(@id,"article")]',
        ];

        foreach ($containers as $query) {
            $nodes = $xpath->query($query);
            if ($nodes->length > 0) {
                $htmlContent = '';
                foreach ($nodes as $node) {
                    $htmlContent .= $doc->saveHTML($node);
                }
                // Clean up HTML if necessary
                $content = trim($htmlContent);
                if (!empty($content)) {
                    break;
                }
            }
        }

        // If still empty, fallback: get <body> content stripped
        if (empty($content)) {
            $bodyNodes = $xpath->query('//body');
            if ($bodyNodes->length > 0) {
                $content = trim($doc->saveHTML($bodyNodes->item(0)));
            }
        }

        return $content;
    }

    //clean up and convert special chars
    public function characterConvert($str)
    {
        $str = strTrim($str);
        $str = strReplace("&amp;", "&", $str);
        $str = strReplace("&lt;", "<", $str);
        $str = strReplace("&gt;", ">", $str);
        $str = strReplace("&quot;", '"', $str);
        $str = strReplace("&apos;", "'", $str);
        return $str;
    }

    //create google news feed XML (template, you can adjust)
    public function createGoogleNewsFeed($posts)
    {
        $url = "";
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
        if (!empty($posts)):
            foreach ($posts as $post)
                $xml .= '<url>
            <loc>' . $url . '</loc>
            <news:news>
                <news:publication>
                    <news:name>The Example Times</news:name>
                    <news:language>en</news:language>
                </news:publication>
                <news:publication_date>2008-12-23</news:publication_date>
                <news:title>Companies A, B in Merger Talks</news:title>
            </news:news>
        </url>';
        endif;
        $xml .= '</urlset>';

        return $xml;
    }
}
