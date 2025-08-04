<?php

use \Config\Globals;

if (strpos($_SERVER['REQUEST_URI'], '/index.php') !== false) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);

    $newUrl = $protocol . '://' . $host . $uri;

    if ($newUrl !== ($protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) {
        header('Location: ' . $newUrl, true, 301);
        exit();
    }
}

//trimg string
if (!function_exists('strTrim')) {
    function strTrim($str)
    {
        if (!empty($str)) {
            return trim($str);
        }
    }
}

//str replace
if (!function_exists('strReplace')) {
    function strReplace($search, $replace, $str)
    {
        if (!empty($str)) {
            return str_replace($search, $replace, $str);
        }
    }
}

//character limiter
if (!function_exists('characterLimiter')) {
    function characterLimiter($str, $limit, $endChar = '')
    {
        if (!empty($str) && strlen($str) > $limit) {
            return mb_strimwidth($str, 0, $limit + 3, $endChar);
        }
        return $str;
    }
}

//set success message
if (!function_exists('setSuccessMessage')) {
    function setSuccessMessage($message, $trans = true)
    {
        if (!empty($message)) {
            $session = \Config\Services::session();
            if ($trans == true) {
                $message = trans($message);
            }
            $session->setFlashdata('success', $message);
        }
    }
}

//set error message
if (!function_exists('setErrorMessage')) {
    function setErrorMessage($message, $trans = true)
    {
        if (!empty($message)) {
            $session = \Config\Services::session();
            if ($trans == true) {
                $message = trans($message);
            }
            $session->setFlashdata('error', $message);
        }
    }
}

//get themes
if (!function_exists('getThemes')) {
    function getThemes()
    {
        return Globals::$themes;
    }
}

//get active theme
if (!function_exists('getActiveTheme')) {
    function getActiveTheme()
    {
        $theme = null;
        if (!empty(getThemes())) {
            foreach (getThemes() as $item) {
                if ($item->is_active == 1) {
                    $theme = $item;
                }
            }
            if (empty($theme)) {
                if (!empty(getThemes()[0])) {
                    $theme = getThemes()[0];
                }
            }
        }
        return $theme;
    }
}

//get theme path
if (!function_exists('getThemePath')) {
    function getThemePath()
    {
        $themePath = 'themes/classic';
        if (!empty(getActiveTheme())) {
            $themePath = 'themes/' . getActiveTheme()->theme_folder;
        }
        return $themePath;
    }
}

//load view
if (!function_exists('loadView')) {
    function loadView($view, $data = null)
    {
        if (!empty($data)) {
            return view(getThemePath() . '/' . $view, $data);
        } else {
            return view(getThemePath() . '/' . $view);
        }
    }
}

//language base URL
if (!function_exists('langBaseUrl')) {
    function langBaseUrl($route = null)
    {
        if (!empty($route)) {
            return Globals::$langBaseUrl . '/' . $route;
        }
        return Globals::$langBaseUrl;
    }
}

//generate base URL by language id
if (!function_exists('generateBaseURLByLangId')) {
    function generateBaseURLByLangId($langId)
    {
        if ($langId == Globals::$generalSettings->site_lang) {
            return base_url() . '/';
        } else {
            $languages = Globals::$languages;
            $shortForm = '';
            if (!empty($languages)) {
                foreach ($languages as $language) {
                    if ($langId == $language->id) {
                        $shortForm = $language->short_form;
                    }
                }
            }
            if ($shortForm != '') {
                return base_url($shortForm) . '/';
            }
        }
        return base_url() . '/';
    }
}

//generate base URL by language
if (!function_exists('generateBaseURLByLang')) {
    function generateBaseURLByLang($lang)
    {
        if (!empty($lang)) {
            if ($lang->id == Globals::$generalSettings->site_lang) {
                return base_url() . '/';
            } else {
                return base_url($lang->short_form) . '/';
            }
        }
        return base_url() . '/';
    }
}

//current full url
if (!function_exists('currentFullURL')) {
    function currentFullURL()
    {
        $currentURL = current_url();
        if (!empty($_SERVER['QUERY_STRING'])) {
            $currentURL = $currentURL . "?" . $_SERVER['QUERY_STRING'];
        }
        return $currentURL;
    }
}

//admin url
if (!function_exists('adminUrl')) {
    function adminUrl($route = null)
    {
        if (!empty($route)) {
            return base_url(Globals::$customRoutes->admin . '/' . $route);
        }
        return base_url(Globals::$customRoutes->admin);
    }
}

//force redirect to URL
if (!function_exists('redirectToUrl')) {
    function redirectToUrl($url)
    {
        header('Location: ' . $url);
        exit();
    }
}

//redirect to back URL
if (!function_exists('redirectToBackURL')) {
    function redirectToBackURL()
    {
        $backURL = inputPost('back_url');
        if (!empty($backURL)) {
            if (strpos($backURL, base_url()) === 0) {
                $backURL = esc($backURL);
                $backURL = str_replace('&amp;', '&', $backURL);
                $backURL = filter_var($backURL, FILTER_SANITIZE_URL);
                header('Location: ' . $backURL);
                exit();
            }
        }
        $previousURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : langBaseUrl();
        header('Location: ' . $previousURL);
        exit();
    }
}


//get request
if (!function_exists('inputGet')) {
    function inputGet($input_name, $removeForbidden = false)
    {
        $input = \Config\Services::request()->getGet($input_name);
        if (!is_array($input)) {
            $input = strTrim($input);
        }
        if ($removeForbidden) {
            $input = removeForbiddenCharacters($input);
        }
        return $input;
    }
}

//post request
if (!function_exists('inputPost')) {
    function inputPost($input_name, $removeForbidden = false)
    {
        $input = \Config\Services::request()->getPost($input_name);
        if (!is_array($input)) {
            $input = strTrim($input);
        }
        if ($removeForbidden) {
            $input = removeForbiddenCharacters($input);
        }
        return $input;
    }
}

//auth check
if (!function_exists('authCheck')) {
    function authCheck()
    {
        return Globals::$authCheck;
    }
}

//get active user
if (!function_exists('user')) {
    function user()
    {
        return Globals::$authUser;
    }
}

//get user session key
if (!function_exists('getUserSessionkey')) {
    function getUserSessionkey($user)
    {
        if (!empty($user)) {
            return hash('sha256', $user->password . $user->id);
        }
        return null;
    }
}

//get user by id
if (!function_exists('getUserById')) {
    function getUserById($id)
    {
        $model = new \App\Models\AuthModel();
        return $model->getUser($id);
    }
}

//is super admin
if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        if (authCheck()) {
            if (user()->is_super_admin) {
                return true;
            }
        }
        return false;
    }
}

//check super admin
if (!function_exists('checkSuperAdmin')) {
    function checkSuperAdmin()
    {
        if (!isSuperAdmin()) {
            redirectToUrl(langBaseUrl());
        }
    }
}

//is user admin
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (authCheck()) {
            if (user()->is_super_admin) {
                return true;
            }
            $allowed = ['add_post', 'admin_panel'];
            $permissions = user()->permissions;
            if (!empty($permissions)) {
                $permissionArray = explode(',', $permissions);
                return countItems($permissionArray) > countItems($allowed);
            }
        }
        return false;
    }
}

//get language
if (!function_exists('getLanguage')) {
    function getLanguage($id)
    {
        $model = new \App\Models\LanguageModel();
        return $model->getLanguage($id);
    }
}

//get favicon
if (!function_exists('getFavicon')) {
    function getFavicon()
    {
        $generalSettings = Globals::$generalSettings;
        if (!empty($generalSettings)) {
            if (!empty($generalSettings->favicon) && file_exists(FCPATH . $generalSettings->favicon)) {
                return base_url($generalSettings->favicon);
            }
            return base_url("assets/img/favicon.png");
        }
        return base_url("assets/img/favicon.png");
    }
}

//get logo
if (!function_exists('getLogo')) {
    function getLogo()
    {
        $generalSettings = Globals::$generalSettings;
        if (!empty($generalSettings)) {
            if (!empty($generalSettings->logo) && file_exists(FCPATH . $generalSettings->logo)) {
                return base_url($generalSettings->logo);
            }
            return base_url("assets/img/logo.svg");
        }
        return base_url("assets/img/logo.svg");
    }
}

//get logo footer
if (!function_exists('getLogoFooter')) {
    function getLogoFooter()
    {
        $generalSettings = Globals::$generalSettings;
        if (!empty($generalSettings)) {
            if (!empty($generalSettings->logo_footer) && file_exists(FCPATH . $generalSettings->logo_footer)) {
                return base_url($generalSettings->logo_footer);
            }
            return base_url("assets/img/logo-footer.svg");
        }
        return base_url("assets/img/logo-footer.svg");
    }
}

