<?php

namespace Config;

use App\Models\PostModel;
use CodeIgniter\Config\BaseConfig;
use \App\Models\AuthModel;

class Globals extends BaseConfig
{
    private static $db = null;
    public static $themes = array();
    public static $activeTheme = null;
    public static $generalSettings = array();
    public static $settings = array();
    public static $customRoutes = array();
    public static $languages = array();
    public static $defaultLang = array();
    public static $languageTranslations = array();
    public static $activeLang = array();
    public static $langBaseUrl = "";
    public static $authCheck = false;
    public static $authUser = null;
    public static $darkMode = 0;

    public static function setGlobals()
    {
        self::$db = \Config\Database::connect();
        $session = \Config\Services::session();
        //set general settings
        self::$generalSettings = self::$db->table('general_settings')->where('id', 1)->get()->getRow();
        //set themes
        self::$themes = self::getThemes();
        //set routes
        self::$customRoutes = self::getCustomRoutes(self::$generalSettings->routes);
        //set languages
        self::$languages = self::getLanguages();
        //set timezone
        if (!empty(self::$generalSettings->timezone)) {
            date_default_timezone_set(self::$generalSettings->timezone);
        }
        //set active language
        self::setDefaultLanguage();
        if (empty(self::$defaultLang)) {
            self::$defaultLang = self::$db->table('languages')->get()->getFirstRow();
        }
        $langSegment = getSegmentValue(1);
        $langId = null;
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if ($langSegment == $lang->short_form) {
                    $langId = $lang->id;
                    break;
                }
            }
        }

        if (empty($langId)) {
            $langId = self::$defaultLang->id;
        }
        self::setActiveLanguage($langId);
        if (empty(self::$activeLang)) {
            self::$activeLang = self::$defaultLang;
        }
        $session->set('activeLangId', self::$activeLang->id);
        //set language base URL
        self::$langBaseUrl = base_url(self::$activeLang->short_form);
        if (self::$activeLang->id == self::$defaultLang->id) {
            self::$langBaseUrl = base_url();
        }
        //set settings
        self::$settings = self::getSettings(self::$activeLang->id);
        //authentication
        if (!empty($session->get('vr_ses_id')) && !empty($session->get('vr_ses_key'))) {
            $user = self::$db->table('users')->select('users.*, roles.role_name AS role_name_data, permissions, is_super_admin')
                ->join('roles', 'users.role_id = roles.id', 'left')->where('users.id', clrNum($session->get('vr_ses_id')))->get()->getRow();
            if (!empty($user)) {
                $hashedKey = getUserSessionkey($user);
                if ($session->get('vr_ses_key') == $hashedKey && $user->status == 1) {
                    self::$authCheck = true;
                    self::$authUser = $user;
                }
            }
        }

        //set active theme
        if (!empty(self::$themes)) {
            foreach (self::$themes as $item) {
                if ($item->is_active == 1) {
                    self::$activeTheme = $item;
                }
            }
            if (empty(self::$activeTheme)) {
                if (!empty(self::$themes[0])) {
                    self::$activeTheme = self::$themes[0];
                }
            }
        }

        //set dark mode
        $mode = self::$generalSettings->theme_mode;
        if (!empty(helperGetCookie('theme_mode'))) {
            $mode = helperGetCookie('theme_mode');
        }
        if ($mode == 'dark') {
            self::$darkMode = 1;
        }
    }

    //get routes
    private static function getCustomRoutes($routes)
    {
        $routesArray = \Config\App::$routes;
        $customRoutes = [];
        if (!empty($routes)) {
            $customRoutes = unserializeData($routes);
        }

        if (!empty($customRoutes) && count($customRoutes) > 0) {
            foreach ($routesArray as $key => $value) {
                if (!empty($customRoutes[$key])) {
                    $routesArray[$key] = $customRoutes[$key];
                }
            }
        }
        return (object)$routesArray;
    }

    //set active language
    public static function setActiveLanguage($langId)
    {
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if ($langId == $lang->id) {
                    self::$activeLang = $lang;
                    //set language translations
                    self::$languageTranslations = self::getLanguageTranslations(self::$activeLang->id);
                    $arrayTranslations = array();
                    if (!empty(self::$languageTranslations)) {
                        foreach (self::$languageTranslations as $item) {
                            $arrayTranslations[$item->label] = $item->translation;
                        }
                    }
                    self::$languageTranslations = $arrayTranslations;
                    self::updateLangBaseURL($lang->short_form);
                    break;
                }
            }
        }
    }

    //update lang base URL
    public static function updateLangBaseURL($shortForm)
    {
        if (self::$defaultLang->short_form == $shortForm) {
            self::$langBaseUrl = base_url();
        } else {
            self::$langBaseUrl = base_url($shortForm);
        }
    }

    //get themes
    private static function getThemes()
    {
        return getOrSetStaticCache('themes', function () {
            return self::$db->table('themes')->get()->getResult();
        });
    }

    //get languages
    private static function getLanguages()
    {
        return getOrSetStaticCache('languages', function () {
            return self::$db->table('languages')->where('status', 1)->get()->getResult();
        });
    }

    //get language translations
    private static function getLanguageTranslations($langId)
    {
        return getOrSetStaticCache('language_translations_lang_' . $langId, function () use ($langId) {
            return self::$db->table('language_translations')->where('lang_id', $langId)->get()->getResult();
        });
    }

    //get settings
    private static function getSettings($langId)
    {
        return getOrSetStaticCache('settings_lang_' . $langId, function () use ($langId) {
            return self::$db->table('settings')->where('lang_id', $langId)->get()->getRow();
        });
    }

    //set default language
    private static function setDefaultLanguage()
    {
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if (self::$generalSettings->site_lang == $lang->id) {
                    self::$defaultLang = $lang;
                }
            }
        }
    }
}

Globals::setGlobals();
