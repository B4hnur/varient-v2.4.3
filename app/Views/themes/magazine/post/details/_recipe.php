<?php $dataRecipe = unserializeData($post->post_data);
$articleAd1 = array();
$articleAd2 = array();
if (!empty($adSpaces)) {
    foreach ($adSpaces as $item) {
        if ($item->ad_space == 'in_article_1') {
            $articleAd1 = $item;
        }
        if ($item->ad_space == 'in_article_2') {
            $articleAd2 = $item;
        }
    }
} ?>
<div class="post-image">
    <div class="post-image-inner">
        <?php if (!empty($postImages) && countItems($postImages) > 0) : ?>
            <div class="show-on-page-load">
                <div id="post-detail-slider" class="post-detail-slider">
                    <div class="post-detail-slider-item">
                        <img src="<?= getPostImage($post, 'default'); ?>" class="img-fluid center-image" alt="<?= esc($post->title); ?>" width="856" height="570"/>
                        <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                    </div>
                    <?php foreach ($postImages as $image):
                        $imgBaseURL = getBaseURLByStorage($image->storage); ?>
                        <div class="post-detail-slider-item">
                            <img src="<?= $imgBaseURL . esc($image->image_default); ?>" class="img-fluid center-image" alt="<?= esc($post->title); ?>" width="856" height="570"/>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="post-detail-slider-nav" class="post-detail-slider-nav">
                    <button class="prev"><i class="icon-arrow-left"></i></button>
                    <button class="next"><i class="icon-arrow-right"></i></button>
                </div>
            </div>
        <?php else:
            if (!empty($post->image_id) || !empty($post->image_url)):?>
                <img src="<?= getPostImage($post, 'default'); ?>" class="img-fluid center-image" alt="<?= esc($post->title); ?>" width="856" height="570"/>
                <?php if (!empty($post->image_description)): ?>
                    <figcaption class="img-description"><?= esc($post->image_description); ?></figcaption>
                <?php endif; ?>
            <?php endif;
        endif; ?>
    </div>
    <div class="recipe-sum-table">
        <div class="row">
            <div class="col-6 col-md-3 col-item">
                <div class="item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 14 14">
                            <g fill="none" stroke="<?= $activeTheme->theme_color; ?>" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 13.5a6.5 6.5 0 1 0 0-13a6.5 6.5 0 0 0 0 13"/>
                                <path d="M7 4.5V7l2.54 2.96"/>
                            </g>
                        </svg>
                    </div>
                    <div class="icon-text">
                        <strong><?= characterLimiter(trans("prep_time"), 15, '..'); ?></strong>
                        <span><?= !empty($dataRecipe) && !empty($dataRecipe['prep_time']) ? $dataRecipe['prep_time'] : ''; ?>&nbsp;<?= trans("minute_short"); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-item">
                <div class="item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 14 14">
                            <path fill="none" stroke="<?= $activeTheme->theme_color; ?>" stroke-linecap="round" stroke-linejoin="round" d="M.858 6.612h12.007a6.005 6.005 0 0 1-3.43 5.426v1.078H4.287v-1.078a6.004 6.004 0 0 1-3.43-5.426ZM10.499.858c-.858 1.231 1.286 2.462.429 3.693M6.633.858C5.775 2.09 7.919 3.32 7.06 4.551M2.78.858c-.858 1.231 1.286 2.462.429 3.693"/>
                        </svg>
                    </div>
                    <div class="icon-text">
                        <strong><?= characterLimiter(trans("cook_time"), 15, '..'); ?></strong>
                        <span><?= !empty($dataRecipe) && !empty($dataRecipe['cook_time']) ? $dataRecipe['cook_time'] : ''; ?>&nbsp;<?= trans("minute_short"); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-item">
                <div class="item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 14 14">
                            <path fill="none" stroke="<?= $activeTheme->theme_color; ?>" stroke-linecap="round" stroke-linejoin="round" d="M7 3a6.5 6.5 0 0 1 6.5 6.5v0a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1A6.5 6.5 0 0 1 7 3m0 0V1.5m-6.5 11h13"/>
                        </svg>
                    </div>
                    <div class="icon-text">
                        <strong><?= characterLimiter(trans("serving"), 15, '..'); ?></strong>
                        <span><?= !empty($dataRecipe) && !empty($dataRecipe['serving']) ? $dataRecipe['serving'] : ''; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-item">
                <div class="item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 14 14">
                            <path fill="none" stroke="<?= $activeTheme->theme_color; ?>" stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.5H.5v6h4.25m4.5-9h-4.5v9h4.5M13.5.5H9.25v13h4.25z"/>
                        </svg>
                    </div>
                    <div class="icon-text">
                        <strong><?= trans("difficulty"); ?></strong>
                        <?php $difficulty = !empty($dataRecipe) && !empty($dataRecipe['difficulty']) ? $dataRecipe['difficulty'] : 0; ?>
                        <span><?php if ($difficulty == 1) {
                                echo characterLimiter(trans("easy"), 15, '..');
                            } elseif ($difficulty == 2) {
                                echo characterLimiter(trans("intermediate"), 15, '..');
                            } elseif ($difficulty == 3) {
                                echo characterLimiter(trans("advanced"), 15, '..');
                            } ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="post-text mt-2 m-b-30">
            <?= $post->recipe_info; ?>
            <?php if (!empty($articleAd1)) {
                echo loadView('partials/_ad_spaces', ['adSpace' => 'in_article_1', 'class' => 'mb-3']);
            } ?>
        </div>
    </div>
    <?php if (!empty($dataRecipe) && !empty($dataRecipe['ingredients'])): ?>
        <div class="col-lg-6 col-md-12">
            <div class="recipe-items-list">
                <h4 class="recipe-sub-title"><?= trans("ingredients"); ?></h4>
                <ul>
                    <?php foreach ($dataRecipe['ingredients'] as $itemIngredient): ?>
                        <li><span><?= esc($itemIngredient); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($dataRecipe) && !empty($dataRecipe['nInfo'])): ?>
        <div class="col-lg-6 col-md-12">
            <div class="recipe-items-list">
                <h4 class="recipe-sub-title"><?= trans("nutritional_information"); ?></h4>
                <?php foreach ($dataRecipe['nInfo'] as $itemInfo): ?>
                    <ul>
                        <li><span><?= !empty($itemInfo) && !empty($itemInfo['n']) ? $itemInfo['n'] : ''; ?>:&nbsp;<b class="font-weight-600"><?= !empty($itemInfo) && !empty($itemInfo['v']) ? $itemInfo['v'] : ''; ?></b></span></li>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-12">
        <?= loadView('post/details/_video', ['post' => $post]); ?>
    </div>
