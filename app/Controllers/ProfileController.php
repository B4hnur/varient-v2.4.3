<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\EmailModel;
use App\Models\PostModel;
use App\Models\ProfileModel;

class ProfileController extends BaseController
{
    protected $authModel;
    protected $profileModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authModel = new AuthModel();
        $this->profileModel = new ProfileModel();
    }

    /**
     * Profile Page
     */
    public function profile($slug)
    {
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data["user"])) {
            return redirect()->to(langBaseUrl());
        }
        $data = setPageMeta($data['user']->username, $data);

        $countKey = 'posts_count_profile' . $data['user']->id;
        $postsKey = 'posts_profile' . $data['user']->id;
        $data['userSession'] = getUserSession();
        $postModel = new PostModel();
        $data['numRows'] = $postModel->getUserPostsCount($this->activeLang->id, $data['user']->id);
        $data['pager'] = paginate($this->generalSettings->pagination_per_page, $data['numRows']);
        $data['posts'] = array();
        if ($data['numRows'] > 0) {
            $data['posts'] = $postModel->getUserPostsPaginated($this->activeLang->id, $data['user']->id, $this->generalSettings->pagination_per_page, $data['pager']->offset);
        }

        $data['following'] = $this->profileModel->getFollowingUsers($data['user']->id);
        $data['followers'] = $this->profileModel->getFollowers($data['user']->id);
        $data['headerNoMargin'] = true;

        echo loadView('partials/_header', $data);
        echo loadView('profile/profile', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Edit Profile
     */
    public function editProfile()
    {
        $data = setPageMeta(trans("update_profile"));
        $data['userSession'] = getUserSession();
        $data["activeTab"] = 'update_profile';

        echo loadView('partials/_header', $data);
        echo loadView('settings/edit_profile', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Edit Profile Post
     */
    public function editProfilePost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $submit = inputPost('submit');
        if ($submit == 'resend_activation_email') {
            //send activation email
            $emailModel = new EmailModel();
            $emailModel->sendEmailActivation(user()->id);
            setSuccessMessage("msg_send_confirmation_email");
            redirectToBackURL();
        }
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email"), 'required|max_length[255]');
        $val->setRule('username', trans("username"), 'required|max_length[255]');
        $val->setRule('slug', trans("slug'"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            redirectToBackURL();
        } else {
            $data = [
                'username' => cleanStr(inputPost('username')),
                'slug' => cleanSlug(inputPost('slug')),
                'email' => cleanStr(inputPost('email')),
                'about_me' => inputPost('about_me')
            ];
            if (!$this->authModel->isEmailUnique($data['email'], user()->id)) {
                setErrorMessage("message_email_unique_error");
                redirectToBackURL();
            }
            if (!$this->authModel->isUniqueUsername($data['username'], user()->id)) {
                setErrorMessage("msg_username_unique_error");
                redirectToBackURL();
            }
            if ($this->authModel->isSlugUnique($data['slug'], user()->id)) {
                setErrorMessage("msg_slug_used");
                redirectToBackURL();
            }
            if ($this->profileModel->editProfile($data)) {
                //check email changed
                setSuccessMessage("msg_updated");
                if ($this->profileModel->checkEmailChanged(user()->id)) {
                    setSuccessMessage("msg_send_confirmation_email");
                }
                redirectToBackURL();
            }
        }
        setErrorMessage("msg_error");
        redirectToBackURL();
    }

    /**
     * Social Accounts
     */
    public function socialAccounts()
    {
        $data = setPageMeta(trans("social_accounts"));
        $data['userSession'] = getUserSession();
        $data['activeTab'] = 'social_accounts';

        echo loadView('partials/_header', $data);
        echo loadView('settings/social_accounts', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Social Accounts Post
     */
    public function socialAccountsPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->profileModel->editSocialAccounts()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Preferences
     */
    public function preferences()
    {
        $data = setPageMeta(trans("preferences"));
        $data['userSession'] = getUserSession();
        $data['activeTab'] = 'preferences';

        echo loadView('partials/_header', $data);
        echo loadView('settings/preferences', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Preferences Post
     */
    public function preferencesPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->profileModel->editPreferences()) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Change Password
     */
    public function changePassword()
    {
        $data = setPageMeta(trans("change_password"));
        $data['userSession'] = getUserSession();
        $data['activeTab'] = 'change_password';

        echo loadView('partials/_header', $data);
        echo loadView('settings/change_password', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Change Password Post
     */
    public function changePasswordPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $val = \Config\Services::validation();
        if (!empty(user()->password)) {
            $val->setRule('old_password', trans("old_password"), 'required');
        }
        $val->setRule('password', trans("password"), 'required|min_length[4]|max_length[200]');
        $val->setRule('password_confirm', trans("confirm_password"), 'required|matches[password]|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            redirectToBackURL();
        } else {
            if ($this->profileModel->changePassword()) {
                setSuccessMessage("message_change_password_success");
            } else {
                setErrorMessage("message_change_password_error");
            }
        }
        redirectToBackURL();
    }

    /**
     * Delete Account
     */
    public function deleteAccount()
    {
        $data = setPageMeta(trans("delete_account"));
        $data['userSession'] = getUserSession();
        $data['activeTab'] = 'delete_account';

        echo loadView('partials/_header', $data);
        echo loadView('settings/delete_account', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Delete Account Post
     */
    public function deleteAccountPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $confirm = inputPost('confirm');
        $password = inputPost('password');
        if (empty($confirm)) {
            setErrorMessage("msg_error");
            redirectToBackURL();
        }
        if (!password_verify($password, user()->password)) {
            setErrorMessage("msg_wrong_password");
            redirectToBackURL();
        }
        //delete account
        $this->authModel->deleteUser(user()->id);
        redirectToBackURL();
    }

    /**
     * Follow Unfollow User
     */
    public function followUnfollowUserPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $this->profileModel->followUnFollowUser();
        redirectToBackURL();
    }
}