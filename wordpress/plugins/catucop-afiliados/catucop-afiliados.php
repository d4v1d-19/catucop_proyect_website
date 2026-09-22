<?php
/**
 * Plugin Name: CATUCOP Afiliados
 * Description: Alta, edición y baja de afiliados. El sitio los muestra en orden aleatorio en cada visita, o en la posición manual si se desactiva el azar.
 * Version: 1.0.0
 * Author: CATUCOP
 * Text Domain: catucop-afiliados
 */

if (!defined('ABSPATH')) {
    exit;
}

function catucop_register_afiliados() {
    register_post_type('afiliado', [
        'labels' => [
            'name' => 'Afiliados',
            'singular_name' => 'Afiliado',
            'add_new_item' => 'Añadir afiliado',
            'edit_item' => 'Editar afiliado',
            'new_item' => 'Nuevo afiliado',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-groups',
        'menu_position' => 25,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'rewrite' => false,
        'exclude_from_search' => true,
    ]);

    register_taxonomy('afiliado_cat', 'afiliado', [
        'labels' => [
            'name' => 'Categorías',
            'singular_name' => 'Categoría',
        ],
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => false,
    ]);
}
add_action('init', 'catucop_register_afiliados');

add_action('afiliado_cat_add_form_fields', function () {
    echo '<div class="form-field"><label for="nombre_en">Nombre en inglés</label><input name="nombre_en" id="nombre_en" type="text"></div>';
});

add_action('afiliado_cat_edit_form_fields', function ($term) {
    $value = get_term_meta($term->term_id, 'nombre_en', true);
    echo '<tr class="form-field"><th><label for="nombre_en">Nombre en inglés</label></th><td><input name="nombre_en" id="nombre_en" type="text" value="' . esc_attr($value) . '"></td></tr>';
});

function catucop_save_cat_en($term_id) {
    if (!isset($_POST['nombre_en'])) {
        return;
    }
    update_term_meta($term_id, 'nombre_en', sanitize_text_field(wp_unslash($_POST['nombre_en'])));
}
add_action('created_afiliado_cat', 'catucop_save_cat_en');
add_action('edited_afiliado_cat', 'catucop_save_cat_en');

add_action('add_meta_boxes', function () {
    add_meta_box('catucop_afiliado', 'Ficha del afiliado', 'catucop_afiliado_box', 'afiliado', 'normal', 'high');
});

function catucop_afiliado_box($post) {
    wp_nonce_field('catucop_afiliado', 'catucop_afiliado_nonce');
    echo '<p>El editor de arriba es la descripción en español. El logo es la imagen destacada. La casilla <strong>Orden</strong> (en Atributos de página) es la posición manual: un número menor sale primero, y solo se usa si el orden aleatorio está apagado.</p>';
    $fields = [
        'descripcion_en' => ['Descripción en inglés', 'textarea'],
        'ubicacion' => ['Ubicación', 'text'],
        'telefono' => ['Teléfono', 'text'],
        'email' => ['Correo', 'text'],
        'web' => ['Sitio web', 'url'],
        'facebook' => ['Facebook', 'url'],
        'instagram' => ['Instagram', 'url'],
        'tiktok' => ['TikTok', 'url'],
        'tripadvisor' => ['TripAdvisor', 'url'],
    ];
    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<p><label for="catucop_' . esc_attr($key) . '"><strong>' . esc_html($field[0]) . '</strong></label><br>';
        if ($field[1] === 'textarea') {
            echo '<textarea style="width:100%" rows="5" id="catucop_' . esc_attr($key) . '" name="catucop_' . esc_attr($key) . '">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input style="width:100%" type="' . esc_attr($field[1]) . '" id="catucop_' . esc_attr($key) . '" name="catucop_' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
        }
        echo '</p>';
    }
}

add_action('save_post_afiliado', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['catucop_afiliado_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['catucop_afiliado_nonce'])), 'catucop_afiliado')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $text = ['descripcion_en', 'ubicacion', 'telefono', 'email'];
    $urls = ['web', 'facebook', 'instagram', 'tiktok', 'tripadvisor'];
    foreach ($text as $key) {
        if (!isset($_POST['catucop_' . $key])) {
            continue;
        }
        $raw = wp_unslash($_POST['catucop_' . $key]);
        $value = $key === 'descripcion_en' ? wp_kses_post($raw) : sanitize_text_field($raw);
        update_post_meta($post_id, $key, $value);
    }
    foreach ($urls as $key) {
        if (!isset($_POST['catucop_' . $key])) {
            continue;
        }
        update_post_meta($post_id, $key, esc_url_raw(wp_unslash($_POST['catucop_' . $key])));
    }
});

