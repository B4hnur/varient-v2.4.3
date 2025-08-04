<?php $socialLinks = getSocialLinksArray($baseSettings, false);
if (!empty($socialLinks)):
    foreach ($socialLinks as $socialLink):
        if (!empty($socialLink['value'])):?>
            <li><a href="<?= esc($socialLink['value']); ?>" target="_blank" title="<?= esc(ucfirst($socialLink['name'])); ?>" class="<?= esc($socialLink['name']); ?>"><i class="icon-<?= esc($socialLink['name']); ?>"></i></a></li>
        <?php endif;
    endforeach;
endif;
if (!empty($generalSettings->show_rss) && $rssHide == false) : ?>
    <li><a class="rss" href="<?= generateURL('rss_feeds'); ?>" aria-label="rss"><i class="icon-rss"></i></a></li>
<?php endif; ?>