//get logo email
if (!function_exists('getLogoEmail')) {
    function getLogoEmail()
    {
        $generalSettings = Globals::$generalSettings;
        if (!empty($generalSettings)) {
            if (!empty($generalSettings->logo_email) && file_exists(FCPATH . $generalSettings->logo_email)) {
                return base_url($generalSettings->logo_email);
            }
            return base_url("assets/img/logo.png");
        }
        return base_url("assets/img/logo.png");
    }
}

//get user avatar
if (!function_exists('getUserAvatar')) {
    function getUserAvatar($avatarPath)
    {
        if (!empty($avatarPath)) {
            if (file_exists(FCPATH . $avatarPath)) {
                return base_url($avatarPath);
            }
            return $avatarPath;
        }
        return base_url("assets/img/user.png");
    }
}

//translation
if (!function_exists('trans')) {
    function trans($string)
    {
        if (isset(Globals::$languageTranslations[$string])) {
            return Globals::$languageTranslations[$string];
        }
        return "";
    }
}

//translation by label
if (!function_exists('getTransByLabel')) {
    function getTransByLabel($label, $langId)
    {
        $model = new \App\Models\LanguageModel();
        return $model->getTransByLabel($label, $langId);
    }
}

//get route
if (!function_exists('getRoute')) {
    function getRoute($key, $slash = false)
    {
        $route = $key;
        if (!empty(Globals::$customRoutes->$key)) {
            $route = Globals::$customRoutes->$key;
            if ($slash == true) {
                $route .= '/';
            }
        }
        return $route;
    }
}

//generate static url
if (!function_exists('generateURL')) {
    function generateURL($route1, $route2 = null)
    {
        if (!empty($route2)) {
            return langBaseUrl(getRoute($route1, true) . getRoute($route2));
        } else {
            return langBaseUrl(getRoute($route1));
        }
    }
}

//generate post url
if (!function_exists('generatePostURL')) {
    function generatePostURL($post, $baseURL = null)
    {
        if ($baseURL == null) {
            $baseURL = langBaseUrl() . '/';
        }
        if (!empty($post)) {
            if (!empty($post->post_url) && Globals::$generalSettings->redirect_rss_posts_to_original == 1) {
                return $post->post_url;
            }
            return $baseURL . $post->slug;
        }
        return "#";
    }
}


//generate tag url
if (!function_exists('generateTagURL')) {
    function generateTagURL($tagSlug)
    {
        if (!empty($tagSlug)) {
            return langBaseUrl(getRoute('tag', true) . $tagSlug);
        }
        return "#";
    }
}

//add new tab for post url
if (!function_exists('postURLNewTab')) {
    function postURLNewTab($post)
    {
        if (!empty($post)) {
            if (!empty($post->post_url) && Globals::$generalSettings->redirect_rss_posts_to_original == 1) {
                echo ' target="_blank"';
            }
        }
    }
}

//generate menu item url
if (!function_exists('generateMenuItemURL')) {
    function generateMenuItemURL($item, $baseCategories)
    {
        if (empty($item)) {
            return langBaseUrl('#');
        }
        if ($item->item_type == 'page') {
            if (!empty($item->item_link)) {
                return $item->item_link;
            } else {
                return langBaseUrl($item->item_slug);
            }
        } elseif ($item->item_type == 'category') {
            $category = getCategory($item->item_id, $baseCategories);
            if (!empty($category)) {
                if (!empty($category->parent_slug)) {
                    return langBaseUrl($category->parent_slug . "/" . $category->slug);
                } else {
                    return langBaseUrl($category->slug);
                }
            }
        } else {
            return langBaseUrl("#");
        }
    }
}

//generate category url
if (!function_exists('generateCategoryURL')) {
    function generateCategoryURL($category)
    {
        if (!empty($category)) {
            if (!empty($category->parent_slug)) {
                return langBaseUrl($category->parent_slug . "/" . $category->slug);
            }
            return langBaseUrl($category->slug);
        }
        return '#';
    }
}

//generate category url by id
if (!function_exists('generateCategoryURLById')) {
    function generateCategoryURLById($categoryId, $baseCategories)
    {
        $category = getCategory(clrNum($categoryId), $baseCategories);
        if (!empty($category)) {
            if (!empty($category->parent_slug)) {
                return langBaseUrl($category->parent_slug . "/" . $category->slug);
            }
            return langBaseUrl($category->slug);
        }
        return "#";
    }
}

//generate tag url
if (!function_exists('generateTagURL')) {
    function generateTagURL($slug)
    {
        if (!empty($slug)) {
            return langBaseUrl(getRoute('tag', true) . $slug);
        }
        return "#";
    }
}


//generate profile url
if (!function_exists('generateProfileURL')) {
    function generateProfileURL($userSlug)
    {
        if (!empty($userSlug)) {
            return langBaseUrl(getRoute('profile', true) . $userSlug);
        }
        return "#";
    }
}

//get sub menu links
if (!function_exists('getSubMenuLinks')) {
    function getSubMenuLinks($menuLinks, $parentId, $type)
    {
        $subLinks = array();
        if (!empty($menuLinks)) {
            $subLinks = array_filter($menuLinks, function ($item) use ($parentId, $type) {
                return $item->item_type == $type && $item->item_parent_id == $parentId;
            });
        }
        return $subLinks;
    }
}

//get gallery album
if (!function_exists('getGalleryAlbum')) {
    function getGalleryAlbum($id)
    {
        $model = new \App\Models\GalleryModel();
        return $model->getAlbum($id);
    }
}

//get gallery category
if (!function_exists('getGalleryCategory')) {
    function getGalleryCategory($id)
    {
        $model = new \App\Models\GalleryModel();
        return $model->getCategory($id);
    }
}

//get gallery cover image
if (!function_exists('getGalleryCoverImage')) {
    function getGalleryCoverImage($albumId)
    {
        $model = new \App\Models\GalleryModel();
        return $model->getCoverImage($albumId);
    }
}

//get page by default name
if (!function_exists('getPageByDefaultName')) {
    function getPageByDefaultName($defaultName, $langId)
    {
        $model = new \App\Models\PageModel();
        return $model->getPageByDefaultName($defaultName, $langId);
    }
}

//get page link by default name
if (!function_exists('getPageLinkByDefaultName')) {
    function getPageLinkByDefaultName($defaultName, $lang_id)
    {
        $page = getPageByDefaultName($defaultName, $lang_id);
        if (!empty($page)) {
            return langBaseUrl($page->slug);
        }
        return "#";
    }
}

//check if user online
if (!function_exists('isUserOnline')) {
    function isUserOnline($timestamp)
    {
        if (!empty($timestamp)) {
            $timeAgo = strtotime($timestamp);
            $currentTime = time();
            $timeDifference = $currentTime - $timeAgo;
            $seconds = $timeDifference;
            $minutes = round($seconds / 60);
            if ($minutes <= 3) {
                return true;
            }
        }
        return false;
    }
}

//check user follows
if (!function_exists('isUserFollows')) {
    function isUserFollows($followingId, $followerId)
    {
        $model = new \App\Models\ProfileModel();
        return $model->isUserFollows($followingId, $followerId);
    }
}

//print meta tag
if (!function_exists('escMeta')) {
    function escMeta($str)
    {
        if (!empty($str)) {
            return esc($str, 'html', 'UTF-8');
        }
        return '';
    }
}

//generate slug
if (!function_exists('strSlug')) {
    function strSlug($str)
    {
        $str = strTrim($str);
        if (!empty($str)) {
            $str = @convert_accented_characters($str);
            return url_title($str, '-', TRUE);
        }
    }
}

//clean slug
if (!function_exists('cleanSlug')) {
    function cleanSlug($slug)
    {
        $slug = strTrim($slug);
        if (!empty($slug)) {
            $slug = urldecode($slug);
        }
        if (!empty($slug)) {
            $slug = strip_tags($slug);
        }
        return removeSpecialCharacters($slug);
    }
}

//clean string
if (!function_exists('cleanStr')) {
    function cleanStr($str)
    {
        $str = strTrim($str);
        $str = removeSpecialCharacters($str);
        return esc($str);
    }
}

//clean number
if (!function_exists('clrNum')) {
    function clrNum($num)
    {
        $num = strTrim($num);
        $num = esc($num);
        if (empty($num)) {
            return 0;
        }
        return intval($num);
    }
}

