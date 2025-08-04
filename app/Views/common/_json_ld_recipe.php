<?php
$totalTime = (!empty($dataRecipe['prep_time']) ? $dataRecipe['prep_time'] : 0) +
    (!empty($dataRecipe['cook_time']) ? $dataRecipe['cook_time'] : 0);

$recipeCategory = getCategory($post->category_id, $baseCategories);
$prepTime = !empty($dataRecipe['prep_time']) ? "PT{$dataRecipe['prep_time']}M" : null;
$cookTime = !empty($dataRecipe['cook_time']) ? "PT{$dataRecipe['cook_time']}M" : null;
$totalTimeFormatted = $totalTime > 0 ? "PT{$totalTime}M" : null;

$recipeSchema = [
    "@context" => "https://schema.org/",
    "@type" => "Recipe",
    "name" => escMeta($postJsonLD->title),
    "image" => [
        "@type" => "ImageObject",
        "url" => getPostImage($postJsonLD, 'big'),
        "width" => 750,
        "height" => 500
    ],
    "author" => [
        "@type" => "Person",
        "name" => escMeta($postJsonLD->author_username),
        "url" => base_url("profile/" . $postJsonLD->author_slug)
    ],
    "datePublished" => !empty($postJsonLD->created_at) ? date(DATE_ISO8601, strtotime($postJsonLD->created_at)) : null,
    "description" => escMeta($postJsonLD->summary),
    "prepTime" => $prepTime,
    "cookTime" => $cookTime,
    "totalTime" => $totalTimeFormatted,
    "keywords" => !empty($keywords) ? escMeta($keywords) : null,
    "recipeYield" => !empty($dataRecipe['serving']) ? "{$dataRecipe['serving']} servings" : "1 serving",
    "recipeCategory" => !empty($recipeCategory) ? escMeta($recipeCategory->name) : null,
];

// Nutrition Information
if (!empty($dataRecipe['nInfo']) && is_array($dataRecipe['nInfo'])) {
    $nutritionMapping = [
        "Total Fat" => "fatContent",
        "Saturated Fat" => "saturatedFatContent",
        "Cholesterol" => "cholesterolContent",
        "Sodium" => "sodiumContent",
        "Total Carbohydrates" => "carbohydrateContent",
        "Dietary Fiber" => "fiberContent",
        "Sugars" => "sugarContent",
        "Protein" => "proteinContent"
    ];

    $nutritionInfo = ["@type" => "NutritionInformation"];
    foreach ($dataRecipe['nInfo'] as $itemInfo) {
        if (!empty($itemInfo['n']) && !empty($itemInfo['v'])) {
            $propertyName = trim(escMeta($itemInfo['n']));
            if (isset($nutritionMapping[$propertyName])) {
                $nutritionInfo[$nutritionMapping[$propertyName]] = escMeta($itemInfo['v']);
            }
        }
    }
    if (count($nutritionInfo) > 1) {
        $recipeSchema["nutrition"] = $nutritionInfo;
    }
}

// Ingredients
if (!empty($dataRecipe['ingredients']) && countItems($dataRecipe['ingredients']) > 0) {
    $recipeSchema["recipeIngredient"] = array_map('escMeta', $dataRecipe['ingredients']);
}

// Instructions
if (!empty($recipeInstructions)) {
    $recipeSchema["recipeInstructions"] = array_map('escMeta', $recipeInstructions);
}

// Video Handling
$videoUrl = null;
$embedUrl = null;

if (!empty($post->video_path)) {
    // Local video case
    $videoUrl = base_url($post->video_path);
    $embedUrl = base_url($post->video_path);
} elseif (!empty($post->video_embed_code)) {
    // Embedded video case (YouTube, Vimeo, etc.)
    $videoUrl = escMeta($postJsonLD->video_url);
    $embedUrl = escMeta($postJsonLD->video_embed_code);
}

if ($videoUrl && $embedUrl) {
    $recipeSchema["video"] = [
        "@type" => "VideoObject",
        "name" => escMeta($postJsonLD->title),
        "description" => escMeta($postJsonLD->summary),
        "thumbnailUrl" => getPostImage($postJsonLD, 'big'),
        "contentUrl" => $videoUrl, // Direct video file URL or external video URL
        "embedUrl" => $embedUrl,   // Embed code or same as contentUrl for local videos
        "uploadDate" => !empty($postJsonLD->created_at) ? date(DATE_ISO8601, strtotime($postJsonLD->created_at)) : null
    ];
}

// Remove empty values
$recipeSchema = array_filter($recipeSchema, function ($value) {
    return !empty($value) || $value === 0;
});

echo '<script type="application/ld+json">' . json_encode($recipeSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
?>
