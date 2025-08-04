<div class="row">
    <div class="col-sm-6 col-xs-12 left">
        <?php if (!empty($nextPrevPosts['prev'])): ?>
            <p>
                <a href="<?= generatePostURL($nextPrevPosts['prev']); ?>"><span><i class="icon-angle-left" aria-hidden="true"></i><?= trans("previous_article"); ?></span></a>
            </p>
            <h3 class="title font">
                <a href="<?= generatePostURL($nextPrevPosts['prev']); ?>" class="font-weight-600"><?= esc(characterLimiter($nextPrevPosts['prev']->title, 80, '...')); ?></a>
            </h3>
        <?php endif; ?>
    </div>
    <div class="col-sm-6 col-xs-12 right">
        <?php if (!empty($nextPrevPosts['next'])): ?>
            <p>
                <a href="<?= generatePostURL($nextPrevPosts['next']); ?>"><span><?= trans("next_article"); ?><i class="icon-angle-right" aria-hidden="true"></i></span></a>
            </p>
            <h3 class="title">
                <a href="<?= generatePostURL($nextPrevPosts['next']); ?>" class="font-weight-600"><?= esc(characterLimiter($nextPrevPosts['next']->title, 80, '...')); ?></a>
            </h3>
        <?php endif; ?>
    </div>
</div>