<?php $aiModels = \Config\AIWriter::$models;
$formDefaults = \Config\AIWriter::$formDefaults;
$aiToneDefault = !empty($formDefaults) && !empty($formDefaults['tone']) ? $formDefaults['tone'] : '';
$aiLengthDefault = !empty($formDefaults) && !empty($formDefaults['length']) ? $formDefaults['length'] : ''; ?>
<div id="modalAiWriter" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 840px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans("ai_writer"); ?></h4>
            </div>
            <form id="formAIWriter">
                <input type="hidden" name="editor_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?= trans("model"); ?></label>
                                <select name="model" class="form-control" required>
                                    <?php if (!empty($aiModels)):
                                        foreach ($aiModels as $key => $value): ?>
                                            <option value="<?= esc($key); ?>" <?= !empty($formDefaults) && !empty($formDefaults['model']) && $formDefaults['model'] == $key ? 'selected' : '' ?>><?= esc($value); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label><?= trans('temperature_response_diversity'); ?></label>
                                <input type="number" class="form-control" name="temperature" step="0.1" min="0" max="1" value="<?= !empty($formDefaults) && !empty($formDefaults['temperature']) ? $formDefaults['temperature'] : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?= trans("tone_style"); ?></label>
                                <select name="tone" class="form-control" required>
                                    <option value="academic" <?= $aiToneDefault == "academic" ? 'selected' : '' ?>><?= trans("tone_academic"); ?></option>
                                    <option value="casual" <?= $aiToneDefault == "casual" ? 'selected' : '' ?>><?= trans("tone_casual"); ?></option>
                                    <option value="critical" <?= $aiToneDefault == "critical" ? 'selected' : '' ?>><?= trans("tone_critical"); ?></option>
                                    <option value="formal" <?= $aiToneDefault == "formal" ? 'selected' : '' ?>><?= trans("tone_formal"); ?></option>
                                    <option value="humorous" <?= $aiToneDefault == "humorous" ? 'selected' : '' ?>><?= trans("tone_humorous"); ?></option>
                                    <option value="inspirational" <?= $aiToneDefault == "inspirational" ? 'selected' : '' ?>><?= trans("tone_inspirational"); ?></option>
                                    <option value="persuasive" <?= $aiToneDefault == "persuasive" ? 'selected' : '' ?>><?= trans("tone_persuasive"); ?></option>
                                    <option value="professional" <?= $aiToneDefault == "professional" ? 'selected' : '' ?>><?= trans("tone_professional"); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label"><?= trans("length_of_text"); ?></label>
                                <select name="length" class="form-control" required>
                                    <option value="very_short" <?= $aiLengthDefault == "very_short" ? 'selected' : '' ?>><?= trans("very_short"); ?></option>
                                    <option value="short" <?= $aiLengthDefault == "short" ? 'selected' : '' ?>><?= trans("short"); ?></option>
                                    <option value="medium" <?= $aiLengthDefault == "medium" ? 'selected' : '' ?>><?= trans("medium"); ?></option>
                                    <option value="long" <?= $aiLengthDefault == "long" ? 'selected' : '' ?>><?= trans("long"); ?></option>
                                    <option value="very_long" <?= $aiLengthDefault == "very_long" ? 'selected' : '' ?>><?= trans("very_long"); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"><?= trans("topic"); ?></label>
                                <textarea name="topic" class="form-control" style="min-height: 50px;" placeholder="<?= trans("enter_topic"); ?>"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="spinnerAIWriter" class="spinner-ai-writer" style="display: none">
                        <p class="text-center"><?= trans("generating_text"); ?></p>
                        <div class="spinner" style="margin-top: 15px;">
                            <div class="bounce1"></div>
                            <div class="bounce2"></div>
                            <div class="bounce3"></div>
                        </div>
                    </div>
                    <div id="generatedContentAIWriter" class="article-content ai-generated-text"></div>
                </div>
                <div class="modal-footer buttons-ai-writer" style="text-align: center;">
                    <button type="button" id="btnAIUseText" class="btn btn-primary" style="display: none"><i class="fa fa-copy"></i>&nbsp;&nbsp;<?= trans("use_text"); ?></button>
                    <button type="submit" id="btnAIGenerate" class="btn btn-success"><i class="fa fa-magic"></i>&nbsp;&nbsp;<?= trans("generate_text"); ?></button>
                    <button type="submit" id="btnAIRegenerate" class="btn btn-success" style="display: none"><i class="fa fa-refresh"></i>&nbsp;&nbsp;<?= trans("regenerate"); ?></button>
                    <button type="button" id="btnAIReset" class="btn btn-default" onclick="resetFormAIWriter();" style="display: none"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?= trans("reset"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>