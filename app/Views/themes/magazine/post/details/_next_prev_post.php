<div class="row">
    <div class="col-sm-6 col-xs-12 left">
        <?php if (!empty($nextPrevPosts['prev'])): ?>
            <div class="head-title text-end">
                <a href="<?= generatePostURL($nextPrevPosts['prev']); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    <?= trans("previous_article"); ?>
                </a>
            </div>
            <h3 class="title text-end">
                <a href="<?= generatePostURL($nextPrevPosts['prev']); ?>"><?= esc(characterLimiter($nextPrevPosts['prev']->title, 80, '...')); ?></a>
            </h3>
        <?php endif; ?>
    </div>
    <div class="col-sm-6 col-xs-12 right">
        <?php if (!empty($nextPrevPosts['next'])): ?>
            <div class="head-title text-start">
                <a href="<?= generatePostURL($nextPrevPosts['next']); ?>">
                    <?= trans("next_article"); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
            </div>
            <h3 class="title text-start">
                <a href="<?= generatePostURL($nextPrevPosts['next']); ?>"><?= esc(characterLimiter($nextPrevPosts['next']->title, 80, '...')); ?></a>
            </h3>
        <?php endif; ?>
    </div>
</div>