<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("update_page"); ?></h3>
            </div>
            <form action="<?= base_url('Admin/editPagePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($page->id); ?>">
                <input type="hidden" name="redirect_url" value="<?= esc(inputGet('redirect_url')); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= esc($page->title); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>" value="<?= esc($page->slug); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans("description"); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($page->description); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($page->keywords); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" onchange="getMenuLinksByLang(this.value);" style="max-width: 600px;">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $page->lang_id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($page->location == "main"): ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('parent_link'); ?></label>
                            <select id="parent_links" name="parent_id" class="form-control" style="max-width: 600px;">
                                <option value="0"><?= trans('none'); ?></option>
                                <?php if (!empty($menuLinks)):
                                    foreach ($menuLinks as $item):
                                        if ($item->item_type != "category" && $item->item_location == "main" && $item->item_parent_id == "0" && $item->item_id != $page->id):
                                            if ($item->item_id == $page->parent_id): ?>
                                                <option value="<?= $item->item_id; ?>" selected><?= $item->item_name; ?></option>
                                            <?php else: ?>
                                                <option value="<?= $item->item_id; ?>"><?= $item->item_name; ?></option>
                                            <?php endif;
                                        endif;
                                    endforeach;
                                endif; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="parent_id" value="<?= esc($page->parent_id); ?>">
                    <?php endif;
                    if ($page->location != "none"): ?>
                        <div class="form-group">
                            <label><?= trans('order'); ?></label>
                            <input type="number" class="form-control" name="page_order" placeholder="<?= trans('order'); ?>" value="<?= $page->page_order; ?>" min="1" max="3000" style="width: 300px;max-width: 100%;">
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="page_order" value="<?= $page->page_order; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label><?= trans("location"); ?></label>
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="top" id="rd_location_1" class="custom-control-input" <?= $page->location == 'top' ? 'checked' : ''; ?>>
                                    <label for="rd_location_1" class="custom-control-label"><?= trans("top_menu"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="main" id="rd_location_2" class="custom-control-input" <?= $page->location == 'main' ? 'checked' : ''; ?>>
                                    <label for="rd_location_2" class="custom-control-label"><?= trans("main_menu"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="footer" id="rd_location_3" class="custom-control-input" <?= $page->location == 'footer' ? 'checked' : ''; ?>>
                                    <label for="rd_location_3" class="custom-control-label"><?= trans("footer"); ?></label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="none" id="rd_location_4" class="custom-control-input" <?= $page->location == 'none' ? 'checked' : ''; ?>>
                                    <label for="rd_location_4" class="custom-control-label"><?= trans("dont_add_menu"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= trans("visibility"); ?></label>
                        <?= formRadio('visibility', 1, 0, trans("show"), trans("hide"), $page->visibility, 'col-md-2'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_only_registered"); ?></label>
                        <?= formRadio('need_auth', 1, 0, trans("yes"), trans("no"), $page->need_auth, 'col-md-2'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_title"); ?></label>
                        <?= formRadio('title_active', 1, 0, trans("yes"), trans("no"), $page->title_active, 'col-md-2'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_breadcrumb"); ?></label>
                        <?= formRadio('breadcrumb_active', 1, 0, trans("yes"), trans("no"), $page->breadcrumb_active, 'col-md-2'); ?>
                    </div>

                    <?php if ($page->page_default_name != "contact" && $page->page_default_name != "gallery"): ?>
                        <div class="form-group">
                            <label><?= trans("show_right_column"); ?></label>
                            <?= formRadio('right_column_active', 1, 0, trans("yes"), trans("no"), $page->right_column_active, 'col-md-2'); ?>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="right_column_active" value="<?= esc($page->right_column_active); ?>">
                    <?php endif; ?>

                    <?php if ($page->page_default_name != "contact" && $page->page_default_name != "gallery"): ?>
                        <div class="form-group">
                            <div id="main_editor">
                                <label><?= trans('content'); ?></label>
                                <div class="row">
                                    <div class="col-sm-12 editor-buttons">
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#file_manager_image" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                    </div>
                                </div>
                                <textarea class="tinyMCE form-control" name="page_content"><?= $page->page_content; ?></textarea>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="page_content" content="">
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => false, 'loadVideos' => false, 'loadAudios' => false]); ?>