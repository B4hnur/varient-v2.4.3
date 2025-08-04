<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("update_category"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('categories'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans("categories"); ?></a>
                </div>
            </div>
            <form action="<?= base_url('Category/editCategoryPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($category->id); ?>">
                <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" onchange="getParentCategoriesByLang(this.value, false);">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $category->lang_id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!empty($category->parent_id)): ?>
                        <div class="form-group">
                            <label><?= trans('parent_category'); ?></label>
                            <select id="categories" class="form-control" name="parent_id" required>
                                <?php if (!empty($parentCategories)):
                                    foreach ($parentCategories as $item): ?>
                                        <option value="<?= $item->id; ?>" <?= $category->parent_id == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label><?= trans("category_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("category_name"); ?>" value="<?= esc($category->name); ?>" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>" value="<?= esc($category->slug); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans('description'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($category->description); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($category->keywords); ?>">
                    </div>
                    <?php if ($category->parent_id == 0): ?>
                        <div class="form-group">
                            <label><?= trans('color'); ?></label>
                            <div class="input-group my-colorpicker">
                                <input type="text" class="form-control" name="color" maxlength="200" value="<?= esc($category->color); ?>" placeholder="<?= trans('color'); ?>">
                                <div class="input-group-addon"><i></i></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label><?= trans('order_1'); ?></label>
                        <input type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="<?= esc($category->category_order); ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('category_status', 1, 0, trans("enable"), trans("disable"), $category->category_status, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_on_menu"); ?></label>
                        <?= formRadio('show_on_menu', 1, 0, trans("yes"), trans("no"), $category->show_on_menu, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_on_homepage"); ?></label>
                        <?= formRadio('show_on_homepage', 1, 0, trans("yes"), trans("no"), $category->show_on_homepage, 'col-md-4'); ?>
                    </div>

                    <?php if ($category->parent_id == 0): ?>
                        <div class="form-group">
                            <?php if ($activeTheme->theme == 'classic'): ?>
                                <label><?= trans('category_block_style'); ?></label>
                                <div class="row m-b-15 m-t-15">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <div class="category-block-box">
                                            <div class="col-sm-12 text-center m-b-15">
                                                <div class="custom-control custom-radio" style="justify-content: center">
                                                    <input type="radio" name="block_type" value="block-<?= $i; ?>" id="block_type_<?= $i; ?>" class="custom-control-input" <?= $category->block_type == 'block-' . $i ? 'checked' : ''; ?>>
                                                    <label for="block_type_<?= $i; ?>" class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </div>
                                            <img src="<?= base_url('assets/admin/img/block-' . $i . '.png'); ?>" alt="" class="img-responsive cat-block-img">
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <label><?= trans('category_block_style'); ?></label>
                                    <div class="row m-b-15 m-t-15">
                                        <?php for ($i = 1; $i <= 6; $i++): ?>
                                            <div class="category-block-box">
                                                <div class="col-sm-12 text-center m-b-15">
                                                    <div class="custom-control custom-radio" style="justify-content: center">
                                                        <input type="radio" name="block_type" value="block-<?= $i; ?>" id="block_type_<?= $i; ?>" class="custom-control-input" <?= $category->block_type == 'block-' . $i ? 'checked' : ''; ?>>
                                                        <label for="block_type_<?= $i; ?>" class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </div>
                                                <?php if ($i == 5): ?>
                                                    <p style="margin-bottom: 2px; text-align: center; font-weight: 700; font-size: 12px;"><?= trans("slider"); ?></p>
                                                <?php endif; ?>
                                                <img src="<?= base_url('assets/admin/img/magazine/block-' . $i . '.jpg'); ?>" alt="" class="img-responsive cat-block-img">
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>