//clear quotes
if (!function_exists('clrQuotes')) {
    function clrQuotes($str)
    {
        $str = strReplace('"', '', $str);
        $str = strReplace("'", '', $str);
        return $str;
    }
}

//clear double quotes
if (!function_exists('clrDQuotes')) {
    function clrDQuotes($str)
    {
        $str = strReplace('"', '', $str);
        return $str;
    }
}

//remove forbidden characters
if (!function_exists('removeForbiddenCharacters')) {
    function removeForbiddenCharacters(?string $str): string
    {
        $str = $str ?? '';
        $str = trim($str);
        $forbiddenChars = [';', '"', '$', '%', '*', '/', '\'', '<', '>', '=', '?', '[', ']', '\\', '^', '`', '{', '}', '|', '~', '+'];
        return str_replace($forbiddenChars, '', $str);
    }
}


//remove special characters
if (!function_exists('removeSpecialCharacters')) {
    function removeSpecialCharacters($str, $removeQuotes = false)
    {
        $str = removeForbiddenCharacters($str);
        $extraForbiddenChars = ['#', '!', '(', ')'];
        $str = str_replace($extraForbiddenChars, '', $str);
        if ($removeQuotes) {
            $str = str_replace(['"', "'"], '', $str);
        }
        return $str;
    }
}

//price formatted
if (!function_exists('priceFormatted')) {
    function priceFormatted($price, $decimalPoint = 2)
    {
        $thousandSep = ',';
        $decPoint = '.';
        if (getThousandSeparator() != ',') {
            $thousandSep = '.';
            $decPoint = ',';
        }
        if (!empty($price)) {
            if (is_int($price)) {
                $price = @number_format($price, 0, $decPoint, $thousandSep);
            } else {
                $price = @number_format($price, $decimalPoint, $decPoint, $thousandSep);
            }
        }
        if (Globals::$generalSettings->currency_symbol_format == "left") {
            $price = "<span>" . Globals::$generalSettings->currency_symbol . "</span>" . $price;
        } else {
            $price = $price . "<span>" . Globals::$generalSettings->currency_symbol . "</span>";
        }
        return $price;
    }
}

//get reward price decimal
if (!function_exists('getRewardPriceDecimal')) {
    function getRewardPriceDecimal()
    {
        if (Globals::$generalSettings->reward_amount >= 0.1) {
            return 5;
        }
        return 6;
    }
}

//get thousands separator
if (!function_exists('getThousandSeparator')) {
    function getThousandSeparator()
    {
        $thousandSeparator = ',';
        if (Globals::$generalSettings->currency_format == 'european') {
            $thousandSeparator = '.';
        }
        return $thousandSeparator;
    }
}

//check admin nav
if (!function_exists('getEarningObjectByDay')) {
    function getEarningObjectByDay($day, $pageViewsCounts)
    {
        if ($day < 10 && strpos($day, '0') == false) {
            $day = str_pad($day, 2, '0', STR_PAD_LEFT);
        }
        $date = date('Y') . '-' . date('m') . '-' . $day;
        $objects = array_filter($pageViewsCounts, function ($item) use ($date) {
            return $item->date == $date;
        });
        $object = null;
        if (!empty($objects)) {
            foreach ($objects as $key => $value) {
                $object = $value;
                break;
            }
        }
        return $object;
    }
}

