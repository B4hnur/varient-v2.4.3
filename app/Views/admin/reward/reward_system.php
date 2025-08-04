<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('reward_system'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('settings'); ?></h3>
            </div>
            <form action="<?= base_url('Reward/updateSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('reward_system_status', 1, 0, trans("enable"), trans("disable"), $generalSettings->reward_system_status); ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('reward_amount'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                            <input type="text" class="form-control price-input" name="reward_amount" value="<?= $generalSettings->reward_amount; ?>" placeholder="E.g. 1.5" required>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" name="submit" value="settings" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('payout_methods'); ?></h3>
            </div>
            <form action="<?= base_url('Reward/updatePayoutPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group m-b-30">
                        <label style="font-weight: bold !important;"><?= trans("paypal"); ?></label>
                        <hr style="margin-top: 0;">
                        <div class="row">
                            <div class="col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('status'); ?></label>
                                    <?= formRadio('paypal_status', 1, 0, trans("enable"), trans("disable"), payoutMethod('paypal_status'), 'col-sm-12'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-8">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('min_poyout_amount'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                                        <input type="number" class="form-control" name="paypal_min_amount" min="0" max="999999" step="0.01" value="<?= esc(payoutMethod('paypal_min_amount')); ?>" placeholder="E.g. 50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold !important;"><?= trans("bitcoin"); ?></label>
                        <hr style="margin-top: 0;">
                        <div class="row">
                            <div class="col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('status'); ?></label>
                                    <?= formRadio('bitcoin_status', 1, 0, trans("enable"), trans("disable"), payoutMethod('bitcoin_status'), 'col-sm-12'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-8">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('min_poyout_amount'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                                        <input type="number" class="form-control price-input" name="bitcoin_min_amount" min="0" max="999999" step="0.01" value="<?= esc(payoutMethod('bitcoin_min_amount')); ?>" placeholder="E.g. 50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold !important;"><?= trans("iban"); ?></label>
                        <hr style="margin-top: 0;">
                        <div class="row">
                            <div class="col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('status'); ?></label>
                                    <?= formRadio('iban_status', 1, 0, trans("enable"), trans("disable"), payoutMethod('iban_status'), 'col-sm-12'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-8">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('min_poyout_amount'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                                        <input type="number" class="form-control price-input" name="iban_min_amount" min="0" max="999999" step="0.01" value="<?= esc(payoutMethod('iban_min_amount')); ?>" placeholder="E.g. 50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold !important;"><?= trans("swift"); ?></label>
                        <hr style="margin-top: 0;">
                        <div class="row">
                            <div class="col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('status'); ?></label>
                                    <?= formRadio('swift_status', 1, 0, trans("enable"), trans("disable"), payoutMethod('swift_status'), 'col-sm-12'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-8">
                                <div class="form-group">
                                    <label class="control-label"><?= trans('min_poyout_amount'); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                                        <input type="number" class="form-control price-input" name="swift_min_amount" min="0" max="999999" step="0.01" value="<?= esc(payoutMethod('swift_min_amount')); ?>" placeholder="E.g. 50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('currency'); ?></h3>
            </div>
            <form action="<?= base_url('Reward/updateCurrencyPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('currency_name'); ?></label>
                        <input type="text" class="form-control" name="currency_name" value="<?= $generalSettings->currency_name; ?>" placeholder="E.g. US Dollar" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('currency_symbol'); ?></label>
                        <input type="text" class="form-control" name="currency_symbol" value="<?= esc($generalSettings->currency_symbol); ?>" placeholder="E.g. $, USD or <?= esc('&#36;') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("currency_format"); ?></label>
                        <?= formRadio('currency_format', 'us', 'european', '1,234,567.89', '1.234.567,89', $generalSettings->currency_format); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("currency_symbol_format"); ?></label>
                        <?= formRadio('currency_symbol_format', 'left', 'right', '$100 (' . trans("left") . ')', '100$ (' . trans("right") . ')', $generalSettings->currency_symbol_format); ?>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('human_verification'); ?><br><small><?= trans("human_verification_exp"); ?></small></h3>
            </div>
            <form action="<?= base_url('Reward/updateSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <?php $verification = unserializeData($generalSettings->human_verification); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?php $verificationStatus = !empty($verification) && !empty($verification['status']) ? 1 : 0; ?>
                        <?= formRadio('status', 1, 0, trans("enable"), trans("disable"), $verificationStatus); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_time_spent_on_page'); ?></label>
                        <input type="number" class="form-control price-input" name="time_spent" min="0" max="10000" step="1" value="<?= !empty($verification) && !empty($verification['time_spent']) ? $verification['time_spent'] : 0; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_mouse_movements'); ?></label>
                        <input type="number" class="form-control price-input" name="mouse" min="0" max="10000" step="1" value="<?= !empty($verification) && !empty($verification['mouse']) ? $verification['mouse'] : 0; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('min_scroll_movements'); ?></label>
                        <input type="number" class="form-control price-input" name="scroll" min="0" max="10000" step="1" value="<?= !empty($verification) && !empty($verification['scroll']) ? $verification['scroll'] : 0; ?>" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="verification" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>