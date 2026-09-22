<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800;900&family=Architects+Daughter&display=swap">
<?php wp_head(); ?>
</head>
<body <?php body_class('lang-' . catucop_lang()); ?>>
<?php wp_body_open(); ?>
<div class="site">
<header class="nav-bar">
    <nav class="nav-pill" aria-label="<?php echo esc_attr(catucop_t('nav.inicio')); ?>">
        <div class="nav-links">
            <?php catucop_menu('primary', 'bar'); ?>
        </div>
        <div class="nav-actions">
            <div class="nav-social">
                <?php catucop_social_links(); ?>
            </div>
            <?php $other = catucop_lang() === 'es' ? 'en' : 'es'; ?>
            <a class="lang-toggle" href="<?php echo esc_url(catucop_switch_url($other)); ?>"><?php echo esc_html(strtoupper(catucop_lang())); ?></a>
            <button class="nav-burger" type="button" aria-expanded="false" aria-controls="nav-mobile" aria-label="<?php echo esc_attr(catucop_lang() === 'es' ? 'Menú' : 'Menu'); ?>">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
    <div class="nav-mobile" id="nav-mobile" hidden>
        <?php catucop_menu('primary', 'mobile'); ?>
        <div class="nav-mobile-social"><?php catucop_social_links(); ?></div>
    </div>
</header>
<main class="site-main">
