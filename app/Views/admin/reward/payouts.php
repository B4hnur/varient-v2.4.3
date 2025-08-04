<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('payouts'); ?></h3>
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
                    <table class="table table-bordered table-striped">
                        <?= view('admin/reward/_filter', ['url' => adminUrl('reward-system/payouts')]); ?>
                        <thead>
                        <tr role="row">
                            <th><?= trans('id'); ?></th>
                            <th><?= trans('user_id'); ?></th>
                            <th><?= trans('username'); ?></th>
                            <th><?= trans('email'); ?></th>
                            <th><?= trans('amount'); ?></th>
                            <th><?= trans('payout_method'); ?></th>
                            <th><?= trans('status'); ?></th>
                            <th><?= trans('date'); ?></th>
                            <th><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($payouts)):
                            foreach ($payouts as $item):
                                $user = getUserById($item->user_id); ?>
                                <tr>
                                    <td><?= $item->id; ?></td>
                                    <td><?= $item->user_id; ?></td>
                                    <td><?= esc($item->username); ?></td>
                                    <td><?= esc($item->email); ?></td>
                                    <td><?= priceFormatted($item->amount); ?></td>
                                    <td>
                                        <div style="display: inline-block; min-width: 70px;">
                                            <?= trans($item->payout_method); ?>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#accountDetailsModel_<?= $item->user_id; ?>"><?= trans("show"); ?></button>
                                        <?= view('admin/reward/_modal_user_payout_method', ['userPayout' => $user]); ?>
                                    </td>
                                    <td>
                                        <?php if ($item->status == 1): ?>
                                            <label class="label label-success"><?= trans("completed"); ?></label>
                                        <?php else: ?>
                                            <label class="label label-warning"><?= trans("pending"); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item->created_at; ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <?php if ($item->status != 1): ?>
                                                    <li>
                                                        <form action="<?= base_url('Reward/approvePayoutPost'); ?>" method="post">
                                                            <?= csrf_field(); ?>
                                                            <input type="hidden" value="<?= $item->id; ?>" name="id">
                                                            <button type="submit" class="btn-list-button"><i class="fa fa-check option-icon"></i><?= trans('approve'); ?></button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="deleteItem('Reward/deletePayoutPost','<?= $item->id; ?>','<?= clrQuotes(trans("confirm_record")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($payouts)): ?>
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