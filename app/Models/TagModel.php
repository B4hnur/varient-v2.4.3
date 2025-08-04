<?php namespace App\Models;

use CodeIgniter\Model;

class TagModel extends BaseModel
{
    protected $builder;
    protected $builderPostTags;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('tags');
        $this->builderPostTags = $this->db->table('post_tags');
    }

    //add tag
    public function addTag($tag, $langId)
    {
        $tag = strTrim($tag);
        $tagSlug = strSlug($tag);
        $row = $this->builder->where('tag_slug', $tagSlug)->where('lang_id', clrNum($langId))->get()->getRow();
        if (empty($row)) {
            $data = [
                'tag' => $tag,
                'tag_slug' => $tagSlug,
                'lang_id' => $langId,
            ];
            if (empty($data['tag_slug']) || $data['tag_slug'] == '-') {
                $data['tag_slug'] = 'tag-' . uniqid();
            }
            return $this->builder->insert($data);
        }
        return false;
    }

    //edit tag
    public function editTag($id, $tag, $langId)
    {
        $row = $this->getTagById($id);
        if (!empty($row)) {
            $tag = strTrim($tag);
            if (!empty($tag) && strlen($tag) > 1) {
                $data = [
                    'tag' => $tag,
                    'tag_slug' => strSlug($tag),
                    'lang_id' => clrNum($langId),
                ];
                if (empty($data['tag_slug']) || $data['tag_slug'] == '-') {
                    $data['tag_slug'] = 'tag-' . uniqid();
                }
                return $this->builder->where('id', $row->id)->update($data);
            }
        }
        return false;
    }

    //get popular tags
    public function getPopularTags($langId)
    {
        return getOrSetStableCache('popular_tags_lang_' . $langId, function () use ($langId) {
            $result = $this->builderPostTags->select("post_tags.tag_id, COUNT(post_tags.post_id) AS post_count")->join("tags", "post_tags.tag_id = tags.id")
                ->where("tags.lang_id", clrNum($langId))->groupBy("post_tags.tag_id")->orderBy('post_count DESC')->limit(SIDEBAR_TAGS_LIMIT)->get()->getResultArray();
            if (countItems($result) > 0) {
                $tagIds = array_column($result, 'tag_id');
                if (!empty($tagIds) && countItems($tagIds) > 0) {
                    return $this->builder->whereIn('tags.id', $tagIds, false)->orderBy('FIELD(tags.id, ' . implode(',', $tagIds) . ')')->get(SIDEBAR_TAGS_LIMIT)->getResult();
                }
            }
        }, POPULAR_TAGS_REFRESH_TIME);
    }

    //get tag suggestions
    public function getTagSuggestions($q)
    {
        $result = $this->builder->select("tag")->like('tags.tag', cleanStr($q), 'after')->limit(30)->distinct()->get()->getResult();
        $tags = array();
        if (countItems($result) > 0) {
            $tags = array_map(function ($item) {
                return esc($item->tag);
            }, $result);
        }
        return $tags;
    }

    //get tag
    public function getTag($tag)
    {
        return $this->builder->where('tags.tag', cleanStr($tag))->get()->getRow();
    }

    //get tag by id
    public function getTagById($id)
    {
        return $this->builder->where('tags.id', clrNum($id))->get()->getRow();
    }

    //get tag by slug
    public function getTagBySlug($slug, $langId)
    {
        return $this->builder->where('tags.tag_slug', cleanStr($slug))->where('tags.lang_id', clrNum($langId))->get()->getRow();
    }

    //get tags count
    public function getTagsCount()
    {
        $this->filterTags();
        return $this->builder->countAllResults();
    }

    //get paginated tags
    public function getTagsPaginated($perPage, $offset)
    {
        $this->filterTags();
        return $this->builder->select('tags.*, 
        (SELECT COUNT(post_tags.id) FROM post_tags WHERE post_tags.tag_id = tags.id) AS count,
        (SELECT name FROM languages WHERE tags.lang_id = languages.id) AS lang_name')->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter tags
    public function filterTags()
    {
        $q = inputGet('q');
        $langId = clrNum(inputGet('lang_id'));
        if (!empty($q)) {
            $this->builder->like('tag', cleanStr($q));
        }
        if (!empty($langId)) {
            $this->builder->where('lang_id', clrNum($langId));
        }
    }

    //get posts tags
    public function getPostTags($postId)
    {
        return $this->builder->join('post_tags', 'tags.id = post_tags.tag_id')->where('post_id', clrNum($postId))->get()->getResult();
    }

    //get posts tags string
    public function getPostTagsString($postId)
    {
        $str = '';
        $tagsArray = $this->getPostTags($postId);
        if (countItems($tagsArray) > 0) {
            $tags = array_column($tagsArray, 'tag');
            $str = implode(',', $tags);
        }
        return $str;
    }

    //add or update post tags
    public function addEditPostTags($postId, $tagsInput)
    {
        $post = getPostById($postId);
        if (empty($post)) {
            return false;
        }

        $tags = array();
        $tagsStr = '';
        $oldTagsStr = $this->getPostTagsString($postId);
        if (!empty($tagsInput)) {
            $tagsInput = json_decode($tagsInput, true);
            $tags = array_map(function ($item) {
                return $item['value'];
            }, $tagsInput);
            if (!empty($tags)) {
                $tagsStr = implode(',', $tags);
            }
        }
        if ($oldTagsStr != $tagsStr) {
            //add new tags
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    $this->addTag($tag, $post->lang_id);
                }
            }
            //delete old post tags
            $this->deletePostTags($postId);

            //add post tags
            if (!empty($tags)) {
                $count = 0;
                foreach ($tags as $tag) {
                    if ($count < POST_TAGS_LIMIT) {
                        $row = $this->getTag($tag);
                        if (!empty($row)) {
                            $data = [
                                'tag_id' => $row->id,
                                'post_id' => $post->id
                            ];
                            $this->builderPostTags->insert($data);
                        }
                    }
                    $count++;
                }
            }
        }
    }

    //add csv post tags
    public function addCsvPostTags($postId, $tagsStr)
    {
        $post = getPostById($postId);
        if (empty($post)) {
            return false;
        }
        $tags = array();
        if(!empty($tagsStr)){
            $tags = explode(',', $tagsStr);
        }

        //add new tags
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $this->addTag($tag, $post->lang_id);
            }

            $count = 0;
            foreach ($tags as $tag) {
                if ($count < POST_TAGS_LIMIT) {
                    $row = $this->getTag($tag);
                    if (!empty($row)) {
                        $data = [
                            'tag_id' => $row->id,
                            'post_id' => $post->id
                        ];
                        $this->builderPostTags->insert($data);
                    }
                }
                $count++;
            }
        }
    }

    //delete post tags
    public function deletePostTags($postId)
    {
        $this->builderPostTags->where('post_id', clrNum($postId))->delete();
    }

    //delete tag
    public function deleteTag($id)
    {
        $this->builder->where('id', clrNum($id))->delete();
        $this->builderPostTags->where('tag_id', clrNum($id))->delete();
    }
}
