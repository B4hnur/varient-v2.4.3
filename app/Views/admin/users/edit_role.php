<div class="row">
    <div class="col-sm-10">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("edit_role"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('roles-permissions'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans("roles"); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Admin/editRolePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= esc(currentFullURL()); ?>">
                <div class="box-body">
                    <input type="hidden" name="id" value="<?= $role->id ?>">
                    <?php foreach ($activeLanguages as $language):
                        $roleName = getRoleName($role, $language->id); ?>
                        <div class="form-group">
                            <label><?= trans("role_name"); ?> (<?= esc($language->name); ?>)</label>
                            <input type="text" class="form-control" name="role_name_<?= $language->id; ?>" value="<?= esc($roleName); ?>" placeholder="<?= trans("role_name"); ?>" maxlength="255" required>
                        </div>
                    <?php endforeach;
                    if ($role->id == 3):?>
                        <div class="form-group">
                            <label class="m-b-15"><?= trans("permissions"); ?></label>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <div class="m-b-15">
                                        <?php $permission = "add_post";
                                        $checkedValue = !empty($rolePermissions) && is_array($rolePermissions) && in_array($permission, $rolePermissions) ? $permission : ''; ?>
                                        <?= formCheckbox('permissions[]', $permission, trans($permission), $checkedValue); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else:
                        if ($role->is_super_admin != 1): ?>
                            <div class="form-group">
                                <label class="m-b-15"><?= trans("permissions"); ?></label>
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <?php if (!empty($permissionsArray)):
                                            $i = 0;
                                            foreach ($permissionsArray as $permission):
                                                if ($permission != "admin_panel"):
                                                    if ($i <= 11): ?>
                                                        <div class="m-b-15">
                                                            <?php $checkedValue = !empty($rolePermissions) && is_array($rolePermissions) && in_array($permission, $rolePermissions) ? $permission : ''; ?>
                                                            <?= formCheckbox('permissions[]', $permission, trans($permission), $checkedValue); ?>
                                                        </div>
                                                    <?php endif; endif;
                                                $i++;
                                            endforeach;
                                        endif; ?>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <?php if (!empty($permissionsArray)):
                                            $i = 0;
                                            foreach ($permissionsArray as $permission):
                                                if ($i > 11): ?>
                                                    <div class="m-b-15">
                                                        <?php $checkedValue = !empty($rolePermissions) && is_array($rolePermissions) && in_array($permission, $rolePermissions) ? $permission : ''; ?>
                                                        <?= formCheckbox('permissions[]', $permission, trans($permission), $checkedValue); ?>
                                                    </div>
                                                <?php endif;
                                                $i++;
                                            endforeach;
                                        endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif;
                    endif; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans("save_changes"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>