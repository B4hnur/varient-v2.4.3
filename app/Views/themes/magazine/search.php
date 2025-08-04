<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                    <li class="breadcrumb-item active"><?= trans("search"); ?></li>
                </ol>
            </nav>
            <h1 class="page-title"><?= trans("search"); ?>:&nbsp;<span class="search-text"><?= esc($q); ?></span></h1>
            <div class="col-sm-12 col-md-12 col-lg-8">
                <div id="postsLoadMoreContent" class="row">
                    <?php $i = 0;
                    if (!empty($posts)):
                        foreach ($posts as $item):
                            if ($i < $postsPerPage):
                                if ($i == 2):
                                    echo loadView('partials/_ad_spaces', ['adSpace' => 'posts_top', 'class' => 'mb-4']);
                                endif; ?>
                                <div class="col-sm-12 col-md-6">
                                    <?= loadView("post/_post_item", ['postItem' => $item, 'showLabel' => true]); ?>
                                </div>
                            <?php endif;
                            $i++;
                        endforeach;
                    else:?>
                        <p class="text-center text-muted">
                            <?= trans("search_noresult"); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?= loadView('partials/_ad_spaces', ['adSpace' => 'posts_bottom', 'class' => '']); ?>
                <?php if (countItems($posts) > $postsPerPage): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <button class="btn btn-custom btn-lg btn-load-more" onclick="loadMorePosts(<?= $activeLang->id; ?>, 'search');">
                            <?= trans("load_more"); ?>
                            <svg width="16" height="16" viewBox="0 0 1792 1792" fill="#ffffff" class="m-l-5" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1664 256v448q0 26-19 45t-45 19h-448q-42 0-59-40-17-39 14-69l138-138q-148-137-349-137-104 0-198.5 40.5t-163.5 109.5-109.5 163.5-40.5 198.5 40.5 198.5 109.5 163.5 163.5 109.5 198.5 40.5q119 0 225-52t179-147q7-10 23-12 15 0 25 9l137 138q9 8 9.5 20.5t-7.5 22.5q-109 132-264 204.5t-327 72.5q-156 0-298-61t-245-164-164-245-61-298 61-298 164-245 245-164 298-61q147 0 284.5 55.5t244.5 156.5l130-129q29-31 70-14 39 17 39 59z"/>
                            </svg>
                            <span class="spinner-border spinner-border-sm spinner-load-more m-l-5" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4">
                <?= loadView('partials/_sidebar'); ?>
            </div>
        </div>
    </div>
</section>