//set cookie
if (!function_exists('helperSetCookie')) {
    function helperSetCookie($name, $value, $time = null)
    {
        if ($time == null) {
            $time = time() + (86400 * 30);
        }
        $config = config('App');
        $params = [
            'expires' => $time,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        if (!empty(getenv('cookie.prefix'))) {
            $name = getenv('cookie.prefix') . $name;
        }
        setcookie($name, $value, $params);
    }
}

//get cookie
if (!function_exists('helperGetCookie')) {
    function helperGetCookie($name)
    {
        if (!empty(getenv('cookie.prefix'))) {
            $name = getenv('cookie.prefix') . $name;
        }
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return false;
    }
}

//delete cookie
if (!function_exists('helperDeleteCookie')) {
    function helperDeleteCookie($name)
    {
        if (!empty(getenv('cookie.prefix'))) {
            $name = getenv('cookie.prefix') . $name;
        }
        if (!empty(helperGetCookie($name))) {
            helperSetCookie($name, '', time() - 3600);
        }
    }
}

//set session
if (!function_exists('setSession')) {
    function setSession($name, $value)
    {
        $session = \Config\Services::session();
        $session->set($name, $value);
    }
}

//get session
if (!function_exists('getSession')) {
    function getSession($name)
    {
        $session = \Config\Services::session();
        if ($session->get($name) !== null) {
            return $session->get($name);
        }
        return null;
    }
}

//delete session
if (!function_exists('deleteSession')) {
    function deleteSession($name)
    {
        $session = \Config\Services::session();
        if ($session->get($name) !== null) {
            $session->remove($name);
        }
    }
}

//generate unique id
if (!function_exists('generateToken')) {
    function generateToken($short = false)
    {
        $token = uniqid('', TRUE);
        $token = strReplace('.', '-', $token);
        if ($short == false) {
            $token = $token . '-' . rand(10000000, 99999999);
        }
        return $token;
    }
}

//update slug
if (!function_exists('updateSlug')) {
    function updateSlug($table, $id)
    {
        $model = new \App\Models\PageModel();
        $model->updateSlug($table, $id);
    }
}

//get validation rules
if (!function_exists('getValRules')) {
    function getValRules($val)
    {
        $rules = $val->getRules();
        $newRules = array();
        if (!empty($rules)) {
            foreach ($rules as $key => $rule) {
                $newRules[$key] = [
                    'label' => $rule['label'],
                    'rules' => $rule['rules'],
                    'errors' => [
                        'required' => trans("form_validation_required"),
                        'min_length' => trans("form_validation_min_length"),
                        'max_length' => trans("form_validation_max_length"),
                        'matches' => trans("form_validation_matches"),
                        'is_unique' => trans("form_validation_is_unique")
                    ]
                ];
            }
        }
        return $newRules;
    }
}

//get segment value
if (!function_exists('getSegmentValue')) {
    function getSegmentValue($segmentNumber)
    {
        try {
            $uri = service('uri');
            if ($uri->getSegment($segmentNumber) !== null) {
                return $uri->getSegment($segmentNumber);
            }
        } catch (Exception $e) {
        }
        return null;
    }
}

//check admin nav
if (!function_exists('isAdminNavActive')) {
    function isAdminNavActive($arrayNavItems)
    {
        $segment = getSegmentValue(2);
        if (!empty($segment) && !empty($arrayNavItems)) {
            if (in_array($segment, $arrayNavItems)) {
                echo ' ' . 'active';
            }
        }
    }
}

//get navigation item edit link
if (!function_exists('getAdminNavItemEditLink')) {
    function getAdminNavItemEditLink($menuItem)
    {
        if (!empty($menuItem)) {
            if ($menuItem->item_type == "category") {
                return adminUrl('edit-category/' . $menuItem->item_id . '?redirect_url=' . current_url() . '?' . $_SERVER['QUERY_STRING']);
            } else {
                if (!empty($menuItem->item_link)) {
                    return adminUrl('edit-menu-link/' . $menuItem->item_id);
                } else {
                    return adminUrl('edit-page/' . $menuItem->item_id . '?redirect_url=' . current_url() . '?' . $_SERVER['QUERY_STRING']);
                }
            }
        }
    }
}

//get navigation item delete function
if (!function_exists('getAdminNavItemDeleteFunction')) {
    function getAdminNavItemDeleteFunction($menuItem)
    {
        if (!empty($menuItem)) {
            if ($menuItem->item_type == "category") {
                return "deleteItem('Category/deleteCategoryPost','" . $menuItem->item_id . "','" . trans("confirm_category") . "');";
            } else {
                if (!empty($menuItem->item_link)) {
                    return "deleteItem('Admin/deleteNavigationPost','" . $menuItem->item_id . "','" . trans("confirm_link") . "');";
                } else {
                    return "deleteItem('Admin/deletePagePost','" . $menuItem->item_id . "','" . trans("confirm_page") . "');";
                }
            }
        }
    }
}

//get navigation item type
if (!function_exists('getAdminNavItemType')) {
    function getAdminNavItemType($menuItem)
    {
        if (!empty($menuItem)) {
            if ($menuItem->item_type == "category") {
                return trans("category");
            } else {
                if (!empty($menuItem->item_link)) {
                    return trans("link");
                } else {
                    return trans("page");
                }
            }
        }
    }
}

//count items
if (!function_exists('countItems')) {
    function countItems($items)
    {
        if (!empty($items) && is_array($items)) {
            return count($items);
        }
        return 0;
    }
}

//is recaptcha enabled
if (!function_exists('isRecaptchaEnabled')) {
    function isRecaptchaEnabled($generalSettings)
    {
        if (!empty($generalSettings->recaptcha_site_key) && !empty($generalSettings->recaptcha_secret_key)) {
            return true;
        }
        return false;
    }
}

//get recaptcha
if (!function_exists('reCaptcha')) {
    function reCaptcha($action, $generalSettings)
    {
        if (isRecaptchaEnabled($generalSettings)) {
            loadLibrary('reCAPTCHA');
            $reCAPTCHA = new reCAPTCHA($generalSettings->recaptcha_site_key, $generalSettings->recaptcha_secret_key);
            $reCAPTCHA->setLanguage(Globals::$activeLang->short_form);
            if (Globals::$darkMode) {
                $reCAPTCHA->setTheme('dark');
            }
            if ($action == "generate") {
                echo $reCAPTCHA->getScript();
                echo $reCAPTCHA->getHtml();
            } elseif ($action == "validate") {
                if (!$reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
                    return 'invalid';
                }
            }
        }
    }
}

//date format with month
if (!function_exists('formatDateFront')) {
    function formatDateFront($timestamp)
    {
        if (!empty($timestamp)) {
            $date = date("M j, Y", strtotime($timestamp));
            return replaceMonthName($date);
        }
    }
}

//date format
if (!function_exists('formatDate')) {
    function formatDate($timestamp)
    {
        if (!empty($timestamp)) {
            return date("Y-m-d / H:i", strtotime($timestamp));
        }
    }
}

//print formatted hour
if (!function_exists('formatHour')) {
    function formatHour($timestamp)
    {
        if (!empty($timestamp)) {
            return date("H:i", strtotime($timestamp));
        }
    }
}

//date format
if (!function_exists('replaceMonthName')) {
    function replaceMonthName($str)
    {
        $str = strTrim($str);
        $str = strReplace("Jan", trans("January"), $str);
        $str = strReplace("Feb", trans("February"), $str);
        $str = strReplace("Mar", trans("March"), $str);
        $str = strReplace("Apr", trans("April"), $str);
        $str = strReplace("May", trans("May"), $str);
        $str = strReplace("Jun", trans("June"), $str);
        $str = strReplace("Jul", trans("July"), $str);
        $str = strReplace("Aug", trans("August"), $str);
        $str = strReplace("Sep", trans("September"), $str);
        $str = strReplace("Oct", trans("October"), $str);
        $str = strReplace("Nov", trans("November"), $str);
        $str = strReplace("Dec", trans("December"), $str);
        return $str;
    }
}

//date diff
if (!function_exists('dateDifference')) {
    function dateDifference($date1, $date2, $format = '%a')
    {
        if (!empty($date1) && !empty($date2)) {
            $datetime1 = date_create($date1);
            $datetime2 = date_create($date2);
            $diff = date_diff($datetime1, $datetime2);
            return $diff->format($format);
        }
    }
}

//date difference in hours
if (!function_exists('dateDifferenceInHours')) {
    function dateDifferenceInHours($date1, $date2)
    {
        if (!empty($date1) && !empty($date2)) {
            $datetime1 = date_create($date1);
            $datetime2 = date_create($date2);
            $diff = date_diff($datetime1, $datetime2);
            $days = $diff->format('%a');
            $hours = $diff->format('%h');
            return $hours + ($days * 24);
        }
    }
}

//check cron time
if (!function_exists('checkCronTime')) {
    function checkCronTime($hour)
    {
        if (empty(Globals::$generalSettings->last_cron_update) || dateDifferenceInHours(date('Y-m-d H:i:s'), Globals::$generalSettings->last_cron_update) >= $hour) {
            return true;
        }
        return false;
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($timestamp)
    {
        if (!empty($timestamp)) {
            $timeDiff = time() - strtotime($timestamp);
            $seconds = $timeDiff;
            $minutes = round($seconds / 60);
            $hours = round($seconds / 3600);
            $days = round($seconds / 86400);
            $weeks = round($seconds / 604800);
            $months = round($seconds / 2629440);
            $years = round($seconds / 31553280);
            if ($seconds <= 60) {
                return trans("just_now");
            } else if ($minutes <= 60) {
                if ($minutes == 1) {
                    return "1 " . trans("minute") . " " . trans("ago");
                } else {
                    return $minutes . " " . trans("minutes") . " " . trans("ago");
                }
            } else if ($hours <= 24) {
                if ($hours == 1) {
                    return "1 " . trans("hour") . " " . trans("ago");
                } else {
                    return $hours . " " . trans("hours") . " " . trans("ago");
                }
            } else if ($days <= 30) {
                if ($days == 1) {
                    return "1 " . trans("day") . " " . trans("ago");
                } else {
                    return $days . " " . trans("days") . " " . trans("ago");
                }
            } else if ($months <= 12) {
                if ($months == 1) {
                    return "1 " . trans("month") . " " . trans("ago");
                } else {
                    return $months . " " . trans("months") . " " . trans("ago");
                }
            } else {
                if ($years == 1) {
                    return "1 " . trans("year") . " " . trans("ago");
                } else {
                    return $years . " " . trans("years") . " " . trans("ago");
                }
            }
        }
    }
}

//paginate
if (!function_exists('paginate')) {
    function paginate($perPage, $total)
    {
        $page = @intval(inputGet('page') ?? '');
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        $pager = \Config\Services::pager();
        $pagerLinks = $pager->makeLinks($page, $perPage, $total, 'default_full');
        $pageObject = new stdClass();
        $pageObject->page = $page;
        $pageObject->currentPage = $pager->getCurrentPage();
        $pageObject->offset = ($page - 1) * $perPage;
        $pageObject->links = $pagerLinks;
        return $pageObject;
    }
}

//paginate
if (!function_exists('getIPAddress')) {
    function getIPAddress()
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }
}

//convert xml characters
if (!function_exists('convertToXMLCharacter')) {
    function convertToXMLCharacter($str)
    {
        $str = strReplace('&', '&amp;', $str);
        $str = strReplace('<', '&lt;', $str);
        $str = strReplace('>', '&gt;', $str);
        $str = strReplace('\'', '&apos;', $str);
        $str = strReplace('"', '&quot;', $str);
        return strReplace('#45;', '', $str);
    }
}

//check newsletter modal
if (!function_exists('checkNewsletterModal')) {
    function checkNewsletterModal()
    {
        if (!authCheck() && Globals::$generalSettings->newsletter_status == 1 && Globals::$generalSettings->newsletter_popup == 1) {
            if (empty(helperGetCookie('newsletter_popup'))) {
                helperSetCookie('newsletter_popup', '1', time() + (86400 * 365));
                return true;
            }
        }
        return false;
    }
}

//set active language ajax post
if (!function_exists('setActiveLangPostRequest')) {
    function setActiveLangPostRequest()
    {
        $sysLangId = clrNum(inputPost('sys_lang_id'));
        if (!empty($sysLangId) && Globals::$generalSettings->site_lang != $sysLangId) {
            $language = getLanguage($sysLangId);
            if (!empty($language)) {
                Globals::setActiveLanguage($language->id);
                Globals::updateLangBaseURL($language->short_form);
            }
        }
    }
}

//get popular tags
if (!function_exists('getPopularTags')) {
    function getPopularTags($langId)
    {
        $model = new \App\Models\TagModel();
        return $model->getPopularTags($langId);
    }
}

//get polls
if (!function_exists('getPollsByActiveLang')) {
    function getPollsByActiveLang()
    {
        $model = new \App\Models\PollModel();
        return $model->getPollsByActiveLang();
    }
}

//calculate total vote of poll option
if (!function_exists('calculateTotalVotePollOption')) {
    function calculateTotalVotePollOption($poll)
    {
        $total = 0;
        if (!empty($poll)) {
            for ($i = 1; $i <= 10; $i++) {
                $op = "option{$i}_vote_count";
                $total += $poll->$op;
            }
        }
        return $total;
    }
}

//get social links array
if (!function_exists('getSocialLinksArray')) {
    function getSocialLinksArray($obj = null, $personalWebsite = false)
    {
        $data = null;
        if (!empty($obj->social_media_data)) {
            $data = unserializeData($obj->social_media_data);
        }
        $array = array(
            array('name' => 'facebook', 'value' => !empty($data) && !empty($data['facebook']) ? $data['facebook'] : ''),
            array('name' => 'twitter', 'value' => !empty($data) && !empty($data['twitter']) ? $data['twitter'] : ''),
            array('name' => 'instagram', 'value' => !empty($data) && !empty($data['instagram']) ? $data['instagram'] : ''),
            array('name' => 'tiktok', 'value' => !empty($data) && !empty($data['tiktok']) ? $data['tiktok'] : ''),
            array('name' => 'whatsapp', 'value' => !empty($data) && !empty($data['whatsapp']) ? $data['whatsapp'] : ''),
            array('name' => 'youtube', 'value' => !empty($data) && !empty($data['youtube']) ? $data['youtube'] : ''),
            array('name' => 'discord', 'value' => !empty($data) && !empty($data['discord']) ? $data['discord'] : ''),
            array('name' => 'telegram', 'value' => !empty($data) && !empty($data['telegram']) ? $data['telegram'] : ''),
            array('name' => 'pinterest', 'value' => !empty($data) && !empty($data['pinterest']) ? $data['pinterest'] : ''),
            array('name' => 'linkedin', 'value' => !empty($data) && !empty($data['linkedin']) ? $data['linkedin'] : ''),
            array('name' => 'twitch', 'value' => !empty($data) && !empty($data['twitch']) ? $data['twitch'] : ''),
            array('name' => 'vk', 'value' => !empty($data) && !empty($data['vk']) ? $data['vk'] : '')
        );
        if ($personalWebsite == true) {
            array_push($array, array('name' => 'personal_website_url', 'value' => !empty($data) && !empty($data['personal_website_url']) ? $data['personal_website_url'] : ''));
        }
        return $array;
    }
}

//get widget
if (!function_exists('getWidget')) {
    function getWidget($baseWidgets, $type)
    {
        if (!empty($baseWidgets)) {
            foreach ($baseWidgets as $widget) {
                if ($widget->type == $type) {
                    return $widget;
                }
            }
        }
        return null;
    }
}

//get category widgets
if (!function_exists('getCategoryWidgets')) {
    function getCategoryWidgets($categoryId, $baseWidgets, $adSpaces, $langId)
    {
        $arrayWidgets = array();
        $widgetIds = array();
        $ads = array();
        $hasWidgets = false;
        if (!empty($baseWidgets)) {
            if (empty($categoryId)) {
                foreach ($baseWidgets as $widget) {
                    if (empty($widget->display_category_id) && !in_array($widget->id, $widgetIds) && $widget->lang_id == $langId) {
                        array_push($arrayWidgets, $widget);
                        array_push($widgetIds, $widget->id);
                    }
                }
            } else {
                foreach ($baseWidgets as $widget) {
                    if ($widget->display_category_id == $categoryId && !in_array($widget->id, $widgetIds) && $widget->lang_id == $langId) {
                        array_push($arrayWidgets, $widget);
                        array_push($widgetIds, $widget->id);
                    }
                }
            }
        }
        if (!empty($adSpaces)) {
            foreach ($adSpaces as $item) {
                if ($item->display_category_id == $categoryId) {
                    array_push($ads, $item);
                }
            }
        }
        if (!empty($arrayWidgets) || !empty($ads)) {
            $hasWidgets = true;
        }

        $classWidgets = new stdClass();
        $classWidgets->widgets = $arrayWidgets;
        $classWidgets->ads = $ads;
        $classWidgets->hasWidgets = $hasWidgets;
        return $classWidgets;
    }
}

//get reactions array
if (!function_exists('getReactionsArray')) {
    function getReactionsArray()
    {
        return ['like', 'dislike', 'love', 'funny', 'angry', 'sad', 'wow'];
    }
}

//is reaction voted
if (!function_exists('isReactionVoted')) {
    function isReactionVoted($postId, $reaction)
    {
        if (!empty(getSession('reaction_' . $reaction . '_' . $postId))) {
            return true;
        }
        if (!empty(helperGetCookie('reaction_' . $reaction . '_' . $postId))) {
            return true;
        }
        return false;
    }
}

//get font family
if (!function_exists('getFontFamily')) {
    function getFontFamily($activeFonts, $key, $removeFamilyText = false)
    {
        if (!empty($activeFonts[$key]) && !empty($activeFonts[$key]->font_family)) {
            $fontFamily = $activeFonts[$key]->font_family;
            if (!empty($fontFamily)) {
                if ($removeFamilyText) {
                    $fontFamilyArray = explode(':', $fontFamily);
                    if (!empty($fontFamilyArray[1])) {
                        return $fontFamilyArray[1];
                    }
                }
                return $activeFonts[$key]->font_family;
            }


        }
        return '';
    }
}

//get font url
if (!function_exists('getFontURL')) {
    function getFontURL($activeFonts, $key)
    {
        if (!empty($activeFonts[$key]) && !empty($activeFonts[$key]->font_url) && $activeFonts[$key]->font_source != 'local') {
            return $activeFonts[$key]->font_url;
        }
        return '';
    }
}

//load library
if (!function_exists('loadLibrary')) {
    function loadLibrary($library)
    {
        $path = APPPATH . 'Libraries/' . $library . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

//get cache data or set data if not exists
if (!function_exists('getOrSetCache')) {
    function getOrSetCache($cacheKey, callable $callback)
    {
        if (Globals::$generalSettings->cache_system == 1) {
            $data = cache($cacheKey);
            if (isset($data)) {
                return $data;
            }
            $data = $callback();
            cache()->save($cacheKey, $data, Globals::$generalSettings->cache_refresh_time);
            return $data;
        }
        return $callback();
    }
}

//get static cache data or set data if not exists
if (!function_exists('getOrSetStaticCache')) {
    function getOrSetStaticCache($cacheKey, callable $callback)
    {
        if (Globals::$generalSettings->static_cache_system == 1) {
            $cacheKey = 'cstatic_' . $cacheKey;
            $data = cache($cacheKey);
            if (isset($data)) {
                return $data;
            }
            $data = $callback();
            cache()->save($cacheKey, $data, 604800); //7 days
            return $data;
        }
        return $callback();
    }
}

//get stable cache data or set data if not exists
if (!function_exists('getOrSetStableCache')) {
    function getOrSetStableCache($cacheKey, callable $callback, $time)
    {
        if (empty($time) || $time < 1) {
            $time = 3600;
        }
        $cacheKey = 'cstable_' . $cacheKey;
        $data = cache($cacheKey);
        if (isset($data)) {
            return $data;
        }
        $data = $callback();
        cache()->save($cacheKey, $data, $time);
        return $data;
    }
}

//reset cache data
if (!function_exists('resetCacheData')) {
    function resetCacheData()
    {
        $cachePath = WRITEPATH . 'cache/';
        $files = glob($cachePath . '*');
        if (!empty($files)) {
            foreach ($files as $file) {
                if (strpos($file, 'index.html') === false && strpos($file, 'cstatic_') === false && strpos($file, 'cstable_') === false) {
                    @unlink($file);
                }
            }
        }
    }
}

//reset static cache
if (!function_exists('resetCacheStatic')) {
    function resetCacheStatic($deleteStable = true)
    {
        $cachePath = WRITEPATH . 'cache/';
        $patterns = [];
        if ($deleteStable) {
            $patterns = [
                $cachePath . 'cstatic_*',
                $cachePath . 'cstable_*'
            ];
        } else {
            $patterns = [
                $cachePath . 'cstatic_*'
            ];
        }
        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }
        }
    }
}

//reset cache data on change
if (!function_exists('resetCacheDataOnChange')) {
    function resetCacheDataOnChange()
    {
        //reset category posts
        $cachePath = WRITEPATH . 'cache/';
        $files = glob($cachePath . 'cstable_category_posts_*');
        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        if (Globals::$generalSettings->refresh_cache_database_changes == 1) {
            resetCacheData();
        }
    }
}

/**
 * --------------------------------------------------------------------------
 * POST FUNCTIONS
 * --------------------------------------------------------------------------
 */

//get selected posts by type
if (!function_exists('getSelectedPostsByType')) {
    function getSelectedPostsByType($postsSelected, $type)
    {
        $posts = array();
        $generalSettings = Globals::$generalSettings;
        if ($type == 'slider') {
            if (!empty($postsSelected)) {
                foreach ($postsSelected as $post) {
                    if ($post->selection_type == 'slider') {
                        array_push($posts, $post);
                    }
                }
            }
            if (!empty($posts)) {
                if ($generalSettings->sort_slider_posts == 'by_slider_order') {
                    usort($posts, function ($a, $b) {
                        return $a->slider_order <=> $b->slider_order;
                    });
                }
            }
        } elseif ($type == 'featured') {
            if (!empty($postsSelected)) {
                foreach ($postsSelected as $post) {
                    if ($post->selection_type == 'featured') {
                        array_push($posts, $post);
                    }
                }
            }
            if (!empty($posts)) {
                if ($generalSettings->sort_featured_posts == 'by_featured_order') {
                    usort($posts, function ($a, $b) {
                        return $a->featured_order <=> $b->featured_order;
                    });
                }
                $posts = array_slice($posts, 0, 10);
            }
        } elseif ($type == 'breaking') {
            if (!empty($postsSelected)) {
                foreach ($postsSelected as $post) {
                    if ($post->selection_type == 'breaking' && countItems($posts) < BREAKING_POSTS_LIMIT) {
                        array_push($posts, $post);
                    }
                }
            }
        } elseif ($type == 'recommended') {
            if (!empty($postsSelected)) {
                foreach ($postsSelected as $post) {
                    if ($post->selection_type == 'recommended' && countItems($posts) < RECOMMENDED_POSTS_LIMIT) {
                        array_push($posts, $post);
                    }
                }
            }
        }
        return $posts;
    }
}

//get category by id
if (!function_exists('getCategoryById')) {
    function getCategoryById($id)
    {
        $model = new \App\Models\CategoryModel();
        return $model->getCategory($id);
    }
}

//get category
if (!function_exists('getCategory')) {
    function getCategory($id, $categories)
    {
        $category = null;
        if (!empty($categories)) {
            $category = array_filter($categories, function ($item) use ($id) {
                return $item->id == $id;
            });
            foreach ($category as $key => $value) {
                $category = $value;
                break;
            }
        }
        return $category;
    }
}

//get categories
if (!function_exists('getCategories')) {
    function getCategories()
    {
        $model = new \App\Models\CategoryModel();
        return $model->getCategories();
    }
}

//get subcategories
if (!function_exists('getSubcategories')) {
    function getSubcategories($parentId, $categories, $showOnHomepageOnly = false)
    {
        $array = array();
        if (!empty($categories)) {
            foreach ($categories as $item) {
                if ($item->parent_id == $parentId) {
                    if ($showOnHomepageOnly == true) {
                        if ($item->show_on_homepage == 1) {
                            array_push($array, $item);
                        }
                    } else {
                        array_push($array, $item);
                    }
                }
            }
        }
        return $array;
    }
}

//get category tree
if (!function_exists('getCategoryTree')) {
    function getCategoryTree($categoryId, $categories)
    {
        $tree = array();
        $categoryId = clrNum($categoryId);
        if (!empty($categoryId)) {
            array_push($tree, $categoryId);
            $subCategories = getSubcategories($categoryId, $categories);
            if (!empty($subCategories)) {
                foreach ($subCategories as $subCategory) {
                    array_push($tree, $subCategory->id);
                }
            }
        }
        return $tree;
    }
}

//get parent category tree
if (!function_exists('getParentCategoryTree')) {
    function getParentCategoryTree($categoryId, $categories)
    {
        $tree = array();
        $categoryId = clrNum($categoryId);
        if (!empty($categoryId)) {
            $category = getCategory($categoryId, $categories);
            if (!empty($category) && $category->parent_id != 0) {
                $parent = getCategory($category->parent_id, $categories);
                if (!empty($parent)) {
                    array_push($tree, $parent);
                }
            }
            array_push($tree, $category);
        }
        return $tree;
    }
}

//get posts by category
if (!function_exists('getPostsByCategoryId')) {
    function getPostsByCategoryId($categoryId, $categories, $latestCategoryPosts)
    {
        if (!empty($latestCategoryPosts)) {
            $categoryTree = getCategoryTree($categoryId, $categories);
            if (!empty($categoryTree)) {
                return array_filter($latestCategoryPosts, function ($item) use ($categoryTree) {
                    return in_array($item->category_id, $categoryTree);
                });
            }
        }
        return null;
    }
}

//get post by id
if (!function_exists('getPostById')) {
    function getPostById($id)
    {
        $model = new \App\Models\PostAdminModel();
        return $model->getPost($id);
    }
}

//get post image
if (!function_exists('getPostImage')) {
    function getPostImage($post, $imageSize)
    {
        if (empty($post)) {
            return '';
        }
        if (!empty($post->image_url)) {
            return $post->image_url;
        }
        if (!empty($post->image_data)) {
            $image = getPostImagePath($post, $imageSize);
            if ($image['storage'] == 'aws_s3') {
                $path = getAWSBaseURL() . $image['path'];
            } else {
                $path = base_url($image['path']);
            }
            return $path;
        }
        return '';
    }
}

//get post image path
if (!function_exists('getPostImagePath')) {
    function getPostImagePath($post, $imageSize)
    {
        if (empty($post)) {
            return '';
        }
        if (!empty($post->image_data)) {
            $pairs = explode("||", $post->image_data);
            $imgArray = [];
            foreach ($pairs as $pair) {
                if (!empty($pair)) {
                    $array = explode("::", $pair);
                    if (!empty($array[0]) && !empty($array[1])) {
                        $imgArray[$array[0]] = $array[1];
                    }
                }
            }
            $path = '';
            $sizeKey = '';
            switch ($imageSize) {
                case 'big':
                    $sizeKey = 'img_bg';
                    break;
                case 'default':
                    $sizeKey = 'img_df';
                    break;
                case 'slider':
                    $sizeKey = 'img_sl';
                    break;
                case 'mid':
                    $sizeKey = 'img_md';
                    break;
                case 'small':
                    $sizeKey = 'img_sm';
                    break;
                default:
                    return '';
            }

            return [
                'path' => !empty($imgArray[$sizeKey]) ? $imgArray[$sizeKey] : '',
                'storage' => $imgArray['img_st'] == 'aws_s3' ? 'aws_s3' : 'local'
            ];
        }
        return '';
    }
}

//check post image exist
if (!function_exists('checkPostImg')) {
    function checkPostImg($post, $type = '')
    {
        $isExist = false;
        if (!empty($post)) {
            if (!empty($post->image_id) || !empty($post->image_url)) {
                $isExist = true;
            }
        }
        if ($isExist == false && $type == 'class') {
            echo " post-item-no-image";
        } else {
            if ($type != 'class') {
                return $isExist;
            }
        }
    }
}

//get popular posts
if (!function_exists('getPopularPosts')) {
    function getPopularPosts($langId)
    {
        $model = new \App\Models\PostModel();
        return $model->getPopularPosts($langId);
    }
}

//get trending posts
if (!function_exists('getTrendingPosts')) {
    function getTrendingPosts($latestCategoryPosts)
    {
        $posts = array();
        if (!empty($latestCategoryPosts)) {
            usort($latestCategoryPosts, function ($a, $b) {
                return $b->pageviews - $a->pageviews;
            });
            $posts = countItems($latestCategoryPosts) > 3 ? array_slice($latestCategoryPosts, 0, 3) : $latestCategoryPosts;
        }
        return $posts;
    }
}

//get post selections
if (!function_exists('getPostSelections')) {
    function getPostSelections($postId)
    {
        $model = new \App\Models\PostAdminModel();
        return $model->getPostSelections($postId);
    }
}

//get post images
if (!function_exists('getPostAdditionalImages')) {
    function getPostAdditionalImages($postId)
    {
        $model = new \App\Models\PostAdminModel();
        return $model->getAdditionalImages($postId);
    }
}

//get post files
if (!function_exists('getPostFiles')) {
    function getPostFiles($postId)
    {
        $model = new \App\Models\PostAdminModel();
        return $model->getPostFiles($postId);
    }
}

//get quiz question answer
if (!function_exists('getQuizQuestionAnswers')) {
    function getQuizQuestionAnswers($questionId)
    {
        $model = new \App\Models\QuizModel();
        return $model->getQuizQuestionAnswers($questionId);
    }
}

//get poll question answer by user
if (!function_exists('getPollQuestionAnswerByUser')) {
    function getPollQuestionAnswerByUser($post, $userPollAnswers, $questionId)
    {
        if (!empty($userPollAnswers)) {
            foreach ($userPollAnswers as $item) {
                if ($item->question_id == $questionId) {
                    return $item->answer_id;
                }
            }
        }
        if (!empty(getSession('pollAnswer' . $questionId))) {
            return getSession('pollAnswer' . $questionId);
        }
        return null;
    }
}

//calculate percentage
if (!function_exists('calculatePercentage')) {
    function calculatePercentage($sum, $value)
    {
        $percentage = 0;
        if (!empty($sum) && !empty($value) && $sum > 0 && $value > 0) {
            $percentage = ($value * 100) / $sum;
            if (!empty($percentage)) {
                $percentage = number_format($percentage, 1);
            }
        }
        return $percentage;
    }
}

//get post audios
if (!function_exists('getPostAudios')) {
    function getPostAudios($postId)
    {
        $model = new \App\Models\PostAdminModel();
        return $model->getPostAudios($postId);
    }
}

//set page meta data
if (!function_exists('setPageMeta')) {
    function setPageMeta($pageTitle, $data = null)
    {
        if ($data == null) {
            $data = array();
        }
        $data['title'] = $pageTitle;
        $data['description'] = $pageTitle . ' - ' . Globals::$settings->site_title;
        $data['keywords'] = $pageTitle . ', ' . Globals::$settings->application_name;
        return $data;
    }
}

//set post meta tags
if (!function_exists('setPostMetaTags')) {
    function setPostMetaTags($post, $postTags, $data)
    {
        $data['title'] = $post->title;
        $data['description'] = $post->summary;
        $data['keywords'] = $post->keywords;
        $data['ogTitle'] = $post->title;
        $data['ogType'] = 'article';
        $data['ogImage'] = getPostImage($post, 'big');
        $data['ogWidth'] = '750';
        $data['ogHeight'] = '422';
        $data['ogCreator'] = $post->author_username;
        $data['ogAuthor'] = $post->author_username;
        $data['ogPublishedTime'] = $post->created_at;
        $data['ogModifiedTime'] = $post->updated_at;
        if (empty($post->updated_at)) {
            $data['ogModifiedTime'] = $post->created_at;
        }
        $data['ogTags'] = $postTags;
        return $data;
    }
}

//check post is published
if (!function_exists('isPostPublished')) {
    function isPostPublished($post)
    {
        if ($post->status != 1 || $post->is_scheduled == 1 || $post->visibility != 1) {
            return false;
        }
        return true;
    }
}

//check post is in the reading list or not
if (!function_exists('isPostInReadingList')) {
    function isPostInReadingList($postId)
    {
        $model = new \App\Models\PostModel();
        return $model->isPostInReadingList($postId);
    }
}

//generate keywords
if (!function_exists('generateKeywords')) {
    function generateKeywords($title)
    {
        if (!empty($title)) {
            $array = explode(" ", $title);
            $keywords = "";
            $i = 0;
            if (!empty($array)) {
                foreach ($array as $item) {
                    $item = strTrim($item);
                    $item = strTrim($item, ",");
                    if (!empty($item) && strlen($item) > 2) {
                        $item = removeSpecialCharacters($item);
                        if ($i == 0) {
                            $keywords = $item;
                        } else {
                            $keywords .= ", " . $item;
                        }
                    }
                    $i++;
                }
            }
            return $keywords;
        }
    }
}

//get aws base url
if (!function_exists('getAWSBaseURL')) {
    function getAWSBaseURL()
    {
        return 'https://s3.' . Globals::$generalSettings->aws_region . '.amazonaws.com/' . Globals::$generalSettings->aws_bucket . '/';
    }
}

//get post image base URL
if (!function_exists('getBaseURLByStorage')) {
    function getBaseURLByStorage($storage)
    {
        $baseURL = base_url() . '/';
        if ($storage == 'aws_s3') {
            $baseURL = getAWSBaseURL();
        }
        return $baseURL;
    }
}

//get csv value
if (!function_exists('getCSVInputValue')) {
    function getCSVInputValue($array, $key, $dataType = 'string')
    {
        if (!empty($array)) {
            if (!empty($array[$key])) {
                return $array[$key];
            }
        }
        if ($dataType == 'int') {
            return 0;
        }
        return '';
    }
}

//check if comment voted
if (!function_exists('isCommentVoted')) {
    function isCommentVoted($commentId)
    {
        if (!empty(helperGetCookie('comment_voted_' . $commentId))) {
            return true;
        }
        return false;
    }
}

//check comment owner
if (!function_exists('checkCommentOwner')) {
    function checkCommentOwner($comment)
    {
        if (!empty($comment)) {
            if (authCheck()) {
                if ($comment->user_id == user()->id) {
                    return true;
                }
            } else {
                if (!empty(helperGetCookie('added_comment_id_' . $comment->id))) {
                    return true;
                }
            }
        }
        return false;
    }
}

//get subcomments
if (!function_exists('getSubComments')) {
    function getSubComments($parentId)
    {
        $model = new \App\Models\CommonModel();
        return $model->getSubComments($parentId);
    }
}

//get media icon
if (!function_exists('getMediaIcon')) {
    function getMediaIcon($post, $class = '')
    {
        if (!empty($post)) {
            $cls = 'media-icon';
            if (!empty($class)) {
                $cls .= ' ' . $class;
            }
            if ($post->post_type == 'video') {
                echo '<span class="' . $cls . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ececec" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/></svg></span>';
            } elseif ($post->post_type == 'audio') {
                echo '<span class="' . $cls . '"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 160 160" fill="#ffffff"><path class="st0" d="M80,10c39,0,70,31,70,70s-31,70-70,70s-70-31-70-70S41,10,80,10z M80,0C36,0,0,36,0,80s36,80,80,80s80-36,80-80S124,0,80,0L80,0z"/><path d="M62.6,94.9c-2.5-1.7-5.8-2.8-9.4-2.8c-8,0-14.4,5.1-14.4,11.5c0,6.3,6.5,11.5,14.4,11.5s14.4-5.1,14.4-11.5v-35l36.7-5.8v26.5c-2.5-1.5-5.6-2.5-9-2.5c-8,0-14.4,5.1-14.4,11.5c0,6.3,6.5,11.5,14.4,11.5c8,0,14.4-5.1,14.4-11.5c0-0.4,0-0.9-0.1-1.3h0.1V40.2l-47.2,9.5V94.9z"/></svg></span>';
            }
        }
    }
}

//unserialize data
if (!function_exists('unserializeData')) {
    function unserializeData(mixed $serializedData): mixed
    {
        if (!is_string($serializedData)) {
            return null;
        }

        $trimmedData = trim($serializedData);
        if ($trimmedData === '') {
            return null;
        }

        // Safe unserialize operation
        try {
            $data = unserialize($trimmedData, ['allowed_classes' => false]);

            // Return null if unserialize fails (returns false)
            // However, handle the special case of 'b:0;' (which represents boolean false)
            if ($data === false && $trimmedData !== 'b:0;') {
                return null;
            }

            return $data;
        } catch (Throwable) {
            return null;
        }
    }
}

//parse serialized name array
if (!function_exists('parseSerializedNameArray')) {
    function parseSerializedNameArray($nameArray, $langId, $getMainName = true)
    {
        if (!empty($nameArray)) {
            $nameArray = unserializeData($nameArray);
            if (!empty($nameArray)) {
                foreach ($nameArray as $item) {
                    if ($item['lang_id'] == $langId && !empty($item['name'])) {
                        return esc($item['name']);
                    }
                }
            }
            //if not exist
            if ($getMainName == true) {
                if (!empty($nameArray)) {
                    foreach ($nameArray as $item) {
                        if ($item['lang_id'] == Globals::$defaultLang->id && !empty($item['name'])) {
                            return esc($item['name']);
                        }
                    }
                }
            }
        }
        return '';
    }
}

//get list style class by num
if (!function_exists('getCssListStyles')) {
    function getCssListStyles()
    {
        return ['decimal', 'decimal-leading-zero', 'circle', 'disc', 'square', 'armenian', 'cjk-ideographic', 'georgian', 'hebrew', 'hiragana', 'hiragana-iroha', 'katakana', 'katakana-iroha', 'lower-alpha',
            'lower-greek', 'lower-latin', 'lower-roman', 'upper-alpha', 'upper-greek', 'upper-latin', 'upper-roman', 'none'];
    }
}

//get post list style
if (!function_exists('getPostListStyle')) {
    function getPostListStyle($post, $index)
    {
        $data = new stdClass();
        $data->style = 'none';
        $data->status = 0;
        if (!empty($post->link_list_style)) {
            $array = unserializeData($post->link_list_style);
            if (!empty($array) && !empty($array[$index])) {
                if (!empty($array[$index]['style']) && in_array($array[$index]['style'], getCssListStyles())) {
                    $data->style = $array[$index]['style'];

                    if (!empty($array[$index]['status'])) {
                        $data->status = $array[$index]['status'];
                    }
                }
            }
        }

        return $data;
    }
}

//create form checkbox
if (!function_exists('formCheckbox')) {
    function formCheckbox($inputName, $val, $text, $checkedValue = null)
    {
        $id = 'c' . generateToken(true);
        $check = $checkedValue == $val ? ' checked' : '';
        return '<div class="custom-control custom-checkbox">' . PHP_EOL .
            '<input type="checkbox" name="' . $inputName . '" value="' . $val . '" id="' . $id . '" class="custom-control-input"' . $check . '>' . PHP_EOL .
            '<label for="' . $id . '" class="custom-control-label">' . $text . '</label>' . PHP_EOL .
            '</div>';
    }
}

//create form radio button
if (!function_exists('formRadio')) {
    function formRadio($inputName, $val1, $val2, $op1Text, $op2Text, $checkedValue = null, $colClass = 'col-md-6')
    {
        $id1 = 'r' . generateToken(true);
        $id2 = 'r' . generateToken(true);
        $op1Check = $checkedValue == $val1 ? ' checked' : '';
        $op2Check = $checkedValue != $val1 ? ' checked' : '';
        return
            '<div class="row">' . PHP_EOL .
            '    <div class="' . $colClass . ' col-sm-12">' . PHP_EOL .
            '        <div class="custom-control custom-radio">' . PHP_EOL .
            '            <input type="radio" name="' . $inputName . '" value="' . $val1 . '" id="' . $id1 . '" class="custom-control-input"' . $op1Check . '>' . PHP_EOL .
            '            <label for="' . $id1 . '" class="custom-control-label">' . $op1Text . '</label>' . PHP_EOL .
            '        </div>' . PHP_EOL .
            '    </div>' . PHP_EOL .
            '    <div class="' . $colClass . ' col-sm-12">' . PHP_EOL .
            '         <div class="custom-control custom-radio">' . PHP_EOL .
            '             <input type="radio" name="' . $inputName . '" value="' . $val2 . '" id="' . $id2 . '" class="custom-control-input"' . $op2Check . '>' . PHP_EOL .
            '             <label for="' . $id2 . '" class="custom-control-label">' . $op2Text . '</label>' . PHP_EOL .
            '        </div>' . PHP_EOL .
            '    </div>' . PHP_EOL .
            '</div>';
    }
}

//add https to the links
if (!function_exists('addHttpsToUrl')) {
    function addHttpsToUrl($url)
    {
        if (!empty($url)) {
            $url = trim($url);
            if (!empty($url)) {
                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                    $url = "https://" . $url;
                }
            }
            return $url;
        }
    }
}

