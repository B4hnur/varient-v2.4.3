<div class="row">
    <div class="col-sm-12">
        <form action="<?= base_url('Post/editPostPost'); ?>" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
            <?= csrf_field(); ?>
            <input type="hidden" name="post_type" value="recipe">
            <div class="row">
                <div class="col-sm-12 form-header">
                    <h1 class="form-title"><?= trans('update_recipe'); ?></h1>
                    <a href="<?= adminUrl('posts'); ?>" class="btn btn-success btn-add-new pull-right">
                        <i class="fa fa-bars"></i>
                        <?= trans('posts'); ?>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-post">
                        <div class="form-post-left">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= view("admin/post/_form_edit_post_left"); ?>
                                </div>
                            </div>
                            <?= view("admin/post/_post_list_items", ['title' => trans('directions'), 'postListItems' => $postListItems, 'postType' => 'recipe']); ?>
                        </div>
                        <div class="form-post-right">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= view('admin/post/_upload_image_box'); ?>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="left">
                                                <h3 class="box-title"><?= trans('recipe_video'); ?></h3>
                                            </div>
                                        </div>
                                        <div class="box-body box-recipe-video">
                                            <?= view('admin/post/_upload_video_box', ['videoUploadPostType' => 'recipe']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <?= view('admin/post/_upload_additional_image_box'); ?>
                                </div>
                                <div class="col-sm-12">
                                    <?= view('admin/post/_upload_file_box'); ?>
                                </div>
                                <div class="col-sm-12">
                                    <?= view('admin/post/_post_owner_box'); ?>
                                </div>
                                <div class="col-sm-12">
                                    <?= view('admin/post/_categories_box'); ?>
                                </div>
                                <div class="col-sm-12">
                                    <?= view('admin/post/_publish_box', ['postType' => 'recipe']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadRecipeImages' => true, 'loadQuizImages' => false, 'loadFiles' => true, 'loadVideos' => true, 'loadAudios' => false]); ?>
