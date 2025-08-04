<div class="row table-filter-container">
    <div class="col-sm-12">
        <button type="button" class="btn btn-default filter-toggle collapsed m-b-10" data-toggle="collapse" data-target="#collapseFilter" aria-expanded="false">
            <i class="fa fa-filter"></i>&nbsp;&nbsp;<?= trans("filter"); ?>
        </button>
        <div class="collapse navbar-collapse p-0" id="collapseFilter">
            <form action="<?= adminUrl('users'); ?>" method="get">
                <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                    <label><?= trans("show"); ?></label>
                    <select name="show" class="form-control">
                        <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                        <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                        <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                        <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
                <div class="item-table-filter">
                    <label><?= trans("role"); ?></label>
                    <select name="role" class="form-control">
                        <option value=""><?= trans("all"); ?></option>
                        <?php $roles = getRoles();
                        if (!empty($roles)):
                            foreach ($roles as $role):
                                $roleName = getRoleName($role, $activeLang->id); ?>
                                <option value="<?= esc($role->id); ?>" <?= inputGet('role', true) == $role->id ? 'selected' : ''; ?>><?= esc($roleName); ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>
                <div class="item-table-filter">
                    <label><?= trans("status"); ?></label>
                    <select name="status" class="form-control">
                        <option value="" <?= inputGet('status') != 'active' && inputGet('status') == 'banned' ? 'selected' : ''; ?>><?= trans("all"); ?></option>
                        <option value="active" <?= inputGet('status') == 'active' ? 'selected' : ''; ?>><?= trans("active"); ?></option>
                        <option value="banned" <?= inputGet('status') == 'banned' ? 'selected' : ''; ?>><?= trans("banned"); ?></option>
                    </select>
                </div>
                <div class="item-table-filter">
                    <label><?= trans("email_status"); ?></label>
                    <select name="email_status" class="form-control">
                        <option value="" <?= inputGet('status') != 'confirmed' && inputGet('status') == 'unconfirmed' ? 'selected' : ''; ?>><?= trans("all"); ?></option>
                        <option value="confirmed" <?= inputGet('email_status') == 'confirmed' ? 'selected' : ''; ?>><?= trans("confirmed"); ?></option>
                        <option value="unconfirmed" <?= inputGet('email_status') == 'unconfirmed' ? 'selected' : ''; ?>><?= trans("unconfirmed"); ?></option>
                    </select>
                </div>
                <div class="item-table-filter">
                    <label><?= trans("reward_system"); ?></label>
                    <select name="reward_system" class="form-control">
                        <option value="" <?= inputGet('reward_system') != 'active' && inputGet('reward_system') == 'inactive' ? 'selected' : ''; ?>><?= trans("all"); ?></option>
                        <option value="active" <?= inputGet('reward_system') == 'active' ? 'selected' : ''; ?>><?= trans("active"); ?></option>
                        <option value="inactive" <?= inputGet('reward_system') == 'inactive' ? 'selected' : ''; ?>><?= trans("inactive"); ?></option>
                    </select>
                </div>
                <div class="item-table-filter item-table-filter-long">
                    <label><?= trans("search"); ?></label>
                    <input name="q" class="form-control" placeholder="<?= trans("search") ?>" type="search" value="<?= esc(inputGet('q', true)); ?>">
                </div>
                <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                    <label style="display: block">&nbsp;</label>
                    <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>