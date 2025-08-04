<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('post_details'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <input type="hidden" name="id" value="<?= esc($post->id); ?>">
        <input type="hidden" name="back_url" class="form-control" value="<?= esc(inputGet('back_url')); ?>">

        <div class="form-group">
            <label class="control-label"><?= trans('title'); ?></label>
            <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= esc($post->title); ?>" required>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('slug'); ?>
                <small>(<?= trans('slug_exp'); ?>)</small>
            </label>
            <input type="text" class="form-control" name="slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($post->slug); ?>">
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
            <textarea class="form-control text-area" name="summary" placeholder="<?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)"><?= esc($post->summary); ?></textarea>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
            <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($post->keywords); ?>">
        </div>

        <?php if ($post->post_type == 'poll'): ?>
            <div class="form-group">
                <label><?= trans("vote_permission"); ?></label>
                <?= formRadio('vote_permission', 'registered', 'all', trans("registered_users_can_vote"), trans("all_users_can_vote"), !empty($post->is_poll_public) ? 'all' : 'registered', 'col-md-4'); ?>
            </div>
        <?php endif; ?>

        <?php if (hasPermission('manage_all_posts')): ?>
            <div class="form-group">
                <label><?= trans("visibility"); ?></label>
                <?= formRadio('visibility', 1, 0, trans("show"), trans("hide"), $post->visibility, 'col-md-4'); ?>
            </div>
        <?php else:
            if ($generalSettings->approve_updated_user_posts == 1): ?>
                <input type="hidden" name="visibility" value="0">
            <?php else: ?>
                <input type="hidden" name="visibility" value="1">
            <?php endif;
        endif; ?>

        <?php if ($activeTheme->theme == 'classic'): ?>
            <div class="form-group">
                <label><?= trans("show_right_column"); ?></label>
                <?= formRadio('show_right_column', 1, 0, trans("yes"), trans("no"), $post->show_right_column, 'col-md-4'); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="show_right_column" value="<?= $post->show_right_column; ?>">
        <?php endif; ?>

        <?php if (hasPermission('manage_all_posts')): ?>
            <div class="form-group">
                <?= formCheckbox('is_slider', 1, trans("add_slider"), $isPostSlider); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_slider" value="<?= $isPostSlider; ?>">
        <?php endif;
        if (hasPermission('manage_all_posts')): ?>
            <div class="form-group">
                <?= formCheckbox('is_featured', 1, trans("add_featured"), $isPostFeatured); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_featured" value="<?= $isPostFeatured; ?>">
        <?php endif;
        if (hasPermission('manage_all_posts')): ?>
            <div class="form-group">
                <?= formCheckbox('is_breaking', 1, trans("add_breaking"), $isPostBreaking); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_breaking" value="<?= $isPostBreaking; ?>">
        <?php endif;
        if (hasPermission('manage_all_posts')): ?>
            <div class="form-group">
                <?= formCheckbox('is_recommended', 1, trans("add_recommended"), $isPostRecommended); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_recommended" value="<?= $isPostRecommended; ?>">
        <?php endif; ?>

        <div class="form-group">
            <?= formCheckbox('need_auth', 1, trans("show_only_registered"), $post->need_auth); ?>
        </div>

        <?php if ($post->post_type == 'sorted_list' || $post->post_type == 'gallery'): ?>
            <div class="form-group">
                <?= formCheckbox('show_item_numbers', 1, trans("show_item_numbers"), $post->show_item_numbers); ?>
            </div>
        <?php endif; ?>

        <div class="form-group m-t-30">
            <?= view("admin/post/_tags_input"); ?>
        </div>

        <div class="form-group row-optional-url">
            <label class="control-label"><?= trans('optional_url'); ?></label>
            <input type="text" class="form-control" name="optional_url" placeholder="<?= trans('optional_url'); ?>" value="<?= esc($post->optional_url); ?>">
        </div>

        <?php if ($post->post_type == 'table_of_contents'): ?>

            <p class="m-t-30" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                <strong class="font-weight-600"><?= trans("link_list_style"); ?></strong>
            </p>

            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <div class="form-group">
                        <label><?= trans("level_1"); ?></label>
                        <select name="link_list_style_1" class="form-control" required>
                            <?php foreach (getCssListStyles() as $style): ?>
                                <option value="<?= $style; ?>" <?= $style == getPostListStyle($post, 1)->style ? 'selected' : ''; ?>><?= $style; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= formCheckbox('link_list_style_show_1', 1, trans("show_list_style_post_text"), getPostListStyle($post, 1)->status); ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="form-group">
                        <label><?= trans("level_2"); ?></label>
                        <select name="link_list_style_2" class="form-control" required>
                            <?php foreach (getCssListStyles() as $style): ?>
                                <option value="<?= $style; ?>" <?= $style == getPostListStyle($post, 2)->style ? 'selected' : ''; ?>><?= $style; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= formCheckbox('link_list_style_show_2', 1, trans("show_list_style_post_text"), getPostListStyle($post, 2)->status); ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="form-group">
                        <label><?= trans("level_3"); ?></label>
                        <select name="link_list_style_3" class="form-control" required>
                            <?php foreach (getCssListStyles() as $style): ?>
                                <option value="<?= $style; ?>" <?= $style == getPostListStyle($post, 3)->style ? 'selected' : ''; ?>><?= $style; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <?= formCheckbox('link_list_style_show_3', 1, trans("show_list_style_post_text"), getPostListStyle($post, 3)->status); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php $dataRecipe = null;
        if ($post->post_type == 'recipe'):
            $dataRecipe = unserializeData($post->post_data); ?>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?= trans('prep_time'); ?>&nbsp;<small>(<?= trans('minutes'); ?>)</small></label>
                        <input type="number" class="form-control" name="prep_time" value="<?= !empty($dataRecipe) && !empty($dataRecipe['prep_time']) ? $dataRecipe['prep_time'] : ''; ?>" min="0" max="999999" placeholder="<?= trans('prep_time'); ?>">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?= trans('cook_time'); ?>&nbsp;<small>(<?= trans('minutes'); ?>)</small></label>
                        <input type="number" class="form-control" name="cook_time" value="<?= !empty($dataRecipe) && !empty($dataRecipe['cook_time']) ? $dataRecipe['cook_time'] : ''; ?>" min="0" max="999999" placeholder="<?= trans('cook_time'); ?>">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?= trans('serving'); ?></label>
                        <input type="number" class="form-control" name="serving" value="<?= !empty($dataRecipe) && !empty($dataRecipe['serving']) ? $dataRecipe['serving'] : ''; ?>" min="0" max="999999" placeholder="<?= trans('serving'); ?>">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?= trans('difficulty'); ?></label>
                        <?php $difficulty = !empty($dataRecipe) && !empty($dataRecipe['difficulty']) ? $dataRecipe['difficulty'] : 0; ?>
                        <select name="difficulty" class="form-control">
                            <option value="1" <?= $difficulty == 1 ? 'selected' : ''; ?>><?= trans("easy"); ?></option>
                            <option value="2" <?= $difficulty == 2 ? 'selected' : ''; ?>><?= trans("intermediate"); ?></option>
                            <option value="3" <?= $difficulty == 3 ? 'selected' : ''; ?>><?= trans("advanced"); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if ($post->post_type == 'recipe'): ?>
    <div class="row">
        <div class="col-sm-12 m-b-30">
            <div class="box">
                <div class="box-header with-border">
                    <div class="left">
                        <h3 class="box-title"><?= trans("info_about_recipe"); ?></h3>
                    </div>
                </div>
                <div class="box-body">
                    <div id="main_editor_recipe">
                        <div class="row">
                            <div class="col-sm-12 editor-buttons">
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#file_manager_image" data-image-type="list_item_editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                <?php if ($baseAIWriter->status == 1 && hasPermission('ai_writer')): ?>
                                    <button type="button" class="btn btn-md btn-default btn-open-ai-writer" data-toggle="modal" data-target="#modalAiWriter" onclick="setAIWriterEditorId('editor_recipe_info');"><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;<?= trans("ai_writer"); ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <textarea id="editor_recipe_info" class="tinyMCE form-control" name="recipe_info"><?= $post->recipe_info; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="box">
                <div class="box-body">
                    <label class="control-label"><?= trans("ingredients"); ?></label>
                    <div id="content_ingredients">
                        <?php if (!empty($dataRecipe) && !empty($dataRecipe['ingredients'])):
                            foreach ($dataRecipe['ingredients'] as $itemIngredient):
                                $uniqId = uniqid(); ?>
                                <div id="itemIngredient<?= $uniqId; ?>" class="item-ingredient m-b-5">
                                    <input type="text" name="ingredients[]" value="<?= esc($itemIngredient); ?>" class="form-control" placeholder="<?= trans("ingredient"); ?>">
                                    <button type="button" data-id="<?= $uniqId; ?>" class="btn btn-sm btn-default btnDeleteIngredient"><i class="fa fa-times"></i></button>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" id="btnAddIngredient" class="btn btn-sm btn-success"><i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?= trans("add_new"); ?></button>
                    <div class="m-t-15"><small class="small-title"><?= trans("ingredient_ex"); ?></small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="box">
                <div class="box-body">
                    <label class="control-label"><?= trans("nutritional_information"); ?></label>
                    <div id="content_nutritional">
                        <?php if (!empty($dataRecipe) && !empty($dataRecipe['nInfo'])):
                            foreach ($dataRecipe['nInfo'] as $itemInfo):
                                $uniqId = uniqid(); ?>
                                <div id="itemNutritional<?= $uniqId; ?>" class="item-ingredient m-b-5">
                                    <input type="text" name="nutritional_name_<?= $uniqId; ?>" value="<?= !empty($itemInfo) && !empty($itemInfo['n']) ? $itemInfo['n'] : ''; ?>" class="form-control" placeholder="<?= trans("name"); ?>">
                                    <input type="text" name="nutritional_value_<?= $uniqId; ?>" value="<?= !empty($itemInfo) && !empty($itemInfo['v']) ? $itemInfo['v'] : ''; ?>" class="form-control" placeholder="<?= trans("value"); ?>">
                                    <input type="hidden" name="nutritional_id[]" value="<?= $uniqId; ?>">
                                    <button type="button" data-id="<?= $uniqId; ?>" class="btn btn-sm btn-default btnDeleteNutritional"><i class="fa fa-times"></i></button>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" id="btnAddNutritional" class="btn btn-sm btn-success"><i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?= trans("add_new"); ?></button>
                    <div class="m-t-15"><small class="small-title"><?= trans("nutritional_ex"); ?></small></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    var postType = "<?= $post->post_type; ?>";
    var textSelectResult = "<?= trans("select_a_result"); ?>";

    $(document).on('click', '#btnAddIngredient', function () {
        var uniqueId = Date.now() + '_' + Math.floor(Math.random() * 1000);
        $('#content_ingredients').append('<div id="itemIngredient' + uniqueId + '" class="item-ingredient m-b-5"><input type="text" name="ingredients[]" class="form-control" placeholder="<?= trans("ingredient"); ?>"><button type="button" data-id="' + uniqueId + '" class="btn btn-sm btn-default btnDeleteIngredient"><i class="fa fa-times"></i></button></div>');
    });
    $(document).on('click', '#btnAddNutritional', function () {
        var uniqueId = Date.now() + '_' + Math.floor(Math.random() * 1000);
        $('#content_nutritional').append('<div id="itemNutritional' + uniqueId + '" class="item-ingredient m-b-5"><input type="text" name="nutritional_name_' + uniqueId + '" class="form-control" placeholder="<?= trans("name"); ?>"><input type="text" name="nutritional_value_' + uniqueId + '" class="form-control" placeholder="<?= trans("value"); ?>"><input type="hidden" name="nutritional_id[]" value="' + uniqueId + '"><button type="button" data-id="' + uniqueId + '" class="btn btn-sm btn-default btnDeleteNutritional"><i class="fa fa-times"></i></button></div>');
    });
    $(document).on('click', '.btnDeleteIngredient', function () {
        var dataId = $(this).attr("data-id");
        $('#itemIngredient' + dataId).remove();
    });
    $(document).on('click', '.btnDeleteNutritional', function () {
        var dataId = $(this).attr("data-id");
        $('#itemNutritional' + dataId).remove();
    });
</script>