add_filter('manage_afiliado_posts_columns', function ($columns) {
    $columns['catucop_orden'] = 'Posición';
    return $columns;
});

add_action('manage_afiliado_posts_custom_column', function ($column, $post_id) {
    if ($column === 'catucop_orden') {
        echo esc_html((string) get_post_field('menu_order', $post_id));
    }
}, 10, 2);

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=afiliado',
        'Orden de afiliados',
        'Orden',
        'manage_options',
        'catucop-afiliados-orden',
        'catucop_afiliados_orden_page'
    );
});

function catucop_afiliados_orden_page() {
    if (isset($_POST['catucop_aleatorio']) && check_admin_referer('catucop_afiliados_orden')) {
        update_option('catucop_afiliados_aleatorio', empty($_POST['aleatorio']) ? '0' : '1');
        echo '<div class="updated"><p>Orden actualizado.</p></div>';
    }
    $on = get_option('catucop_afiliados_aleatorio', '1') === '1';
    echo '<div class="wrap"><h1>Orden de afiliados</h1>';
    echo '<p>Con el azar encendido, cada visita a la página de afiliados baraja el grid para que nadie quede siempre de primero. Apáguelo para respetar el número de <strong>Orden</strong> de cada afiliado.</p>';
    echo '<form method="post">';
    wp_nonce_field('catucop_afiliados_orden');
    echo '<input type="hidden" name="catucop_aleatorio" value="1">';
    echo '<label><input type="checkbox" name="aleatorio" value="1" ' . checked($on, true, false) . '> Mostrar en orden aleatorio</label>';
    submit_button('Guardar');
    echo '</form>';
    if ($on) {
        echo '<p>Si más adelante instala un plugin de caché, excluya la página de afiliados. Si esa página queda cacheada, el orden dejaría de cambiar en cada visita.</p>';
    }
    echo '</div>';
}

add_action('admin_notices', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'afiliado') {
        return;
    }
    if (get_option('catucop_afiliados_aleatorio', '1') !== '1') {
        return;
    }
    echo '<div class="notice notice-info"><p>El sitio muestra los afiliados en orden aleatorio. Para usar la posición manual, vaya a Afiliados → Orden.</p></div>';
});

add_action('template_redirect', function () {
    if (!is_page()) {
        return;
    }
    if (get_page_template_slug() !== 'page-templates/template-afiliados.php') {
        return;
    }
    if (get_option('catucop_afiliados_aleatorio', '1') === '1') {
        nocache_headers();
    }
});

function catucop_afiliado_cats() {
    $order = ['hospedaje', 'tours', 'transporte', 'comercio', 'comunitario'];
    $terms = get_terms([
        'taxonomy' => 'afiliado_cat',
        'hide_empty' => true,
    ]);
    if (is_wp_error($terms)) {
        return [];
    }
    usort($terms, function ($a, $b) use ($order) {
        $ia = array_search($a->slug, $order, true);
        $ib = array_search($b->slug, $order, true);
        $ia = $ia === false ? 99 : $ia;
        $ib = $ib === false ? 99 : $ib;
        return $ia <=> $ib;
    });
    return $terms;
}