</div>

<div class="post-text mt-3">
    <?php if (!empty($articleAd1)) {
        echo loadView('partials/_ad_spaces', ['adSpace' => 'in_article_2', 'class' => 'mb-3']);
    } ?>
    <h4 class="recipe-sub-title"><?= trans("directions"); ?></h4>
    <div class="recipe-directions">
        <?php $i = 1;
        $recipeInstructions = [];
        if (!empty($recipeDirections)):
            foreach ($recipeDirections as $direction):
                $directionImageUrl = '';
                if ($direction->image) {
                    $imgBaseURL = getBaseURLByStorage($direction->storage);
                    $directionImageUrl = $imgBaseURL . $direction->image;
                }

                $stepData = [
                    "@type" => "HowToStep",
                    "name" => $direction->title,
                    "url" => currentFullURL() . "#step" . $i
                ];
                if (!empty($direction->content)) {
                    $stepData["text"] = strip_tags($direction->content);
                }
                if (!empty($directionImageUrl)) {
                    $stepData["image"] = $directionImageUrl;
                }
                $recipeInstructions[] = $stepData; ?>
                <div id="step<?= $i; ?>" class="direction">
                    <h5 class="title"><?= countItems($recipeDirections) > 1 ? $i . '.&nbsp;' : ''; ?><?= esc($direction->title); ?></h5>
                    <?php if (!empty($direction->content)): ?>
                        <div>
                            <?= $direction->content; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($direction->image)):
                        ?>
                        <div class="text-center">
                            <img src="<?= $imgBaseURL . $direction->image; ?>" alt="<?= esc($direction->title); ?>" class="img-fluid" width="856" height="570"/>
                        </div>
                    <?php endif; ?>
                </div>
                <?php $i++;
            endforeach;
        endif; ?>
    </div>
</div>

<?= view('common/_json_ld_recipe', ['post' => $post, 'dataRecipe' => $dataRecipe, 'recipeInstructions' => $recipeInstructions]); ?>