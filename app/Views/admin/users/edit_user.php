<div class="row">
    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('update_profile'); ?></h3>
                </div>
            </div>
            <form action="<?= base_url('Admin/editUserPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $user->id; ?>">

                <div class="box-body">
                    <div class="form-group">
                        <?php $role = getRole($user->role_id);
                        if (!empty($role)):
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
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <img src="<?= getUserAvatar($user->avatar); ?>" alt="" class="thumbnail img-responsive img-update" style="max-width: 240px; max-height: 240px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <p>
                                    <a class="btn btn-success btn-sm btn-file-upload">
                                        <?= trans('change_avatar'); ?>
                                        <input name="file" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));" type="file">
                                    </a>
                                </p>
                                <p class='label label-info' id="upload-file-info"></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= trans('email'); ?></label>
                        <input type="email" class="form-control form-input" name="email" placeholder="<?= trans('email'); ?>" value="<?= esc($user->email); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('username'); ?></label>
                        <input type="text" class="form-control form-input" name="username" placeholder="<?= trans('username'); ?>" value="<?= esc($user->username); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('slug'); ?></label>
                        <input type="text" class="form-control form-input" name="slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($user->slug); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('about_me'); ?></label>
                        <textarea class="form-control text-area" name="about_me" placeholder="<?= trans('about_me'); ?>"><?= esc($user->about_me); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><?= trans('social_accounts'); ?></label>
                        <?php $socialArray = getSocialLinksArray($user, true);
                        foreach ($socialArray as $item):?>
                            <input type="text" class="form-control form-input m-b-10" name="<?= $item['name']; ?>" placeholder="<?= trans($item['name']); ?>" value="<?= esc($item['value']); ?>" maxlength="1000">
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label><?= trans('balance'); ?></label>
                        <input type="text" class="form-control form-input price-input" name="balance" placeholder="<?= trans('balance'); ?>" value="<?= $user->balance; ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('total_pageviews'); ?></label>
                        <input type="text" class="form-control form-input" name="total_pageviews" placeholder="<?= trans('total_pageviews'); ?>" value="<?= $user->total_pageviews; ?>">
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>