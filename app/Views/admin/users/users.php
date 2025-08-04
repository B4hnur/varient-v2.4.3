<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans("users"); ?></h3>
        </div>
        <?php if (isSuperAdmin()): ?>
            <div class="right">
                <a href="<?= adminUrl('add-user'); ?>" class="btn btn-success btn-add-new">
                    <i class="fa fa-plus"></i>
                    <?= trans("add_user"); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <?= view('admin/users/_filter'); ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr role="row">
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('user'); ?></th>
                            <th><?= trans('email'); ?></th>
                            <th><?= trans('status'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users)):
                            foreach ($users as $user):
                                $role = getRole($user->role_id); ?>
                                <tr>
                                    <td><?= $user->id; ?></td>
                                    <td class="td-user">
                                        <a href="<?= generateProfileURL($user->slug); ?>" target="_blank" class="table-link">
                                            <img src="<?= getUserAvatar($user->avatar); ?>" alt="" class="img-responsive">
                                            <strong><?= esc($user->username); ?></strong>
                                        </a>
                                        <p>
                                            <?php if (!empty($role)):
                                                $roleName = getRoleName($role, $activeLang->id);
                                                if ($user->role_id == 1):?>
                                                    <label class="label bg-maroon"><?= esc($roleName); ?></label>
                                                <?php elseif ($user->role_id == 2): ?>
                                                    <label class="label label-success"><?= esc($roleName); ?></label>
                                                <?php elseif ($user->role_id == 3): ?>
                                                    <label class="label label-default"><?= esc($roleName); ?></label>
                                                <?php else: ?>
                                                    <label class="label label-warning"><?= esc($roleName); ?></label>
                                                <?php endif;
                                            endif; ?>
                                            <?php if ($user->reward_system_enabled == 1): ?>
                                                <label class="label bg-primary"><?= trans('reward_system'); ?></label>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?= esc($user->email);
                                        if ($user->email_status == 1): ?>
                                            <small class="text-success">(<?= trans("confirmed"); ?>)</small>
                                        <?php else: ?>
                                            <small class="text-danger">(<?= trans("unconfirmed"); ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user->status == 1): ?>
                                            <label class="label label-success"><?= trans('active'); ?></label>
                                        <?php else: ?>
                                            <label class="label label-danger"><?= trans('banned'); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= formatDate($user->created_at); ?></td>
                                    <td class="td-select-option">
                                        <form action="<?= base_url('Admin/userOptionsPost'); ?>" method="post" style="<?= !empty($role) && $role->is_super_admin == 1 && !isSuperAdmin() ? 'display:none;' : ''; ?>">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $user->id; ?>">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <?php if (!empty($role) && $role->is_super_admin == 1):
                                                        if (isSuperAdmin()):?>
                                                            <li>
                                                                <button type="submit" name="submit" value="reward_system" class="btn-list-button"><i class="fa fa-money option-icon"></i><?= $user->reward_system_enabled == 1 ? trans('disable_reward_system') : trans('enable_reward_system'); ?></button>
                                                            </li>
                                                            <li>
                                                                <a href="<?= adminUrl('edit-user/' . $user->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                            </li>
                                                        <?php endif;
                                                    else: ?>
                                                        <li>
                                                            <button type="button" class="btn-list-button" data-toggle="modal" data-target="#myModal" onclick="$('#modal_user_id').val('<?= $user->id; ?>');">
                                                                <i class="fa fa-user option-icon"></i><?= trans('change_user_role'); ?>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="submit" name="submit" value="reward_system" class="btn-list-button"><i class="fa fa-money option-icon"></i><?= $user->reward_system_enabled == 1 ? trans('disable_reward_system') : trans('enable_reward_system'); ?></button>
                                                        </li>
                                                        <?php if ($user->email_status != 1): ?>
                                                            <li>
                                                                <button type="submit" name="submit" value="confirm_email" class="btn-list-button"><i class="fa fa-check option-icon"></i><?= trans('confirm_user_email'); ?></button>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <button type="submit" name="submit" value="ban_user" class="btn-list-button"><i class="fa fa-stop-circle option-icon"></i><?= $user->status == 1 ? trans('ban_user') : trans('remove_ban'); ?></button>
                                                        </li>
                                                        <li>
                                                            <a href="<?= adminUrl('edit-user/' . $user->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)" onclick="deleteItem('Admin/deleteUserPost','<?= $user->id; ?>','<?= clrQuotes(trans("confirm_user")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($users)): ?>
                        <p class="text-center text-muted"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-12 text-right">
                <?= $pager->links; ?>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans('change_user_role'); ?></h4>
            </div>
            <form action="<?= base_url('Admin/changeUserRolePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="user_id" id="modal_user_id" value="">
                        <select name="role_id" class="form-control" autocomplete="off" required>
                            <option value=""><?= trans('select'); ?></option>
                            <?php if (!empty($roles)):
                                foreach ($roles as $role):
                                    $roleName = getRoleName($role, $activeLang->id);
                                    if (isSuperAdmin() || $role->is_super_admin != 1):?>
                                        <option value="<?= esc($role->id); ?>"><?= esc($roleName); ?></option>
                                    <?php endif;
                                endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans('save'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>