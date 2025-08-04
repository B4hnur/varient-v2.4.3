<?php if (empty($newItemOrder)):
    $newItemOrder = 1;
endif;
$showEditorButtons = true;
$showImageDesc = true;
$idFileManager = 'file_manager_image';
$dataImageType = 'list_item';
if (!empty($postType) && $postType == 'recipe') {
    $showEditorButtons = false;
    $showImageDesc = false;
    $idFileManager = 'file_manager_recipe_image';
    $dataImageType = 'recipe';
}
if (!empty($postListItem)): ?>
    <div id="panel_list_item_<?= $postListItem->id; ?>" class="panel panel-default panel-list-item" data-list-item-id="<?= $postListItem->id; ?>">
        <div class="panel-heading">
            <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_<?= $postListItem->id; ?>">
                #<span id="list_item_order_<?= $postListItem->id; ?>"><?= $postListItem->item_order; ?></span>&nbsp;&nbsp;<span id="list_item_title_<?= $postListItem->id; ?>"><?= esc($postListItem->title); ?></span>
            </h4>
            <div class="btn-group btn-group-post-list-option" role="group">
                <input type="number" name="list_item_order_<?= $postListItem->id; ?>" class="input_list_item_order" data-list-item-id="<?= $postListItem->id; ?>" value="<?= $postListItem->item_order; ?>"
                       placeholder="<?= trans("order_1"); ?>" min="1" max="99999999">
                <button type="button" class="btn btn-default" onclick="deletePostListItemDatabase('<?= $postListItem->id; ?>','<?= $postListItem->item_post_type; ?>', '<?= clrQuotes(trans("confirm_item")); ?>');"><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <div id="collapse_<?= $postListItem->id; ?>" class="panel-collapse collapse <?= empty($postListItem->title) ? 'in' : ''; ?>">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label"><?= trans("title"); ?></label>
                            <input type="text" class="form-control input-post-list-item-title" data-title-id="list_item_title_<?= $postListItem->id; ?>" name="list_item_title_<?= $postListItem->id ?>" placeholder="<?= trans("title"); ?>" value="<?= esc($postListItem->title); ?>">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="list-item-description">
                            <div class="left">
                                <label class="control-label"><?= trans("image"); ?></label>

                                <div id="post_list_item_image_container_<?= $postListItem->id; ?>" class="m-b-5">
                                    <div class="list-item-image-container">
                                        <input type="hidden" name="list_item_image_<?= $postListItem->id; ?>" value="<?= $postListItem->image; ?>">
                                        <input type="hidden" name="list_item_image_large_<?= $postListItem->id; ?>" value="<?= $postListItem->image_large; ?>">
                                        <input type="hidden" name="list_item_image_storage_<?= $postListItem->id; ?>" value="<?= $postListItem->storage; ?>">
                                        <?php if (!empty($postListItem->image)):
                                            $imgBaseURL = getBaseURLByStorage($postListItem->storage); ?>
                                            <img src="<?= $imgBaseURL . $postListItem->image; ?>" alt="">
                                            <a class="btn btn-danger btn-sm btn-delete-selected-file-image btn-delete-selected-list-item-image" data-image-type="<?= $dataImageType; ?>" data-list-item-id="<?= $postListItem->id; ?>" data-is-update="1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class='btn-select-image' data-toggle="modal" data-target="#<?= $idFileManager; ?>" data-image-type="<?= $dataImageType; ?>" data-list-item-id="<?= $postListItem->id; ?>" data-is-update="1">
                                                <div class="btn-select-image-inner">
                                                    <i class="fa fa-image"></i>
                                                    <button class="btn"><?= trans("select_image"); ?></button>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($showImageDesc): ?>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="list_item_image_description_<?= $postListItem->id; ?>" value="<?= esc($postListItem->image_description); ?>" placeholder="<?= trans("image_description"); ?>" maxlength="300">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="right">
                                <label class="control-label"><?= trans("content"); ?></label>
                                <?php if ($showEditorButtons): ?>
                                    <div class="row">
                                        <div class="col-sm-12 editor-buttons">
                                            <button type="button" class="btn btn-md btn-default" data-toggle="modal" data-target="#<?= $idFileManager; ?>" data-image-type="list_item_editor" data-editor-id="<?= $postListItem->id; ?>"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                            <?php if ($baseAIWriter->status == 1 && hasPermission('ai_writer')): ?>
                                                <button type="button" class="btn btn-md btn-default btn-open-ai-writer" data-toggle="modal" data-target="#modalAiWriter" onclick="setAIWriterEditorId('editor_<?= $postListItem->id; ?>');"><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;<?= trans("ai_writer"); ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <textarea id="editor_<?= $postListItem->id; ?>" class="tinyMCEQuiz form-control" name="list_item_content_<?= $postListItem->id; ?>"><?= $postListItem->content; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <?php if ($postListItem->item_post_type == 'table_of_contents'): ?>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"><?= trans("parent_link"); ?></label>
                                <input type="number" class="form-control" name="parent_link_num_<?= $postListItem->id ?>" min="1" max="99999" placeholder="<?= trans("example"); ?>: 1" value="<?= empty($postListItem->parent_link_num) ? '' : esc($postListItem->parent_link_num); ?>" style="max-width:245px;">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else:
    $uniqueID = uniqid(); ?>
    <div id="panel_list_item_<?= $uniqueID; ?>" class="panel panel-default panel-list-item" data-list-item-id="<?= $uniqueID; ?>">
        <div class="panel-heading">
            <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_<?= $uniqueID; ?>">
                #<span id="list_item_order_<?= $uniqueID; ?>"><?= $newItemOrder; ?></span>&nbsp;&nbsp;<span id="list_item_title_<?= $uniqueID; ?>"></span>
            </h4>
            <div class="btn-group btn-group-post-list-option" role="group">
                <input type="number" name="list_item_order[]" class="input_list_item_order" data-list-item-id="<?= $uniqueID; ?>" value="<?= $newItemOrder; ?>" placeholder="<?= trans("order_1"); ?>" min="1" max="99999999">
                <button type="button" class="btn btn-default" onclick="deletePostListItem('<?= $uniqueID; ?>','<?= trans("confirm_item"); ?>');"><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <div id="collapse_<?= $uniqueID; ?>" class="panel-collapse collapse in">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label"><?= trans("title"); ?></label>
                            <input type="text" class="form-control input-post-list-item-title" data-title-id="list_item_title_<?= $uniqueID; ?>" name="list_item_title[]" placeholder="<?= trans("title"); ?>" value="">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="list-item-description">
                            <div class="left">
                                <label class="control-label"><?= trans("image"); ?></label>
                                <div id="post_list_item_image_container_<?= $uniqueID; ?>" class="m-b-5">
                                    <div class="list-item-image-container">
                                        <input type="hidden" name="list_item_image[]" value="">
                                        <input type="hidden" name="list_item_image_large[]" value="">
                                        <input type="hidden" name="list_item_image_storage[]" value="">
                                        <a class='btn-select-image' data-toggle="modal" data-target="#<?= $idFileManager; ?>" data-image-type="<?= $dataImageType; ?>" data-list-item-id="<?= $uniqueID; ?>" data-is-update="0">
                                            <div class="btn-select-image-inner">
                                                <i class="fa fa-image"></i>
                                                <button class="btn"><?= trans("select_image"); ?></button>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php if ($showImageDesc): ?>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="list_item_image_description[]" placeholder="<?= trans("image_description"); ?>" maxlength="300">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="right">
                                <label class="control-label"><?= trans("content"); ?></label>
                                <?php if ($showEditorButtons): ?>
                                    <div class="row">
                                        <div class="col-sm-12 editor-buttons">
                                            <button type="button" class="btn btn-md btn-default" data-toggle="modal" data-target="#<?= $idFileManager; ?>" data-image-type="list_item_editor" data-editor-id="<?= $uniqueID; ?>"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                            <?php if ($baseAIWriter->status == 1 && hasPermission('ai_writer')): ?>
                                                <button type="button" class="btn btn-md btn-default btn-open-ai-writer" data-toggle="modal" data-target="#modalAiWriter" onclick="setAIWriterEditorId('editor_<?= $uniqueID; ?>');"><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;<?= trans("ai_writer"); ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <textarea id="editor_<?= $uniqueID; ?>" class="tinyMCEQuiz form-control" name="list_item_content[]"></textarea>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($postType) && $postType == 'table_of_contents'): ?>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"><?= trans("parent_link"); ?></label>
                                <input type="number" class="form-control" name="parent_link_num[]" min="1" max="99999" placeholder="<?= trans("example"); ?>: 1" style="max-width:245px;">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>