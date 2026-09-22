<?php

if (!defined('ABSPATH')) {
    exit;
}

function catucop_ensure_page($slug, $title, $template = '') {
    $opt = 'catucop_page_' . $slug;
    $id = (int) get_option($opt);
    if (!$id || get_post_status($id) !== 'publish') {
        $existing = get_page_by_path($slug);
        if ($existing && $existing->post_status === 'publish') {
            $id = (int) $existing->ID;
        } else {
            $id = (int) wp_insert_post([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_title' => $title,
                'post_name' => $slug,
                'post_content' => '',
            ]);
        }
        if ($id) {
            update_option($opt, $id);
        }
    }
    if ($id && $template) {
        update_post_meta($id, '_wp_page_template', $template);
    }
    return $id;
}

function catucop_ensure_menu($name, $location, $items) {
    $menu = wp_get_nav_menu_object($name);
    if (!$menu) {
        $id = (int) wp_create_nav_menu($name);
        foreach ($items as $item) {
            wp_update_nav_menu_item($id, 0, [
                'menu-item-title' => $item['title'],
                'menu-item-object' => 'page',
                'menu-item-object-id' => $item['id'],
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
            ]);
        }
    } else {
        $id = (int) $menu->term_id;
    }
    $locations = get_theme_mod('nav_menu_locations', []);
    if (!is_array($locations)) {
        $locations = [];
    }
    $locations[$location] = $id;
    set_theme_mod('nav_menu_locations', $locations);
}

function catucop_seed_site() {
    if (get_option('catucop_site_seeded')) {
        return;
    }

    $inicio = catucop_ensure_page('inicio', 'Inicio');
    $afiliados = catucop_ensure_page('afiliados', 'Afiliados', 'page-templates/template-afiliados.php');
    $lugares = catucop_ensure_page('lugares-y-horarios', 'Lugares y horarios', 'page-templates/template-lugares.php');
    update_option('catucop_page_lugares', $lugares);

    $es = [
        ['title' => 'Inicio', 'id' => $inicio],
        ['title' => 'Afiliados', 'id' => $afiliados],
        ['title' => 'Lugares / Horarios', 'id' => $lugares],
    ];
    $en = [
        ['title' => 'Home', 'id' => $inicio],
        ['title' => 'Affiliates', 'id' => $afiliados],
        ['title' => 'Places / Schedules', 'id' => $lugares],
    ];
    catucop_ensure_menu('Principal ES', 'primary_es', $es);
    catucop_ensure_menu('Principal EN', 'primary_en', $en);
    catucop_ensure_menu('Pie ES', 'footer_es', $es);
    catucop_ensure_menu('Pie EN', 'footer_en', $en);

    update_option('show_on_front', 'page');
    update_option('page_on_front', $inicio);
    update_option('blogname', 'CATUCOP');
    update_option('blogdescription', 'Cámara de Turismo de Puerto Jiménez');
    update_option('permalink_structure', '/%postname%/');
    update_option('timezone_string', 'America/Costa_Rica');
    update_option('date_format', 'j F Y');

    if (!get_theme_mod('catucop_facebook')) {
        set_theme_mod('catucop_facebook', 'https://www.facebook.com/');
        set_theme_mod('catucop_instagram', 'https://www.instagram.com/');
        set_theme_mod('catucop_tiktok', 'https://www.tiktok.com/');
    }

    $seo = [
        $inicio => [
            'title_es' => 'CATUCOP | Cámara de Turismo de Puerto Jiménez',
            'title_en' => 'CATUCOP | Puerto Jiménez Chamber of Tourism',
            'desc_es' => 'Turismo sostenible en la Península de Osa y Corcovado. Afiliados, lugares y horarios desde Puerto Jiménez.',
            'desc_en' => 'Sustainable tourism on the Osa Peninsula and Corcovado. Affiliates, places and schedules from Puerto Jiménez.',
        ],
        $afiliados => [
            'title_es' => 'Afiliados | CATUCOP Puerto Jiménez',
            'title_en' => 'Affiliates | CATUCOP Puerto Jiménez',
            'desc_es' => 'Hospedajes, tours, transporte y comercios afiliados a la Cámara de Turismo de Puerto Jiménez.',
            'desc_en' => 'Lodging, tours, transport and businesses affiliated with the Puerto Jiménez Chamber of Tourism.',
        ],
        $lugares => [
            'title_es' => 'Lugares y horarios | CATUCOP',
            'title_en' => 'Places and schedules | CATUCOP',
            'desc_es' => 'Mapa de la Península de Osa, destinos y horarios de buses y botes desde Puerto Jiménez.',
            'desc_en' => 'Map of the Osa Peninsula, destinations and bus and boat schedules from Puerto Jiménez.',
        ],
    ];
    foreach ($seo as $id => $fields) {
        foreach ($fields as $key => $value) {
            if (!get_post_meta($id, '_catucop_seo_' . $key, true)) {
                update_post_meta($id, '_catucop_seo_' . $key, $value);
            }
        }
    }

    $sample = get_page_by_path('sample-page');
    if ($sample) {
        wp_update_post(['ID' => $sample->ID, 'post_status' => 'draft']);
    }
    $hello = get_posts(['name' => 'hello-world', 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1]);
    if ($hello) {
        wp_update_post(['ID' => $hello[0]->ID, 'post_status' => 'draft']);
    }

    update_option('catucop_site_seeded', 1);
    flush_rewrite_rules(false);
}

add_action('after_switch_theme', 'catucop_seed_site');

add_action('init', function () {
    if (!get_option('catucop_site_seeded')) {
        catucop_seed_site();
    }
    if (function_exists('catucop_seed_afiliados')) {
        catucop_seed_afiliados();
    }
    if (function_exists('catucop_seed_contenido')) {
        catucop_seed_contenido();
    }
}, 30);
