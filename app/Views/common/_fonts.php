<?php
$localFonts = [];
if (!empty($activeFonts)) {
    foreach ($activeFonts as $font) {
        if ($font->font_source == 'local') {
            $fontIds = array_map(function($f) { return $f->id; }, $localFonts);
            if (!in_array($font->id, $fontIds)) {
                $localFonts[] = $font;
            }
        }
    }
}
if(!empty($localFonts)):
foreach ($localFonts as $font):
if($font->font_source == 'local' && $font->font_key=='open-sans'):?>
<style>@font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 400; font-display: swap; src: url('<?= base_url('assets/fonts/open-sans/open-sans-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-400.woff'); ?>') format('woff')}  @font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 600; font-display: swap; src: url('<?= base_url('assets/fonts/open-sans/open-sans-600.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-600.woff'); ?>') format('woff')}  @font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 700; font-display: swap; src: url('<?= base_url('assets/fonts/open-sans/open-sans-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-700.woff'); ?>') format('woff')}</style>
<?php endif;if($font->font_source == 'local' && $font->font_key=='inter'):?>
<style>@font-face {font-family: 'Inter'; font-style: normal; font-weight: 400; font-display: swap; src: url('<?= base_url('assets/fonts/inter/inter-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/inter/inter-400.woff'); ?>') format('woff')}  @font-face {font-family: 'Inter'; font-style: normal; font-weight: 600; font-display: swap; src: url('<?= base_url('assets/fonts/inter/inter-600.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/inter/inter-600.woff'); ?>') format('woff')}  @font-face {font-family: 'Inter'; font-style: normal; font-weight: 700; font-display: swap; src: url('<?= base_url('assets/fonts/inter/inter-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/inter/inter-700.woff'); ?>') format('woff')}</style>
<?php endif;if($font->font_source == 'local' && $font->font_key=='roboto'):?>
<style>@font-face {font-family: 'Roboto'; font-style: normal; font-weight: 400; font-display: swap; src: url('<?= base_url('assets/fonts/roboto/roboto-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/roboto/roboto-400.woff'); ?>') format('woff')}  @font-face {font-family: 'Roboto'; font-style: normal; font-weight: 500; font-display: swap; src: url('<?= base_url('assets/fonts/roboto/roboto-500.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/roboto/roboto-500.woff'); ?>') format('woff')}  @font-face {font-family: 'Roboto'; font-style: normal; font-weight: 700; font-display: swap; url('<?= base_url('assets/fonts/roboto/roboto-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/roboto/roboto-700.woff'); ?>') format('woff')}</style>
<?php endif;if($font->font_source == 'local' && $font->font_key=='pt-serif'):?>
<style>@font-face {font-family: 'PT Serif'; font-style: normal; font-weight: 400; font-display: swap; src: url('<?= base_url('assets/fonts/pt-serif/pt-serif-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/pt-serif/pt-serif-400.woff'); ?>') format('woff')}  @font-face {font-family: 'PT Serif'; font-style: normal; font-weight: 700; font-display: swap; src: url('<?= base_url('assets/fonts/pt-serif/pt-serif-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/pt-serif/pt-serif-700.woff'); ?>') format('woff')}</style>
<?php endif;if($font->font_source == 'local' && $font->font_key=='source-sans-3'):?>
<style>@font-face {font-family: 'Source Sans 3'; font-style: normal; font-weight: 400; font-display: swap; src: url('<?= base_url('assets/fonts/source-sans/source-sans-3-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/source-sans/source-sans-3-400.woff'); ?>') format('woff');} @font-face {font-family: 'Source Sans 3';font-style: normal;font-weight: 600;font-display: swap;src: url('<?= base_url('assets/fonts/source-sans/source-sans-3-600.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/source-sans/source-sans-3-600.woff'); ?>') format('woff');} @font-face {font-family: 'Source Sans 3';font-style: normal;font-weight: 700;font-display: swap;src: url('<?= base_url('assets/fonts/source-sans/source-sans-3-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/source-sans/source-sans-3-700.woff'); ?>') format('woff');}</style>
<?php endif; endforeach;endif; ?>
<?= getFontURL($activeFonts,'primary'); ?>
<?= getFontURL($activeFonts,'secondary'); ?>
<?= getFontURL($activeFonts,'tertiary'); ?>