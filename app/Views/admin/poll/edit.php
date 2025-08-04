<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('update_poll'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('polls'); ?>" class="btn btn-success btn-add-new"><i class="fa fa-bars"></i><?= trans('polls'); ?></a>
                </div>
            </div>
            <form action="<?= base_url('Admin/editPollPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <input type="hidden" name="id" value="<?= esc($poll->id); ?>">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control max-600">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $poll->lang_id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('question'); ?></label>
                        <textarea class="form-control text-area" name="question" placeholder="<?= trans('question'); ?>" required><?= esc($poll->question); ?></textarea>
                    </div>

                    <?php for ($i = 1; $i <= 10; $i++):
                        $varOption = 'option' . $i; ?>
                        <div class="form-group">
                            <label class="control-label"><?= trans('option_' . $i); ?></label>
                            <input type="text" class="form-control" name="option<?= $i; ?>" placeholder="<?= trans('option_' . $i); ?>" value="<?= esc($poll->$varOption); ?>" <?= $i <= 2 ? 'required' : ''; ?>>
                        </div>
                    <?php endfor; ?>

                    <div class="form-group">
                        <label><?= trans("vote_permission"); ?></label>
                        <?= formRadio('vote_permission', 'all', 'registered', trans("all_users_can_vote"), trans("registered_users_can_vote"), $poll->vote_permission, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('status', 1, 0, trans("active"), trans("inactive"), $poll->status, 'col-md-4'); ?>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>