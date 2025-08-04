<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\SettingsModel;
use App\Models\TagModel;

class CategoryController extends BaseAdminController
{
    protected $categoryModel;
    protected $tagModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->categoryModel = new CategoryModel();
        $this->tagModel = new TagModel();
    }

    /**
     * Categories
     */
    public function addCategory()
    {
        checkPermission('categories');
        $data['title'] = trans("add_category");
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);
        $data['panelSettings'] = panelSettings();
        $data['type'] = inputGet('type');
        if (empty($data['type']) || $data['type'] != 'sub') {
            $data['type'] = 'parent';
        }
        $settingsModel = new SettingsModel();
        $data['widgets'] = $settingsModel->getWidgets();

        echo view('admin/includes/_header', $data);
        echo view('admin/category/add_category', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Category Post
     */
    public function addCategoryPost()
    {
        checkPermission('categories');
        $type = inputPost('type');
        if (empty($type) || $type != 'sub') {
            $type = 'parent';
        }
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');
        $data['panelSettings'] = panelSettings();
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('add-category?type=' . $type))->withInput();
        } else {
            if ($this->categoryModel->addCategory($type)) {
                setSuccessMessage("msg_added");
                resetCacheDataOnChange();
            } else {
                setErrorMessage("msg_error");
            }
        }
        return redirect()->to(adminUrl('add-category?type=' . $type));
    }

    /**
     * Categories
     */
    public function categories()
    {
        checkPermission('categories');
        $data['title'] = trans("categories");
        $data['panelSettings'] = panelSettings();
        $numRows = $this->categoryModel->getCategoriesCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['categories'] = $this->categoryModel->getCategoriesPaginated($this->perPage, $data['pager']->offset);

        $langId = clrNum(inputGet('lang_id'));
        if (!empty($langId)) {
            $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($langId);
        } else {
            $data['parentCategories'] = $this->categoryModel->getParentCategories();
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/category/categories', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category
     */
    public function editCategory($id)
    {
        checkPermission('categories');
        $data['title'] = trans("update_category");
        $data['panelSettings'] = panelSettings();
        $data['category'] = $this->categoryModel->getCategory($id);
        if (empty($data['category'])) {
            return redirect()->to(adminUrl('categories'));
        }
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($data['category']->lang_id);
        $settingsModel = new SettingsModel();
        $data['widgets'] = $settingsModel->getWidgets();

        echo view('admin/includes/_header', $data);
        echo view('admin/category/edit_category', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category Post
     */
    public function editCategoryPost()
    {
        checkPermission('categories');
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            redirectToBackURL();
        } else {
            $id = inputPost('id');
            if ($this->categoryModel->editCategory($id)) {
                setSuccessMessage("msg_updated");
                resetCacheDataOnChange();
                redirectToBackURL();
            }
        }
        setErrorMessage("msg_error");
        redirectToBackURL();
    }

    /**
     * Subcategories
     */
    public function subCategories()
    {
        checkPermission('categories');
        $data['title'] = trans("subcategories");
        $data['categories'] = $this->categoryModel->getSubCategories();
        $data['panelSettings'] = panelSettings();
        $data['parentCategories'] = $this->categoryModel->getParentCategoriesByLang($this->activeLang->id);
        $data['langSearchColumn'] = 2;

        echo view('admin/includes/_header', $data);
        echo view('admin/category/subcategories', $data);
        echo view('admin/includes/_footer');
    }

    //get parent categories by language
    public function getParentCategoriesByLang()
    {
        $langId = inputPost('lang_id');
        if (!empty($langId)) {
            $categories = $this->categoryModel->getParentCategoriesByLang($langId);
        } else {
            $categories = $this->categoryModel->getParentCategories();
        }
        if (!empty($categories)) {
            foreach ($categories as $item) {
                echo '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }
    }

    //get subcategories
    public function getSubCategories()
    {
        $parentId = inputPost('parent_id');
        if (!empty($parentId)) {
            $subCategories = $this->categoryModel->getSubCategoriesByParentId($parentId);
            foreach ($subCategories as $item) {
                echo '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }
    }

    /**
     * Delete Category Post
     */
    public function deleteCategoryPost()
    {
        if (!hasPermission('categories')) {
            exit();
        }
        $id = inputPost('id');
        if (!empty($this->categoryModel->getSubCategoriesByParentId($id))) {
            setErrorMessage("msg_delete_subcategories");
            exit();
        }
        $postModel = new PostModel();
        $categories = $this->categoryModel->getCategories();
        $categoryTree = getCategoryTree($id, $categories);
        if (!empty($postModel->getPostCountByCategory($id, $categoryTree))) {
            setErrorMessage("msg_delete_posts");
            exit();
        }
        if ($this->categoryModel->deleteCategory($id)) {
            setSuccessMessage("msg_deleted");
            resetCacheDataOnChange();
        } else {
            setErrorMessage("msg_error");
        }
    }

    /*
     * --------------------------------------------------------------------
     * Tags
     * --------------------------------------------------------------------
     */

    /**
     * Tags
     */
    public function tags()
    {
        checkPermission('tags');
        $data['title'] = trans("tags");
        $data['panelSettings'] = panelSettings();
        $numRows = $this->tagModel->getTagsCount();
        $data['pager'] = paginate($this->perPage, $numRows);
        $data['tags'] = $this->tagModel->getTagsPaginated($this->perPage, $data['pager']->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/tag/tags', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Tag Post
     */
    public function addTagPost()
    {
        checkPermission('tags');
        $tag = inputPost('tag');
        $langId = inputPost('lang_id');

        if ($this->tagModel->addTag($tag, $langId)) {
            setSuccessMessage("msg_added");
        } else {
            setErrorMessage("msg_tag_exists");
        }
        redirectToBackURL();
    }

    /**
     * Edit Tag Post
     */
    public function editTagPost()
    {
        checkPermission('tags');
        $id = inputPost('id');
        $tag = inputPost('tag');
        $langId = inputPost('lang_id');

        if ($this->tagModel->editTag($id, $tag, $langId)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }

    /**
     * Delete Tag Post
     */
    public function deleteTagPost()
    {
        checkPermission('tags');
        $id = inputPost('id');
        if ($this->tagModel->deleteTag($id)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        redirectToBackURL();
    }
}
