<?php namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\QueryBuilder;

class PostModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = new QueryBuilder('posts', $this->db);
    }

    //build sql query
    public function buildQuery($langId = null, $fetchContent = false, $isPreview = false)
    {
        $langId = $langId ?? $this->activeLang->id;
        $this->builder->resetQuery();
        $this->builder->join('categories', 'categories.id = posts.category_id')->join('users', 'users.id = posts.user_id');
        if ($fetchContent) {
            $this->builder->select('posts.*');
        } else {
            $this->builder->select('posts.id, posts.lang_id, posts.title, posts.slug, posts.summary, posts.category_id, posts.image_id, posts.slider_order, posts.featured_order, posts.post_type, posts.image_url, posts.user_id, posts.pageviews, posts.post_url, posts.comment_count, posts.updated_at, posts.created_at');
        }
        $this->builder->select("(SELECT CONCAT('img_bg::', i.image_big, '||','img_df::', i.image_default, '||','img_sl::', i.image_slider, '||','img_md::', i.image_mid, '||','img_sm::', i.image_small, '||','img_mi::', i.image_mime, '||',
        'img_st::', i.storage) FROM images i WHERE i.id = posts.image_id LIMIT 1) AS image_data");

        $this->builder->select('categories.name AS category_name, categories.slug AS category_slug , categories.color AS category_color, users.username AS author_username, users.slug AS author_slug ');
        if ($isPreview == false) {
            $this->builder->where('posts.lang_id', clrNum($langId))->where('posts.is_scheduled', 0)->where('posts.visibility', 1)->where('posts.status = 1');
        }
    }

    //get latest posts by category
    public function getLatestCategoryPosts($langId)
    {
        return getOrSetStableCache('category_posts_lang_' . $langId, function () use ($langId) {
            $idxSub = FORCE_DB_INDEXES ? ' USE INDEX (idx_latest_category_posts)' : '';
            $subQuery = "
            (SELECT id 
             FROM (
                 SELECT id, category_id, 
                        @ct_row_number := IF(@prev = category_id, @ct_row_number + 1, 1) AS number_of_rows, 
                        @prev := category_id
             FROM posts {$idxSub}
                 JOIN (SELECT @prev := NULL, @ct_row_number := 0) AS vars
                 WHERE posts.is_scheduled = 0 
                   AND posts.visibility = 1 
                   AND posts.status = 1
                 ORDER BY category_id, created_at DESC
             ) AS filtered_posts
             WHERE number_of_rows <= 15
            ) AS limited_posts";
            $this->buildQuery($langId);
            return $this->builder->join($subQuery, 'limited_posts.id = posts.id', 'inner', false)->orderBy('posts.created_at DESC, posts.id DESC')->get()->getResult();
        }, CATEGORY_POSTS_CACHE_REFRESH_TIME);
    }

    //get latest posts
    public function getLatestPosts($langId, $perPage, $offset)
    {
        $cacheKey = 'posts_latest_lang_' . $langId . '_' . $perPage . '_' . $offset;
        return getOrSetCache($cacheKey, function () use ($langId, $perPage, $offset) {
            $this->buildQuery($langId);
            return $this->builder->addUseIndex('idx_created_at')->orderBy('posts.created_at DESC')->limit(clrNum($perPage) + 1, clrNum($offset))->get()->getResult();
        });
    }

    //get selected posts
    public function getPostsSelected($langId)
    {
        return getOrSetCache('posts_selected_lang_' . $langId, function () use ($langId) {
            $sql = "(SELECT ps.post_id, ps.selection_type FROM post_selections ps WHERE ps.selection_type = 'slider' LIMIT 100) UNION ALL
                (SELECT ps.post_id, ps.selection_type FROM post_selections ps WHERE ps.selection_type = 'featured' LIMIT 100) UNION ALL
                (SELECT ps.post_id, ps.selection_type FROM post_selections ps WHERE ps.selection_type = 'breaking' LIMIT 100) UNION ALL
                (SELECT ps.post_id, ps.selection_type FROM post_selections ps WHERE ps.selection_type = 'recommended' LIMIT 100)";
            $this->buildQuery($langId);
            return $this->builder->select('selections.selection_type')->join("($sql) AS selections", 'posts.id = selections.post_id', 'inner')->orderBy('posts.created_at DESC')->get()->getResult();
        });
    }

    //get popular posts
    public function getPopularPosts($langId)
    {
        return getOrSetStableCache('posts_popular_lang_' . $langId, function () use ($langId) {
            $result = $this->db->query("SELECT post_pageviews_month.post_id, COUNT(*) AS count FROM post_pageviews_month JOIN posts ON posts.id = post_pageviews_month.post_id 
                WHERE posts.lang_id = ? GROUP BY post_pageviews_month.post_id ORDER BY count DESC LIMIT 20", [clrNum($langId)])->getResult();
            if (countItems($result) > 0) {
                $postIds = array_column($result, 'post_id');
                if (!empty($postIds) && countItems($postIds) > 0) {
                    $this->buildQuery($langId);
                    return $this->builder->whereIn('posts.id', $postIds, false)->orderBy('posts.pageviews DESC, posts.id')->get(POPULAR_POSTS_LIMIT)->getResult();
                }
            }
            return [];
        }, POPULAR_POSTS_REFRESH_TIME);
    }

    //get post count
    public function getPostCount($langId)
    {
        return getOrSetCache('posts_count_' . $langId, function () use ($langId) {
            $this->buildQuery($langId);
            return $this->builder->countAllResults();
        });
    }

    //get posts paginated
    public function getPostsPaginated($langId, $perPage, $offset)
    {
        $cacheKey = 'posts_lang_' . $langId . '_' . $perPage . '_' . $offset;
        return getOrSetCache($cacheKey, function () use ($langId, $perPage, $offset) {
            $this->buildQuery($langId);
            return $this->builder->addUseIndex('idx_created_at')->orderBy('posts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
        });
    }

    //get post count by category
    public function getPostCountByCategory($categoryId, $categoryTree)
    {
        return getOrSetCache('posts_count_category_' . $categoryId, function () use ($categoryTree) {
            if (!is_array($categoryTree) || countItems($categoryTree) < 1) {
                return 0;
            }
            $this->buildQuery();
            return $this->builder->whereIn('posts.category_id', $categoryTree, false)->countAllResults();
        });
    }

    //get posts by cateogry paginated
    public function getPostsByCategoryPaginated($categoryId, $categoryTree, $perPage, $offset)
    {
        $cacheKey = 'posts_category_' . $categoryId . '_' . $perPage . '_' . $offset;
        return getOrSetCache($cacheKey, function () use ($categoryTree, $perPage, $offset) {
            if (!is_array($categoryTree) || countItems($categoryTree) < 1) {
                return [];
            }
            $this->buildQuery();
            return $this->builder->addUseIndex('idx_created_at')->whereIn('posts.category_id', $categoryTree, false)->orderBy('posts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
        });
    }

    //get user posts count
    public function getUserPostsCount($langId, $userId)
    {
        $cacheKey = 'posts_count_lang_' . $langId . '_user_' . $userId;
        return getOrSetCache($cacheKey, function () use ($langId, $userId) {
            $this->buildQuery($langId);
            return $this->builder->where('posts.user_id', clrNum($userId))->countAllResults();
        });
    }

    //get paginated user posts
    public function getUserPostsPaginated($langId, $userId, $perPage, $offset)
    {
        $cacheKey = 'posts_lang_' . $langId . '_user_' . $userId . '_' . $perPage . '_' . $offset;
        return getOrSetCache($cacheKey, function () use ($langId, $userId, $perPage, $offset) {
            $this->buildQuery($langId);
            return $this->builder->addUseIndex('idx_posts_profile')->where('posts.user_id', clrNum($userId))->orderBy('posts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
        });
    }

    //get post by slug
    public function getPostBySlug($slug)
    {
        $this->buildQuery(null, true);
        return $this->builder->where('posts.slug', cleanSlug($slug))->get()->getRow();
    }

    //get post count by tag
    public function getPostCountByTag($tagId, $langId)
    {
        $this->buildQuery($langId);
        return $this->builder->join('post_tags', 'post_tags.post_id = posts.id')->where('post_tags.tag_id', clrNum($tagId))->countAllResults();
    }

    //get paginated tag posts
    public function getTagPostsPaginated($tagId, $langId, $perPage, $offset)
    {
        $this->buildQuery($langId);
        return $this->builder->join('post_tags', 'post_tags.post_id = posts.id')->where('post_tags.tag_id', clrNum($tagId))->orderBy('posts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get paginated search posts
    public function getSearchPostsPaginated($langId, $q, $perPage, $offset)
    {
        $this->buildQuery($langId);
        $search = removeForbiddenCharacters($q);
        $escSearch = $this->db->escape($search);
        return $this->builder->where("MATCH(title, summary, content) AGAINST({$escSearch} IN NATURAL LANGUAGE MODE)")->limit(clrNum($perPage) + 1, clrNum($offset))->get()->getResult();
    }

    //get related posts
    public function getRelatedPosts($categoryId, $postId, $categories)
    {
        $categoryIds = getCategoryTree($categoryId, $categories);
        if (countItems($categoryIds) < 1) {
            return array();
        }
        $this->buildQuery();
        return $this->builder->addUseIndex('idx_created_at')->whereIn('posts.category_id', $categoryIds, false)->orderBy('posts.created_at DESC')->get(20)->getResult();
    }

    //get next and previous posts
    public function getNextPrevPosts($postId, $langId)
    {
        $prevQuery = $this->db->table('posts')->select('MAX(id) AS id')->where('id <', clrNum($postId))->where('posts.is_scheduled = 0')->where('posts.visibility = 1')
            ->where('posts.status = 1')->where('posts.lang_id', clrNum($langId))->getCompiledSelect();;
        $nextQuery = $this->db->table('posts')->select('MIN(id) AS id')->where('id >', clrNum($postId))->where('posts.is_scheduled = 0')->where('posts.visibility = 1')
            ->where('posts.status = 1')->where('posts.lang_id', clrNum($langId))->getCompiledSelect();

        $sql = "SELECT * FROM posts WHERE id IN (($prevQuery), ($nextQuery))";
        $result = $this->db->query($sql)->getResult();

        $prev = null;
        $next = null;
        if (!empty($result)) {
            foreach ($result as $row) {
                if ($row->id < $postId) {
                    $prev = $row;
                } elseif ($row->id > $postId) {
                    $next = $row;
                }
            }
        }
        return ['prev' => $prev, 'next' => $next];
    }

    //get reading list posts count
    public function getReadingListPostsCount($userId)
    {
        $this->buildQuery();
        return $this->builder->join('reading_lists', 'reading_lists.post_id = posts.id')->where('reading_lists.user_id', clrNum($userId))->countAllResults();
    }

    //get paginated reading list posts
    public function getReadingListPostsPaginated($userId, $perPage, $offset)
    {
        $this->buildQuery();
        return $this->builder->join('reading_lists', 'reading_lists.post_id = posts.id')->where('reading_lists.user_id', clrNum($userId))->orderBy('reading_lists.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //check post is in the reading list or not
    public function isPostInReadingList($postId)
    {
        if (authCheck()) {
            if ($this->db->table('reading_lists')->where('post_id', clrNum($postId))->where('user_id', clrNum(user()->id))->countAllResults() > 0) {
                return true;
            }
        }
        return false;
    }

    //add to reading list
    public function addRemoveReadingListItem($postId, $operation)
    {
        if (authCheck()) {
            if ($operation == 'add') {
                $data = [
                    'post_id' => clrNum($postId),
                    'user_id' => user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                return $this->db->table('reading_lists')->insert($data);
            } elseif ($operation == 'remove') {
                return $this->db->table('reading_lists')->where('post_id', clrNum($postId))->where('user_id', clrNum(user()->id))->delete();
            }
        }
        return false;
    }

    //get preview post
    public function getPostPreview($slug)
    {
        $this->buildQuery(null, true, true);
        return $this->builder->where('posts.slug', cleanSlug($slug))->groupStart()->where('posts.status', 0)->orWhere('posts.is_scheduled', 1)->orWhere('posts.visibility', 0)->groupEnd()->get()->getRow();
    }

    //get rss posts
    public function getRSSPosts($userId, $categoryId, $categories, $limit)
    {
        $cacheKey = 'rss_posts_lang_' . clrNum($this->activeLang->id);
        if (!empty($categoryId)) {
            $cacheKey .= '_cat_' . clrNum($categoryId);
        }
        if (!empty($userId)) {
            $cacheKey .= '_author_' . clrNum($userId);
        }
        $posts = cache($cacheKey);
        if (!empty($posts)) {
            return $posts;
        }
        $this->buildQuery(null, true);
        $this->builder->select("'" . esc($this->activeLang->short_form) . "' AS lang_short_form");
        if (!empty($categoryId)) {
            $categoryIds = getCategoryTree($categoryId, $categories);
            if (countItems($categoryIds) > 0) {
                $this->builder->whereIn('posts.category_id', $categoryIds, false);
            }
        }
        if (!empty($userId)) {
            $this->builder->where('posts.user_id', clrNum($userId));
        }
        $posts = $this->builder->addUseIndex('idx_created_at')->orderBy('posts.created_at DESC')->get(clrNum($limit))->getResult();
        cache()->save($cacheKey, $posts, RSS_CACHE_REFRESH_TIME);
        return $posts;
    }

    //get google news feeds
    public function getGoogleNewsFeeds($categories)
    {
        $langId = clrNum(inputGet('lang'));
        $categoryId = clrNum(inputGet('category'));
        $userId = clrNum(inputGet('author'));
        $limit = clrNum(inputGet('limit'));
        if ($limit <= 0) {
            $limit = 1000;
        }
        $cacheKey = 'google_news_lang' . $langId . '_cat' . $categoryId . '_author' . $userId . '_limit' . $limit;
        $posts = cache($cacheKey);
        if (!empty($posts)) {
            return $posts;
        }
        $langShortForm = 'en';
        $lang = getLanguage($langId);
        if (!empty($lang)) {
            $langShortForm = esc($lang->short_form);
        }
        $this->buildQuery($langId, true);
        $this->builder->select("'" . $langShortForm . "' AS lang_short_form");
        if (!empty($categoryId)) {
            $categoryIds = getCategoryTree($categoryId, $categories);
            if (countItems($categoryIds) > 0) {
                $this->builder->whereIn('posts.category_id', $categoryIds, false);
            }
        }
        if (!empty($userId)) {
            $this->builder->where('posts.user_id', clrNum($userId));
        }
        $posts = $this->builder->where('posts.is_scheduled', 0)->where('posts.visibility', 1)->where('posts.status = 1')->orderBy('posts.created_at DESC')->get($limit)->getResult();
        cache()->save($cacheKey, $posts, RSS_CACHE_REFRESH_TIME);
        return $posts;
    }

    //increase post pageviews
    public function incrementPostViews($postId)
    {
        //check session
        $sesPostRead = getSession('vpr_' . $postId);
        if (!empty($sesPostRead)) {
            return false;
        }
        //check robot
        $agent = $this->request->getUserAgent();
        if ($agent->isRobot()) {
            return false;
        }
        //check visit hash
        $userAgent = $agent->getAgentString();
        $ipAddress = getIPAddress();
        $visitHash = md5($postId . $ipAddress . $userAgent);
        $row = $this->db->table('post_pageviews_month')->where('visit_hash', $visitHash)->get()->getRow();
        if (!empty($row)) {
            return false;
        }
        //increment post views
        $post = getPostById($postId);
        if (empty($post)) {
            return false;
        }
        $author = getUserById($post->user_id);
        if (empty($author)) {
            return false;
        }
        if ($this->builder->where('id', $post->id)->update(['pageviews' => $post->pageviews + 1])) {
            setSession('vpr_' . $post->id, '1');
            $rewardAmount = 0;
            if ($this->generalSettings->reward_system_status == 1 && !empty($this->generalSettings->reward_amount) && $author->reward_system_enabled == 1) {
                $rewardAmount = number_format($this->generalSettings->reward_amount / 1000, 10, '.', '');
            }
            $data = [
                'post_id' => $post->id,
                'post_user_id' => $post->user_id,
                'ip_address' => $ipAddress,
                'reward_amount' => $rewardAmount,
                'visit_hash' => $visitHash,
                'created_at' => date('Y-m-d H:i:s')
            ];
            if ($this->db->table('post_pageviews_month')->insert($data)) {
                if ($rewardAmount > 0) {
                    $newBalance = $author->balance + $rewardAmount;
                    if (!empty($newBalance)) {
                        $newBalance = number_format($newBalance, 10, '.', '');
                    }
                    $this->db->query("UPDATE users SET balance = ?, total_pageviews = total_pageviews + 1 WHERE id = ?", [$newBalance, $post->user_id]);
                }
            }
        }
    }

    //check scheduled posts
    public function checkScheduledPosts()
    {
        $posts = cache('cstable_scheduled_posts');
        $isUpdated = false;
        if (!empty($posts)) {
            $date = date('Y-m-d H:i:s');
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    if (strtotime($post->created_at) <= strtotime($date)) {
                        $this->builder->where('id', $post->id)->update(['is_scheduled' => 0, 'created_at' => $date]);
                        $isUpdated = true;
                    }
                }
            }
        }
        if ($isUpdated) {
            resetCacheDataOnChange();
            @unlink(WRITEPATH . 'cache/cstable_scheduled_posts');
            $this->setScheduledPostsCache();
        }
    }

    // set scheduled posts cache
    public function setScheduledPostsCache()
    {
        $posts = $this->builder->select('id, created_at')->where('status', 1)->where('is_scheduled', 1)->get()->getResult();
        if (!empty($posts)) {
            cache()->save('cstable_scheduled_posts', $posts, 21600); //6 hours
        }
    }

    //delete old page views
    public function deleteOldPageviews()
    {
        $now = date('Y-m-d H:i:s');
        $month = strtotime("-30 days", strtotime($now));
        $this->db->query("DELETE FROM post_pageviews_month WHERE created_at < '" . date('Y-m-d H:i:s', $month) . "'");
    }
}