<div class="row">
    <div class="col-sm-12 col-lg-10">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('update_rss_feed'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('feeds'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-bars"></i>
                        <?= trans('rss_feeds'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Rss/editFeedPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <input type="hidden" name="id" value="<?= esc($feed->id); ?>">

                    <div class="form-group">
                        <label><?= trans("feed_name"); ?></label>
                        <input type="text" class="form-control" name="feed_name" placeholder="<?= trans("feed_name"); ?>" value="<?= esc($feed->feed_name); ?>" maxlength="400" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans("feed_url"); ?></label>
                        <input type="text" class="form-control" name="feed_url" placeholder="<?= trans("feed_url"); ?>" value="<?= esc($feed->feed_url); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans("number_of_posts_import"); ?></label>
                        <input type="number" class="form-control max-500" name="post_limit" placeholder="<?= trans("number_of_posts_import"); ?>" value="<?= esc($feed->post_limit); ?>" min="1" max="500">
                    </div>

                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control max-500" onchange="getParentCategoriesByLang(this.value);">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $feed->lang_id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('category'); ?></label>
                        <select id="categories" name="category_id" class="form-control max-500" onchange="getSubCategories(this.value);" required>
                            <option value=""><?= trans('select_category'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= $item->id == $parentCategoryId ? 'selected' : ''; ?> ><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('subcategory'); ?></label>
                        <select id="subcategories" name="subcategory_id" class="form-control max-500">
                            <option value="0"><?= trans('select_category'); ?></option>
                            <?php if (!empty($subCategories)):
                                foreach ($subCategories as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= $item->id == $subCategoryId ? 'selected' : ''; ?> ><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("images"); ?></label>
                        <?= formRadio('image_saving_method', 'url', 'download', trans("show_images_from_original_source"), trans("download_images_my_server"), $feed->image_saving_method, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("auto_update"); ?></label>
                        <?= formRadio('auto_update', 1, 0, trans("yes"), trans("no"), $feed->auto_update, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("generate_keywords_from_title"); ?></label>
                        <?= formRadio('generate_keywords_from_title', 1, 0, trans("yes"), trans("no"), $feed->generate_keywords_from_title, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_read_more_button"); ?></label>
                        <?= formRadio('read_more_button', 1, 0, trans("yes"), trans("no"), $feed->read_more_button, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("add_posts_as_draft"); ?></label>
                        <?= formRadio('add_posts_as_draft', 1, 0, trans("yes"), trans("no"), $feed->add_posts_as_draft, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("read_more_button_text"); ?></label>
                        <input type="text" class="form-control max-500" name="read_more_button_text" placeholder="<?= trans("read_more_button_text"); ?>" value="<?= esc($feed->read_more_button_text); ?>">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>