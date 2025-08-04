<!DOCTYPE html>
<html lang="<?= $activeLang->short_form ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?></title>
<meta name="description" content="<?= escMeta($description); ?>"/>
<meta name="keywords" content="<?= escMeta($keywords); ?>"/>
<meta name="author" content="<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="robots" content="all">
<meta property="og:locale" content="<?= escMeta($activeLang->language_code); ?>"/>
<meta property="og:site_name" content="<?= escMeta($baseSettings->application_name); ?>"/>
<?= csrf_meta(); ?>

<?php if (isset($postType)): ?>
<meta property="og:type" content="<?= escMeta($ogType); ?>"/>
<meta property="og:title" content="<?= escMeta($ogTitle); ?>"/>
<meta property="og:description" content="<?= escMeta($description); ?>"/>
<meta property="og:url" content="<?= esc(currentFullURL()); ?>"/>
<meta property="og:image" content="<?= escMeta($ogImage); ?>"/>
<meta property="og:image:width" content="<?= $ogWidth; ?>"/>
<meta property="og:image:height" content="<?= $ogHeight; ?>"/>
<meta property="article:author" content="<?= escMeta($ogAuthor); ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<?php foreach ($ogTags as $tag): ?>
<meta property="article:tag" content="<?= escMeta($tag->tag); ?>"/>
<?php endforeach; ?>
<meta property="article:published_time" content="<?= $ogPublishedTime; ?>"/>
<meta property="article:modified_time" content="<?= $ogModifiedTime; ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="twitter:creator" content="@<?= escMeta($ogCreator); ?>"/>
<meta name="twitter:title" content="<?= escMeta($post->title); ?>"/>
<meta name="twitter:description" content="<?= escMeta($description); ?>"/>
<meta name="twitter:image" content="<?= escMeta($ogImage); ?>"/>
<?php else: ?>
<meta property="og:image" content="<?= getLogo(); ?>"/>
<meta property="og:image:width" content="<?= getLogoSize('width'); ?>"/>
<meta property="og:image:height" content="<?= getLogoSize('height'); ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta property="og:description" content="<?= escMeta($description); ?>"/>
<meta property="og:url" content="<?= esc(currentFullURL()); ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="twitter:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta name="twitter:description" content="<?= escMeta($description); ?>"/>
<?php endif;
if ($generalSettings->pwa_status == 1): ?>
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?= escMeta($baseSettings->application_name); ?>">
<meta name="msapplication-TileImage" content="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<meta name="msapplication-TileColor" content="#2F3BA2">
<link rel="manifest" href="<?= base_url('manifest.json'); ?>">
<link rel="apple-touch-icon" href="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<?php endif; ?>
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
<link rel="canonical" href="<?= esc(base_url(uri_string()));?>"/>
<link rel="alternate" href="<?= esc(currentFullURL()); ?>" hreflang="<?= escMeta($activeLang->language_code); ?>"/>
<?= view('common/_fonts'); ?>
<link href="<?= base_url($assetsPath . '/css/style-2.4.1.min.css'); ?>" rel="stylesheet"/>
<?= loadView('partials/_css_js_header'); ?>
<?= $generalSettings->custom_header_codes; ?>
</head>
<body class="<?= $darkMode == true ? 'dark-mode' : ''; ?> <?= $activeLang->text_direction == 'rtl' ? 'rtl-mode' : ''; ?>">
<header id="header">
<?= loadView('nav/_nav_top'); ?>
<div class="logo-banner">
<div class="container">
<div class="col-sm-12">
<div class="row">
<div class="left">
<a href="<?= langBaseUrl(); ?>">
<img src="<?= $darkMode == 1 ? getLogoFooter() : getLogo(); ?>" alt="logo" class="logo" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
</a>
</div>
<div class="right">
<div class="pull-right">
<?= loadView('partials/_ad_spaces', ['adSpace' => 'header', 'class' => 'bn-header']); ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?= loadView('nav/_nav_main'); ?>
<div class="mobile-nav-container">
<div class="nav-mobile-header">
<div class="container-fluid">
<div class="row">
<div class="nav-mobile-header-container">
<div class="menu-icon">
<a href="javascript:void(0)" class="btn-open-mobile-nav"><i class="icon-menu"></i></a>
</div>
<div class="mobile-logo">
<a href="<?= langBaseUrl(); ?>">
<img src="<?= $darkMode == 1 ? getLogoFooter() : getLogo(); ?>" alt="logo" class="logo" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
</a>
</div>
<div class="mobile-search">
<a class="search-icon"><i class="icon-search"></i></a>
</div>
</div>
</div>
</div>
</div>
</div>
</header>
<div id="overlay_bg" class="overlay-bg"></div>
<div class="mobile-nav-search">
<div class="search-form">
<form action="<?= generateURL('search'); ?>" method="get">
<input type="text" name="q" maxlength="300" pattern=".*\S+.*" class="form-control form-input" placeholder="<?= trans("placeholder_search"); ?>" required>
<button class="btn btn-default"><i class="icon-search"></i></button>
</form>
</div>
</div>
<?= loadView('nav/_nav_mobile'); ?>
<?= loadView('partials/_modals'); ?>