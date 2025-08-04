<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class PageModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('pages');
    }

    //input values
    public function inputValues()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'slug' => inputPost('slug'),
            'description' => inputPost('description'),
            'keywords' => inputPost('keywords'),
            'page_content' => inputPost('page_content'),
            'page_order' => inputPost('page_order'),
            'parent_id' => inputPost('parent_id'),
            'visibility' => inputPost('visibility'),
            'title_active' => inputPost('title_active'),
            'breadcrumb_active' => inputPost('breadcrumb_active'),
            'right_column_active' => inputPost('right_column_active'),
            'need_auth' => inputPost('need_auth'),
            'location' => inputPost('location'),
            'page_type' => "page",
        ];
    }

    //add page
    public function addPage()
    {
        $data = $this->inputValues();
        if (empty($data["slug"])) {
            $data["slug"] = strSlug($data["title"]);
            if (empty($data["slug"])) {
                $data["slug"] = "page-" . uniqid();
            }
        } else {
            $data["slug"] = removeSpecialCharacters($data["slug"]);
        }
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }
        if (empty($data['need_auth'])) {
            $data['need_auth'] = 0;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            updateSlug('pages', $lastId);
        }
        return true;
    }

    //update page
    public function editPage($id)
    {
        $page = $this->getPageById($id);
        if (!empty($page)) {
            $data = $this->inputValues();
            if (empty($data["slug"])) {
                $data["slug"] = strSlug($data["title"]);
                if (empty($data["slug"])) {
                    $data["slug"] = "page-" . uniqid();
                }
            } else {
                $data["slug"] = removeSpecialCharacters($data["slug"]);
            }
            if (empty($data['parent_id'])) {
                $data['parent_id'] = 0;
            }
            if (empty($data['need_auth'])) {
                $data['need_auth'] = 0;
            }
            if ($this->builder->where('id', $page->id)->update($data)) {
                updateSlug('pages', $page->id);
                return true;
            }
        }
        return false;
    }

    //get page by lang
    public function getPageByLang($slug, $langId)
    {
        return $this->builder->where('slug', cleanSlug($slug))->where('lang_id', clrNum($langId))->get()->getRow();
    }

    //get pages
    public function getPages()
    {
        return $this->builder->orderBy('page_order')->get()->getResult();
    }

    //get page by id
    public function getPageById($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get page by default name
    public function getPageByDefaultName($defaultName, $langId)
    {
        return $this->builder->where('page_default_name', cleanStr($defaultName))->where('lang_id', clrNum($langId))->get()->getRow();
    }

    //get subpages
    public function getSubpages($parentId)
    {
        return $this->builder->where('parent_id', clrNum($parentId))->get()->getResult();
    }

    //update slug
    public function updateSlug($table, $id)
    {
        $validTables = ['pages', 'categories', 'posts'];
        if (!in_array($table, $validTables)) {
            return false;
        }

        $id = clrNum($id);
        $row = $this->db->table($table)->where('id', $id)->get()->getRow();
        if (empty($row)) {
            return false;
        }

        $slug = $row->slug;
        if (empty($slug) || $slug == '-') {
            $slug = $id;
        }

        if ($table == 'posts' && $this->generalSettings->post_url_structure == 'id') {
            $slug = $id;
        }

        $orjSlug = $slug;
        if ($this->isSlugExists($orjSlug, $id)) {
            $slug = $orjSlug . '-' . $id;
        }
        if ($this->isSlugExists($slug, $id)) {
            $slug = $orjSlug . '-' . uniqid();
        }
        $this->db->table($table)->where('id', $id)->update(['slug' => $slug]);
    }

    //check if slug exists
    public function isSlugExists($slug, $id)
    {
        $sql = "SELECT id FROM pages WHERE slug = ? AND id != ? UNION ALL SELECT id FROM categories WHERE slug = ? AND id != ? UNION ALL SELECT id FROM posts WHERE slug = ? AND id != ?";
        $query = $this->db->query($sql, [$slug, $id, $slug, $id, $slug, $id]);
        return $query->getNumRows() > 0;
    }

    //delete page
    public function deletePage($id)
    {
        $page = $this->getPageById($id);
        if (!empty($page)) {
            return $this->builder->where('id', $page->id)->delete();
        }
        return false;
    }

    /*
    * --------------------------------------------------------------------
    * NAVIGATION
    * --------------------------------------------------------------------
    */

    //input values
    public function inputValuesNav()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'link' => inputPost('link'),
            'page_order' => inputPost('page_order'),
            'visibility' => inputPost('visibility'),
            'parent_id' => inputPost('parent_id'),
            'location' => "main",
            'page_type' => "link",
        ];
    }

    //add link
    public function addLink()
    {
        $data = $this->inputValuesNav();
        if (empty($data["slug"])) {
            $data["slug"] = strSlug($data["title"]);
        }
        if (empty($data['link'])) {
            $data['link'] = "#";
        }
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->builder->insert($data);
    }

    //update link
    public function editLink($id)
    {
        $link = $this->getPageById($id);
        if (!empty($link)) {
            $data = $this->inputValuesNav();
            if (empty($data["slug"])) {
                $data["slug"] = strSlug($data["title"]);
            }
            if (empty($data['parent_id'])) {
                $data['parent_id'] = 0;
            }
            return $this->builder->where('id', $link->id)->update($data);
        }
        return false;
    }

    //get menu links
    public function getMenuLinks($langId)
    {
        $sql = "SELECT * FROM (
        (SELECT categories.id AS item_id, categories.lang_id AS item_lang_id, categories.name AS item_name, categories.slug AS item_slug, categories.category_order AS item_order, 'main' 
        AS item_location, 'category' AS item_type, '#' AS item_link, categories.parent_id AS item_parent_id, categories.show_on_menu AS item_visibility FROM categories WHERE categories.category_status = 1 AND categories.lang_id = ?) 
        UNION
        (SELECT pages.id AS item_id, pages.lang_id AS item_lang_id, pages.title AS item_name, pages.slug AS item_slug, pages.page_order AS item_order, pages.location AS item_location, 'page' 
        AS item_type, pages.link AS item_link, pages.parent_id AS item_parent_id, pages.visibility AS item_visibility FROM pages WHERE pages.lang_id = ?)) AS menu_items ORDER BY item_order, item_id";
        return $this->db->query($sql, array($langId, $langId))->getResult();
    }

    //sort menu items
    public function sortMenuItems()
    {
        $jsonMenuItems = inputPost('json_menu_items');
        $menuItems = json_decode($jsonMenuItems);
        if (!empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                if (!empty($menuItem->item_type)) {
                    if ($menuItem->item_type == 'page') {
                        $page = $this->getPageById($menuItem->item_id);
                        if (!empty($page)) {
                            $data = [
                                'parent_id' => clrNum($menuItem->parent_id),
                                'page_order' => clrNum($menuItem->new_order)
                            ];
                            $this->builder->where('id', $page->id)->update($data);
                        }
                    } elseif ($menuItem->item_type == 'category') {
                        $categoryModel = new CategoryModel();
                        $category = $categoryModel->getCategory($menuItem->item_id);
                        if (!empty($category)) {
                            $data = [
                                'parent_id' => clrNum($menuItem->parent_id),
                                'category_order' => clrNum($menuItem->new_order)
                            ];
                            $this->db->table('categories')->where('id', $category->id)->update($data);
                        }
                    }
                }
            }
        }
    }

    //get parent link
    public function getParentLink($parentId, $type)
    {
        if ($type == 'page' || $type == 'link') {
            return $this->getPageById($parentId);
        }
        if ($type == "category") {
            $categoryModel = new CategoryModel();
            return $categoryModel->getCategory($parentId);
        }
    }

    //hide show home link
    public function hideShowHomeLink()
    {
        if ($this->generalSettings->show_home_link == 1) {
            $data = ['show_home_link' => 0];
        } else {
            $data = ['show_home_link' => 1];
        }
        $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //update menu limit
    public function updateMenuLimit()
    {
        $data = [
            'menu_limit' => inputPost('menu_limit'),
        ];
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }
}
