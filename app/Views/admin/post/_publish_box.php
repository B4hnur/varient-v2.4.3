<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('publish'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <?php if (!empty($post)): ?>
            <?php if ($post->status == 0): ?>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="scheduled_post" value="1" id="cb_scheduled" class="custom-control-input" <?= $post->is_scheduled == 1 ? 'checked' : ''; ?>>
                        <label for="cb_scheduled" class="custom-control-label"><?= trans("scheduled_post"); ?></label>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="scheduled_post" value="<?= $post->is_scheduled; ?>">
            <?php endif; ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <label><?= trans('date_publish'); ?></label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type='text' class="form-control" name="date_published" placeholder="<?= trans("date_publish"); ?>" value="<?= $post->created_at; ?>">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php if ($post->status == 0): ?>
                    <button type="submit" name="publish" value="1" class="btn btn-warning pull-right m-l-10" onclick="allowSubmitForm = true;"><?= trans('publish'); ?></button>
                <?php endif; ?>
                <button type="submit" name="publish" value="0" class="btn btn-primary pull-right" onclick="allowSubmitForm = true;"><?= trans('save_changes'); ?></button>
            </div>
        <?php else: ?>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="scheduled_post" value="1" id="cb_scheduled" class="custom-control-input">
                    <label for="cb_scheduled" class="custom-control-label"><?= trans("scheduled_post"); ?></label>
                </div>
            </div>
            <div id="date_published_content" class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <label><?= trans('date_publish'); ?></label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type='text' class="form-control" name="date_published" id="input_date_published" placeholder="<?= trans("date_publish"); ?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="status" value="1" class="btn btn-primary pull-right" onclick="allowSubmitForm = true;"><?= trans('btn_submit'); ?></button>
                <button type="submit" name="status" value="0" class="btn btn-warning btn-draft pull-right" onclick="allowSubmitForm = true;"><?= trans('save_draft'); ?></button>
            </div>
        <?php endif; ?>
    </div>
</div>