//convert number short version
function numberFormatShort($n, $prec = 1)
{
    if ($n < 999) {
        $nFormat = number_format($n, $prec);
        $suffix = '';
    } else if ($n < 900000) {
        $nFormat = number_format($n / 1000, $prec);
        $suffix = trans("number_short_thousand");
    } else if ($n < 900000000) {
        $nFormat = number_format($n / 1000000, $prec);
        $suffix = trans("number_short_million");
    } else if ($n < 900000000000) {
        $nFormat = number_format($n / 1000000000, $prec);
        $suffix = trans("number_short_billion");
    } else {
        $nFormat = number_format($n / 1000000000000, $prec);
        $suffix = 't';
    }
    if ($prec > 0) {
        $dotzero = '.' . str_repeat('0', $prec);
        $nFormat = str_replace($dotzero, '', $nFormat);
    }
    return $nFormat . $suffix;
}

//get ai writer
if (!function_exists('aiWriter')) {
    function aiWriter()
    {
        $data = unserializeData(Globals::$generalSettings->ai_writer);
        $aiWriter = new \stdClass();
        $aiWriter->status = !empty($data['status']) ? true : false;
        $aiWriter->apiKey = !empty($data['api_key']) ? $data['api_key'] : '';
        return $aiWriter;
    }
}

