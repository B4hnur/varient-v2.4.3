<?php $editorId = uniqid(); ?>

<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= !empty($textEditorLabel) ? $textEditorLabel : trans('content'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div id="main_editor">
            <div class="row">
                <div class="col-sm-12 editor-buttons">
                    <button type="button" class="btn btn-md btn-default" data-toggle="modal" data-target="#file_manager_image" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                    <?php if ($baseAIWriter->status == 1 && hasPermission('ai_writer')): ?>
                        <button type="button" class="btn btn-md btn-default btn-open-ai-writer" data-toggle="modal" data-target="#modalAiWriter" onclick="setAIWriterEditorId('editor-<?= $editorId; ?>');"><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;<?= trans("ai_writer"); ?></button>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($post)): ?>
                <textarea id="editor-<?= $editorId; ?>" class="tinyMCE form-control" name="content"><?= $post->content; ?></textarea>
            <?php else: ?>
                <textarea id="editor-<?= $editorId; ?>" class="tinyMCE form-control" name="content"><?= old('content'); ?></textarea>
            <?php endif; ?>
        </div>
    </div>
</div>