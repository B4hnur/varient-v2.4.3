<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Globals;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [
        'text', 'cookie', 'security', 'xml'
    ];

    public $session;
    public $settingsModel;
    public $commonModel;
    public $postModel;
    public $generalSettings;
    public $settings;
    public $activeLanguages;
    public $activeTheme;
    public $activeLang;
    public $activeFonts;
    public $rtl;
    public $darkMode;
    public $widgets;
    public $menuLinks;
    public $categories;
    public $postsSelected;
    public $latestCategoryPosts;
    public $adSpaces;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // prevent iframe
        $this->response->setHeader('Content-Security-Policy', "frame-ancestors 'none';");

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->settingsModel = new SettingsModel();
        $this->commonModel = new CommonModel();
        $this->postModel = new PostModel();

        //general settings
        $this->generalSettings = Globals::$generalSettings;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            setActiveLangPostRequest();
        }

        //settings
        $this->settings = Globals::$settings;
        //active languages
        $this->activeLanguages = Globals::$languages;
        //active theme
        $this->activeTheme = Globals::$activeTheme;
        //active lang
        $this->activeLang = Globals::$activeLang;

        //maintenance mode
        if ($this->generalSettings->maintenance_mode_status == 1) {
            if (!isSuperAdmin()) {
                echo view('common/maintenance', ['generalSettings' => $this->generalSettings, 'baseSettings' => $this->settings]);
            }
        }
        //site fonts
        $this->activeFonts = $this->getFonts($this->settings);
        //widgets
        $this->widgets = $this->getWidgets($this->activeLang->id);
        //ad spaces
        $this->adSpaces = $this->getAdSpaces($this->activeLang->id);
        //menu links
        $this->menuLinks = $this->getMenuLinks($this->activeLang->id);
        //menu links
        $this->categories = $this->getCategories($this->activeLang->id);

        //rtl
        $this->rtl = false;
        if ($this->activeLang->text_direction == 'rtl') {
            $this->rtl = true;
        }
        $this->darkMode = Globals::$darkMode;

        $this->postsSelected = $this->postModel->getPostsSelected($this->activeLang->id);

        //latest categories posts
        $this->latestCategoryPosts = $this->postModel->getLatestCategoryPosts($this->activeLang->id);

        // check scheduled posts
        $this->postModel->checkScheduledPosts();

        //view variables
        $view = \Config\Services::renderer();
        $view->setData(['assetsPath' => 'assets/' . getThemePath(), 'activeTheme' => $this->activeTheme, 'activeLang' => $this->activeLang, 'generalSettings' => $this->generalSettings, 'baseSettings' => $this->settings, 'activeLanguages' => $this->activeLanguages, 'rtl' => $this->rtl,
            'darkMode' => $this->darkMode, 'activeFonts' => $this->activeFonts, 'baseMenuLinks' => $this->menuLinks, 'baseWidgets' => $this->widgets, 'baseCategories' => $this->categories, 'basePostsSelected' => $this->postsSelected, 'baseLatestCategoryPosts' => $this->latestCategoryPosts, 'adSpaces' => $this->adSpaces]);
    }

    //get fonts
    private function getFonts($settings)
    {
        return getOrSetStaticCache('fonts_' . $settings->id, function () use ($settings) {
            return $this->settingsModel->getSelectedFonts($settings);
        });
    }

    //get widgets
    private function getWidgets($langId)
    {
        return getOrSetStaticCache('widgets_lang_' . $langId, function () use ($langId) {
            return $this->settingsModel->getWidgetsByLang($langId);
        });
    }

    //get ad spaces
    private function getAdSpaces($langId)
    {
        return getOrSetStaticCache('ad_spaces_lang_' . $langId, function () use ($langId) {
            return $this->commonModel->getAdSpacesByLang($langId);
        });
    }

    //get menu links
    private function getMenuLinks($langId)
    {
        return getOrSetStaticCache('menu_links_lang_' . $langId, function () use ($langId) {
            $pageModel = new PageModel();
            return $pageModel->getMenuLinks($langId);
        });
    }

    //get categories
    private function getCategories($langId)
    {
        return getOrSetStaticCache('categories_lang' . $langId, function () use ($langId) {
            $model = new CategoryModel();
            return $model->getCategoriesByLang($langId);
        });
    }
}
