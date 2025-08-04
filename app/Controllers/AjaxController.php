<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\NewsletterModel;
use App\Models\PollModel;
use App\Models\PostModel;
use App\Models\QuizModel;
use App\Models\ReactionModel;
use App\Models\TagModel;
use Config\Globals;

class AjaxController extends BaseController
{

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!$this->request->isAJAX()) {
            exit();
        }
        $langId = inputPost('sysLangId');
        if (!empty($langId)) {
            Globals::setActiveLanguage($langId);
        }
    }

    /**
     * Set Theme Mode Post
     */
    public function setThemeModePost()
    {
        checkSuperAdmin();
        $this->settingsModel->setThemeMode();
        exit();
    }

    /**
     * Increment Post Page Views
     */
    public function incrementPostViews()
    {
        $postId = inputPost('postId');
        $mouseMoveCount = inputPost('mouseMoveCount');
        $scrollCount = inputPost('scrollCount');

        $isValid = false;
        $verification = unserializeData($this->generalSettings->human_verification);
        if (!empty($verification) && !empty($verification['status'])) {
            $mouse = isset($verification['mouse']) ? intval($verification['mouse']) : 0;
            $scroll = isset($verification['scroll']) ? intval($verification['scroll']) : 0;
            if ($mouseMoveCount >= $mouse && $scrollCount >= $scroll) {
                $isValid = true;
            }
        } else {
            $isValid = true;
        }
        if ($isValid) {
            $this->postModel->incrementPostViews($postId);
        }
    }

    /**
     * Run After Page Load
     */
    public function runOnPageLoad()
    {
        try {
            $langId = clrNum(inputPost('sysLangId'));
            $isPostPage = inputPost('isPostPage');
            $htmlContent = '';

            //update last seen
            $authModel = new \App\Models\AuthModel();
            $authModel->updateLastSeen();

            if ($isPostPage == true) {
                $postModel = new PostModel();
                $postId = clrNum(inputPost('postId'));
                $nextPrevPosts = $postModel->getNextPrevPosts($postId, $langId);
                $htmlContent = loadView('post/details/_next_prev_post', ['nextPrevPosts' => $nextPrevPosts]);
            }

            $data = [
                'result' => 1,
                'isPostPage' => $isPostPage,
                'htmlContent' => $htmlContent
            ];
            echo json_encode($data);
            exit();
        } catch (Exception $e) {
            echo json_encode([
                'result' => 0
            ]);
        }
        exit();
    }

    /**
     * Load More Posts
     */
    public function loadMorePosts()
    {
        $langId = clrNum(inputPost('lang_id'));
        $page = clrNum(inputPost('page'));
        $type = inputPost('type');
        $view = inputPost('view');
        if ($page < 1) {
            $page = 1;
        }
        $perPage = $type == 'search' ? $this->generalSettings->pagination_per_page : POST_NUM_LOAD_MORE;
        $offset = ($page - 1) * $perPage;
        $posts = array();
        if ($type == 'latest') {
            $posts = $this->postModel->getLatestPosts($langId, $perPage, $offset);
        } elseif ($type == 'search') {
            $q = inputPost('q');
            if (!empty($q)) {
                $q = strip_tags($q);
            }
            $posts = $this->postModel->getSearchPostsPaginated($langId, $q, $perPage, $offset);
        }
        Globals::setActiveLanguage($langId);
        if (!empty($posts)) {
            $htmlContent = '';
            $i = 0;
            foreach ($posts as $post) {
                if ($i < $perPage) {
                    if ($view == '_post_item_horizontal' && $this->activeTheme->theme == 'classic') {
                        $vars = ['post' => $post, 'showLabel' => true];
                        if ($type == 'latest') {
                            $htmlContent .= loadView('post/_post_item_horizontal', $vars);
                        } else {
                            if ($i % 2 == 0) {
                                $htmlContent .= '<div class="col-sm-12"></div>';
                            }
                            $htmlContent .= '<div class="col-sm-6 col-xs-12">' . loadView('post/_post_item', $vars) . '</div>';
                        }
                    } else {
                        $widgets = getCategoryWidgets(0, $this->widgets, $this->adSpaces, $langId);
                        $vars = ['postItem' => $post, 'showLabel' => true];
                        $divClass = $widgets->hasWidgets ? 'col-sm-12 col-md-6' : 'col-sm-12 col-md-4';
                        $htmlContent .= ' <div class="' . $divClass . '">' . loadView('post/_post_item', $vars) . '</div>';
                    }
                }
                $i++;
            }
            $data = [
                'result' => 1,
                'htmlContent' => $htmlContent,
                'hasMore' => countItems($posts) > $perPage ? true : false
            ];
            echo json_encode($data);
            exit();
        }
        echo json_encode(['result' => 0]);
        exit();
    }

    /**
     * Load More Users
     */
    public function loadMoreUsers()
    {
        checkPermission('newsletter');
        $page = clrNum(inputPost('page'));
        $q = inputPost('q');
        $perPage = 500;
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $perPage;
        $authModel = new \App\Models\AuthModel();
        $users = $authModel->loadMoreUsers($q, $perPage, $offset);
        $htmlContent = '';
        if (!empty($users)) {
            foreach ($users as $user) {
                $htmlContent .= '<tr><td><input type="checkbox" name="user_id[]" value="' . $user->id . '"></td><td>' . $user->id . '</td><td>' . esc($user->username) . '</td><td>' . esc($user->email) . '</td></tr>';
            }
        }
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent
        ];
        echo json_encode($data);
        exit();
    }

    /**
     * Load More Subscribers
     */
    public function loadMoreSubscribers()
    {
        checkPermission('newsletter');
        $page = clrNum(inputPost('page'));
        $q = inputPost('q');
        $perPage = 500;
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $perPage;
        $model = new NewsletterModel();
        $subscribers = $model->loadMoreSubscribers($q, $perPage, $offset);
        $htmlContent = '';
        if (!empty($subscribers)) {
            foreach ($subscribers as $subscriber) {
                $htmlContent .= '<tr>
                    <td><input type="checkbox" name="subscriber_id[]" value="' . esc($subscriber->id) . '"></td>
                    <td>' . $subscriber->id . '</td>
                    <td>' . esc($subscriber->email) . '</td>
                    <td>
                        <a href="javascript:void(0)" 
                           onclick="deleteItem(\'Admin/deleteSubscriberPost\', \'' . $subscriber->id . '\', \'' . clrDQuotes(trans("confirm_item")) . '\');" 
                           class="text-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;' . trans('delete') . '
                        </a>
                    </td>
                </tr>';
            }
        }
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent
        ];
        echo json_encode($data);
        exit();
    }

    /**
     * Get Quiz Answers
     */
    public function getQuizAnswers()
    {
        $postId = inputPost('post_id');
        $arrayQuizAnswers = array();
        $quizModel = new QuizModel();
        $questions = $quizModel->getQuizQuestions($postId);
        if (!empty($questions)) {
            $i = 0;
            foreach ($questions as $question) {
                $correctAnswer = $quizModel->getQuizQuestionCorrectAnswer($question->id);
                if (!empty($correctAnswer)) {
                    $item = [$question->id, $correctAnswer->id];
                    array_push($arrayQuizAnswers, $item);
                }
                $i++;
            }
        }
        $data = [
            'result' => 1,
            'arrayQuizAnswers' => $arrayQuizAnswers,
        ];
        echo json_encode($data);
    }

    /**
     * Get Quiz Results
     */
    public function getQuizResults()
    {
        $postId = inputPost('post_id');
        $arrayQuizResults = array();
        $quizModel = new QuizModel();
        $results = $quizModel->getQuizResults($postId);
        if (!empty($results)) {
            foreach ($results as $result) {
                $vars = ['result' => $result];
                $htmlContent = loadView('post/details/_quiz_result', $vars);
                //array: [0]: result id, [1]: min correct, [2]: max correct, [3]: html content
                $item = [$result->id, $result->min_correct_count, $result->max_correct_count, $htmlContent];
                array_push($arrayQuizResults, $item);
            }
        }
        $data = [
            'result' => 1,
            'arrayQuizResults' => $arrayQuizResults,
        ];
        echo json_encode($data);
    }

    /**
     * Add Poll Vote
     */
    public function addPollVote()
    {
        $pollId = inputPost('poll_id');
        $option = inputPost('option');
        $jsonData = [
            'result' => 1,
            'htmlContent' => '',
        ];
        if (is_null($option) || $option == '') {
            $jsonData['htmlContent'] = 'required';
        } else {
            $pollModel = new PollModel();
            $data['poll'] = $pollModel->getPoll($pollId);
            if (!empty($data['poll'])) {
                $result = "";
                if ($data['poll']->vote_permission == 'registered') {
                    $result = $pollModel->addVoteRegistered($pollId, $option);
                } else {
                    $result = $pollModel->addVoteUnRegistered($pollId, $option);
                }
                if ($result == 'success') {
                    $data['poll'] = $pollModel->getPoll($pollId);
                    $jsonData['htmlContent'] = view('common/_poll_results', $data);
                } else {
                    $jsonData['htmlContent'] = 'voted';
                }
            }
        }
        echo json_encode($jsonData);
    }

    /**
     * Add Post Poll Vote
     */
    public function addPostPollVote()
    {
        $questionId = inputPost('question_id');
        $answerId = inputPost('answer_id');
        $jsonData = [
            'result' => 0
        ];
        if (!empty($questionId) && !empty($answerId)) {
            $quizModel = new QuizModel();
            if ($quizModel->addPostPollVote($questionId, $answerId)) {
                $jsonData['result'] = 1;
                $result = $quizModel->getPollQuestionVotes($questionId);
                $jsonData['arrayVotes'] = $result['arrayVotes'];
                $jsonData['totalVotes'] = $result['totalVotes'];
            }
        }
        echo json_encode($jsonData);
    }

    /**
     * Add or Remove Reading List Item
     */
    public function addRemoveReadingListItem()
    {
        $postId = clrNum(inputPost('post_id'));
        if ($this->postModel->isPostInReadingList($postId) == true) {
            $this->postModel->addRemoveReadingListItem($postId, 'remove');
        } else {
            $this->postModel->addRemoveReadingListItem($postId, 'add');
        }
    }

    /**
     * Make Reaction
     */
    public function addReaction()
    {
        $postId = clrNum(inputPost('post_id'));
        $reaction = cleanStr(inputPost('reaction'));
        $data['post'] = getPostById($postId);
        $data['resultArray'] = array();
        $reactionModel = new ReactionModel();
        if (!empty($data['post'])) {
            $data['resultArray'] = $reactionModel->addReaction($postId, $reaction);
            Globals::setActiveLanguage($data['post']->lang_id);
        }
        $data['reactions'] = $reactionModel->getReaction($postId);
        $jsonData = [
            'result' => 1,
            'htmlContent' => view('common/_emoji_reactions', $data)
        ];
        echo json_encode($jsonData);
    }

    /**
     * Add Comment
     */
    public function addCommentPost()
    {
        if ($this->generalSettings->comment_system != 1) {
            exit();
        }
        $limit = clrNum(inputPost('limit'));
        $postId = clrNum(inputPost('post_id'));
        if (authCheck()) {
            $this->commonModel->addComment();
        } else {
            if (reCAPTCHA('validate', $this->generalSettings) != 'invalid') {
                $this->commonModel->addComment();
            }
        }
        if ($this->generalSettings->comment_approval_system == 1 && !hasPermission('comments_contact')) {
            $jsonData = [
                'type' => 'message',
                'htmlContent' => "<p class='comment-success-message'><i class='icon-check'></i>&nbsp;&nbsp;" . trans("msg_comment_sent_successfully") . '</p>'
            ];
            echo json_encode($jsonData);
        } else {
            $this->loadCommentsData($postId, $limit);
        }
    }

    /**
     * Load Subcomment Box
     */
    public function loadSubcommentBox()
    {
        $commentId = clrNum(inputPost('comment_id'));
        $limit = clrNum(inputPost('limit'));
        $data['parentComment'] = $this->commonModel->getComment($commentId);
        $data['commentLimit'] = $limit;
        $htmlContent = view('common/_add_subcomment', $data);
        echo json_encode(['result' => 1, 'htmlContent' => $htmlContent]);
    }

    /**
     * Load More Comments
     */
    public function loadMoreComments()
    {
        $postId = clrNum(inputPost('post_id'));
        $limit = clrNum(inputPost('limit'));
        $newLimit = $limit + COMMENT_LIMIT;
        $this->loadCommentsData($postId, $newLimit);
    }

    /**
     * Like Comment
     */
    public function likeCommentPost()
    {
        $commentId = clrNum(inputPost('comment_id'));
        $data = [
            'result' => 1,
            'likeCount' => $this->commonModel->likeComment($commentId)
        ];
        echo json_encode($data);
    }

    /**
     * Delete Comment
     */
    public function deleteCommentPost()
    {
        $id = clrNum(inputPost('id'));
        $postId = clrNum(inputPost('post_id'));
        $limit = clrNum(inputPost('limit'));
        $comment = $this->commonModel->getComment($id);
        if (authCheck() && !empty($comment)) {
            if (hasPermission('comments_contact') || user()->id == $comment->user_id) {
                $this->commonModel->deleteComment($id);
            }
        }
        $this->loadCommentsData($postId, $limit);
    }

    //load comments data
    private function loadCommentsData($postId, $limit)
    {
        $data = [
            'post' => getPostById($postId),
            'comments' => $this->commonModel->getComments($postId, $limit),
            'commentCount' => $this->commonModel->getCommentCountByPostId($postId),
            'commentLimit' => $limit,
        ];
        $jsonData = [
            'result' => 1,
            'htmlContent' => view('common/_comments', $data)
        ];
        echo json_encode($jsonData);
        exit();
    }

    /**
     * Add to Newsletter
     */
    public function addNewsletterPost()
    {
        $url = inputPost('url');
        if (!empty($url)) {
            exit();
        }
        $data = [
            'result' => 0,
            'message' => '',
            'isSuccess' => '',
        ];
        $email = cleanStr(inputPost('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['message'] = trans("message_invalid_email");
        } else {
            if (!empty($email)) {
                $newsletterModel = new NewsletterModel();
                if (empty($newsletterModel->getSubscriber($email))) {
                    if ($newsletterModel->addSubscriber($email)) {
                        $data['message'] = trans("message_newsletter_success");
                        $data['isSuccess'] = 1;
                    }
                } else {
                    $data['message'] = trans("message_newsletter_error");
                }
            }
        }
        $data['result'] = 1;
        echo json_encode($data);
    }

    /**
     * AI Writer
     */
    public function generateTextAI()
    {
        hasPermission("ai_writer");
        $model = inputPost('model');
        $temperature = inputPost('temperature');
        $tone = inputPost('tone');
        $length = inputPost('length');
        $topic = inputPost('topic');
        $langId = inputPost('sysLangId');

        // Get language code
        $lang = getLanguage($langId);
        $langCode = (!empty($lang)) ? $lang->short_form : 'en';

        return \Config\AIWriter::generateText($model, $temperature, $tone, $length, $topic, $langCode);
    }

    /**
     * Get Tag Suggestions
     */
    public function getTagSuggestions()
    {
        hasPermission("add_post");
        $q = inputPost('searchTerm');
        $tagModel = new TagModel();
        $tags = $tagModel->getTagSuggestions($q);
        if (!empty($tags)) {
            $data = [
                'result' => 1,
                'tags' => $tags
            ];
            echo json_encode($data);
            exit();
        }
        echo json_encode(['result' => 0]);
        exit();
    }

    /**
     * Close Cookies Warning
     */
    public function closeCookiesWarningPost()
    {
        helperSetCookie('cks_warning', '1');
    }
}
