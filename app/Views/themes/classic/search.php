<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 page-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                    <li class="breadcrumb-item active"><?= trans("search") . ": " . esc(characterLimiter($q, 150, '...')); ?></li>
                </ol>
            </div>
            <div id="content" class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="page-title"><span> <?= trans("search"); ?>:</span>&nbsp;<strong><?= esc($q); ?></strong></h1>
                    </div>
                    <div id="postsLoadMoreContent">
                        <?php $count = 0;
                        if (!empty($posts)):
                            foreach ($posts as $post):
                                if ($count < $postsPerPage):
                                    if ($count != 0 && $count % 2 == 0): ?>
                                        <div class="col-sm-12"></div>
                                    <?php endif; ?>
                                    <div class="col-sm-6 col-xs-12">
                                        <?= loadView("post/_post_item", ["post" => $post, 'showLabel' => true]); ?>
                                    </div>
                                    <?php
                                    if ($count == 1):
                                        echo loadView('partials/_ad_spaces', ['adSpace' => 'posts_top', 'class' => 'p-b-30']);
                                    endif;
                                endif;
                                $count++;
                            endforeach;
                        else: ?>
                            <p class="text-center text-muted">
                                <?= trans("search_noresult"); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?= loadView('partials/_ad_spaces', ['adSpace' => 'posts_bottom', 'class' => '']); ?>
                    <div class="col-sm-12 col-xs-12">
                        <div id="load_posts_spinner" class="col-sm-12 col-xs-12 load-more-spinner">
                            <div class="row">
                                <div class="spinner">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                            </div>
                        </div>
                        <?php if (countItems($posts) > $postsPerPage): ?>
                            <div class="col-sm-12 col-xs-12">
                                <div class="row">
                                    <button class="btn-load-more" onclick="loadMorePosts(<?= $activeLang->id; ?>, 'search');"><i class="icon-refresh"></i>&nbsp;<?= trans("load_more"); ?></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="sidebar" class="col-sm-4">
                <?= loadView('partials/_sidebar'); ?>
            </div>
        </div>
    </div>
</div>