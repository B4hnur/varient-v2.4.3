<?php $editorLanguageOptions = \Config\App::$editorLanguageOptions; ?>
<div class="row">
    <div class="col-lg-5 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("update_language"); ?></h3>
            </div>
            <form action="<?= base_url('Language/editLanguagePost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $language->id; ?>">

                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("language_name"); ?>" value="<?= $language->name; ?>" maxlength="200" required>
                        <small>(Ex: English)</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("short_form"); ?> </label>
                        <input type="text" class="form-control" name="short_form" placeholder="<?= trans("short_form"); ?>" value="<?= $language->short_form; ?>" maxlength="200" required>
                        <small>(Ex: en)</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("language_code"); ?> </label>
                        <input type="text" class="form-control" name="language_code" placeholder="<?= trans("language_code"); ?>" value="<?= $language->language_code; ?>" maxlength="200" required>
                        <small>(Ex: en_us)</small>
                    </div>

                    <div class="form-group">
                        <label><?= trans('order_1'); ?></label>
                        <input type="number" class="form-control" name="language_order" placeholder="<?= trans('order'); ?>" value="<?= $language->language_order; ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans('text_editor_language'); ?></label>
                        <select name="text_editor_lang" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php foreach ($editorLanguageOptions as $item): ?>
                                <option value="<?= $item['short']; ?>" <?= $item['short'] == $language->text_editor_lang ? 'selected' : ''; ?>><?= $item['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("text_direction"); ?></label>
                        <?= formRadio('text_direction', 'ltr', 'rtl', trans("left_to_right"), trans("right_to_left"), $language->text_direction); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('status', 1, 0, trans("active"), trans("inactive"), $language->status); ?>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>