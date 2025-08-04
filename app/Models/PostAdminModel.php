<?php namespace App\Models;

require_once APPPATH . 'Libraries/GoogleIndexer.php';

use App\Libraries\QueryBuilder;
use CodeIgniter\Model;
use App\Libraries\GoogleIndexer;
use Google\Service\Indexing;

class PostAdminModel extends BaseModel
{
    protected $builder;
    protected $builderPostsSelections;
    protected $builderPostImages;
    protected $builderPostFiles;
    protected $builderPostAudios;

    public function __construct()
    {
        parent::__construct();
        $this->builder = new QueryBuilder('posts', $this->db);
        $this->builderPostsSelections = $this->db->table('post_selections');
        $this->builderPostImages = $this->db->table('post_images');
        $this->builderPostFiles = $this->db->table('post_files');
        $this->builderPostAudios = $this->db->table('post_audios');
    }

    public function buildQuery()
    {
        $this->builder->select("posts.*, (SELECT CONCAT('img_bg::', i.image_big, '||','img_df::', i.image_default, '||','img_sl::', i.image_slider, '||','img_md::', i.image_mid, '||','img_sm::', i.image_small, '||','img_mi::', i.image_mime, '||',
        'img_st::', i.storage) FROM images i WHERE i.id = posts.image_id LIMIT 1) AS image_data");
    }

    //input values
    public function inputValues()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'slug' => inputPost('slug'),
            'summary' => inputPost('summary'),
            'category_id' => inputPost('category_id'),
            'content' => inputPost('content'),
            'optional_url' => inputPost('optional_url'),
            'need_auth' => inputPost('need_auth'),
            'visibility' => inputPost('visibility'),
            'show_right_column' => inputPost('show_right_column'),
            'keywords' => inputPost('keywords'),
            'image_description' => inputPost('image_description')
        ];
    }

    //add post
    public function addPost($postType)
    {
        $data = $this->setPostData($postType);
        $isScheduled = inputPost('scheduled_post');
        $datePublished = inputPost('date_published');
        $data['is_scheduled'] = 0;
        if ($isScheduled) {
            $data['is_scheduled'] = 1;
        }
        if (!empty($datePublished)) {
            $data['created_at'] = $datePublished;
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $data['show_post_url'] = 0;
        $data['post_type'] = $postType;
        $data['user_id'] = user()->id;
        $data['status'] = inputPost('status');
        if (empty($data['status'])) {
            $data['status'] = 0;
        }
        if (empty($data['show_right_column'])) {
            $data['show_right_column'] = 0;
        }
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //update post
    public function editPost($id, $postType)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $data = $this->setPostData($postType);
            $data['created_at'] = inputPost('date_published');
            $data['user_id'] = inputPost('user_id');
            $data['is_scheduled'] = 0;
            if (!empty(inputPost('scheduled_post'))) {
                $data['is_scheduled'] = 1;
                $data['status'] = 1;
            }
            $publish = inputPost('publish');
            if (!empty($publish) && $publish == 1) {
                $data['status'] = 1;
            }
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->builder->where('id', $post->id)->update($data);
        }
        return false;
    }

    //set post data
    public function setPostData($postType)
    {
        $data = $this->inputValues();
        if (!isset($data['need_auth'])) {
            $data['need_auth'] = 0;
        }
        $subCategoryId = inputPost('subcategory_id');
        if (!empty($subCategoryId)) {
            $data['category_id'] = $subCategoryId;
        }
        $data['show_item_numbers'] = 0;
        if (!empty(inputPost('show_item_numbers'))) {
            $data['show_item_numbers'] = 1;
        }
        $arrayLS = array();
        $arrayLS[1] = [
            'style' => in_array(inputPost('link_list_style_1'), getCssListStyles()) ? inputPost('link_list_style_1') : 'none',
            'status' => !empty(inputPost('link_list_style_show_1')) ? 1 : 0
        ];
        $arrayLS[2] = [
            'style' => in_array(inputPost('link_list_style_2'), getCssListStyles()) ? inputPost('link_list_style_2') : 'none',
            'status' => !empty(inputPost('link_list_style_show_2')) ? 1 : 0
        ];
        $arrayLS[3] = [
            'style' => in_array(inputPost('link_list_style_3'), getCssListStyles()) ? inputPost('link_list_style_3') : 'none',
            'status' => !empty(inputPost('link_list_style_show_3')) ? 1 : 0
        ];
        $data['link_list_style'] = serialize($arrayLS);

        if (empty($data['slug'])) {
            $data['slug'] = strSlug($data['title']);
        } else {
            $data['slug'] = removeSpecialCharacters($data['slug'], true);
            if (!empty($data['slug'])) {
                $data['slug'] = str_replace(' ', '-', $data['slug']);
            }
        }
        if ($postType == 'video' || $postType == 'recipe') {
            $data['video_url'] = inputPost('video_url');
            $data['video_embed_code'] = inputPost('video_embed_code');
            $data['video_path'] = inputPost('video_path');
            $data['video_storage'] = inputPost('video_storage');
        }
        $votePermission = inputPost('vote_permission');
        if ($votePermission == 'registered') {
            $data['is_poll_public'] = 0;
        } else {
            $data['is_poll_public'] = 1;
        }
        if ($postType == 'recipe') {
            $data['recipe_info'] = inputPost('recipe_info');
            $recipeData = array();
            $recipeData['prep_time'] = inputPost('prep_time');
            $recipeData['cook_time'] = inputPost('cook_time');
            $recipeData['serving'] = inputPost('serving');
            $recipeData['difficulty'] = inputPost('difficulty');
            $recipeData['ingredients'] = inputPost('ingredients');
            $recipeData['nInfo'] = array();
            $nutritionalIds = inputPost('nutritional_id');
            if (!empty($nutritionalIds)) {
                foreach ($nutritionalIds as $nId) {
                    $item = array(
                        'n' => inputPost('nutritional_name_' . $nId),
                        'v' => inputPost('nutritional_value_' . $nId)
                    );
                    array_push($recipeData['nInfo'], $item);
                }
            }
            $data['post_data'] = serialize($recipeData);
        }
        if (!empty(inputPost('post_image_id'))) {
            $data['image_id'] = inputPost('post_image_id');
        }
        if (!empty(inputPost('image_url'))) {
            $data['image_url'] = inputPost('image_url');
        }
        if (empty($data['visibility'])) {
            $data['visibility'] = 0;
        }
        return $data;
    }

    //update post selections
    public function updatePostSelections($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $this->addRemoveSelectedPost($post, 'slider', empty(inputPost('is_slider')) ? 'remove' : 'add');
            $this->addRemoveSelectedPost($post, 'featured', empty(inputPost('is_featured')) ? 'remove' : 'add');
            $this->addRemoveSelectedPost($post, 'recommended', empty(inputPost('is_recommended')) ? 'remove' : 'add');
            $this->addRemoveSelectedPost($post, 'breaking', empty(inputPost('is_breaking')) ? 'remove' : 'add');
        }
    }

    //add or remove selected post
    public function addRemoveSelectedPost($post, $type, $action = 'both')
    {
        if (!empty($post)) {
            $type = cleanStr($type);
            $types = ['slider', 'featured', 'breaking', 'recommended'];
            if (!in_array($type, $types)) {
                return false;
            }
            $row = $this->builderPostsSelections->where('post_id', $post->id)->where('selection_type', cleanStr($type))->get()->getRow();
            if ($action == 'both') {
                if (empty($row)) {
                    return $this->builderPostsSelections->insert(['post_id' => $post->id, 'selection_type' => $type]);
                } else {
                    return $this->builderPostsSelections->where('id', $row->id)->delete();
                }
            } elseif ($action == 'add') {
                if (empty($row)) {
                    return $this->builderPostsSelections->insert(['post_id' => $post->id, 'selection_type' => $type]);
                }
            } elseif ($action == 'remove') {
                if (!empty($row)) {
                    return $this->builderPostsSelections->where('id', $row->id)->delete();
                }
            }
        }
        return false;
    }

    //check if post selected
    public function isPostSelected($postId, $type)
    {
        if ($this->builderPostsSelections->where('post_id', clrNum($postId))->where('selection_type', cleanStr($type))->countAllResults() > 0) {
            return true;
        }
        return false;
    }

    //get post selections
    public function getPostSelections($postId)
    {
        return $this->builderPostsSelections->where('post_id', clrNum($postId))->get()->getResult();
    }

    //get post
    public function getPost($id)
    {
        $this->buildQuery();
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get posts count
    public function getPostsCount($list = null)
    {
        $this->filterPosts($list);
        if ($list == null || $list == 'posts') {
            $this->builder->addUseIndex('idx_posts_optimized');
        }
        return $this->builder->where('posts.is_scheduled', 0)->where('posts.status', 1)->where('posts.visibility', 1)->countAllResults();
    }

    //get paginated posts
    public function getPostsPaginated($list, $perPage, $offset)
    {
        $this->filterPosts($list);
        if ($list == 'posts') {
            $this->builder->addUseIndex('idx_created_at');
        }
        return $this->builder->where('posts.visibility', 1)->where('posts.status', 1)->where('posts.is_scheduled', 0)->orderBy('posts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get pending posts count
    public function getPendingPostsCount()
    {
        $this->filterPosts();
        return $this->builder->where('posts.is_scheduled', 0)->where('posts.status', 1)->where('posts.visibility', 0)->countAllResults();
    }

    //get paginated pending posts
    public function getPendingPostsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.visibility', 0)->where('posts.status', 1)->where('posts.is_scheduled', 0)->orderBy('posts.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get scheduled posts count
    public function getScheduledPostsCount()
    {
        $this->filterPosts();
        return $this->builder->where('posts.is_scheduled', 1)->where('posts.status', 1)->countAllResults();
    }

    //get paginated scheduled posts
    public function getScheduledPostsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.is_scheduled', 1)->where('posts.status', 1)->orderBy('posts.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get drafts count
    public function getDraftsCount()
    {
        $this->filterPosts();
        return $this->builder->where('posts.status', 0)->countAllResults();
    }

    //get paginated scheduled posts
    public function getDraftsPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->where('posts.status', 0)->orderBy('posts.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter by values
    public function filterPosts($list = null)
    {
        $langId = clrNum(inputGet('lang_id'));
        $postType = cleanStr(inputGet('post_type'));
        $user = clrNum(inputGet('user'));
        $category = clrNum(inputGet('category'));
        $subCategory = clrNum(inputGet('subcategory'));
        $q = inputGet('q');
        if (!empty($subCategory)) {
            $category = $subCategory;
        }
        $userId = null;
        if (hasPermission('manage_all_posts')) {
            if (!empty($user)) {
                $userId = $user;
            }
        } else {
            $userId = user()->id;
        }

        $this->buildQuery();
        if (!empty($userId)) {
            $this->builder->where('posts.user_id', clrNum($userId));
        }
        if (!empty($langId)) {
            $this->builder->where('posts.lang_id', clrNum($langId));
        }
        if (!empty($postType)) {
            $this->builder->where('posts.post_type', cleanStr($postType));
        }
        if (!empty($category)) {
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getCategories();
            $categoryIds = getCategoryTree($category, $categories);
            if (!empty($categoryIds) && countItems($categoryIds) > 0) {
                $this->builder->whereIn('posts.category_id', $categoryIds, false);
            }
        }
        if (!empty($q)) {
            $this->builder->like('posts.title', cleanStr($q));
        }
        if (!empty($list)) {
            if ($list == 'slider_posts') {
                $this->builder->select('post_selections.selection_type')->join('post_selections', 'posts.id = post_selections.post_id')->where('post_selections.selection_type', 'slider');
            }
            if ($list == 'featured_posts') {
                $this->builder->select('post_selections.selection_type')->join('post_selections', 'posts.id = post_selections.post_id')->where('post_selections.selection_type', 'featured');
            }
            if ($list == 'breaking_news') {
                $this->builder->select('post_selections.selection_type')->join('post_selections', 'posts.id = post_selections.post_id')->where('post_selections.selection_type', 'breaking');
            }
            if ($list == 'recommended_posts') {
                $this->builder->select(' post_selections.selection_type')->join('post_selections', 'posts.id = post_selections.post_id')->where('post_selections.selection_type', 'recommended');
            }
        }
    }

    //approve post
    public function approvePost($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['visibility' => 1]);
        }
        return false;
    }

    //publish post
    public function publishPost($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['is_scheduled' => 0, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    //publish draft
    public function publishDraft($post)
    {
        if (!empty($post)) {
            return $this->builder->where('id', $post->id)->update(['status' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    //save home slider post order
    public function setHomeSliderPostOrder($id, $order)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $order = clrNum($order);
            if ($order > 999999) {
                $order = 999999;
            }
            $this->builder->where('id', $post->id)->update(['slider_order' => $order]);
        }
    }

    //save feaured post order
    public function setFeauredPostOrder($id, $order)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $order = clrNum($order);
            if ($order > 999999) {
                $order = 999999;
            }
            $this->builder->where('id', $post->id)->update(['featured_order' => $order]);
        }
    }

    //post bulk options
    public function postBulkOptions($operation, $postIds)
    {
        $data = array();
        $action = null;
        $slType = null;
        if ($operation == 'add_slider') {
            $action = 'add';
            $slType = 'slider';
        } elseif ($operation == 'remove_slider') {
            $action = 'remove';
            $slType = 'slider';
        } elseif ($operation == 'add_featured') {
            $action = 'add';
            $slType = 'featured';
        } elseif ($operation == 'remove_featured') {
            $action = 'remove';
            $slType = 'featured';
        } elseif ($operation == 'add_breaking') {
            $action = 'add';
            $slType = 'breaking';
        } elseif ($operation == 'remove_breaking') {
            $action = 'remove';
            $slType = 'breaking';
        } elseif ($operation == 'add_recommended') {
            $action = 'add';
            $slType = 'recommended';
        } elseif ($operation == 'remove_recommended') {
            $action = 'remove';
            $slType = 'recommended';
        } elseif ($operation == 'publish_scheduled') {
            $data['is_scheduled'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
        } elseif ($operation == 'approve') {
            $data['visibility'] = 1;
        } elseif ($operation == 'publish_draft') {
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $post = $this->getPost($id);
                if (!empty($post)) {
                    if (!empty($action) && !empty($slType)) {
                        $this->addRemoveSelectedPost($post, $slType, $action);
                    } else {
                        if (!empty($data)) {
                            $this->builder->where('id', $post->id)->update($data);
                        }
                    }
                }
            }
        }
    }

    //delete post
    public function deletePost($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            //delete from selections
            $this->builderPostsSelections->where('post_id', $post->id)->delete();
            //delete post images
            if ($this->generalSettings->delete_images_with_post == 1) {
                $fileModel = new FileModel();
                $fileModel->deletePostImage($post);
                //delete additional images
                $this->deleteAdditionalImages($post->id, true);
            } else {
                $this->deleteAdditionalImages($post->id, false);
            }
            //delete audios
            $this->deletePostAudios($post->id);
            //delete list items
            $postItemModel = new PostItemModel();
            $postItemModel->deletePostListItems($post->id, 'gallery');
            $postItemModel->deletePostListItems($post->id, 'sorted_list');
            //delete quiz questions
            $quizModel = new QuizModel();
            $quizModel->deleteQuizQuestions($post->id);
            $quizModel->deleteQuizResults($post->id);
            //delete post tags
            $tagModel = new TagModel();
            $tagModel->deletePostTags($post->id);
            //delete comments
            $this->db->table('comments')->where('post_id', $post->id)->delete();
            //remove google index
            $this->requestRemoveGoogleIndexing($post);
            //delete post
            return $this->builder->where('id', $post->id)->delete();
        }
        return false;
    }

    //delete multi post
    public function deleteMultiPosts($postIds)
    {
        if (!empty($postIds)) {
            foreach ($postIds as $id) {
                $this->deletePost($id);
            }
        }
    }

    //delete old posts
    public function deleteOldPosts()
    {
        if ($this->generalSettings->auto_post_deletion == 1) {
            $days = $this->generalSettings->auto_post_deletion_days;
            if ($this->generalSettings->auto_post_deletion_delete_all != 1) {
                $this->builder->where("feed_id != ''");
            }
            $posts = $this->builder->where('created_at < DATE_SUB(NOW(), INTERVAL ' . clrNum($days) . ' DAY)')->get()->getResult();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $this->deletePost($post->id);
                }
            }
        }
    }

    /*
    *------------------------------------------------------------------------------------------
    * POST FILES
    *------------------------------------------------------------------------------------------
    */

    //delete post main image
    public function deletePostMainImage($postId)
    {
        $post = $this->getPost($postId);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            $this->builder->where('id', $post->id)->update(['image_id' => '', 'image_url'=>'']);
        }
    }

    //add post additional images
    public function addPostAdditionalImages($postId)
    {
        $imageIds = inputPost('additional_post_image_id');
        if (!empty($imageIds)) {
            foreach ($imageIds as $imageId) {
                $fileModel = new FileModel();
                $image = $fileModel->getImage($imageId);
                if (!empty($image)) {
                    $item = [
                        'post_id' => $postId,
                        'image_big' => $image->image_big,
                        'image_default' => $image->image_default,
                        'storage' => $image->storage
                    ];
                    if (!empty($item['image_default'])) {
                        $this->builderPostImages->insert($item);
                    }
                }
            }
        }
    }

    //delete additional image
    public function deletePostAdditionalImage($fileId)
    {
        $image = $this->getAdditionalImage($fileId);
        if (!empty($image)) {
            $this->builderPostImages->where('id', $image->id)->delete();
        }
    }

    //get additional images
    public function getAdditionalImages($postId)
    {
        return $this->builderPostImages->where('post_id', clrNum($postId))->get()->getResult();
    }

    //get additional image
    public function getAdditionalImage($id)
    {
        return $this->builderPostImages->where('id', cleanStr($id))->get()->getRow();
    }

    //delete additional images
    public function deleteAdditionalImages($postId, $deleteFile)
    {
        $fileModel = new FileModel();
        $images = $this->getAdditionalImages($postId);
        if (!empty($images)) {
            foreach ($images as $item) {
                if ($deleteFile) {
                    $image = $this->db->table('images')->where('image_big', $item->image_big)->get()->getRow();
                    if (!empty($image)) {
                        $fileModel->deleteImage($image->id);
                    }
                }
                $this->builderPostImages->where('id', $item->id)->delete();
            }
        }
    }

    //get post audio
    public function getPostAudio($audioId)
    {
        return $this->builderPostAudios->where('id', clrNum($audioId))->get()->getRow();
    }

    //add post audios
    public function addPostAudios($postId)
    {
        $audioIds = inputPost('post_audio_id');
        if (!empty($audioIds)) {
            foreach ($audioIds as $audioId) {
                $fileModel = new FileModel();
                $audio = $fileModel->getAudio($audioId);
                if (!empty($audio)) {
                    $item = [
                        'post_id' => $postId,
                        'audio_id' => $audio->id,
                    ];
                    $this->builderPostAudios->insert($item);
                }
            }
        }
    }

    //get post audios
    public function getPostAudios($postId)
    {
        return $this->db->table('audios')->join('post_audios', 'audios.id = post_audios.audio_id')->select('audios.*, post_audios.id AS post_audio_id')
            ->where('post_audios.post_id', clrNum($postId))->orderBy('post_audios.id')->get()->getResult();
    }

    //delete post audio
    public function deletePostAudio($id)
    {
        $row = $this->getPostAudio($id);
        if (!empty($row)) {
            $post = $this->getPost($row->post_id);
            if (!empty($post)) {
                if (!checkPostOwnership($post->user_id)) {
                    return false;
                }
                $this->builderPostAudios->where('id', clrNum($id))->delete();
            }
        }
    }

    //delete post audios
    public function deletePostAudios($postId)
    {
        $audios = $this->builderPostAudios->where('post_id', clrNum($postId))->get()->getResult();
        if (!empty($audios)) {
            foreach ($audios as $audio) {
                $this->builderPostAudios->where('id', $audio->id)->delete();
            }
        }
    }

    //delete post video
    public function deletePostVideo($postId)
    {
        $post = $this->getPost($postId);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            $this->builder->where('id', $post->id)->update(['video_path' => '']);
        }
    }

    //add post files
    public function addPostFiles($postId)
    {
        $fileIds = inputPost('post_selected_file_id');
        if (!empty($fileIds)) {
            foreach ($fileIds as $fileId) {
                $fileModel = new FileModel();
                $file = $fileModel->getFile($fileId);
                if (!empty($file)) {
                    $item = [
                        'post_id' => $postId,
                        'file_id' => $file->id
                    ];
                    $this->builderPostFiles->insert($item);
                }
            }
        }
    }

    //get post file
    public function getPostFile($id)
    {
        return $this->builderPostFiles->where('id', cleanStr($id))->get()->getRow();
    }

    //get post files
    public function getPostFiles($postId)
    {
        return $this->builderPostFiles->join('files', 'files.id = post_files.file_id')->select('files.*, post_files.id AS post_file_id')
            ->where('post_files.post_id', clrNum($postId))->orderBy('post_files.id')->get()->getResult();
    }

    //delete post file
    public function deletePostFile($id)
    {
        $file = $this->getPostFile($id);
        if (!empty($file)) {
            $post = $this->getPost($file->post_id);
            if (!checkPostOwnership($post->user_id)) {
                return false;
            }
            if (!empty($post)) {
                $this->builderPostFiles->where('id', $file->id)->delete();
            }
        }
    }

    //submit post URL to google
    public function requestGoogleIndexing($post)
    {
        if (!empty($post) && $this->generalSettings->google_indexing_api == 1 && isPostPublished($post)) {
            $baseUrl = generateBaseURLByLangId($post->lang_id);
            $postUrl = generatePostURL($post, $baseUrl);
            if (!empty($postUrl)) {
                $indexer = new GoogleIndexer();
                $indexer->indexUrl($postUrl);
            }
        }
    }

    //submit post URL to remove from google
    public function requestRemoveGoogleIndexing($post)
    {
        if (!empty($post) && $this->generalSettings->google_indexing_api == 1) {
            $baseUrl = generateBaseURLByLangId($post->lang_id);
            $postUrl = generatePostURL($post, $baseUrl);
            if (!empty($postUrl)) {
                $indexer = new GoogleIndexer();
                $indexer->removeUrl($postUrl);
            }
        }
    }

    //test google indexing
    public function requestTestGoogleIndexing()
    {
        if ($this->generalSettings->google_indexing_api == 1) {
            $indexer = new GoogleIndexer();
            return $indexer->indexUrl(base_url());
        }
        return null;
    }

    //generate CSV object
    public function generateCSVObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                return false;
            }
            fclose($handle);
            if (!empty($array)) {
                $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
                fwrite($txtFile, serialize($array));
                fclose($txtFile);
                $obj = new \stdClass();
                $obj->numberOfItems = countItems($array);
                $obj->txtFileName = $txtName;
                @unlink($filePath);
                return $obj;
            }
        }
        return false;
    }

    //import csv item
    public function importCSVItem($txtFileName, $index)
    {
        $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
        $file = fopen($filePath, 'r');
        $content = fread($file, filesize($filePath));
        $array = @unserialize($content);
        if (!empty($array)) {
            $uploadModel = new UploadModel();
            $i = 1;
            foreach ($array as $item) {
                if ($i == $index) {
                    $data = array();
                    $data['lang_id'] = getCSVInputValue($item, 'lang_id', 'int');
                    $data['title'] = getCSVInputValue($item, 'title');
                    $data['slug'] = getCSVInputValue($item, 'slug') ? getCSVInputValue($item, 'slug') : strSlug($data['title']);
                    $data['title_hash'] = '';
                    $data['keywords'] = getCSVInputValue($item, 'keywords');
                    $data['summary'] = getCSVInputValue($item, 'summary');
                    $data['content'] = getCSVInputValue($item, 'content');
                    $data['category_id'] = getCSVInputValue($item, 'category_id', 'int');
                    $data['image_id'] = '';
                    $data['optional_url'] = '';
                    $data['pageviews'] = 0;
                    $data['need_auth'] = 0;
                    $data['slider_order'] = 0;
                    $data['featured_order'] = 0;
                    $data['is_scheduled'] = 0;
                    $data['visibility'] = 0;
                    $data['post_type'] = getCSVInputValue($item, 'post_type') ? getCSVInputValue($item, 'post_type') : 'article';
                    $data['video_path'] = '';
                    $data['image_url'] = '';
                    $data['video_url'] = '';
                    $data['video_embed_code'] = getCSVInputValue($item, 'video_embed_code');
                    $data['user_id'] = user()->id;
                    $data['status'] = getCSVInputValue($item, 'status', 'int');
                    $data['feed_id'] = 0;
                    $data['post_url'] = '';
                    $data['show_post_url'] = 0;
                    $data['image_description'] = getCSVInputValue($item, 'image_description');
                    $data['show_item_numbers'] = 0;
                    $data['created_at'] = date('Y-m-d H:i:s');
                    //download image
                    $imgURL = getCSVInputValue($item, 'image_url');
                    if (!empty($imgURL)) {
                        $ext = pathinfo($imgURL, PATHINFO_EXTENSION);
                        if ($ext == 'gif') {
                            try {
                                $tempPath = $uploadModel->downloadTempImage($imgURL, 'gif');
                                if (!empty($tempPath) && file_exists($tempPath)) {
                                    $gifPath = $uploadModel->uploadGIF('temp.gif', 'images');
                                    $dataImage = [
                                        'image_big' => $gifPath,
                                        'image_default' => $gifPath,
                                        'image_slider' => $gifPath,
                                        'image_mid' => $gifPath,
                                        'image_small' => $gifPath,
                                        'image_mime' => 'gif',
                                        'file_name' => $data['slug'],
                                        'user_id' => user()->id
                                    ];
                                }
                            } catch (\Exception $e) {
                            }
                        } else {
                            try {
                                $tempPath = $uploadModel->downloadTempImage($imgURL, 'jpg');
                                if (!empty($tempPath) && file_exists($tempPath)) {
                                    $dataImage = [
                                        'image_big' => $uploadModel->uploadPostImage($tempPath, 'big'),
                                        'image_default' => $uploadModel->uploadPostImage($tempPath, 'default'),
                                        'image_slider' => $uploadModel->uploadPostImage($tempPath, 'slider'),
                                        'image_mid' => $uploadModel->uploadPostImage($tempPath, 'mid'),
                                        'image_small' => $uploadModel->uploadPostImage($tempPath, 'small'),
                                        'image_mime' => 'jpg',
                                        'file_name' => $data['slug'],
                                        'user_id' => user()->id
                                    ];
                                }
                            } catch (\Exception $e) {
                            }
                        }
                        //add image to database
                        if (!empty($dataImage)) {
                            if ($this->db->table('images')->insert($dataImage)) {
                                $data['image_id'] = $this->db->insertID();
                            }
                        }
                    }
                    //check visibility
                    if (hasPermission('manage_all_posts') || $this->generalSettings->approve_updated_user_posts != 1) {
                        $data['visibility'] = 1;
                    }
                    if ($this->builder->insert($data)) {
                        $lastId = $this->db->insertID();
                        //update slug
                        updateSlug('posts', $lastId);
                        //add tags
                        $tags = getCSVInputValue($item, 'tags');
                        if (!empty($tags)) {
                            $tagModel = new TagModel();
                            $tagModel->addCsvPostTags($lastId, $tags);
                        }
                    }
                    return $data['title'];
                }
                $i++;
            }
        }
    }
}
