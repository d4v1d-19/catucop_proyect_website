<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 96,
        'width' => 96,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus([
        'primary_es' => 'Menú principal (español)',
        'primary_en' => 'Menú principal (inglés)',
        'footer_es' => 'Menú del pie (español)',
        'footer_en' => 'Menú del pie (inglés)',
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('understrap-styles');
    wp_deregister_style('understrap-styles');
    wp_dequeue_script('understrap-scripts');
    wp_deregister_script('understrap-scripts');

    $post = is_singular() ? get_post() : null;
    if (!$post || !has_blocks($post->post_content)) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }

    $css = get_stylesheet_directory() . '/assets/css/catucop.css';
    $js = get_stylesheet_directory() . '/assets/js/site.js';
    wp_enqueue_style('catucop', get_stylesheet_directory_uri() . '/assets/css/catucop.css', [], file_exists($css) ? (string) filemtime($css) : '1.0.0');
    wp_enqueue_script('catucop', get_stylesheet_directory_uri() . '/assets/js/site.js', [], file_exists($js) ? (string) filemtime($js) : '1.0.0', true);
}, 100);

add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_shortlink_wp_head');
});

add_action('customize_register', function ($wp) {
    $wp->add_section('catucop_social', [
        'title' => 'Redes CATUCOP',
        'priority' => 30,
    ]);
    $nets = [
        'catucop_facebook' => 'Facebook',
        'catucop_instagram' => 'Instagram',
        'catucop_tiktok' => 'TikTok',
    ];
    foreach ($nets as $id => $label) {
        $wp->add_setting($id, [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp->add_control($id, [
            'label' => $label,
            'section' => 'catucop_social',
            'type' => 'url',
        ]);
    }
});

function catucop_section_order() {
    $default = ['hero', 'hitos', 'actividades', 'eventos', 'contactos'];
    $saved = get_option('catucop_home_order', $default);
    if (!is_array($saved)) {
        return $default;
    }
    $clean = [];
    foreach ($saved as $key) {
        $key = sanitize_key($key);
        if (in_array($key, $default, true) && !in_array($key, $clean, true)) {
            $clean[] = $key;
        }
    }
    foreach ($default as $key) {
        if (!in_array($key, $clean, true)) {
            $clean[] = $key;
        }
    }
    return $clean;
}

class Catucop_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $active = in_array('current-menu-item', $classes, true) || in_array('current_page_item', $classes, true);
        $variant = (is_object($args) && !empty($args->catucop_variant)) ? $args->catucop_variant : 'bar';
        $class = 'nav-link';
        if ($variant === 'footer') {
            $class = 'footer-link';
        } elseif ($variant === 'mobile') {
            $class = 'nav-mobile-link';
        }
        if ($active) {
            $class .= ' is-active';
        }
        $output .= '<a class="' . esc_attr($class) . '" href="' . esc_url(catucop_url($item->url)) . '">' . esc_html($item->title) . '</a>';
    }
}

function catucop_menu($location_base, $variant) {
    $lang = catucop_lang();
    $location = $location_base . '_' . $lang;
    if (has_nav_menu($location)) {
        wp_nav_menu([
            'theme_location' => $location,
            'container' => false,
            'items_wrap' => '%3$s',
            'fallback_cb' => false,
            'walker' => new Catucop_Walker(),
            'catucop_variant' => $variant,
            'depth' => 1,
        ]);
        return;
    }
    $links = [
        home_url('/') => catucop_t('nav.inicio'),
        catucop_page_url('afiliados') => catucop_t('nav.afiliados'),
        catucop_page_url('lugares') => catucop_t('nav.lugares'),
    ];
    $class = $variant === 'footer' ? 'footer-link' : ($variant === 'mobile' ? 'nav-mobile-link' : 'nav-link');
    foreach ($links as $url => $label) {
        echo '<a class="' . esc_attr($class) . '" href="' . esc_url(catucop_url($url)) . '">' . esc_html($label) . '</a>';
    }
}

function catucop_social_links() {
    $nets = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
    ];
    foreach ($nets as $key => $label) {
        $url = get_theme_mod('catucop_' . $key);
        if (!$url) {
            continue;
        }
        echo '<a class="social-link" href="' . esc_url($url) . '" target="_blank" rel="noreferrer noopener" aria-label="' . esc_attr($label) . '">';
        echo '<img src="' . esc_url(catucop_img($key . '.png')) . '" alt="" width="16" height="16">';
        echo '</a>';
    }
}

function catucop_map_url() {
    return add_query_arg('catucop_mapa', '1', home_url('/'));
}

add_action('template_redirect', function () {
    if (!isset($_GET['catucop_mapa'])) {
        return;
    }
    $file = get_stylesheet_directory() . '/assets/mapa/mapa-3d-osa.html';
    if (!is_readable($file)) {
        status_header(404);
        exit;
    }
    status_header(200);
    header('Content-Type: text/html; charset=utf-8');
    header('X-Robots-Tag: noindex');
    $html = file_get_contents($file);
    $json = content_url('uploads/catucop/destinos.json');
    $script = '<script>fetch(' . wp_json_encode($json) . ').then(function(r){return r.ok?r.json():[]}).then(function(list){var box=document.getElementById("pl-sites");if(!box||!list||!list.length)return;var cap=document.createElement("p");cap.textContent="Destinos de la cámara";cap.style.cssText="margin:14px 0 4px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;opacity:.7";box.appendChild(cap);list.forEach(function(s){if(!s||!s.nombre)return;var a=document.createElement("a");a.href=s.maps||"#";a.target="_blank";a.rel="noreferrer noopener";a.textContent=s.nombre;a.style.cssText="display:block;margin:6px 0;font-weight:600;color:inherit";box.appendChild(a);});}).catch(function(){});</script>';
    if (str_contains($html, '</body>')) {
        $html = str_replace('</body>', $script . '</body>', $html);
    } else {
        $html .= $script;
    }
    echo $html;
    exit;
});
