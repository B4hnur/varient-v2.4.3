<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= $title; ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('post-format'); ?>" class="btn btn-success btn-add-new pull-right">
                <i class="fa fa-plus"></i>
                <?= trans('add_post'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <?= view('admin/post/_filter_posts'); ?>
                        <thead>
                        <tr role="row">
                            <?php if (hasPermission('manage_all_posts')): ?>
                                <th width="20"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                            <?php endif; ?>
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('post'); ?></th>
                            <th><?= trans('language'); ?></th>
                            <th><?= trans('post_type'); ?></th>
                            <th><?= trans('category'); ?></th>
                            <th><?= trans('author'); ?></th>
                            <th><?= trans('date_added'); ?></th>
                            <th><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($posts)):
                            foreach ($posts as $item):
                                $language = getLanguage($item->lang_id);
                                $postURL = '';
                                if (!empty($language)) {
                                    $postURL = generateBaseURLByLang($language) . 'preview/' . $item->slug;
                                } ?>
                                <tr>
                                    <?php if (hasPermission('manage_all_posts')): ?>
                                        <td><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                    <?php endif; ?>
                                    <td><?= esc($item->id); ?></td>
                                    <td>
                                        <div class="td-post-item">
                                            <div class="post-image">
                                                <a href="<?= $postURL; ?>" target="_blank">
                                                    <div class="image">
                                                        <img src="<?= IMG_BASE64_1x1; ?>" data-src="<?= getPostImage($item, "small"); ?>" alt="" class="lazyload img-responsive"/>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="post-title">
                                                <a href="<?= $postURL; ?>" target="_blank">
                                                    <?= esc($item->title); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= !empty($language) ? esc($language->name) : ''; ?></td>
                                    <td class="td-post-type"><?= trans($item->post_type); ?></td>
                                    <td>
                                        <?php $categories = getParentCategoryTree($item->category_id, $baseCategories);
                                        if (!empty($categories)):
                                            foreach ($categories as $category):
                                                if (!empty($category)): ?>
                                                    <label class="category-label m-r-5 label-table" style="background-color: <?= esc($category->color); ?>!important;">
                                                        <?= esc($category->name); ?>
                                                    </label>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                    </td>
                                    <td>
                                        <?php $author = getUserById($item->user_id);
                                        if (!empty($author)): ?>
                                            <a href="<?= generateProfileURL($author->slug); ?>" target="_blank" class="table-user-link">
                                                <strong><?= esc($author->username); ?></strong>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $item->created_at; ?>
                                        <?php if (!empty($item->updated_at)): ?>
                                            <div class="text-muted m-t-5">
                                                <small class="font-600"><?= trans("edited"); ?>:&nbsp;<?= timeAgo($item->updated_at); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="width: 180px;">
                                        <form action="<?= base_url('Post/postOptionsPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $item->id; ?>">
                                            <input type="hidden" name="back_url" value="<?= currentFullURL(); ?>">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_an_option'); ?>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <?php if (hasPermission('manage_all_posts')): ?>
                                                        <li>
                                                            <button type="submit" name="option" value="approve" class="btn-list-button">
                                                                <i class="fa fa-check option-icon"></i><?= trans('approve'); ?>
                                                            </button>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a href="<?= adminUrl('edit-post/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="deleteItem('Post/deletePost','<?= $item->id; ?>','<?= clrQuotes(trans("confirm_post")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($posts)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                    <div class="col-sm-12 table-ft">
                        <div class="row">
                            <div class="pull-right">
                                <?= $pager->links; ?>
                            </div>
                            <?php if (!empty($posts) && countItems($posts) > 0): ?>
                                <div class="pull-left bulk-options">
                                    <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectePosts('<?= clrQuotes(trans("confirm_posts")); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></button>
                                    <button class="btn btn-sm btn-default btn-table-delete" onclick="postBulkOptions('approve');"><i class="fa fa-check option-icon"></i><?= trans('approve'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>