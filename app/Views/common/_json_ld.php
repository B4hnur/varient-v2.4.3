<?php
$socialArray = getSocialLinksArray($baseSettings);
$socialLinks = [];

if (!empty($socialArray)) {
    foreach ($socialArray as $item) {
        if (!empty($item['value'])) {
            $socialLinks[] = escMeta($item['value']);
        }
    }
}

// ORGANIZATION JSON
$organizationSchema = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "url" => base_url(),
    "logo" => [
        "@type" => "ImageObject",
        "width" => 600,
        "height" => 60,
        "url" => getLogo()
    ]
];

if (!empty($socialLinks)) {
    $organizationSchema["sameAs"] = $socialLinks;
}

echo '<script type="application/ld+json">' . json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

// WEBSITE JSON
$websiteSchema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "url" => base_url(),
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => base_url() . "/search?q={search_term_string}",
        "query-input" => "required name=search_term_string"
    ]
];

echo '<script type="application/ld+json">' . json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

// NEWS ARTICLE JSON
if (!empty($postJsonLD)) {
    $dateModified = !empty($postJsonLD->updated_at) ? $postJsonLD->updated_at : $postJsonLD->created_at;
    $keywords = !empty($postJsonLD->keywords) ? explode(',', escMeta($postJsonLD->keywords)) : null;

    $newsArticleSchema = [
        "@context" => "https://schema.org",
        "@type" => "NewsArticle",
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => generatePostURL($postJsonLD)
        ],
        "headline" => escMeta($postJsonLD->title),
        "name" => escMeta($postJsonLD->title),
        "articleBody" => escMeta(strip_tags($postJsonLD->content)),
        "articleSection" => escMeta($postJsonLD->category_name),
        "datePublished" => date(DATE_ISO8601, strtotime($postJsonLD->created_at)),
        "dateModified" => date(DATE_ISO8601, strtotime($dateModified)),
        "inLanguage" => $activeLang->language_code,
        "author" => [
            "@type" => "Person",
            "name" => escMeta($postJsonLD->author_username),
            "url" => base_url("profile/" . $postJsonLD->author_slug)
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => clrQuotes($baseSettings->application_name),
            "logo" => [
                "@type" => "ImageObject",
                "width" => 600,
                "height" => 60,
                "url" => getLogo()
            ]
        ],
        "image" => [
            "@type" => "ImageObject",
            "url" => getPostImage($postJsonLD, 'big'),
            "contentUrl" => getPostImage($postJsonLD, 'big'),
            "width" => 870,
            "height" => 580
        ],
        "isAccessibleForFree" => true,
        "hasPart" => [
            "@type" => "WebPageElement",
            "isAccessibleForFree" => true,
            "cssSelector" => [".post-content"]
        ],
        "speakable" => [
            "@type" => "SpeakableSpecification",
            "cssSelector" => [".post-content"]
        ]
    ];

    if (!empty($postJsonLD->video_path) || !empty($postJsonLD->video_url) || !empty($postJsonLD->video_embed_code)) {
        $videoUrl = '';
        if (!empty($postJsonLD->video_path)) {
            $videoUrl = getBaseURLByStorage($postJsonLD->video_storage) . $postJsonLD->video_path;
        } elseif (!empty($postJsonLD->video_url)) {
            $videoUrl = $postJsonLD->video_url;
        }
        $videoData = [
            "@type" => "VideoObject",
            "name" => escMeta($postJsonLD->title),
            "description" => escMeta(characterLimiter(strip_tags($postJsonLD->summary ?? $postJsonLD->content), 200, '...')),
            "thumbnailUrl" => [getPostImage($postJsonLD, 'big')],
            "uploadDate" => date(DATE_ISO8601, strtotime($postJsonLD->created_at)),
            "embedUrl" => !empty($postJsonLD->video_embed_url) ? escMeta($postJsonLD->video_embed_url) : $videoUrl
        ];

        if (!empty($videoUrl)) {
            $videoData["contentUrl"] = $videoUrl;
        }

        $newsArticleSchema["video"] = $videoData;
    }

    if (!empty($keywords)) {
        $newsArticleSchema["keywords"] = $keywords;
    }

    echo '<script type="application/ld+json">' . json_encode($newsArticleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

// BREADCRUMB JSON
if (!empty($category)) {
    $breadcrumbSchema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => []
    ];

    $position = 1;

    if (!empty($parentCategory)) {
        $breadcrumbSchema["itemListElement"][] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => escMeta($parentCategory->name),
            "item" => generateCategoryURL($parentCategory)
        ];
    }

    $breadcrumbSchema["itemListElement"][] = [
        "@type" => "ListItem",
        "position" => $position,
        "name" => escMeta($category->name),
        "item" => generateCategoryURL($category)
    ];

    echo '<script type="application/ld+json">' . json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
} ?>
