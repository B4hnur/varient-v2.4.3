<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\PostAdminModel;
use App\Models\PostItemModel;
use App\Models\PostModel;
use App\Models\QuizModel;
use App\Models\TagModel;
use App\Models\UploadModel;

class PostController extends BaseAdminController
{
    protected $postAdminModel;
    protected $postModel;
    protected $postItemModel;
    protected $categoryModel;
    protected $quizModel;
    protected $authModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->postAdminModel = new PostAdminModel();
        $this->postModel = new PostModel();
        $this->postItemModel = new PostItemModel();
        $this->categoryModel = new CategoryModel();
        $this->quizModel = new QuizModel();
        $this->authModel = new AuthModel();
        if ($this->generalSettings->email_verification == 1 && user()->email_status == 0) {
            setErrorMessage("msg_confirmed_required");
            redirectToUrl(langBaseUrl());
            exit();
        }
        $this->postModel->setScheduledPostsCache();
    }

    /**
     * Post Format
     */
    public function postFormat()
    {
        checkPermission('add_post');
        $data['title'] = trans("choose_post_format");
        $data['panelSettings'] = panelSettings();

        echo view('admin/includes/_header', $data);
        echo view('admin/post/post_format', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Post
     */
    public function addPost()
    {
        checkPermission('add_post');
        $type = inputGet('type');
        $validTypes = ['article', 'gallery', 'sorted_list', 'table_of_contents', 'video', 'audio', 'trivia_quiz', 'personality_quiz', 'poll', 'recipe'];
        if (!in_array($type, $validTypes)) {
            $type = 'article';
        }
        $postFormat = "post_format_" . $type;
        if ($this->generalSettings->$postFormat != 1) {
            return redirect()->to(adminUrl('post-format'));
        }
        $title = "add_" . $type;
        $data['title'] = trans($title);
        $data['postType'] = $type;
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);
        $data['panelSettings'] = panelSettings();
        $view = $title;
        if ($type == 'trivia_quiz' || $type == 'personality_quiz') {
            $view = 'quiz/' . $title;
        }
        if ($type == 'poll') {
            $view = 'poll/' . $title;
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/post/' . $view, $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Post Post
     */
    public function addPostPost()
    {
        checkPermission('add_post');
        $postType = inputPost('post_type');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('category_id', trans("category"), 'required');
        $val->setRule('optional_url', trans("cateoptional_urlgory"), 'max_length[1000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('add-post?type=' . cleanStr($postType)))->withInput();
        } else {
            $postId = $this->postAdminModel->addPost($postType);
            if (!empty($postId)) {
                //update slug
                updateSlug('posts', $postId);
                $this->postAdminModel->updatePostSelections($postId);
                //add post tags
                $tagModel = new TagModel();
                $tagModel->addEditPostTags($postId, inputPost('tags'));

                if ($postType == 'article') {
                    $this->postAdminModel->addPostAdditionalImages($postId);
                } elseif ($postType == 'gallery') {
                    $this->postItemModel->addPostListItems($postId, 'gallery');
                } elseif ($postType == 'sorted_list') {
                    $this->postItemModel->addPostListItems($postId, 'sorted_list');
                } elseif ($postType == 'table_of_contents') {
                    $this->postItemModel->addPostListItems($postId, 'table_of_contents');
                } elseif ($postType == 'audio') {
                    $this->postAdminModel->addPostAudios($postId);
                } elseif ($postType == 'trivia_quiz') {
                    $this->quizModel->addQuizQuestions($postId);
                    $this->quizModel->addQuizResults($postId);
                } elseif ($postType == 'personality_quiz') {
                    $this->quizModel->addQuizResults($postId);
                    $this->quizModel->addQuizQuestions($postId);
                } elseif ($postType == 'poll') {
                    $this->quizModel->addQuizQuestions($postId);
                } elseif ($postType == 'recipe') {
                    $this->postItemModel->addPostListItems($postId, 'recipe');
                }
                //add post files
                if ($postType != 'gallery' && $postType != 'sorted_list') {
                    $this->postAdminModel->addPostFiles($postId);
                }
                resetCacheDataOnChange();
                $msg = trans("msg_added");
                $post = getPostById($postId);
                if (!empty($post)) {
                    $baseUrl = generateBaseURLByLangId($post->lang_id);
                    $postUrl = generatePostURL($post, $baseUrl);
                    if (!isPostPublished($post)) {
                        $postUrl = $baseUrl . 'preview/' . $post->slug;
                    }
                    $this->postAdminModel->requestGoogleIndexing($post);
                    $msg .= '&nbsp;&nbsp;<a href="' . $postUrl . '" target="_blank" class="alert-post-link font-w-700">' . trans("view_post") . '&nbsp;&nbsp;<i class="fa fa-external-link"></i></a>';
                }
                setSuccessMessage($msg, false);
                return redirect()->to(adminUrl('add-post?type=' . cleanStr($postType)));
            }
        }
        setErrorMessage("msg_error");
        return redirect()->to(adminUrl('add-post?type=' . cleanStr($postType)))->withInput();
    }

    /**
     * Update Post
     */
    public function editPost($id)
    {
        checkPermission('add_post');
        $data['post'] = $this->postAdminModel->getPost($id);
        if (empty($data['post'])) {
            return redirect()->to(adminUrl());
        }
        if (!checkPostOwnership($data['post']->user_id)) {
            return redirect()->to(adminUrl());
        }
        $data['title'] = trans('update_' . $data['post']->post_type);
        $tagModel = new TagModel();
        $data['tags'] = $tagModel->getPostTagsString($id);
        $data['postImages'] = $this->postAdminModel->getAdditionalImages($id);
        $authModel = new AuthModel();
        //define category ids
        $category = $this->categoryModel->getCategory($data['post']->category_id);
        $data['parentCategoryId'] = $data['post']->category_id;
        $data['subCategoryId'] = 0;
        if (!empty($category) && $category->parent_id != 0) {
            $parentCategory = $this->categoryModel->getCategory($category->parent_id);
            if (!empty($parentCategory)) {
                $data['parentCategoryId'] = $parentCategory->id;
                $data['subCategoryId'] = $category->id;
            }
        }
        $data['panelSettings'] = panelSettings();
        $data['categories'] = $this->categoryModel->getParentCategoriesByLang($data['post']->lang_id);
        $data['subCategories'] = $this->categoryModel->getSubCategoriesByParentId($data['parentCategoryId']);
        $data['postListItems'] = $this->postItemModel->getPostListItems($data['post']->id, $data['post']->post_type);

        $view = 'edit_' . $data['post']->post_type;
        if ($data['post']->post_type == 'trivia_quiz' || $data['post']->post_type == 'personality_quiz') {
            $view = 'quiz/edit_' . $data['post']->post_type;
            $quizModel = new QuizModel();
            $data['quizQuestions'] = $quizModel->getQuizQuestions($data['post']->id);
            $data['quizResults'] = $quizModel->getQuizResults($data['post']->id);
        }
        if ($data['post']->post_type == 'poll') {
            $view = 'poll/edit_' . $data['post']->post_type;
            $quizModel = new QuizModel();
            $data['quizQuestions'] = $quizModel->getQuizQuestions($data['post']->id);
        }

        $data['isPostSlider'] = $this->postAdminModel->isPostSelected($data['post']->id, 'slider');
        $data['isPostFeatured'] = $this->postAdminModel->isPostSelected($data['post']->id, 'featured');
        $data['isPostBreaking'] = $this->postAdminModel->isPostSelected($data['post']->id, 'breaking');
        $data['isPostRecommended'] = $this->postAdminModel->isPostSelected($data['post']->id, 'recommended');

        echo view('admin/includes/_header', $data);
        echo view('admin/post/' . $view, $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Update Post Post
     */
    public function editPostPost()
    {
        checkPermission('add_post');
        $postId = inputPost('id');
        $post = getPostById($postId);
        if (empty($post)) {
            return redirect()->to(adminUrl());
        }
        if (!checkPostOwnership($post->user_id)) {
            return redirect()->to(adminUrl());
        }
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('category_id', trans("category"), 'required');
        $val->setRule('optional_url', trans("cateoptional_urlgory"), 'max_length[1000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('edit-post/' . clrNum($postId)));
        } else {
            $postType = inputPost('post_type');
            if ($this->postAdminModel->editPost($postId, $postType)) {
                updateSlug('posts', $postId);
                $this->postAdminModel->updatePostSelections($postId);
                //edit tags
                $tagModel = new TagModel();
                $tagModel->addEditPostTags($postId, inputPost('tags'));

                if ($postType == 'article') {
                    $this->postAdminModel->addPostAdditionalImages($postId);
                } elseif ($postType == 'gallery') {
                    $this->postItemModel->editPostListItems($postId, 'gallery');
                } elseif ($postType == 'sorted_list') {
                    $this->postItemModel->editPostListItems($postId, 'sorted_list');
                } elseif ($postType == 'table_of_contents') {
                    $this->postItemModel->editPostListItems($postId, 'table_of_contents');
                } elseif ($postType == 'audio') {
                    $this->postAdminModel->addPostAudios($postId);
                } elseif ($postType == 'trivia_quiz') {
                    $this->quizModel->editQuizQuestions($post);
                    $this->quizModel->editQuizResults($post);
                } elseif ($postType == 'personality_quiz') {
                    $this->quizModel->editQuizResults($post);
                    $this->quizModel->editQuizQuestions($post);
                } elseif ($postType == 'poll') {
                    $this->quizModel->editQuizQuestions($post);
                } elseif ($postType == 'recipe') {
                    $this->postItemModel->editPostListItems($postId, 'recipe');
                }
                //add post files
                if ($postType != 'gallery' && $postType != 'sorted_list') {
                    $this->postAdminModel->addPostFiles($postId);
                }
                resetCacheDataOnChange();

                $msg = trans("msg_updated");
                $post = getPostById($postId);
                if (!empty($post)) {
                    $baseUrl = generateBaseURLByLangId($post->lang_id);
                    $postUrl = generatePostURL($post, $baseUrl);
                    if (!isPostPublished($post)) {
                        $postUrl = $baseUrl . 'preview/' . $post->slug;
                    }
                    $this->postAdminModel->requestGoogleIndexing($post);
                    $msg .= '&nbsp;&nbsp;<a href="' . $postUrl . '" target="_blank" class="alert-post-link font-w-700">' . trans("view_post") . '&nbsp;&nbsp;<i class="fa fa-external-link"></i></a>';
                }
                setSuccessMessage($msg, false);

            } else {
                setErrorMessage("msg_error");
            }
            return redirect()->to(adminUrl('edit-post/' . clrNum($postId)));
        }
    }

    /**
     * Posts
     */
    public function posts()
    {
        checkPermission('add_post');
        $data['title'] = trans('posts');
        $data['formAction'] = adminUrl('posts');
        $data['listType'] = 'posts';
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPostsCount('posts');
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = [];
        if ($numRows > 0) {
            $data['posts'] = $this->postAdminModel->getPostsPaginated('posts', $this->perPage, $data['pager']->offset);
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Slider Posts
     */
    public function sliderPosts()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('slider_posts');
        $data['formAction'] = adminUrl('slider-posts');
        $data['listType'] = 'slider_posts';
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPostsCount('slider_posts');
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated('slider_posts', $this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Featured Posts
     */
    public function featuredPosts()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('featured_posts');
        $data['formAction'] = adminUrl('featured-posts');
        $data['listType'] = 'featured_posts';
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPostsCount('featured_posts');
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated('featured_posts', $this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Breaking news
     */
    public function breakingNews()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('breaking_news');
        $data['formAction'] = adminUrl('breaking-news');
        $data['listType'] = 'breaking_news';
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPostsCount('breaking_news');
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated('breaking_news', $this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Recommended Posts
     */
    public function recommendedPosts()
    {
        checkPermission('manage_all_posts');
        $data['title'] = trans('recommended_posts');
        $data['formAction'] = adminUrl('recommended-posts');
        $data['listType'] = 'recommended_posts';
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPostsCount('recommended_posts');
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getPostsPaginated('recommended_posts', $this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Pending Posts
     */
    public function pendingPosts()
    {
        checkPermission('add_post');
        $data['title'] = trans('pending_posts');
        $data['formAction'] = adminUrl('pending-posts');
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getPendingPostsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getPendingPostsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/pending_posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Scheduled Posts
     */
    public function scheduledPosts()
    {
        checkPermission('add_post');
        $data['title'] = trans('scheduled_posts');
        $data['formAction'] = adminUrl('scheduled-posts');
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getScheduledPostsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getScheduledPostsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/scheduled_posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Drafts
     */
    public function drafts()
    {
        checkPermission('add_post');
        $data['title'] = trans('drafts');
        $data['formAction'] = adminUrl('drafts');
        $data['panelSettings'] = panelSettings();
        $numRows = $this->postAdminModel->getDraftsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['posts'] = $this->postAdminModel->getDraftsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/post/drafts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Post Options Post
     */
    public function postOptionsPost()
    {
        $id = inputPost('id');
        $option = inputPost('option');
        $backURL = inputPost('back_url');
        $post = $this->postAdminModel->getPost($id);
        if (empty($post)) {
            return redirect()->to(adminUrl());
        }
        $result = false;
        if ($option == 'add_remove_slider') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->addRemoveSelectedPost($post, 'slider')) {
                $result = true;
            }
        } elseif ($option == 'add_remove_featured') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->addRemoveSelectedPost($post, 'featured')) {
                $result = true;
            }
        } elseif ($option == 'add_remove_breaking') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->addRemoveSelectedPost($post, 'breaking')) {
                $result = true;
            }
        } elseif ($option == 'add_remove_recommended') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->addRemoveSelectedPost($post, 'recommended')) {
                $result = true;
            }
        } elseif ($option == 'approve') {
            checkPermission('manage_all_posts');
            if ($this->postAdminModel->approvePost($post)) {
                $this->postAdminModel->requestGoogleIndexing($post);
                $result = true;
            }
        } elseif ($option == 'publish') {
            checkPermission('add_post');
            if ($this->postAdminModel->publishPost($post)) {
                $this->postAdminModel->requestGoogleIndexing($post);
                $result = true;
            }
        } elseif ($option == 'publish_draft') {
            checkPermission('add_post');
            if ($this->postAdminModel->publishDraft($post)) {
                $this->postAdminModel->requestGoogleIndexing($post);
                $result = true;
            }
        }
        if ($result) {
            resetCacheDataOnChange();
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        if (empty($backURL)) {
            $backURL = adminUrl('posts');
        }
        return redirect()->to($backURL);
    }

    /**
     * Delete Post
     */
    public function deletePost()
    {
        checkPermission('add_post');
        $id = inputPost('id');
        if ($this->postAdminModel->deletePost($id)) {
            resetCacheDataOnChange();
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
    }

    /**
     * Delete Post Item List Item Post
     */
    public function deletePostListItemPost()
    {
        if (!hasPermission('add_post')) {
            exit();
        }
        $itemId = inputPost('item_id');
        $postType = inputPost('post_type');
        $this->postItemModel->deletePostListItem($itemId, $postType);
    }

    /**
     * Get List Item HTML
     */
    public function getListItemHTML()
    {
        if (!hasPermission('add_post')) {
            exit();
        }
        $postType = inputPost('post_type');
        $vars = ['newItemOrder' => inputPost('new_item_order'), 'postType' => $postType];
        $data = [
            'result' => 1,
            'html' => view('admin/post/_post_list_item', $vars)
        ];
        echo json_encode($data);
    }

    /**
     * Add List Item
     */
    public function addListItem()
    {
        if (!hasPermission('add_post')) {
            exit();
        }
        $postId = inputPost('post_id');
        $postType = inputPost('post_type');
        $listItemId = $this->postItemModel->addPostListItem($postId, $postType);
        $listItem = $this->postItemModel->getPostListItem($listItemId, $postType);
        if (!empty($listItem)) {
            $vars = ['postListItem' => $listItem, 'postType' => $postType];
            $data = [
                'result' => 1,
                'html' => view('admin/post/_post_list_item', $vars)
            ];
        } else {
            $data = [
                'result' => 0,
                'html' => ''
            ];
        }
        echo json_encode($data);
    }


    /*
    *-------------------------------------------------------------------------------------------------
    * AUDIO
    *-------------------------------------------------------------------------------------------------
    */

    /**
     * Delete Post Audio
     */
    public function deletePostAudio()
    {
        $postAudioId = inputPost('post_audio_id');
        $this->postAdminModel->deletePostAudio($postAudioId);
    }

    /*
    *-------------------------------------------------------------------------------------------------
    * VIDEO
    *-------------------------------------------------------------------------------------------------
    */

    /**
     * Get Video from URL
     */
    public function getVideoFromURL()
    {
        loadLibrary('VideoURLParser');
        $videoParser = new \VideoURLParser();
        $url = inputPost('url');
        if (!empty($url)) {
            $data = [
                'videoEmbedCode' => $videoParser->getVideoEmbedCode($url),
                'videoThumbnail' => $videoParser->getVideoThumbnail($url)
            ];
            echo json_encode($data);
        }
    }

    /**
     * Delete Video
     */
    public function deletePostVideo()
    {
        $postId = inputPost('post_id');
        $this->postAdminModel->deletePostVideo($postId);
    }

    /*
    *-------------------------------------------------------------------------------------------------
    * QUIZ
    *-------------------------------------------------------------------------------------------------
    */

    /**
     * Get Quiz Question HTML
     */
    public function getQuizQuestionHTML()
    {
        $vars = ['postType' => inputPost('post_type'), 'newQuestionOrder' => inputPost('new_question_order')];
        $data = [
            'result' => 1,
            'html' => view('admin/post/quiz/_add_question', $vars)
        ];
        echo json_encode($data);
    }

    /**
     * Add Quiz Question
     */
    public function addQuizQuestion()
    {
        $postId = inputPost('post_id');
        $postType = inputPost('post_type');
        $questionId = $this->quizModel->addQuizQuestion($postId);
        if (!empty($questionId)) {
            $question = $this->quizModel->getQuizQuestion($questionId);
            if (!empty($question)) {
                $vars = ['postType' => $postType, 'question' => $question];
                $data = [
                    'result' => 1,
                    'html' => view('admin/post/quiz/_edit_question', $vars)
                ];
            }
            echo json_encode($data);
            exit();
        }
        $data = [
            'result' => 0,
            'html' => ''
        ];
        echo json_encode($data);
    }

    /**
     * Delete Quiz Question
     */
    public function deleteQuizQuestion()
    {
        $questionId = inputPost('question_id');
        $this->quizModel->deleteQuizQuestion($questionId);
    }

    /**
     * Get Quiz Result HTML
     */
    public function getQuizResultHTML()
    {
        $vars = ['postType' => inputPost('post_type')];
        $data = [
            'result' => 1,
            'html' => view('admin/post/quiz/_add_result', $vars)
        ];
        echo json_encode($data);
    }

    /**
     * Add Quiz Result
     */
    public function addQuizResult()
    {
        $postId = inputPost('post_id');
        $postType = inputPost('post_type');
        $resultId = $this->quizModel->addQuizResult($postId);
        if (!empty($resultId)) {
            $result = $this->quizModel->getQuizResult($resultId);
            if (!empty($result)) {
                $vars = ['postType' => $postType, 'result' => $result];
                $data = [
                    'result' => 1,
                    'html' => view('admin/post/quiz/_edit_result', $vars)
                ];
                echo json_encode($data);
                exit();
            }
        }
        $data = [
            'result' => 0,
            'html' => ''
        ];
        echo json_encode($data);
    }

    /**
     * Delete Quiz Result
     */
    public function deleteQuizResult()
    {
        $resultId = inputPost('result_id');
        $this->quizModel->deleteQuizResult($resultId);
    }

    /**
     * Get Quiz Answer HTML
     */
    public function getQuizAnswerHTML()
    {
        $vars = ['postType' => inputPost('post_type'), 'questionId' => inputPost('question_id')];
        $data = [
            'result' => 1,
            'html' => view('admin/post/quiz/_add_answer', $vars)
        ];
        echo json_encode($data);
    }

    /**
     * Add Quiz Question Answer
     */
    public function addQuizQuestionAnswer()
    {
        $questionId = inputPost('question_id');
        $postType = inputPost('post_type');
        $answerId = $this->quizModel->addQuizQuestionAnswer($questionId);
        if (!empty($answerId)) {
            $answer = $this->quizModel->getQuizQuestionAnswer($answerId);
            if (!empty($answer)) {
                $vars = ['postType' => $postType, 'answer' => $answer];
                $data = [
                    'result' => 1,
                    'html' => view('admin/post/quiz/_edit_answer', $vars)
                ];
                echo json_encode($data);
                exit();
            }
        }
        $data = [
            'result' => 0,
            'html' => ''
        ];
        echo json_encode($data);
    }

    /**
     * Delete Quiz Question Answer
     */
    public function deleteQuizQuestionAnswer()
    {
        $answerId = inputPost('answer_id');
        $this->quizModel->deleteQuizQuestionAnswer($answerId);
    }

    /*
    *-------------------------------------------------------------------------------------------------
    * COMMON
    *-------------------------------------------------------------------------------------------------
    */

    /**
     * Post Buld Options
     */
    public function postBulkOptionsPost()
    {
        if (hasPermission('manage_all_posts')) {
            $operation = inputPost('operation');
            $postIds = inputPost('post_ids');
            $this->postAdminModel->postBulkOptions($operation, $postIds);
            resetCacheDataOnChange();
        }
    }

    /**
     * Delete Selected Posts
     */
    public function deleteSelectedPosts()
    {
        if (hasPermission('manage_all_posts')) {
            $postIds = inputPost('post_ids');
            $this->postAdminModel->deleteMultiPosts($postIds);
            resetCacheDataOnChange();
        }
    }

    //Set Home Slider Post Order
    public function setHomeSliderPostOrderPost()
    {
        if (hasPermission('manage_all_posts')) {
            $id = inputPost('id');
            $order = inputPost('order');
            $this->postAdminModel->setHomeSliderPostOrder($id, $order);
            resetCacheDataOnChange();
        }
    }

    //Set Featured Post Order
    public function setFeaturedPostOrderPost()
    {
        if (hasPermission('manage_all_posts')) {
            $id = inputPost('id');
            $order = inputPost('order');
            $this->postAdminModel->setFeauredPostOrder($id, $order);
            resetCacheDataOnChange();
        }
    }

    /**
     * Delete Post Main Image
     */
    public function deletePostMainImage()
    {
        $postId = inputPost('post_id');
        $this->postAdminModel->deletePostMainImage($postId);
    }

    /**
     * Delete Additional Image
     */
    public function deletePostAdditionalImage()
    {
        $fileId = inputPost('file_id');
        $this->postAdminModel->deletePostAdditionalImage($fileId);
    }

    /**
     * Delete Post File
     */
    public function deletePostFile()
    {
        $id = inputPost('id');
        $this->postAdminModel->deletePostFile($id);
    }

    /*
    *-------------------------------------------------------------------------------------------------
    * IMPORT POSTS
    *-------------------------------------------------------------------------------------------------
    */

    /**
     * Bulk Post Upload
     */
    public function bulkPostUpload()
    {
        checkPermission('add_post');
        if (!isSuperAdmin() && $this->generalSettings->bulk_post_upload_for_authors != 1) {
            return redirect()->to(adminUrl());
        }

        $data['title'] = trans("bulk_post_upload");
        $data['panelSettings'] = panelSettings();
        echo view('admin/includes/_header', $data);
        echo view('admin/post/bulk_post_upload', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Generate CSV Object Post
     */
    public function generateCSVObjectPost()
    {
        checkPermission('add_post');
        $uploadModel = new UploadModel();
        //delete old txt files
        $files = glob(FCPATH . 'uploads/tmp/*.txt');
        if (!empty($files)) {
            foreach ($files as $item) {
                @unlink($item);
            }
        }
        $file = $uploadModel->uploadCSVFile('file');
        if (!empty($file) && !empty($file['path'])) {
            $obj = $this->postAdminModel->generateCSVObject($file['path']);
            if (!empty($obj)) {
                $data = [
                    'result' => 1,
                    'numberOfItems' => $obj->numberOfItems,
                    'txtFileName' => $obj->txtFileName,
                ];
                echo json_encode($data);
                exit();
            }
        }
        echo json_encode(['result' => 0]);
    }

    /**
     * Import CSV Item Post
     */
    public function importCSVItemPost()
    {
        checkPermission('add_post');
        $txtFileName = inputPost('txtFileName');
        $index = inputPost('index');
        $title = $this->postAdminModel->importCSVItem($txtFileName, $index);
        if (!empty($title)) {
            resetCacheDataOnChange();
            $data = [
                'result' => 1,
                'title' => $title,
                'index' => $index
            ];
            echo json_encode($data);
        } else {
            $data = [
                'result' => 0,
                'index' => $index
            ];
            echo json_encode($data);
        }
    }

    /**
     * Download CSV File Post
     */
    public function downloadCSVFilePost()
    {
        $submit = inputPost('submit');
        $response = \Config\Services::response();
        if ($submit == 'csv_template') {
            return $response->download(FCPATH . 'assets/file/csv_template.csv', null);
        } elseif ($submit == 'csv_example') {
            return $response->download(FCPATH . 'assets/file/csv_example.csv', null);
        }
    }

    /**
     * Test Google Indexing Api Post
     */
    public function testGoogleIndexingApiPost()
    {
        checkPermission('seo_tools');
        $message = '';
        if ($this->generalSettings->google_indexing_api == 0) {
            $message = ['status' => 0, 'message' => "Error: API is disabled!"];
        } elseif (!file_exists(WRITEPATH . 'keys/google-service-account.json')) {
            $message = ['status' => 0, 'message' => "Error: The google-service-account.json file does not exist!"];
        } else {
            $result = $this->postAdminModel->requestTestGoogleIndexing();
            $message = [
                'status' => !empty($result) && !empty($result['success']) ? 1 : 0,
                'message' => !empty($result) && !empty($result['message']) ? $result['message'] : ''
            ];
        }
        if (!empty($message)) {
            $this->session->setFlashdata('msgTestApi', $message);
        }
        return redirect()->to(adminUrl('seo-tools'));
    }
}
