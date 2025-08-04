<?php $editorLanguageOptions = \Config\App::$editorLanguageOptions; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-left">
                    <h3 class="box-title"><?= trans('languages'); ?></h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped dataTable" id="cs_datatable" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('language_name'); ?></th>
                                    <th><?= trans('default_language'); ?></th>
                                    <th><?= trans('translation'); ?>/<?= trans('export'); ?></th>
                                    <th><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($languages)):
                                    foreach ($languages as $item): ?>
                                        <tr>
                                            <td><?= esc($item->id); ?></td>
                                            <td>
                                                <?= esc($item->name); ?>&nbsp;&nbsp;
                                                <?php if ($item->status == 1): ?>
                                                    <label class="label label-success"><?= trans('active'); ?></label>
                                                <?php else: ?>
                                                    <label class="label label-danger"><?= trans('inactive'); ?></label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($generalSettings->site_lang == $item->id): ?>
                                                    <label class="label label-default lbl-lang-status"><?= trans('default'); ?></label>
                                                <?php else:
                                                    if ($item->status == 1):?>
                                                        <form action="<?= base_url('Language/setDefaultLanguagePost'); ?>" method="post" style="display: inline-block">
                                                            <?= csrf_field(); ?>
                                                            <input type="hidden" name="site_lang" value="<?= $item->id; ?>">
                                                            <button type="submit" class="btn btn-sm btn-success float-right">
                                                                <i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?= trans('set_as_default'); ?>
                                                            </button>
                                                        </form>
                                                    <?php endif;
                                                endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= adminUrl('edit-translations/' . $item->id . '?show=50'); ?>" class="btn btn-sm btn-info float-right m-b-5">
                                                    <i class="fa fa-exchange"></i>&nbsp;&nbsp;<?= trans('edit_translations'); ?>
                                                </a>&nbsp;&nbsp;
                                                <form action="<?= base_url('Language/exportLanguagePost'); ?>" method="post" style="display: inline-block">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="lang_id" value="<?= $item->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning float-right m-b-5">
                                                        <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp;&nbsp;<?= trans('export'); ?>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="td-select-option">
                                                <div class="dropdown">
                                                    <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu options-dropdown">
                                                        <li>
                                                            <a href="<?= adminUrl('edit-language/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)" onclick="deleteItem('Language/deleteLanguagePost','<?= $item->id; ?>','<?= clrQuotes(trans("confirm_language")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("add_language"); ?></h3>
            </div>
            <form action="<?= base_url('Language/addLanguagePost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language_name"); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="<?= trans("language_name"); ?>" value="<?= old('name'); ?>" maxlength="200" required>
                        <small>(Ex: English)</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("short_form"); ?> </label>
                        <input type="text" class="form-control" name="short_form" placeholder="<?= trans("short_form"); ?>" value="<?= old('short_form'); ?>" maxlength="200" required>
                        <small>(Ex: en)</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("language_code"); ?> </label>
                        <input type="text" class="form-control" name="language_code" placeholder="<?= trans("language_code"); ?>" value="<?= old('language_code'); ?>" maxlength="200" required>
                        <small>(Ex: en_us)</small>
                    </div>

                    <div class="form-group">
                        <label><?= trans('order_1'); ?></label>
                        <input type="number" class="form-control" name="language_order" placeholder="<?= trans('order'); ?>" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans('text_editor_language'); ?></label>
                        <select name="text_editor_lang" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php foreach ($editorLanguageOptions as $item): ?>
                                <option value="<?= $item['short']; ?>"><?= $item['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans("text_direction"); ?></label>
                        <?= formRadio('text_direction', 'ltr', 'rtl', trans("left_to_right"), trans("right_to_left"), 'ltr'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('status', 1, 0, trans("active"), trans("inactive"), 1); ?>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_language'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-sm-12">
        <div class="box" style="max-width: 500px;">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("import_language"); ?></h3>
            </div>
            <form action="<?= base_url('Language/importLanguagePost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('json_language_file'); ?></label>
                        <div class="display-block">
                            <a class='btn btn-default btn-sm btn-file-upload'>
                                <i class="fa fa-file text-muted"></i>&nbsp;&nbsp;<?= trans('select_file'); ?>
                                <input type="file" name="file" size="40" accept=".json" required onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));">
                            </a>
                            <br>
                            <span class='label label-default label-file-upload' id="upload-file-info"></span>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('import_language'); ?></button>
                </div>
            </form>
        </div>
        <div class="alert alert-info alert-large" style="width: auto !important;display: inline-block;max-width: 500px;">
            <?= trans("languages"); ?>: <a href="https://codingest.net/languages" target="_blank" style="color: #0c5460;font-weight: bold">https://codingest.net/languages</a>
        </div>
    </div>
</div>
