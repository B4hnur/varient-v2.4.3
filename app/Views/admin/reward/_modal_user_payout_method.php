<?php if (!empty($userPayout)): ?>
    <div id="accountDetailsModel_<?= $userPayout->id; ?>" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= esc($userPayout->username); ?></h4>
                </div>
                <div class="modal-body">
                    <?php if (payoutMethod('paypal_status')): ?>
                        <strong><?= trans("paypal"); ?></strong>
                        <hr style="margin-top: 2px;">
                        <div class="row m-b-30">
                            <div class="col-sm-4">
                                <?= trans("paypal_email_address"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'paypal_email')); ?></strong>
                            </div>
                        </div>
                    <?php endif;
                    if (payoutMethod('bitcoin_status')): ?>
                        <strong><?= trans("bitcoin"); ?></strong>
                        <hr style="margin-top: 2px;">
                        <div class="row m-b-15">
                            <div class="col-sm-4">
                                <?= trans("bitcoin_address"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'btc_address')); ?></strong>
                            </div>
                        </div>
                    <?php endif;
                    if (payoutMethod('iban_status')): ?>
                        <strong><?= trans("iban"); ?></strong>
                        <hr style="margin-top: 2px;">
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("full_name"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'iban_full_name')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("country"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'iban_country')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("bank_name"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'iban_bank_name')); ?></strong>
                            </div>
                        </div>
                        <div class="row m-b-15">
                            <div class="col-sm-4">
                                <?= trans("iban_long"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'iban_number')); ?></strong>
                            </div>
                        </div>
                    <?php endif;
                    if (payoutMethod('swift_status')): ?>
                        <strong><?= trans("swift"); ?></strong>
                        <hr style="margin-top: 2px;">
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("full_name"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_full_name')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("country"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_country')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("state"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_state')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("city"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_city')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("postcode"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_postcode')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("address"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_address')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("bank_account_holder_name"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_bank_account_holder_name')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("bank_name"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_bank_name')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("bank_branch_country"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_bank_branch_country')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("bank_branch_city"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_bank_branch_city')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("swift_iban"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_iban')); ?></strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= trans("swift_code"); ?>
                            </div>
                            <div class="col-sm-8">
                                <strong>:&nbsp;&nbsp;<?= esc(userPayoutMethod($userPayout, 'swift_code')); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= trans('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>