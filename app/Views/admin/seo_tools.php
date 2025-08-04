<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('seo_tools'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('settings'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/seoToolsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control max-400" onchange="window.location.href = '<?= adminUrl(); ?>'+'/seo-tools?lang='+this.value;">
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $selectedLangId == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('site_title'); ?></label>
                        <input type="text" class="form-control" name="site_title" placeholder="<?= trans('site_title'); ?>" value="<?= esc($seoSettings->site_title); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('home_title'); ?></label>
                        <input type="text" class="form-control" name="home_title" placeholder="<?= trans('home_title'); ?>" value="<?= esc($seoSettings->home_title); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('site_description'); ?></label>
                        <input type="text" class="form-control" name="site_description" placeholder="<?= trans('site_description'); ?>" value="<?= esc($seoSettings->site_description); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?></label>
                        <textarea class="form-control text-area" name="keywords" placeholder="<?= trans('keywords'); ?>" style="min-height: 70px;"><?= esc($seoSettings->keywords); ?></textarea>
                    </div>

                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" name="submit" value="settings" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('google_indexing_api'); ?></h3>
            </div>
            <form id="formGoogleIndexingApi" action="<?= base_url('Admin/googleIndexingApiPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('google_indexing_api', 1, 0, trans("enable"), trans("disable"), $generalSettings->google_indexing_api); ?>
                    </div>
                    <div class="box-footer text-right" style="padding-left: 0; padding-right: 0;">
                        <button type="button" onclick="testGoogleIndexingApi()" class="btn btn-warning"><?= trans("test_api"); ?></button>
                        <button type="submit" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                    </div>
                    <?php $session = session();
                    $apiResult = '';
                    if ($session->getFlashdata('msgTestApi')) {
                        $apiResult = $session->getFlashdata('msgTestApi');
                    }
                    if (!empty($apiResult)):?>
                        <div class="alert <?= $apiResult['status'] == 1 ? 'alert-success' : 'alert-danger'; ?> alert-large m-t-10">
                            <strong><?= $apiResult['message']; ?>
                        </div>
                    <?php endif; ?>
                    <div class="alert alert-info alert-large m-t-10">
                        <strong><?= trans("warning"); ?>!</strong>&nbsp;&nbsp;<?= trans("warning_documentation"); ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('google_analytics'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/seoToolsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('google_analytics_code'); ?></label>
                        <textarea class="form-control text-area" name="google_analytics" placeholder="<?= trans('google_analytics_code'); ?>" style="min-height: 100px;"><?= $generalSettings->google_analytics; ?></textarea>
                    </div>
                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" name="submit" value="google_analytics" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('sitemap'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/sitemapSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label class="label-sitemap"><?= trans('frequency'); ?></label>
                        <small class="small-sitemap"> (<?= trans('frequency_exp'); ?>)</small>
                        <?= formRadio('frequency', 'auto', 'none', trans("automatically_calculated"), trans("none"), $generalSettings->sitemap_frequency); ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sitemap"><?= trans('last_modification'); ?></label>
                        <small class="small-sitemap"> (<?= trans('last_modification_exp'); ?>)</small>
                        <?= formRadio('last_modification', 'auto', 'none', trans("automatically_calculated"), trans("none"), $generalSettings->sitemap_last_modification); ?>
                    </div>
                    <div class="form-group">
                        <label class="label-sitemap"><?= trans('priority'); ?></label>
                        <small class="small-sitemap"> (<?= trans('priority_exp'); ?>)</small>
                        <?= formRadio('priority', 'auto', 'none', trans("automatically_calculated"), trans("none"), $generalSettings->sitemap_priority); ?>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="generate" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-12">
                    <div style="padding: 20px;">
                        <h3 style="font-size: 18px; font-weight: 500;"><?= trans("generated_sitemaps") ?></h3>
                        <hr>
                        <div style="display: block; max-height: 400px; overflow-y: auto; padding-bottom: 30px;">
                            <?php if ($numSitemaps >= 1):
                                for ($i = 0; $i < $numSitemaps; $i++):
                                    $fileName = $i == 0 ? 'sitemap.xml' : 'sitemap-' . $i . '.xml'; ?>
                                    <div>
                                        <form action="<?= base_url('Admin/sitemapPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="index" value="<?= $i; ?>">
                                            <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">
                                            <?php if (file_exists(FCPATH . $fileName)): ?>
                                                <div style="display: inline-block; width: 140px; font-weight: 600;">
                                                    <a href="<?= base_url($fileName); ?>" target="_blank"><?= $fileName; ?></a>
                                                </div>
                                                <button type="submit" name="submit" value="refresh" class="btn btn-primary"><?= trans('refresh'); ?></button>
                                                <button type="submit" name="submit" value="download" class="btn btn-success"><?= trans('download'); ?></button>
                                                <button type="submit" name="submit" value="delete" class="btn btn-danger"><?= trans('delete'); ?></button>
                                            <?php else: ?>
                                                <div style="display: inline-block; width: 140px;">
                                                    <?= $fileName; ?>
                                                </div>
                                                <button type="submit" name="submit" value="generate" class="btn btn-primary"><?= trans('generate_sitemap'); ?></button>
                                            <?php endif; ?>
                                        </form>
                                    </div>

                                    <?php if ($numSitemaps > 1): ?>
                                    <hr>
                                <?php endif; ?>

                                <?php endfor;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info alert-large m-t-10">
            <strong><?= trans("warning"); ?>!</strong>&nbsp;&nbsp;<?= trans("sitemap_generate_exp"); ?>
        </div>
        <div class="callout" style="margin-top: 30px;background-color: #fff; border-color:#00c0ef;max-width: 600px;">
            <h4>Cron Job</h4>
            <p><strong><?= base_url(); ?>/cron/update-sitemap</strong></p>
            <small><?= trans('msg_cron_sitemap'); ?></small>
        </div>
    </div>
</div>

<script>
    function testGoogleIndexingApi() {
        var form = document.getElementById('formGoogleIndexingApi');
        form.action = VrConfig.baseURL + '/Post/testGoogleIndexingApiPost';
        console.log(form);
        form.requestSubmit();
    }
</script>