function catucop_get_afiliados() {
    $q = new WP_Query([
        'post_type' => 'afiliado',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'no_found_rows' => true,
    ]);
    $posts = $q->posts;
    if (get_option('catucop_afiliados_aleatorio', '1') === '1') {
        shuffle($posts);
    }
    $lang = function_exists('catucop_lang') ? catucop_lang() : 'es';
    $items = [];
    foreach ($posts as $post) {
        $terms = get_the_terms($post->ID, 'afiliado_cat');
        $term = (is_array($terms) && $terms) ? $terms[0] : null;
        $label = $term ? $term->name : '';
        if ($term && $lang === 'en') {
            $en = get_term_meta($term->term_id, 'nombre_en', true);
            if ($en) {
                $label = $en;
            }
        }
        $desc = $post->post_content;
        if ($lang === 'en') {
            $en_desc = get_post_meta($post->ID, 'descripcion_en', true);
            if ($en_desc) {
                $desc = $en_desc;
            }
        }
        $items[] = [
            'id' => $post->ID,
            'nombre' => $post->post_title,
            'logo' => get_the_post_thumbnail_url($post->ID, 'medium') ?: '',
            'cat' => $term ? $term->slug : '',
            'cat_label' => $label,
            'ubicacion' => (string) get_post_meta($post->ID, 'ubicacion', true),
            'telefono' => (string) get_post_meta($post->ID, 'telefono', true),
            'email' => (string) get_post_meta($post->ID, 'email', true),
            'desc' => $desc,
            'enlaces' => [
                'web' => (string) get_post_meta($post->ID, 'web', true),
                'facebook' => (string) get_post_meta($post->ID, 'facebook', true),
                'instagram' => (string) get_post_meta($post->ID, 'instagram', true),
                'tiktok' => (string) get_post_meta($post->ID, 'tiktok', true),
                'tripadvisor' => (string) get_post_meta($post->ID, 'tripadvisor', true),
            ],
        ];
    }
    return $items;
}

if (!function_exists('catucop_import_image')) {
    function catucop_import_image($filepath, $parent = 0) {
        if (!is_readable($filepath)) {
            return 0;
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $filename = sanitize_file_name(basename($filepath));
        $upload = wp_upload_bits($filename, null, file_get_contents($filepath));
        if (!empty($upload['error'])) {
            return 0;
        }
        $type = wp_check_filetype($filename);
        $id = wp_insert_attachment([
            'post_mime_type' => $type['type'] ?: 'image/jpeg',
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
            'post_status' => 'inherit',
        ], $upload['file'], $parent);
        if (!$id || is_wp_error($id)) {
            return 0;
        }
        $meta = wp_generate_attachment_metadata($id, $upload['file']);
        wp_update_attachment_metadata($id, $meta);
        if ($parent) {
            set_post_thumbnail($parent, $id);
        }
        return (int) $id;
    }
}

function catucop_seed_afiliados() {
    if (get_option('catucop_afiliados_seeded')) {
        return;
    }
    $dir = get_stylesheet_directory() . '/assets/seed/logos';
    $json = plugin_dir_path(__FILE__) . 'seed-data.json';
    if (!is_dir($dir) || !is_readable($json)) {
        return;
    }
    $rows = json_decode(file_get_contents($json), true);
    if (!is_array($rows)) {
        return;
    }
    $cats = [
        'hospedaje' => ['Hospedaje', 'Lodging'],
        'tours' => ['Tours', 'Tours'],
        'transporte' => ['Transporte', 'Transport'],
        'comercio' => ['Comercio', 'Business'],
        'comunitario' => ['Comunitario', 'Community'],
    ];
    foreach ($cats as $slug => $names) {
        $term = term_exists($slug, 'afiliado_cat');
        if (!$term) {
            $term = wp_insert_term($names[0], 'afiliado_cat', ['slug' => $slug]);
        }
        if (!is_wp_error($term)) {
            $term_id = is_array($term) ? (int) $term['term_id'] : (int) $term;
            update_term_meta($term_id, 'nombre_en', $names[1]);
        }
    }
    foreach ($rows as $index => $row) {
        $post_id = wp_insert_post([
            'post_type' => 'afiliado',
            'post_status' => 'publish',
            'post_title' => $row['nombre'],
            'post_name' => $row['slug'],
            'post_content' => $row['descripcion_es'],
            'menu_order' => $index + 1,
        ]);
        if (!$post_id || is_wp_error($post_id)) {
            continue;
        }
        update_post_meta($post_id, 'descripcion_en', $row['descripcion_en']);
        update_post_meta($post_id, 'ubicacion', $row['ubicacion']);
        update_post_meta($post_id, 'telefono', $row['telefono']);
        update_post_meta($post_id, 'email', $row['email']);
        foreach (['web', 'facebook', 'instagram', 'tiktok', 'tripadvisor'] as $net) {
            update_post_meta($post_id, $net, $row['enlaces'][$net] ?? '');
        }
        wp_set_object_terms($post_id, $row['categoria'], 'afiliado_cat');
        catucop_import_image($dir . '/' . $row['logo'], $post_id);
    }
    if (get_option('catucop_afiliados_aleatorio', null) === null) {
        update_option('catucop_afiliados_aleatorio', '1');
    }
    update_option('catucop_afiliados_seeded', 1);
}
