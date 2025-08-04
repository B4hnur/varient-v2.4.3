<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_category"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('categories'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans("categories"); ?></a>
                </div>
            </div>
            <form action="<?= base_url('Category/addCategoryPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" onchange="getParentCategoriesByLang(this.value);">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans('parent_category'); ?></label>
                        <select id="categories" class="form-control" name="parent_id" onchange="hideParentCategoryInputs(this.value);">
                            <option value=""><?= trans('none'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $item): ?>
                                    <option value="<?= $item->id; ?>"><?= $item->name; ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("category_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("category_name"); ?>" value="<?= old('name'); ?>" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>" value="<?= old('slug'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="description" placeholder="<?= trans('description'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('description'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('keywords'); ?>">
                    </div>

                    <div class="form-group input-parent">
                        <label><?= trans('color'); ?></label>
                        <div class="input-group my-colorpicker">
                            <input type="text" class="form-control" name="color" maxlength="200" placeholder="<?= trans('color'); ?>">
                            <div class="input-group-addon"><i></i></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= trans('order_1'); ?></label>
                        <input type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('category_status', 1, 0, trans("enable"), trans("disable"), '1', 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_on_menu"); ?></label>
                        <?= formRadio('show_on_menu', 1, 0, trans("yes"), trans("no"), '1', 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_on_homepage"); ?></label>
                        <?= formRadio('show_on_homepage', 1, 0, trans("yes"), trans("no"), '1', 'col-md-4'); ?>
                    </div>

                    <?php if ($activeTheme->theme == 'classic'): ?>
                        <div class="form-group input-parent">
                            <label><?= trans('category_block_style'); ?></label>
                            <div class="row m-b-15 m-t-15">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="category-block-box">
                                        <div class="col-sm-12 text-center m-b-15">
                                            <div class="custom-control custom-radio" style="justify-content: center">
                                                <input type="radio" name="block_type" value="block-<?= $i; ?>" id="block_type_<?= $i; ?>" class="custom-control-input" <?= $i == 1 ? 'checked' : ''; ?>>
                                                <label for="block_type_<?= $i; ?>" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </div>
                                        <img src="<?= base_url('assets/admin/img/block-' . $i . '.png'); ?>" alt="" class="img-responsive cat-block-img">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-group input-parent">
                            <label><?= trans('category_block_style'); ?></label>
                            <div class="row m-b-15 m-t-15">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <div class="category-block-box">
                                        <div class="col-sm-12 text-center m-b-15">
                                            <div class="custom-control custom-radio" style="justify-content: center">
                                                <input type="radio" name="block_type" value="block-<?= $i; ?>" id="block_type_<?= $i; ?>" class="custom-control-input" <?= $i == 1 ? 'checked' : ''; ?>>
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
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_category'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function hideParentCategoryInputs(val) {
        if (val) {
            $('.input-parent').hide();
        } else {
            $('.input-parent').show();
        }
    }
</script>
