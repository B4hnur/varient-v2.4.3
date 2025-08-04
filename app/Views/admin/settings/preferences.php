<?php $tab = inputGet('tab');
if ($tab != "general" && $tab != "homepage" && $tab != "posts" && $tab != "post_formats") {
    $tab = "general";
} ?>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-sm-12">
        <h3 style="font-size: 18px; font-weight: 600;margin-top: 10px;"><?= trans('preferences'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <form action="<?= base_url('Admin/preferencesPost'); ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?= $tab == "general" ? 'active' : ''; ?>"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?= trans('general'); ?></a></li>
                    <li class="<?= $tab == "homepage" ? 'active' : ''; ?>"><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?= trans('homepage'); ?></a></li>
                    <li class="<?= $tab == "posts" ? 'active' : ''; ?>"><a href="#tab_3" data-toggle="tab" aria-expanded="false"><?= trans('posts'); ?></a></li>
                    <li class="<?= $tab == "post_formats" ? 'active' : ''; ?>"><a href="#tab_4" data-toggle="tab" aria-expanded="false"><?= trans('post_formats'); ?></a></li>
                </ul>
                <div class="tab-content settings-tab-content">
                    <div class="tab-pane <?= empty($tab) || $tab == "general" ? 'active' : ''; ?>" id="tab_1">
                        <div class="form-group">
                            <label><?= trans("multilingual_system"); ?></label>
                            <?= formRadio('multilingual_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->multilingual_system, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("registration_system"); ?></label>
                            <?= formRadio('registration_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->registration_system, 'col-md-4'); ?>
                        </div>
                        <?php if ($activeTheme->theme == 'classic'): ?>
                            <div class="form-group">
                                <label><?= trans("sticky_sidebar"); ?></label>
                                <?= formRadio('sticky_sidebar', 1, 0, trans("enable"), trans("disable"), $generalSettings->sticky_sidebar, 'col-md-4'); ?>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="sticky_sidebar" value="<?= $generalSettings->sticky_sidebar; ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label><?= trans("rss"); ?></label>
                            <?= formRadio('show_rss', 1, 0, trans("enable"), trans("disable"), $generalSettings->show_rss, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("rss_content"); ?></label>
                            <?= formRadio('rss_content_type', 'summary', 'content', trans("distribute_only_post_summary"), trans("distribute_post_content"), $generalSettings->rss_content_type, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("file_manager"); ?></label>
                            <?= formRadio('file_manager_show_files', 1, 0, trans("show_all_files"), trans("show_only_own_files"), $generalSettings->file_manager_show_files, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("delete_images_with_post"); ?></label>
                            <?= formRadio('delete_images_with_post', 1, 0, trans("yes"), trans("no"), $generalSettings->delete_images_with_post, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("audio_download_button"); ?></label>
                            <?= formRadio('audio_download_button', 1, 0, trans("enable"), trans("disable"), $generalSettings->audio_download_button, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_user_email_profile"); ?></label>
                            <?= formRadio('show_user_email_on_profile', 1, 0, trans("yes"), trans("no"), $generalSettings->show_user_email_on_profile, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("progressive_web_app"); ?>&nbsp;(<small style="color: #337ab7 !important;"><?= trans("warning_documentation"); ?></small>)</label>
                            <?= formRadio('pwa_status', 1, 0, trans("enable"), trans("disable"), $generalSettings->pwa_status, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans("pwa_logo"); ?></label>
                            <?php if (!empty(getPwaLogo($generalSettings, 'md'))): ?>
                                <div class="display-block m-b-10">
                                    <img src="<?= base_url(getPwaLogo($generalSettings, 'md')); ?>?t=<?= uniqid(); ?>" width="100">
                                </div>
                            <?php endif; ?>
                            <div class="display-block">
                                <a class='btn btn-success btn-sm btn-file-upload'>
                                    <?= trans('select'); ?>
                                    <input type="file" name="pwa_logo" size="40" accept=".png" onchange="$('#upload-file-info-pwa').html($(this).val().replace(/.*[\/\\]/, ''));">
                                </a>
                                (PNG, 512x512 px)
                            </div>
                            <span class='label label-info' id="upload-file-info-pwa"></span>
                        </div>

                        <div class="form-group text-right" style="margin-top: 60px;">
                            <button type="submit" name="submit" value="general" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                        </div>
                    </div>

                    <div class="tab-pane <?= $tab == "homepage" ? 'active' : ''; ?>" id="tab_2">
                        <div class="form-group">
                            <label><?= trans("show_featured_section"); ?></label>
                            <?= formRadio('show_featured_section', 1, 0, trans("yes"), trans("no"), $generalSettings->show_featured_section, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_latest_posts_homepage"); ?></label>
                            <?= formRadio('show_latest_posts', 1, 0, trans("yes"), trans("no"), $generalSettings->show_latest_posts, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_news_ticker"); ?></label>
                            <?= formRadio('show_newsticker', 1, 0, trans("yes"), trans("no"), $generalSettings->show_newsticker, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_latest_posts_on_slider"); ?></label>
                            <?= formRadio('show_latest_posts_on_slider', 1, 0, trans("yes"), trans("no"), $generalSettings->show_latest_posts_on_slider, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_latest_posts_on_featured"); ?></label>
                            <?= formRadio('show_latest_posts_on_featured', 1, 0, trans("yes"), trans("no"), $generalSettings->show_latest_posts_on_featured, 'col-md-4'); ?>
                        </div>

                        <div class="form-group">
                            <label><?= trans("sort_slider_posts"); ?></label>
                            <?php if ($generalSettings->show_latest_posts_on_slider != 1): ?>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_slider_posts" value="by_slider_order" id="rd_sort_slider_posts_1" class="custom-control-input" <?= $generalSettings->sort_slider_posts == "by_slider_order" ? 'checked' : ''; ?>>
                                            <label for="rd_sort_slider_posts_1" class="custom-control-label"><?= trans("by_slider_order"); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_slider_posts" value="by_date" id="rd_sort_slider_posts_2" class="custom-control-input" <?= $generalSettings->sort_slider_posts == "by_date" ? 'checked' : ''; ?>>
                                            <label for="rd_sort_slider_posts_2" class="custom-control-label"><?= trans("by_date"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_slider_posts" value="by_date" id="rd_sort_slider_posts_2" class="custom-control-input" checked>
                                            <label for="rd_sort_slider_posts_2" class="custom-control-label"><?= trans("by_date"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label><?= trans("sort_featured_posts"); ?></label>
                            <?php if ($generalSettings->show_latest_posts_on_featured != 1): ?>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_featured_posts" value="by_featured_order" id="rd_sort_featured_posts_1" class="custom-control-input" <?= $generalSettings->sort_featured_posts == "by_featured_order" ? 'checked' : ''; ?>>
                                            <label for="rd_sort_featured_posts_1" class="custom-control-label"><?= trans("by_featured_order"); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_featured_posts" value="by_date" id="rd_sort_featured_posts_2" class="custom-control-input" <?= $generalSettings->sort_featured_posts == "by_date" ? 'checked' : ''; ?>>
                                            <label for="rd_sort_featured_posts_2" class="custom-control-label"><?= trans("by_date"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="sort_featured_posts" value="by_date" id="rd_sort_featured_posts_2" class="custom-control-input" checked>
                                            <label for="rd_sort_featured_posts_2" class="custom-control-label"><?= trans("by_date"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group text-right" style="margin-top: 60px;">
                            <button type="submit" name="submit" value="homepage" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                        </div>
                    </div>

                    <div class="tab-pane <?= $tab == "posts" ? 'active' : ''; ?>" id="tab_3">
                        <div class="form-group">
                            <label><?= trans("post_url_structure"); ?>&nbsp;<small class="text-muted">(<?= trans('post_url_structure_exp'); ?>)</small></label>
                            <?= formRadio('post_url_structure', 'slug', 'id', trans("post_url_structure_slug") . ' (domain.com/slug)', trans("post_url_structur_id") . ' (domain.com/id)', $generalSettings->post_url_structure, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("bulk_post_upload_for_authors"); ?></label>
                            <?= formRadio('bulk_post_upload_for_authors', 1, 0, trans("enable"), trans("disable"), $generalSettings->bulk_post_upload_for_authors, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("comment_system"); ?></label>
                            <?= formRadio('comment_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->comment_system, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("comment_approval_system"); ?></label>
                            <?= formRadio('comment_approval_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->comment_approval_system, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("emoji_reactions"); ?></label>
                            <?= formRadio('emoji_reactions', 1, 0, trans("enable"), trans("disable"), $generalSettings->emoji_reactions, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_post_author"); ?></label>
                            <?= formRadio('show_post_author', 1, 0, trans("yes"), trans("no"), $generalSettings->show_post_author, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_post_dates"); ?></label>
                            <?= formRadio('show_post_date', 1, 0, trans("yes"), trans("no"), $generalSettings->show_post_date, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("show_post_view_counts"); ?></label>
                            <?= formRadio('show_hits', 1, 0, trans("yes"), trans("no"), $generalSettings->show_hits, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("approve_added_user_posts"); ?></label>
                            <?= formRadio('approve_added_user_posts', 1, 0, trans("yes"), trans("no"), $generalSettings->approve_added_user_posts, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("approve_updated_user_posts"); ?></label>
                            <?= formRadio('approve_updated_user_posts', 1, 0, trans("yes"), trans("no"), $generalSettings->approve_updated_user_posts, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("redirect_rss_posts_to_original"); ?></label>
                            <?= formRadio('redirect_rss_posts_to_original', 1, 0, trans("yes"), trans("no"), $generalSettings->redirect_rss_posts_to_original, 'col-md-4'); ?>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <label class="control-label"><?= trans('pagination_number_posts'); ?></label>
                                </div>
                                <div class="col-sm-6 col-md-5 col-xs-12 col-option">
                                    <input type="number" class="form-control" name="pagination_per_page" value="<?= $generalSettings->pagination_per_page; ?>" min="1" max="3000" required style="max-width: 450px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right" style="margin-top: 60px;">
                            <button type="submit" name="submit" value="posts" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                        </div>
                    </div>

                    <div class="tab-pane <?= $tab == "post_formats" ? 'active' : ''; ?>" id="tab_4">
                        <div class="form-group">
                            <label><?= trans("article"); ?></label>
                            <?= formRadio('post_format_article', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_article, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("gallery"); ?></label>
                            <?= formRadio('post_format_gallery', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_gallery, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("sorted_list"); ?></label>
                            <?= formRadio('post_format_sorted_list', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_sorted_list, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("table_of_contents"); ?></label>
                            <?= formRadio('post_format_table_of_contents', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_table_of_contents, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("video"); ?></label>
                            <?= formRadio('post_format_video', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_video, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("audio"); ?></label>
                            <?= formRadio('post_format_audio', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_audio, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("trivia_quiz"); ?></label>
                            <?= formRadio('post_format_trivia_quiz', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_trivia_quiz, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("personality_quiz"); ?></label>
                            <?= formRadio('post_format_personality_quiz', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_personality_quiz, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("poll"); ?></label>
                            <?= formRadio('post_format_poll', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_poll, 'col-md-4'); ?>
                        </div>
                        <div class="form-group">
                            <label><?= trans("recipe"); ?></label>
                            <?= formRadio('post_format_recipe', 1, 0, trans("enable"), trans("disable"), $generalSettings->post_format_recipe, 'col-md-4'); ?>
                        </div>
                        <div class="form-group text-right" style="margin-top: 60px;">
                            <button type="submit" name="submit" value="post_formats" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= trans('ai_content_creator'); ?>&nbsp;(<?= trans("ai_writer"); ?>)</h3>
                    </div>
                    <form action="<?= base_url('Admin/aiWriterPost'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="box-body">
                            <?php $aiWriter = aiWriter(); ?>
                            <div class="form-group">
                                <label><?= trans("status"); ?></label>
                                <?= formRadio('status', 1, 0, trans("enable"), trans("disable"), $aiWriter->status); ?>
                            </div>
                            <div class="form-group">
                                <label><?= trans('api_key'); ?></label>
                                <input type="text" class="form-control" name="api_key" placeholder="<?= trans('api_key'); ?>" value="<?= esc($aiWriter->apiKey); ?>" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="submit" value="post_deletion" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                        </div>
                    </form>
                </div>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= trans('auto_post_deletion'); ?></h3>
                    </div>
                    <form action="<?= base_url('Admin/preferencesPost'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="box-body">
                            <div class="form-group">
                                <label><?= trans("status"); ?></label>
                                <?= formRadio('auto_post_deletion', 1, 0, trans("enable"), trans("disable"), $generalSettings->auto_post_deletion); ?>
                            </div>
                            <div class="form-group">
                                <label><?= trans('number_of_days'); ?>&nbsp;<small>(E.g. <?= trans("number_of_days_exp") ?>)</small></label>
                                <input type="number" class="form-control" name="auto_post_deletion_days" value="<?= $generalSettings->auto_post_deletion_days; ?>" min="1" max="99999999" required style="max-width: 600px;">
                            </div>
                            <div class="form-group">
                                <label><?= trans("posts"); ?></label>
                                <?= formRadio('auto_post_deletion_delete_all', 1, 0, trans("delete_all_posts"), trans("delete_only_rss_posts"), $generalSettings->auto_post_deletion_delete_all); ?>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="submit" value="post_deletion" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= trans("file_upload") ?></h3>
                    </div>
                    <form action="<?= base_url('Admin/fileUploadSettingsPost'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="box-body">
                            <div class="form-group">
                                <label><?= trans("image_file_format"); ?></label>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="image_file_format" value="JPG" id="rd_image_file_format_1" class="custom-control-input" <?= $generalSettings->image_file_format == 'JPG' ? 'checked' : ''; ?>>
                                            <label for="rd_image_file_format_1" class="custom-control-label">JPG</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="image_file_format" value="WEBP" id="rd_image_file_format_2" class="custom-control-input" <?= $generalSettings->image_file_format == 'WEBP' ? 'checked' : ''; ?>>
                                            <label for="rd_image_file_format_2" class="custom-control-label">WEBP</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="image_file_format" value="PNG" id="rd_image_file_format_3" class="custom-control-input" <?= $generalSettings->image_file_format == 'PNG' ? 'checked' : ''; ?>>
                                            <label for="rd_image_file_format_3" class="custom-control-label">PNG</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans('allowed_file_extensions'); ?></label>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input id="tag-input" type="text" name="allowed_file_extensions" value="<?= strReplace('"', '', $generalSettings->allowed_file_extensions); ?>" class="form-control tags-input"/>
                                        <small>(<?= trans('type_extension'); ?>&nbsp;E.g. zip, jpg, doc, pdf..)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="submit" value="post_deletion" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="<?= base_url("assets/admin/plugins/tagify/tagify.css") ?>">
<script src="<?= base_url("assets/admin/plugins/tagify/tagify.js") ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputElement = document.querySelector('#tag-input');
        const tagify = new Tagify(inputElement, {
            enforceWhitelist: false,
        });
    });
</script>