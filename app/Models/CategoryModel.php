<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('categories');
    }

    //input values
    public function inputValues()
    {
        $data = [
            'lang_id' => inputPost('lang_id'),
            'name' => inputPost('name'),
            'slug' => inputPost('slug'),
            'parent_id' => inputPost('parent_id'),
            'description' => inputPost('description'),
            'keywords' => inputPost('keywords'),
            'color' => inputPost('color'),
            'category_order' => inputPost('category_order'),
            'category_status' => !empty(inputPost('category_status')) ? 1 : 0,
            'show_on_homepage' => !empty(inputPost('show_on_homepage')) ? 1 : 0,
            'show_on_menu' => !empty(inputPost('show_on_menu')) ? 1 : 0,
            'block_type' => inputPost('block_type')
        ];
        if (empty($data['color'])) {
            $data['color'] = '#2d65fe';
        }
        return $data;
    }

    //add category
    public function addCategory($type)
    {
        $data = $this->inputValues();
        if (empty($data["slug"])) {
            $data["slug"] = strSlug($data["name"]);
        } else {
            $data["slug"] = removeSpecialCharacters($data["slug"], true);
            if (!empty($data['slug'])) {
                $data['slug'] = str_replace(' ', '-', $data['slug']);
            }
        }
        if (!empty($data['parent_id'])) {
            $parent = $this->getCategory($data['parent_id']);
            if (!empty($parent)) {
                $data['color'] = $parent->color;
            }
            $data['block_type'] = '';
        } else {
            $data['parent_id'] = 0;
        }
        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            updateSlug('categories', $lastId);
            return true;
        }
        return false;
    }

    //edit category
    public function editCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            $data = $this->inputValues();
            if (empty($data["slug"])) {
                $data["slug"] = strSlug($data["name"]);
            } else {
                $data["slug"] = removeSpecialCharacters($data["slug"], true);
                if (!empty($data['slug'])) {
                    $data['slug'] = str_replace(' ', '-', $data['slug']);
                }
            }
            if (!empty($data['parent_id'])) {
                $parent = $this->getCategory($data['parent_id']);
                if (!empty($parent)) {
                    $data['color'] = $parent->color;
                }
                $data['block_type'] = '';
            } else {
                $data['parent_id'] = 0;
                $this->updateSubCategoriesColor($id, $data['color']);
                //update subcategories lang_id
                $this->builder->where('parent_id', $category->id)->update(['lang_id' => $data['lang_id']]);
            }
            if ($this->builder->where('id', $category->id)->update($data)) {
                updateSlug('categories', $category->id);
                return true;
            }
        }
        return false;
    }

    //update subcategory color
    public function updateSubCategoriesColor($parentId, $color)
    {
        $categories = $this->getSubCategoriesByParentId($parentId);
        if (!empty($categories)) {
            foreach ($categories as $item) {
                $this->builder->where('id', $item->id)->update(['color' => $color]);
            }
        }
    }

    //build query
    public function buildQuery()
    {
        $this->builder->select('categories.*, (SELECT slug FROM categories AS tbl_categories WHERE tbl_categories.id = categories.parent_id) as parent_slug');
    }

    //get category
    public function getCategory($id)
    {
        $this->buildQuery();
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get category by slug
    public function getCategoryBySlug($slug)
    {
        $this->buildQuery();
        return $this->builder->where('categories.slug', cleanSlug($slug))->where('categories.lang_id', clrNum($this->activeLang->id))->where('category_status', 1)->orderBy('category_order')->get()->getRow();
    }

    //get categories
    public function getCategories()
    {
        $this->buildQuery();
        return $this->builder->orderBy('category_order')->get()->getResult();
    }

    //get categories by lang
    public function getCategoriesByLang($langId)
    {
        $this->buildQuery();
        return $this->builder->where('categories.lang_id', clrNum($langId))->where('category_status', 1)->orderBy('category_order')->get()->getResult();
    }

    //get parent categories
    public function getParentCategories()
    {
        return $this->builder->where('parent_id', 0)->where('category_status', 1)->orderBy('created_at DESC')->get()->getResult();
    }

    //get parent categories by lang
    public function getParentCategoriesByLang($langId)
    {
        return $this->builder->where('parent_id', 0)->where('lang_id', clrNum($langId))->where('category_status', 1)->orderBy('name')->get()->getResult();
    }

    //get subcategories
    public function getSubCategories()
    {
        return $this->builder->where('parent_id !=', 0)->where('category_status', 1)->get()->getResult();
    }

    //get subcategories by parent id
    public function getSubCategoriesByParentId($parentId)
    {
        return $this->builder->where('parent_id', clrNum($parentId))->where('category_status', 1)->orderBy('name')->get()->getResult();
    }

    //get categories count
    public function getCategoriesCount()
    {
        $this->filterCategories();
        return $this->builder->countAllResults();
    }

    //get paginated categories
    public function getCategoriesPaginated($perPage, $offset)
    {
        $this->filterCategories();
        return $this->builder->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter categories
    public function filterCategories()
    {
        $q = inputGet('q');
        $langId = clrNum(inputGet('lang_id'));
        $parentId = clrNum(inputGet('category'));
        if (!empty($q)) {
            $this->builder->like('name', cleanStr($q));
        }
        if (!empty($langId)) {
            $this->builder->where('lang_id', clrNum($langId));
        }
        if (!empty($parentId)) {
            $this->builder->where('parent_id', clrNum($parentId));
        }
    }

    //delete category
    public function deleteCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            return $this->builder->where('id', $category->id)->delete();
        }
        return false;
    }
}