<div class="row">
    <div class="col-sm-12 col-md-10">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= $title; ?></h3>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-pills nav-payout-accounts display-flex justify-content-center">
                    <?php if (payoutMethod('paypal_status')): ?>
                        <li class="<?= $selectedPayout == 'paypal' ? 'active' : ''; ?>">
                            <a class="nav-link-paypal" href="<?= adminUrl("set-payout-account") . '?payout=paypal'; ?>"><?= trans("paypal"); ?></a>
                        </li>
                    <?php endif;
                    if (payoutMethod('bitcoin_status')): ?>
                        <li class="<?= $selectedPayout == 'bitcoin' ? 'active' : ''; ?>">
                            <a class="nav-link-paypal" href="<?= adminUrl("set-payout-account") . '?payout=bitcoin'; ?>"><?= trans("bitcoin"); ?></a>
                        </li>
                    <?php endif;
                    if (payoutMethod('iban_status')): ?>
                        <li class="<?= $selectedPayout == 'iban' ? 'active' : ''; ?>">
                            <a class="nav-link-paypal" href="<?= adminUrl("set-payout-account") . '?payout=iban'; ?>"><?= trans("iban"); ?></a>
                        </li>
                    <?php endif;
                    if (payoutMethod('swift_status')): ?>
                        <li class="<?= $selectedPayout == 'swift' ? 'active' : ''; ?>">
                            <a class="nav-link-paypal" href="<?= adminUrl("set-payout-account") . '?payout=swift'; ?>"><?= trans("swift"); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="tab-content">
                    <form action="<?= base_url('Earnings/setPayoutAccountPost'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <?php if (payoutMethod('paypal_status') && $selectedPayout == 'paypal'): ?>
                            <div class="tab-pane active" id="tab_paypal">
                                <div class="form-group">
                                    <label class="control-label"><?= trans("paypal_email_address"); ?>*</label>
                                    <input type="email" name="paypal_email" class="form-control form-input" maxlength="255" value="<?= esc(userPayoutMethod(user(), 'paypal_email')); ?>" required>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" name="submit" value="paypal" class="btn btn-primary"><?= trans("save_changes"); ?></button>
                                </div>
                            </div>
                        <?php elseif (payoutMethod('bitcoin_status') && $selectedPayout == 'bitcoin'): ?>
                            <div class="tab-pane active" id="tab_bitcoin">
                                <div class="form-group">
                                    <label class="control-label"><?= trans("bitcoin_address"); ?>*</label>
                                    <input type="text" name="btc_address" class="form-control form-input" maxlength="500" value="<?= esc(userPayoutMethod(user(), 'btc_address')); ?>" required>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" name="submit" value="bitcoin" class="btn btn-primary"><?= trans("save_changes"); ?></button>
                                </div>
                            </div>
                        <?php elseif (payoutMethod('iban_status') && $selectedPayout == 'iban'): ?>
                            <div class="tab-pane iban" id="tab_iban">
                                <div class="form-group">
                                    <label class="control-label"><?= trans("full_name"); ?>*</label>
                                    <input type="text" name="iban_full_name" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'iban_full_name')); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("country"); ?>*</label>
                                            <input type="text" name="iban_country" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'iban_country')); ?>" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="control-label"><?= trans("bank_name"); ?>*</label>
                                            <input type="text" name="iban_bank_name" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'iban_bank_name')); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?= trans("iban_long"); ?>(<?= trans("iban"); ?>)*</label>
                                    <input type="text" name="iban_number" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'iban_number')); ?>" required>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" name="submit" value="iban" class="btn btn-primary"><?= trans("save_changes"); ?></button>
                                </div>
                            </div>
                        <?php elseif (payoutMethod('swift_status') && $selectedPayout == 'swift'): ?>
                            <div class="tab-pane active" id="tab_swift">
                                <div class="form-group">
                                    <label class="control-label"><?= trans("full_name"); ?>*</label>
                                    <input type="text" name="swift_full_name" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_full_name')); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("country"); ?>*</label>
                                            <input type="text" name="swift_country" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_country')); ?>" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="control-label"><?= trans("state"); ?>*</label>
                                            <input type="text" name="swift_state" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_state')); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("city"); ?>*</label>
                                            <input type="text" name="swift_city" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_city')); ?>" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="control-label"><?= trans("postcode"); ?>*</label>
                                            <input type="text" name="swift_postcode" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_postcode')); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?= trans("address"); ?>*</label>
                                    <input type="text" name="swift_address" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_address')); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("bank_account_holder_name"); ?>*</label>
                                            <input type="text" name="swift_bank_account_holder_name" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_bank_account_holder_name')); ?>" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="control-label"><?= trans("bank_name"); ?>*</label>
                                            <input type="text" name="swift_bank_name" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_bank_name')); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("bank_branch_country"); ?>*</label>
                                            <input type="text" name="swift_bank_branch_country" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_bank_branch_country')); ?>" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="control-label"><?= trans("bank_branch_city"); ?>*</label>
                                            <input type="text" name="swift_bank_branch_city" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_bank_branch_city')); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?= trans("swift_iban"); ?>*</label>
                                    <input type="text" name="swift_iban" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_iban')); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"><?= trans("swift_code"); ?>*</label>
                                    <input type="text" name="swift_code" class="form-control form-input" value="<?= esc(userPayoutMethod(user(), 'swift_code')); ?>" required>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" name="submit" value="swift" class="btn btn-primary"><?= trans("save_changes"); ?></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .nav-pills {
        margin-bottom: 25px;
    }

    .nav-pills > li > a {
        background-color: #ebebeb;
        padding: 10px 30px;
        border-radius: 3px;
        border-top: 0 !important;
        margin-right: 10px;
        cursor: pointer;
        margin-bottom: 5px;
    }

    .nav-pills > li > a:hover, .nav-pills > li > a:focus, .nav-pills > li > a:active {
        background-color: #ebebeb !important;
    }

    .nav-pills > .active > a:hover, .nav-pills > .active > a:focus, .nav-pills > .active > a:active {
        background-color: #337ab7 !important;
    }
</style>