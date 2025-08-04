<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('earnings'); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('reward-system/add-payout'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>
                <?= trans('add_payout'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <?= view('admin/reward/_filter', ['url' => adminUrl('reward-system/earnings')]); ?>
                        <thead>
                        <tr role="row">
                            <th><?= trans('user_id'); ?></th>
                            <th><?= trans('username'); ?></th>
                            <th><?= trans('email'); ?></th>
                            <th><?= trans('total_pageviews'); ?></th>
                            <th><?= trans('balance'); ?></th>
                            <th><?= trans('payout_method'); ?></th>
                            <th><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($earnings)):
                            foreach ($earnings as $item): ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td><?= esc($item->username); ?></td>
                                    <td><?= esc($item->email); ?></td>
                                    <td><?= esc($item->total_pageviews); ?></td>
                                    <td><?= priceFormatted($item->balance); ?></td>
                                    <td>
                                        <p class="m-0">
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#accountDetailsModel_<?= $item->id; ?>"><?= trans("show"); ?></button>
                                        </p>
                                    </td>
                                    <td class="td-select-option">
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="<?= adminUrl('edit-user/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
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
            <div class="col-sm-12 text-right">
                <?= $pager->links; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($earnings)) :
    foreach ($earnings as $item):
        echo view('admin/reward/_modal_user_payout_method', ['userPayout' => $item]);
    endforeach;
endif; ?>

<style>
    .modal .row {
        min-height: 26px;
    }
</style>
