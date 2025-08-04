<div class="row">
    <div class="col-sm-10">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_role"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('roles-permissions'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans("roles"); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Admin/addRolePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= esc(currentFullURL()); ?>">
                <div class="box-body">
                    <?php foreach ($activeLanguages as $language): ?>
                        <div class="form-group">
                            <label><?= trans("role_name"); ?> (<?= esc($language->name); ?>)</label>
                            <input type="text" class="form-control" name="role_name_<?= $language->id; ?>" placeholder="<?= trans("role_name"); ?>" maxlength="255" required>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <label class="m-b-15"><?= trans("permissions"); ?></label>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <?php $permissions = getPermissionsArray();
                                if (!empty($permissions)):
                                    $i = 0;
                                    foreach ($permissions as $permission):
                                        if ($permission != "admin_panel"):
                                            if ($i <= 11):?>
                                                <div class="m-b-15">
                                                    <?= formCheckbox('permissions[]', $permission, trans($permission)); ?>
                                                </div>
                                            <?php endif;
                                        endif;
                                        $i++;
                                    endforeach;
                                endif; ?>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <?php if (!empty($permissions)):
                                    $i = 0;
                                    foreach ($permissions as $key => $permission):
                                        if ($i > 11):?>
                                            <div class="m-b-15">
                                                <?= formCheckbox('permissions[]', $permission, trans($permission)); ?>
                                            </div>
                                        <?php endif;
                                        $i++;
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans("add_role"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>