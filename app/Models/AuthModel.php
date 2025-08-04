<?php namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends BaseModel
{
    protected $builder;
    protected $builderRoles;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('users');
        $this->builderRoles = $this->db->table('roles');
    }

    //input values
    public function inputValues()
    {
        return [
            'username' => inputPost('username'),
            'email' => inputPost('email'),
            'password' => inputPost('password')
        ];
    }

    //login
    public function login()
    {
        $data = $this->inputValues();
        $user = $this->getUserByEmail($data['email']);
        if (!empty($user)) {
            if ($user->status == 0) {
                return 'banned';
            }
            if (!password_verify($data['password'], $user->password)) {
                return false;
            }
            $this->loginUser($user);
            return "success";
        }
        return false;
    }

    //login user
    public function loginUser($user)
    {
        if (!empty($user)) {
            $userData = [
                'vr_ses_id' => $user->id,
                'vr_ses_key' => getUserSessionkey($user)
            ];
            $this->session->set($userData);
        }
    }

    //login with facebook
    public function loginWithFacebook($fbUser)
    {
        if (!empty($fbUser)) {
            $user = $this->getUserByEmail($fbUser->email);
            if (empty($user)) {
                if (empty($fbUser->name)) {
                    $fbUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($fbUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'facebook_id' => $fbUser->id,
                    'email' => $fbUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'facebook',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($fbUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $fbUser->pictureURL);
                    }
                }
            }
            if (!empty($user)) {
                if ($user->status == 0) {
                    return false;
                }
                $this->loginUser($user);
            }
        }
        return false;
    }

    //login with google
    public function loginWithGoogle($gUser)
    {
        if (!empty($gUser)) {
            $user = $this->getUserByEmail($gUser->email);
            if (empty($user)) {
                if (empty($gUser->name)) {
                    $gUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($gUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'google_id' => $gUser->id,
                    'email' => $gUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'google',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($gUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $gUser->avatar);
                    }
                }
            }
            if (!empty($user)) {
                if ($user->status == 0) {
                    return false;
                }
                $this->loginUser($user);
            }
        }
    }

    //login with vk
    public function loginWithVK($vkUser)
    {
        if (!empty($vkUser)) {
            $user = $this->getUserByEmail($vkUser->email);
            if (empty($user)) {
                if (empty($vkUser->name)) {
                    $vkUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($vkUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'vk_id' => $vkUser->id,
                    'email' => $vkUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'vkontakte',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($vkUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $vkUser->avatar);
                    }
                }
            }
            if (!empty($user)) {
                if ($user->status == 0) {
                    return false;
                }
                //login
                $this->loginUser($user);
            }
        }
    }

    //download social profile image
    public function downloadSocialProfileImage($user, $imgURL)
    {
        if (!empty($user) && !empty($imgURL)) {
            $uploadModel = new UploadModel();
            $tempPath = $uploadModel->downloadTempImage($imgURL, 'jpg', 'profile_temp');
            if (!empty($tempPath) && file_exists($tempPath)) {
                $data['avatar'] = $uploadModel->uploadAvatar($user->id, $tempPath);
            }
            if (!empty($data) && !empty($data['avatar'])) {
                $this->builder->where('id', $user->id)->update($data);
            }
        }
    }

    //register
    public function register()
    {
        $data = $this->inputValues();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['user_type'] = 'registered';
        $data["slug"] = $this->generateUniqueSlug($data['username']);
        $data['status'] = 1;
        $data['token'] = generateToken();
        $data['role_id'] = 3;
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->builder->insert($data)) {
            $id = $this->db->insertID();
            $user = $this->getUser($id);
            if ($this->generalSettings->email_verification == 1 && !empty($user)) {
                $data['email_status'] = 0;
                $emailModel = new EmailModel();
                $emailModel->sendEmailActivation($user->id);
            } else {
                $data['email_status'] = 1;
            }
            if (!empty($user)) {
                $this->loginUser($user);
            }
            return true;
        }
        return false;
    }

    //add user
    public function addUser()
    {
        $data = $this->inputValues();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['user_type'] = "registered";
        $data["slug"] = $this->generateUniqueSlug($data["username"]);
        $data['status'] = 1;
        $data['email_status'] = 1;
        $data['token'] = generateToken();
        $data['role_id'] = inputPost('role_id');
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->builder->insert($data);
    }

    //generate unique username
    public function generateUniqueUsername($username)
    {
        $newUsername = $username;
        $counter = 1;
        while (!empty($this->getUserByUsername($newUsername))) {
            if ($counter > 100) {
                $newUsername = $username . '-' . uniqid();
                break;
            }
            $newUsername = $username . '-' . $counter;
            $counter++;
        }
        return $newUsername;
    }

    //generate uniqe slug
    public function generateUniqueSlug($username)
    {
        $slug = strSlug($username);
        $originalSlug = $slug;
        $counter = 1;
        while (!empty($this->getUserBySlug($slug))) {
            $slug = strSlug($originalSlug . '-' . $counter);
            $counter++;
            if ($counter > 100) {
                $slug = strSlug($originalSlug . '-' . uniqid());
                break;
            }
        }
        return $slug;
    }

    //logout
    public function logout()
    {
        $this->session->remove('vr_ses_id');
        $this->session->remove('vr_ses_key');
    }

    //reset password
    public function resetPassword($token)
    {
        $user = $this->getUserByToken($token);
        if (!empty($user)) {
            $data = [
                'password' => password_hash(inputPost('password'), PASSWORD_DEFAULT),
                'token' => generateToken()
            ];
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //verify email
    public function verifyEmail($user)
    {
        if (!empty($user)) {
            $data = [
                'email_status' => 1,
                'token' => generateToken()
            ];
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //change user role
    public function changeUserRole($userId, $roleId)
    {
        $user = $this->getUser($userId);
        $role = $this->getRole($roleId);
        if (!empty($user) && !empty($role)) {
            if (($role->is_super_admin == 1 || $user->is_super_admin == 1) && !isSuperAdmin()) {
                return false;
            }
            return $this->builder->where('id', $user->id)->update(['role_id' => $roleId]);
        }
        return false;
    }

    //ban user
    public function banUser($user)
    {
        if (!empty($user)) {
            if ($user->status == 1) {
                $data = ['status' => 0];
            } else {
                $data = ['status' => 1];
            }
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //get user by id
    public function getUser($id)
    {
        return $this->builder->select('users.*, roles.role_name AS role_name_data, permissions, is_super_admin')
            ->join('roles', 'users.role_id = roles.id', 'left')->where('users.id', clrNum($id))->get()->getRow();
    }

    //get user by email
    public function getUserByEmail($email)
    {
        return $this->builder->where('email', removeForbiddenCharacters($email))->get()->getRow();
    }

    //get user by username
    public function getUserByUsername($username)
    {
        return $this->builder->where('username', removeForbiddenCharacters($username))->get()->getRow();
    }

    //get user by slug
    public function getUserBySlug($slug)
    {
        return $this->builder->where('slug', cleanSlug($slug))->get()->getRow();
    }

    //get user by token
    public function getUserByToken($token)
    {
        return $this->builder->where('token', removeForbiddenCharacters($token))->get()->getRow();
    }

    //get user by vk id
    public function getUserByVKId($vkId)
    {
        return $this->builder->where('vk_id', cleanStr($vkId))->get()->getRow();
    }

    //load more users
    public function loadMoreUsers($q, $perPage, $offset)
    {
        $q = cleanStr($q);
        if (!empty($q)) {
            $this->builder->like('username', $q)->orLike('email', $q);
        }
        return $this->builder->select('id, username, email')->orderBy('id')->limit($perPage, $offset)->get()->getResult();
    }

    //get user emails by ids
    public function getUserEmailsByIds($ids)
    {
        $emails = array();
        $rows = $this->builder->select('email')->whereIn('id', $ids, false)->get()->getResult();
        if (!empty($rows)) {
            $emails = array_map(function ($item) {
                return $item->email;
            }, $rows);
        }
        return $emails;
    }

    //get latest users
    public function getLatestUsers()
    {
        return $this->builder->orderBy('id DESC')->get(6)->getResult();
    }

    //user count
    public function getUserCount()
    {
        return $this->builder->countAllResults();
    }

    //edit user
    public function editUser($id)
    {
        $user = $this->getUser($id);
        if (!empty($user)) {
            $data = [
                'username' => inputPost('username'),
                'email' => inputPost('email'),
                'slug' => inputPost('slug'),
                'about_me' => inputPost('about_me'),
                'balance' => !empty(inputPost('balance')) ? inputPost('balance') : 0,
                'total_pageviews' => !empty(inputPost('total_pageviews')) ? inputPost('total_pageviews') : 0
            ];

            $settingsModel = new SettingsModel();
            $social = $settingsModel->getSocialMediaData(true);
            $data['social_media_data'] = !empty($social) ? serialize($social) : '';

            $uploadModel = new UploadModel();
            $file = $uploadModel->uploadTempFile('file', true);
            if (!empty($file) && !empty($file['path'])) {
                $data["avatar"] = $uploadModel->uploadAvatar($user->id, $file['path']);
                @unlink(FCPATH . $user->avatar);
                $uploadModel->deleteTempFile($file['path']);
            }
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //is slug unique
    public function isSlugUnique($slug, $id)
    {
        if (!empty($this->builder->where('id !=', clrNum($id))->where('slug', cleanSlug($slug))->get()->getRow())) {
            return true;
        }
        return false;
    }

    //check if email is unique
    public function isEmailUnique($email, $userId = 0)
    {
        $user = $this->getUserByEmail($email);
        if ($userId == 0) {
            if (!empty($user)) {
                return false;
            }
            return true;
        } else {
            if (!empty($user) && $user->id != $userId) {
                return false;
            }
            return true;
        }
    }

    //check if username is unique
    public function isUniqueUsername($username, $userId = 0)
    {
        $user = $this->getUserByUsername($username);
        if ($userId == 0) {
            if (!empty($user)) {
                return false;
            }
            return true;
        } else {
            if (!empty($user) && $user->id != $userId) {
                return false;
            }
            return true;
        }
    }

    //update last seen time
    public function updateLastSeen()
    {
        if (authCheck()) {
            $this->builder->where('id', user()->id)->update(['last_seen' => date('Y-m-d H:i:s')]);
        }
    }

    //get paginated users count
    public function getUsersCount()
    {
        $this->filterUsers();
        return $this->builder->countAllResults();
    }

    //get paginated users
    public function getUsersPaginated($perPage, $offset)
    {
        $this->filterUsers();
        return $this->builder->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //users filter
    public function filterUsers()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builder->groupStart()->like('username', cleanStr($q))->orLike('email', cleanStr($q))->groupEnd();
        }
        //status
        $status = inputGet('status');
        if ($status == 'active') {
            $this->builder->where('status', 1);
        } elseif ($status == 'banned') {
            $this->builder->where('status', 0);
        }
        //role
        $role = clrNum(inputGet('role'));
        if (!empty($role)) {
            $this->builder->where('role_id', $role);
        }
        //email status
        $emailStatus = inputGet('email_status');
        if ($emailStatus == 'confirmed') {
            $this->builder->where('email_status', 1);
        } elseif ($emailStatus == 'unconfirmed') {
            $this->builder->where('email_status', 0);
        }
        //reward system
        $rewardSystem = inputGet('reward_system');
        if ($rewardSystem == 'active') {
            $this->builder->where('reward_system_enabled', 1);
        } elseif ($rewardSystem == 'inactive') {
            $this->builder->where('reward_system_enabled', 0);
        }
    }

    //load users dropdown
    public function loadUsersDropdown($q)
    {
        $q = cleanStr($q);
        if (!empty($q)) {
            $this->builder->like('id', $q)->orLike('username', $q);
        }
        return $this->builder->select('id, username')->where('status', 1)->orderBy('username DESC')->get()->getResult();
    }

    //delete user
    public function deleteUser($id)
    {
        if ($id == 1) {
            return false;
        }
        $user = $this->getUser($id);
        if (!empty($user)) {
            if (file_exists(FCPATH . $user->avatar)) {
                @unlink(FCPATH . $user->avatar);
            }
            $this->db->table('comments')->where('user_id', $user->id)->delete();
            $this->db->table('reading_lists')->where('user_id', $user->id)->delete();
            $posts = $this->db->table('posts')->where('user_id', $user->id)->get()->getResult();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $postAdminModel = new PostAdminModel();
                    $postAdminModel->deletePost($post->id);
                }
            }
            return $this->builder->where('id', $user->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Roles & Permissions
     * --------------------------------------------------------------------
     */

    //set role data
    public function setRoleData()
    {
        $nameArray = array();
        $permissionsArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('role_name_' . $language->id, true)
            ];
            array_push($nameArray, $item);
        }
        $permissions = inputPost('permissions');
        if (!empty($permissions)) {
            $defaultPermissions = getPermissionsArray();
            foreach ($permissions as $permission) {
                if (in_array($permission, $defaultPermissions)) {
                    array_push($permissionsArray, $permission);
                }
            }
        }
        $permissionsStr = '';
        if (!empty($permissionsArray)) {
            if (in_array('manage_all_posts', $permissionsArray) && !in_array('add_post', $permissionsArray)) {
                array_push($permissionsArray, 'add_post');
            }

            array_push($permissionsArray, 'admin_panel');
            $permissionsStr = implode(',', $permissionsArray);
        }
        $data = [
            'role_name' => serialize($nameArray),
            'permissions' => $permissionsStr
        ];
        return $data;
    }

    //add role
    public function addRole()
    {
        $data = $this->setRoleData();
        return $this->builderRoles->insert($data);
    }

    //update role
    public function editRole($id)
    {
        $role = $this->getRole($id);
        if (!empty($role)) {
            if ($role->is_super_admin == 1 && !isSuperAdmin()) {
                return false;
            }
            $data = $this->setRoleData();
            return $this->builderRoles->where('id', clrNum($id))->update($data);
        }
        return false;
    }

    //get roles
    public function getRoles()
    {
        return $this->builderRoles->get()->getResult();
    }

    //get role
    public function getRole($id)
    {
        return $this->builderRoles->where('id', clrNum($id))->get()->getRow();
    }

    //delete role
    public function deleteRole($id)
    {
        $role = $this->getRole($id);
        if (!empty($role)) {
            if ($role->is_default == 1) {
                return false;
            }
            return $this->builderRoles->where('id', $role->id)->delete();
        }
        return false;
    }
}