//get role
if (!function_exists('getRole')) {
    function getRole($roleId)
    {
        $model = new \App\Models\AuthModel();
        return $model->getRole($roleId);
    }
}

//get roles
if (!function_exists('getRoles')) {
    function getRoles()
    {
        $model = new \App\Models\AuthModel();
        return $model->getRoles();
    }
}

//get role name
if (!function_exists('getRoleName')) {
    function getRoleName($role, $langId)
    {
        $name = '';
        $nameDefault = '';
        $nameFirst = '';
        if (!empty($role)) {
            $nameArray = unserializeData($role->role_name);
            if (!empty($nameArray) && countItems($nameArray) > 0) {
                $i = 0;
                foreach ($nameArray as $item) {
                    if (!empty($item['lang_id']) && !empty($item['name'])) {
                        if ($item['lang_id'] == $langId) {
                            $name = $item['name'];
                        }
                        if ($item['lang_id'] == Globals::$defaultLang->id) {
                            $nameDefault = $item['name'];
                        }
                        if ($i == 0) {
                            $nameFirst = $item['name'];
                        }
                    }
                    $i++;
                }
            }
        }
        if (empty($name)) {
            $name = $nameDefault;
        }
        if (empty($name)) {
            $name = $nameFirst;
        }
        return $name;
    }
}


