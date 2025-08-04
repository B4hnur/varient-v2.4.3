<?php if (!empty($postUser)): ?>
    <div class="col-sm-12 col-xs-12">
        <div class="row">
            <div class="about-author">
                <div class="about-author-left">
                    <a href="<?= generateProfileURL($postUser->slug); ?>" class="author-link">
                        <img src="<?= getUserAvatar($postUser->avatar); ?>" alt="" class="img-responsive img-author">
                    </a>
                </div>
                <div class="about-author-right">
                    <div class="about-author-row">
                        <p><strong><a href="<?= generateProfileURL($postUser->slug); ?>" class="author-link"> <?= esc($postUser->username); ?> </a></strong></p>
                    </div>
                    <div class="about-author-row">
                        <?= esc($postUser->about_me);
                        $socialLinks = getSocialLinksArray($postUser);
                        if (!empty($socialLinks)):?>
                            <div class="profile-buttons">
                                <ul>
                                    <?php foreach ($socialLinks as $socialLink):
                                        if (!empty($socialLink['value'])):?>
                                            <li><a class="<?= esc($socialLink['name']); ?>" href="<?= $socialLink['value']; ?>" target="_blank"><i class="icon-<?= esc($socialLink['name']); ?>"></i></a></li>
                                        <?php endif;
                                    endforeach;
                                    if (!empty($postUser->personal_website_url)):?>
                                        <li><a href="<?= esc($postUser->personal_website_url); ?>" target="_blank"><i class="icon-globe"></i></a></li>
                                    <?php endif;
                                    if ($postUser->show_rss_feeds): ?>
                                        <li><a href="<?= langBaseUrl('rss/author/' . $postUser->slug); ?>"><i class="icon-rss"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>