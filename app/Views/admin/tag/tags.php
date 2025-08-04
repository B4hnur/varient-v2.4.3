<div class="row">
    <div class="col-md-12 col-lg-10">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("tags"); ?></h3>
                </div>
                <div class="right">
                    <button type="button" class="btn btn-success btn-add-new" data-toggle="modal" data-target="#modalAddTag"><i class="fa fa-plus"></i><?= trans("add_tag"); ?></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <div class="row table-filter-container">
                                    <div class="col-sm-12">
                                        <form action="<?= adminUrl('tags'); ?>" method="get">
                                            <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                                <label><?= trans("show"); ?></label>
                                                <select name="show" class="form-control">
                                                    <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                                                    <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                                                    <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                                                    <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                                                </select>
                                            </div>
                                            <div class="item-table-filter">
                                                <label><?= trans("language"); ?></label>
                                                <select name="lang_id" class="form-control">
                                                    <option value=""><?= trans("all"); ?></option>
                                                    <?php foreach ($activeLanguages as $language): ?>
                                                        <option value="<?= $language->id; ?>" <?= inputGet('lang_id') == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="item-table-filter">
                                                <label><?= trans("search"); ?></label>
                                                <input name="q" class="form-control" placeholder="<?= trans("search") ?>" type="search" value="<?= esc(inputGet('q', true)); ?>">
                                            </div>
                                            <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                                <label style="display: block">&nbsp;</label>
                                                <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <thead>
                                <tr role="row">
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('tag'); ?></th>
                                    <th><?= trans('language'); ?></th>
                                    <th><?= trans('posts'); ?></th>
                                    <th class="max-width-120"><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($tags)):
                                    foreach ($tags as $tag): ?>
                                        <tr>
                                            <td><?= esc($tag->id); ?></td>
                                            <td style="width: 50%;">
                                                <span><?= esc($tag->tag); ?></span>
                                            </td>
                                            <td><?= esc($tag->lang_name); ?></td>
                                            <td><?= $tag->count; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-edit-tag" data-id="<?= $tag->id; ?>" data-tag="<?= esc($tag->tag); ?>" data-lang="<?= esc($tag->lang_id); ?>" data-toggle="modal" data-target="#modalEditTag"><?= trans("edit"); ?></button>
                                                    <button type="button" class="btn btn-default" onclick='deleteItem("Category/deleteTagPost","<?= $tag->id; ?>","<?= trans("confirm_item"); ?>");'><i class="fa fa-trash-o"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                            <?php if (empty($tags)): ?>
                                <p class="text-center text-muted"><?= trans("no_records_found"); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-12 text-right">
                        <?= $pager->links; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalAddTag" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 840px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans("add_tag"); ?></h4>
            </div>
            <form action="<?= base_url('Category/addTagPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" required>
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("tag"); ?></label>
                        <input type="text" name="tag" value="" class="form-control" placeholder="<?= trans("tag"); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("add_tag"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalEditTag" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 840px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans("edit"); ?></h4>
            </div>
            <form action="<?= base_url('Category/editTagPost'); ?>" method="post" id="formEditTag">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" required>
                            <?php foreach ($activeLanguages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= trans("tag"); ?></label>
                        <input type="text" name="tag" value="" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><?= trans("save_changes"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.btn-edit-tag', function () {
        let id = $(this).attr("data-id");
        let lang_id = $(this).attr("data-lang");
        let tag = $(this).attr("data-tag");

        $("#formEditTag input[name='id']").val(id);
        $("#formEditTag select[name='lang_id']").val(lang_id);
        $("#formEditTag input[name='tag']").val(tag);
    });
</script>