//get permissions array
if (!function_exists('getPermissionsArray')) {
    function getPermissionsArray()
    {
        return ['admin_panel', 'add_post', 'manage_all_posts', 'navigation', 'pages', 'rss_feeds', 'categories', 'tags', 'widgets', 'polls', 'gallery', 'comments_contact', 'newsletter',
            'ad_spaces', 'users', 'roles_permissions', 'seo_tools', 'settings', 'reward_system', 'ai_writer'];
    }
}

//has permission
if (!function_exists('hasPermission')) {
    function hasPermission($permission, $user = null)
    {
        if (authCheck() && empty($user)) {
            $user = user();
        }
        if (!empty($user)) {
            if ($user->is_super_admin == 1) {
                return true;
            }
            if (!empty($user->permissions)) {
                $array = explode(',', $user->permissions);
                if (!empty($array) && countItems($array) > 0 && in_array($permission, $array)) {
                    return true;
                }
            }
        }
        return false;
    }
}

//check permission
if (!function_exists('checkPermission')) {
    function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            redirectToUrl(base_url());
        }
    }
}

//check post delete permission
if (!function_exists('checkPostOwnership')) {
    function checkPostOwnership($ownerId)
    {
        if (authCheck()) {
            if (hasPermission('manage_all_posts')) {
                return true;
            }
            if ($ownerId == user()->id) {
                return true;
            }
        }
        return false;
    }
}

