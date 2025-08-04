<div id="navMobile" class="nav-mobile">
<div class="nav-mobile-inner">
<div class="row">
<div class="col-sm-12 mobile-nav-buttons">
<?php if (authCheck()):
if (hasPermission('add_post')): ?>
<button class="btn btn-custom btn-mobile-nav btn-mobile-nav-add close-menu-click" data-toggle="modal" data-target="#modal_add_post">
<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
</svg>&nbsp;&nbsp;<?= trans("add_post"); ?>
</button>
<?php endif;
else:
if ($generalSettings->registration_system == 1): ?>
<a href="javascript:void(0)" data-toggle="modal" data-target="#modalLogin" class="btn btn-custom btn-mobile-nav close-menu-click btn_open_login_modal m-r-5"><i class="icon-login"></i><?= trans("login"); ?></a>
<a href="<?= generateURL('register'); ?>" class="btn btn-custom btn-mobile-nav"><i class="icon-user-plus-o"></i><?= trans("register"); ?></a>
<?php endif;
endif; ?>
</div>
</div>
<?php if (authCheck()) : ?>
<div class="row">
<div class="col-sm-12">
<div class="dropdown profile-dropdown nav-item">
<a href="#" class="dropdown-toggle image-profile-drop nav-link" data-toggle="dropdown" aria-expanded="false">
<img src="<?= getUserAvatar(user()->avatar); ?>" alt="<?= esc(user()->username); ?>">
<?= esc(user()->username); ?> <span class="icon-arrow-down"></span>
</a>
<?= view("common/_nav_profile.php"); ?>
</div>
</div>
</div>
<?php endif; ?>
<div class="row">
<div class="col-sm-12">
<ul class="nav navbar-nav">
<?php if ($generalSettings->show_home_link == 1): ?>
<li class="nav-item"><a href="<?= langBaseUrl(); ?>" class="nav-link"><?= trans("home"); ?></a></li>
<?php endif;
if (!empty($baseMenuLinks)):
foreach ($baseMenuLinks as $item):
if ($item->item_visibility == 1 && ($item->item_location == "top" || $item->item_location == "main") && $item->item_parent_id == "0"):
$subLinks = getSubMenuLinks($baseMenuLinks, $item->item_id, $item->item_type);
if (!empty($subLinks)): ?>
<li class="nav-item dropdown">
<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><?= esc($item->item_name) ?><i class="icon-arrow-down"></i></a>
<ul class="dropdown-menu">
<?php if ($item->item_type == "category"): ?>
<li class="nav-item"><a href="<?= generateMenuItemURL($item, $baseCategories); ?>" class="nav-link"><?= trans("all"); ?></a></li>
<?php endif;
foreach ($subLinks as $sub):
if ($sub->item_visibility == 1):?>
<li class="nav-item"><a href="<?= generateMenuItemURL($sub, $baseCategories); ?>" class="nav-link"><?= esc($sub->item_name) ?></a></li>
<?php endif;
endforeach; ?>
</ul>
</li>
<?php else: ?>
<li class="nav-item"><a href="<?= generateMenuItemURL($item, $baseCategories); ?>" class="nav-link"><?= esc($item->item_name) ?></a></li>
<?php endif;
endif;
endforeach;
endif;
if ($generalSettings->multilingual_system == 1 && countItems($activeLanguages) > 1): ?>
<li class="nav-item border-0">
<a href="#" class="nav-link"><?= trans("language"); ?></a>
<ul class="mobile-language-options">
<?php foreach ($activeLanguages as $language):
$langURL = base_url($language->short_form . "/");
if ($language->id == $generalSettings->site_lang):
$langURL = base_url();
endif; ?>
<li><a href="<?= $langURL; ?>" class="<?= $language->id == $activeLang->id ? 'selected' : ''; ?> "><?= esc($language->name); ?></a></li>
<?php endforeach; ?>
</ul>
</li>
<?php endif; ?>
<li class="nav-item">
<form action="<?= base_url('switch-dark-mode'); ?>" method="post">
<?= csrf_field(); ?>
<input type="hidden" name="back_url" value="<?= esc(currentFullURL()); ?>">
<?php if ($darkMode == 1): ?>
<button type="submit" name="theme_mode" value="light" class="btn-switch-mode-mobile">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
<path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
</svg>
</button>
<?php else: ?>
<button type="submit" name="theme_mode" value="dark" class="btn-switch-mode-mobile">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="dark-mode-icon" viewBox="0 0 16 16">
<path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
</svg>
</button>
<?php endif; ?>
</form>
</li>
</ul>
</div>
</div>
</div>
</div>