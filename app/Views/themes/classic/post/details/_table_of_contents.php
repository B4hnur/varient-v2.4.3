<?php $arrayLinkNums = array();
$arrayLinkTree = array();
if (!empty($tableOfContentsItems)) {
    foreach ($tableOfContentsItems as $item) {
        array_push($arrayLinkNums, $item->item_order);
    }
    $i = 0;
    $arrayIds = array();
    foreach ($tableOfContentsItems as $item) {
        if (!in_array($item->id, $arrayIds)) {
            array_push($arrayIds, $item->id);
            if ($i > 0 && !empty($item->parent_link_num) && in_array($item->parent_link_num, $arrayLinkNums)) {
                $arrayLinkTree[$item->parent_link_num][] = $item;
            } else {
                $arrayLinkTree[0][] = $item;
            }
            $i++;
        }
    }
} ?>
<div class="row">
    <div class="col-sm-12">
        <div class="post-image">
            <div class="post-image-inner">
                <?php if (!empty($postImages) && countItems($postImages) > 0) : ?>
                    <div class="show-on-page-load">
                        <div id="post-detail-slider" class="post-detail-slider">
                            <div class="post-detail-slider-item">
                                <img src="<?= getPostImage($post, 'default'); ?>" class="img-responsive center-image" alt="<?= esc($post->title); ?>"/>
                                <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                            </div>
                            <?php foreach ($postImages as $image):
                                $imgBaseURL = getBaseURLByStorage($image->storage); ?>
                                <div class="post-detail-slider-item">
                                    <img src="<?= $imgBaseURL . esc($image->image_default); ?>" class="img-responsive center-image" alt="<?= esc($post->title); ?>"/>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="post-detail-slider-nav" class="slider-nav post-detail-slider-nav">
                            <button class="prev"><i class="icon-arrow-left"></i></button>
                            <button class="next"><i class="icon-arrow-right"></i></button>
                        </div>
                    </div>
                <?php else:
                    if (!empty($post->image_id) || !empty($post->image_url)):?>
                        <img src="<?= getPostImage($post, 'default'); ?>" class="img-responsive center-image" alt="<?= esc($post->title); ?>"/>
                        <?php if (!empty($post->image_description)): ?>
                            <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                        <?php endif; ?>
                    <?php endif;
                endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 m-b-15">
        <div class="table-of-contents">
            <div class="title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20" fill="currentColor">
                    <path d="M64 144a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM64 464a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm48-208a48 48 0 1 0 -96 0 48 48 0 1 0 96 0z"/>
                </svg>&nbsp;<h2> <?= trans("table_of_contents"); ?></h2>
            </div>
            <?php $arrayIds = array();
            if (!empty($arrayLinkTree[0])): ?>
                <ul class="ul-main">
                    <?php foreach ($arrayLinkTree[0] as $item0):
                        if (!in_array($item0->id, $arrayIds)):
                            array_push($arrayIds, $item0->id); ?>
                            <li style="list-style: <?= getPostListStyle($post, 1)->style; ?>">
                                <a href="#<?= strSlug($item0->title) . '-' . $item0->id; ?>" data-id="toc-<?= $item0->id; ?>" class="link-table-of-content"><?= esc($item0->title); ?></a>
                                <?php if (!empty($arrayLinkTree[$item0->item_order])): ?>
                                    <ul>
                                        <?php foreach ($arrayLinkTree[$item0->item_order] as $item1):
                                            if (!in_array($item1->id, $arrayIds)):
                                                array_push($arrayIds, $item1->id); ?>
                                                <li style="list-style: <?= getPostListStyle($post, 2)->style; ?>">
                                                    <a href="#<?= strSlug($item1->title) . '-' . $item1->id; ?>" data-id="toc-<?= $item1->id; ?>" class="link-table-of-content"><?= esc($item1->title); ?></a>
                                                    <?php if (!empty($arrayLinkTree[$item1->item_order])): ?>
                                                        <ul>
                                                            <?php foreach ($arrayLinkTree[$item1->item_order] as $item2):
                                                                if (!in_array($item2->id, $arrayIds)):
                                                                    array_push($arrayIds, $item2->id); ?>
                                                                    <li style="list-style: <?= getPostListStyle($post, 3)->style; ?>"><a href="#<?= strSlug($item2->title) . '-' . $item2->id; ?>" data-id="toc-<?= $item2->id; ?>" class="link-table-of-content"><?= esc($item2->title); ?></a></li>
                                                                <?php endif;
                                                            endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif;
                                        endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif;
                    endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="container-toc">
            <?php if (!empty($arrayLinkTree[0])): ?>
                <ul class="ul-toc-content ul-toc-content-main">
                    <?php $arrayIds = array();
                    foreach ($arrayLinkTree[0] as $item0):
                        if (!in_array($item0->id, $arrayIds)):
                            array_push($arrayIds, $item0->id); ?>
                            <li class="li-toc-content m-b-30">
                                <div data-title="<?= strSlug($item0->title) . '-' . $item0->id; ?>">
                                    <h3 class="title-post-item" style="list-style: <?= getPostListStyle($post, 1)->status == 1 ? getPostListStyle($post, 1)->style : 'none'; ?>;list-style-position: inside;"><?= esc($item0->title); ?></h3>
                                    <?php if (!empty($item0->image)):
                                        $imgBaseURL = getBaseURLByStorage($item0->storage); ?>
                                        <div class="post-image">
                                            <div class="post-image-inner">
                                                <img src="<?= $imgBaseURL . $item0->image_large; ?>" alt="<?= esc($item0->title); ?>" class="img-fluid" width="750" height="500"/>
                                                <figcaption class="img-description"><?= esc($item0->image_description); ?></figcaption>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-text">
                                        <?= $item0->content; ?>
                                    </div>
                                </div>
                                <?php if (!empty($arrayLinkTree[$item0->item_order])): ?>
                                    <ul class="ul-toc-content ul-toc-content-sub">
                                        <?php foreach ($arrayLinkTree[$item0->item_order] as $item1):
                                            if (!in_array($item1->id, $arrayIds)):
                                                array_push($arrayIds, $item1->id); ?>
                                                <li class="li-toc-content">
                                                    <div data-title="<?= strSlug($item1->title) . '-' . $item1->id; ?>">
                                                        <h3 class="title-post-item" style="list-style: <?= getPostListStyle($post, 2)->status == 1 ? getPostListStyle($post, 2)->style : 'none'; ?>;list-style-position: inside;"><?= esc($item1->title); ?></h3>
                                                        <?php if (!empty($item1->image)):
                                                            $imgBaseURL = getBaseURLByStorage($item1->storage); ?>
                                                            <div class="post-image">
                                                                <div class="post-image-inner">
                                                                    <img src="<?= $imgBaseURL . $item1->image_large; ?>" alt="<?= esc($item1->title); ?>" class="img-fluid" width="750" height="500"/>
                                                                    <figcaption class="img-description"><?= esc($item1->image_description); ?></figcaption>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="post-text">
                                                            <?= $item1->content; ?>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($arrayLinkTree[$item1->item_order])): ?>
                                                        <ul class="ul-toc-content ul-toc-content-sub">
                                                            <?php foreach ($arrayLinkTree[$item1->item_order] as $item2):
                                                                if (!in_array($item2->id, $arrayIds)):
                                                                    array_push($arrayIds, $item2->id); ?>
                                                                    <li class="li-toc-content">
                                                                        <div data-title="<?= strSlug($item2->title) . '-' . $item2->id; ?>">
                                                                            <h3 class="title-post-item" style="list-style: <?= getPostListStyle($post, 3)->status == 1 ? getPostListStyle($post, 3)->style : 'none'; ?>;list-style-position: inside;"><?= esc($item2->title); ?></h3>
                                                                            <?php if (!empty($item2->image)):
                                                                                $imgBaseURL = getBaseURLByStorage($item2->storage); ?>
                                                                                <div class="post-image">
                                                                                    <div class="post-image-inner">
                                                                                        <img src="<?= $imgBaseURL . $item2->image_large; ?>" alt="<?= esc($item2->title); ?>" class="img-fluid" width="750" height="500"/>
                                                                                        <figcaption class="img-description"><?= esc($item2->image_description); ?></figcaption>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="post-text">
                                                                                <?= $item2->content; ?>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                <?php endif;
                                                            endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif;
                                        endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif;
                    endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash) {
            const hash = decodeURIComponent(window.location.hash.substring(1));
            scrollToSection(hash);
        }
        const scrollLinks = document.querySelectorAll('.link-table-of-content');
        scrollLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const hash = link.getAttribute('href');
                window.location.hash = hash;
                scrollToSection(hash.substring(1));
            });
        });
    });
    function scrollToSection(title) {
        const element = document.querySelector(`[data-title="${title}"]`);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>