//get pwa logo
if (!function_exists('getPwaLogo')) {
    function getPwaLogo($generalSettings, $size = 'lg')
    {
        $pwaLogo = $generalSettings->pwa_logo;
        if (!empty($pwaLogo)) {
            $pwaLogoArr = unserializeData($pwaLogo);
            if (!empty($pwaLogoArr) && countItems($pwaLogoArr)) {
                if (!empty($pwaLogoArr[$size])) {
                    return $pwaLogoArr[$size];
                }
            }

        }
        return '';
    }
}

//get logo size
if (!function_exists('getLogoSize')) {
    function getLogoSize($param)
    {
        $width = 178;
        $height = 56;
        $logoSize = Globals::$generalSettings->logo_size;
        if (!empty($logoSize)) {
            $array = explode('x', $logoSize);
            if (!empty($array[0])) {
                if (intval($array[0]) >= 10 && intval($array[0]) <= 300) {
                    $width = $array[0];
                }
            }
            if (!empty($array[1])) {
                if (intval($array[1]) >= 10 && intval($array[1]) <= 300) {
                    $height = $array[1];
                }
            }
        }
        if ($param == 'height') {
            return $height;
        } else {
            return $width;
        }
    }
}

//get newsletter image
if (!function_exists('getNewsletterImage')) {
    function getNewsletterImage()
    {
        if (!empty(Globals::$generalSettings->newsletter_image) && file_exists(FCPATH . Globals::$generalSettings->newsletter_image)) {
            return base_url(Globals::$generalSettings->newsletter_image);
        }
        return base_url("assets/img/newsletter.webp");
    }
}

//get payout method item
if (!function_exists('payoutMethod')) {
    function payoutMethod($key)
    {
        $methods = Globals::$generalSettings->payout_methods;
        if (!empty($methods)) {
            $array = unserializeData($methods);
            if (!empty($array) && isset($array[$key])) {
                return $array[$key];
            }
        }
        return '';
    }
}

//get user payout method
if (!function_exists('userPayoutMethod')) {
    function userPayoutMethod($user, $key)
    {
        if (!empty($user->payout_methods)) {
            $array = unserializeData($user->payout_methods);
            if (!empty($array) && isset($array[$key])) {
                return $array[$key];
            }
        }
        return '';
    }
}