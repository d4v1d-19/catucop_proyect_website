<?php
/**
 * Plugin Name: CATUCOP Contenido
 * Description: Actividades, eventos, hitos, contactos, destinos del mapa, horarios y el orden de las secciones del inicio.
 * Version: 1.0.0
 * Author: CATUCOP
 * Text Domain: catucop-contenido
 */

if (!defined('ABSPATH')) {
    exit;
}

function catucop_contenido_types() {
    return [
        'actividad' => ['Actividades', 'Actividad', 'Añadir actividad', true],
        'evento' => ['Eventos', 'Evento', 'Añadir evento', true],
        'hito' => ['Hitos', 'Hito', 'Añadir hito', true],
        'destino' => ['Destinos', 'Destino', 'Añadir destino', true],
        'contacto' => ['Contactos', 'Contacto', 'Añadir contacto', false],
    ];
}

function catucop_register_contenido() {
    foreach (catucop_contenido_types() as $type => $label) {
        $supports = ['title', 'thumbnail', 'page-attributes'];
        if ($label[3]) {
            $supports[] = 'editor';
        }
        register_post_type($type, [
            'labels' => [
                'name' => $label[0],
                'singular_name' => $label[1],
                'add_new_item' => $label[2],
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'catucop-contenido',
            'supports' => $supports,
            'rewrite' => false,
            'exclude_from_search' => true,
        ]);
    }
}
add_action('init', 'catucop_register_contenido');

add_action('admin_menu', function () {
    add_menu_page('CATUCOP', 'CATUCOP', 'manage_options', 'catucop-contenido', 'catucop_orden_page', 'dashicons-palmtree', 26);
    add_submenu_page('catucop-contenido', 'Orden del inicio', 'Orden del inicio', 'manage_options', 'catucop-contenido', 'catucop_orden_page');
    add_submenu_page('catucop-contenido', 'Horarios y rutas', 'Horarios y rutas', 'manage_options', 'catucop-horarios', 'catucop_horarios_page');
}, 9);

add_action('add_meta_boxes', function () {
    foreach (['actividad', 'evento', 'hito', 'destino'] as $type) {
        add_meta_box('catucop_contenido_box', 'Textos e información', 'catucop_contenido_box', $type, 'normal', 'high');
    }
});

function catucop_contenido_box($post) {
    wp_nonce_field('catucop_contenido', 'catucop_contenido_nonce');
    echo '<p>El título y el editor son el texto en español. La imagen destacada es la foto. El número de Orden define la posición: el menor aparece primero.</p>';
    $titulo_en = get_post_meta($post->ID, 'titulo_en', true);
    $desc_en = get_post_meta($post->ID, 'descripcion_en', true);
    echo '<p><label><strong>Título en inglés</strong><br><input style="width:100%" type="text" name="catucop_titulo_en" value="' . esc_attr($titulo_en) . '"></label></p>';
    echo '<p><label><strong>Descripción en inglés</strong><br><textarea style="width:100%" rows="5" name="catucop_descripcion_en">' . esc_textarea($desc_en) . '</textarea></label></p>';
    if ($post->post_type === 'hito') {
        echo '<p><label><strong>Fuente</strong><br><input style="width:100%" type="text" name="catucop_fuente_label" value="' . esc_attr(get_post_meta($post->ID, 'fuente_label', true)) . '"></label></p>';
        echo '<p><label><strong>Enlace de la fuente</strong><br><input style="width:100%" type="url" name="catucop_fuente_url" value="' . esc_attr(get_post_meta($post->ID, 'fuente_url', true)) . '"></label></p>';
    }
    if ($post->post_type === 'destino') {
        echo '<p>Un destino nuevo entra en la lista de la página y en el panel del mapa. La ruta se abre en Google Maps. El relieve 3D conserva los sitios ya cartografiados.</p>';
        echo '<p><label><strong>Sector</strong><br><input style="width:100%" type="text" name="catucop_sector" value="' . esc_attr(get_post_meta($post->ID, 'sector', true)) . '"></label></p>';
        echo '<p><label><strong>Latitud</strong><br><input style="width:100%" type="text" name="catucop_lat" value="' . esc_attr(get_post_meta($post->ID, 'lat', true)) . '"></label></p>';
        echo '<p><label><strong>Longitud</strong><br><input style="width:100%" type="text" name="catucop_lon" value="' . esc_attr(get_post_meta($post->ID, 'lon', true)) . '"></label></p>';
        echo '<p><label><strong>Enlace de Google Maps</strong><br><input style="width:100%" type="url" name="catucop_maps" value="' . esc_attr(get_post_meta($post->ID, 'maps', true)) . '"></label></p>';
        echo '<p>Si deja el enlace vacío y escribe latitud y longitud, el sitio arma el enlace solo.</p>';
    }
}

add_action('save_post', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['catucop_contenido_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['catucop_contenido_nonce'])), 'catucop_contenido')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $map = [
        'titulo_en' => 'text',
        'descripcion_en' => 'html',
        'fuente_label' => 'text',
        'fuente_url' => 'url',
        'sector' => 'text',
        'lat' => 'text',
        'lon' => 'text',
        'maps' => 'url',
    ];
    foreach ($map as $key => $kind) {
        if (!isset($_POST['catucop_' . $key])) {
            continue;
        }
        $raw = wp_unslash($_POST['catucop_' . $key]);
        if ($kind === 'url') {
            $value = esc_url_raw($raw);
        } elseif ($kind === 'html') {
            $value = wp_kses_post($raw);
        } else {
            $value = sanitize_text_field($raw);
        }
        update_post_meta($post_id, $key, $value);
    }
    if (get_post_type($post_id) === 'destino') {
        catucop_sync_destinos_json();
    }
});

add_action('before_delete_post', function ($post_id) {
    if (get_post_type($post_id) === 'destino') {
        add_action('deleted_post', function () {
            catucop_sync_destinos_json();
        });
    }
});

function catucop_sync_destinos_json() {
    $q = new WP_Query([
        'post_type' => 'destino',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'no_found_rows' => true,
    ]);
    $out = [];
    foreach ($q->posts as $post) {
        $lat = (string) get_post_meta($post->ID, 'lat', true);
        $lon = (string) get_post_meta($post->ID, 'lon', true);
        $maps = (string) get_post_meta($post->ID, 'maps', true);
        if ($maps === '' && $lat !== '' && $lon !== '') {
            $maps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lon);
        }
        $out[] = [
            'nombre' => get_the_title($post),
            'maps' => $maps,
        ];
    }
    $dir = WP_CONTENT_DIR . '/uploads/catucop';
    if (!is_dir($dir)) {
        wp_mkdir_p($dir);
    }
    file_put_contents($dir . '/destinos.json', wp_json_encode($out));
}

function catucop_section_labels() {
    return [
        'hero' => 'Hero',
        'hitos' => 'Hitos',
        'actividades' => 'Actividades',
        'eventos' => 'Eventos',
        'contactos' => 'Contactos',
    ];
}

function catucop_orden_page() {
    $labels = catucop_section_labels();
    $order = function_exists('catucop_section_order') ? catucop_section_order() : array_keys($labels);
    if (isset($_POST['catucop_move']) && check_admin_referer('catucop_orden')) {
        $index = isset($_POST['index']) ? (int) $_POST['index'] : -1;
        $dir = isset($_POST['dir']) ? sanitize_key(wp_unslash($_POST['dir'])) : '';
        if ($dir === 'up' && $index > 0) {
            $tmp = $order[$index - 1];
            $order[$index - 1] = $order[$index];
            $order[$index] = $tmp;
        }
        if ($dir === 'down' && $index < count($order) - 1 && $index >= 0) {
            $tmp = $order[$index + 1];
            $order[$index + 1] = $order[$index];
            $order[$index] = $tmp;
        }
        $order = array_values($order);
        update_option('catucop_home_order', $order);
        echo '<div class="updated"><p>Orden del inicio actualizado.</p></div>';
    }
    echo '<div class="wrap"><h1>Orden del inicio</h1>';
    echo '<p>El primer bloque es el que se ve arriba. Las actividades, eventos, hitos y contactos se editan en el menú de la izquierda.</p><ol>';
    foreach ($order as $index => $key) {
        echo '<li style="margin:8px 0"><strong>' . esc_html($labels[$key] ?? $key) . '</strong> ';
        echo '<form method="post" style="display:inline">';
        wp_nonce_field('catucop_orden');
        echo '<input type="hidden" name="catucop_move" value="1">';
        echo '<input type="hidden" name="index" value="' . esc_attr((string) $index) . '">';
        echo '<button class="button" name="dir" value="up">Subir</button> ';
        echo '<button class="button" name="dir" value="down">Bajar</button>';
        echo '</form></li>';
    }
    echo '</ol></div>';
}

function catucop_horarios_defaults() {
    return [
        'bus_operador' => 'Transportes Blanco Lobo',
        'bote_operador' => 'Puerto Jiménez ⇄ Golfito',
        'bus_image' => 0,
        'bote_image' => 0,
        'rutas' => [
            [
                'titulo_es' => 'San José → Puerto Jiménez',
                'titulo_en' => 'San José → Puerto Jiménez',
                'hora' => '12:00 MD',
                'nota_es' => 'Sale de San José',
                'nota_en' => 'Departs San José',
            ],
            [
                'titulo_es' => 'Puerto Jiménez → San José',
                'titulo_en' => 'Puerto Jiménez → San José',
                'hora' => '5:00 AM',
                'nota_es' => 'Sale de Puerto Jiménez',
                'nota_en' => 'Departs Puerto Jiménez',
            ],
        ],
        'bus_ubic' => [
            ['label_es' => 'Ubicación PJ', 'label_en' => 'Location PJ', 'url' => 'https://maps.app.goo.gl/rCM2Jw7kbuEdkuTGA'],
            ['label_es' => 'Ubicación en SJ', 'label_en' => 'Location in SJ', 'url' => 'https://www.google.com/maps/search/?api=1&query=Terminal+Blanco+Lobo+San+Jose+Costa+Rica'],
        ],
        'tablas' => [
            [
                'titulo_es' => 'Puerto Jiménez → Golfito',
                'titulo_en' => 'Puerto Jiménez → Golfito',
                'lv' => '6:15, 7:45, 11:00, 3:00',
                'sab' => '7:00, 9:30, 2:00',
                'dom' => '7:30, 2:00',
            ],
            [
                'titulo_es' => 'Golfito → Puerto Jiménez',
                'titulo_en' => 'Golfito → Puerto Jiménez',
                'lv' => '7:00, 10:00, 1:00, 4:30',
                'sab' => '8:00, 11:00, 3:00',
                'dom' => '10:00, 3:00',
            ],
        ],
        'bote_ubic' => [
            ['label_es' => 'Ubicación PJ', 'label_en' => 'Location PJ', 'url' => 'https://www.google.com/maps/search/?api=1&query=Muelle+Puerto+Jimenez'],
            ['label_es' => 'Ubicación Golfito', 'label_en' => 'Location Golfito', 'url' => 'https://www.google.com/maps/search/?api=1&query=Muelle+lanchas+Golfito'],
        ],
    ];
}

function catucop_get_horarios() {
    $saved = get_option('catucop_horarios', []);
    $data = wp_parse_args(is_array($saved) ? $saved : [], catucop_horarios_defaults());
    $bus = (int) $data['bus_image'];
    $bote = (int) $data['bote_image'];
    $data['bus_image_url'] = $bus ? (wp_get_attachment_image_url($bus, 'large') ?: '') : '';
    $data['bote_image_url'] = $bote ? (wp_get_attachment_image_url($bote, 'large') ?: '') : '';
    if ($data['bus_image_url'] === '' && function_exists('catucop_img')) {
        $data['bus_image_url'] = catucop_img('bus.png');
    }
    if ($data['bote_image_url'] === '' && function_exists('catucop_img')) {
        $data['bote_image_url'] = catucop_img('bote.png');
    }
    return $data;
}

function catucop_horarios_page() {
    if (isset($_POST['catucop_horarios_save']) && check_admin_referer('catucop_horarios')) {
        $clean_rows = function ($rows, $keys) {
            $out = [];
            if (!is_array($rows)) {
                return $out;
            }
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $item = [];
                $empty = true;
                foreach ($keys as $key => $kind) {
                    $raw = isset($row[$key]) ? wp_unslash($row[$key]) : '';
                    $item[$key] = $kind === 'url' ? esc_url_raw($raw) : sanitize_text_field($raw);
                    if ($item[$key] !== '') {
                        $empty = false;
                    }
                }
                if (!$empty) {
                    $out[] = $item;
                }
            }
            return $out;
        };
        $data = [
            'bus_operador' => sanitize_text_field(wp_unslash($_POST['bus_operador'] ?? '')),
            'bote_operador' => sanitize_text_field(wp_unslash($_POST['bote_operador'] ?? '')),
            'bus_image' => (int) ($_POST['bus_image'] ?? 0),
            'bote_image' => (int) ($_POST['bote_image'] ?? 0),
            'rutas' => $clean_rows($_POST['rutas'] ?? [], [
                'titulo_es' => 'text', 'titulo_en' => 'text', 'hora' => 'text', 'nota_es' => 'text', 'nota_en' => 'text',
            ]),
            'bus_ubic' => $clean_rows($_POST['bus_ubic'] ?? [], [
                'label_es' => 'text', 'label_en' => 'text', 'url' => 'url',
            ]),
            'tablas' => $clean_rows($_POST['tablas'] ?? [], [
                'titulo_es' => 'text', 'titulo_en' => 'text', 'lv' => 'text', 'sab' => 'text', 'dom' => 'text',
            ]),
            'bote_ubic' => $clean_rows($_POST['bote_ubic'] ?? [], [
                'label_es' => 'text', 'label_en' => 'text', 'url' => 'url',
            ]),
        ];
        update_option('catucop_horarios', $data);
        echo '<div class="updated"><p>Horarios guardados.</p></div>';
    }
    $data = catucop_get_horarios();
    echo '<div class="wrap"><h1>Horarios y rutas</h1>';
    echo '<p>Las horas de los botes van separadas por comas. Puede dejar filas vacías: no se publican.</p>';
    echo '<form method="post">';
    wp_nonce_field('catucop_horarios');
    echo '<input type="hidden" name="catucop_horarios_save" value="1">';
    echo '<h2>Buses</h2><p><label>Operador<br><input class="regular-text" name="bus_operador" value="' . esc_attr($data['bus_operador']) . '"></label></p>';
    echo '<p><label>ID de imagen del bus (0 usa la imagen del tema)<br><input name="bus_image" value="' . esc_attr((string) $data['bus_image']) . '"></label></p>';
    echo '<table class="widefat"><thead><tr><th>Título ES</th><th>Título EN</th><th>Hora</th><th>Nota ES</th><th>Nota EN</th></tr></thead><tbody>';
    $rutas = array_merge($data['rutas'], array_fill(0, 2, []));
    foreach ($rutas as $i => $row) {
        echo '<tr>';
        foreach (['titulo_es', 'titulo_en', 'hora', 'nota_es', 'nota_en'] as $key) {
            echo '<td><input style="width:100%" name="rutas[' . (int) $i . '][' . $key . ']" value="' . esc_attr($row[$key] ?? '') . '"></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<h3>Ubicaciones del bus</h3><table class="widefat"><thead><tr><th>Etiqueta ES</th><th>Etiqueta EN</th><th>URL</th></tr></thead><tbody>';
    $ubus = array_merge($data['bus_ubic'], array_fill(0, 1, []));
    foreach ($ubus as $i => $row) {
        echo '<tr>';
        foreach (['label_es', 'label_en', 'url'] as $key) {
            echo '<td><input style="width:100%" name="bus_ubic[' . (int) $i . '][' . $key . ']" value="' . esc_attr($row[$key] ?? '') . '"></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<h2>Botes</h2><p><label>Operador<br><input class="regular-text" name="bote_operador" value="' . esc_attr($data['bote_operador']) . '"></label></p>';
    echo '<p><label>ID de imagen del bote (0 usa la imagen del tema)<br><input name="bote_image" value="' . esc_attr((string) $data['bote_image']) . '"></label></p>';
    echo '<table class="widefat"><thead><tr><th>Título ES</th><th>Título EN</th><th>Lunes a viernes</th><th>Sábado</th><th>Domingo</th></tr></thead><tbody>';
    $tablas = array_merge($data['tablas'], array_fill(0, 1, []));
    foreach ($tablas as $i => $row) {
        echo '<tr>';
        foreach (['titulo_es', 'titulo_en', 'lv', 'sab', 'dom'] as $key) {
            echo '<td><input style="width:100%" name="tablas[' . (int) $i . '][' . $key . ']" value="' . esc_attr($row[$key] ?? '') . '"></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<h3>Ubicaciones del bote</h3><table class="widefat"><thead><tr><th>Etiqueta ES</th><th>Etiqueta EN</th><th>URL</th></tr></thead><tbody>';
    $ubote = array_merge($data['bote_ubic'], array_fill(0, 1, []));
    foreach ($ubote as $i => $row) {
        echo '<tr>';
        foreach (['label_es', 'label_en', 'url'] as $key) {
            echo '<td><input style="width:100%" name="bote_ubic[' . (int) $i . '][' . $key . ']" value="' . esc_attr($row[$key] ?? '') . '"></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    submit_button('Guardar horarios');
    echo '</form></div>';
}

function catucop_posts_bilingual($type) {
    $q = new WP_Query([
        'post_type' => $type,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'ASC'],
        'no_found_rows' => true,
        'update_post_term_cache' => false,
    ]);
    $lang = function_exists('catucop_lang') ? catucop_lang() : 'es';
    $out = [];
    foreach ($q->posts as $post) {
        $title = $post->post_title;
        $text = $post->post_content;
        if ($lang === 'en') {
            $title_en = get_post_meta($post->ID, 'titulo_en', true);
            $text_en = get_post_meta($post->ID, 'descripcion_en', true);
            if ($title_en) {
                $title = $title_en;
            }
            if ($text_en) {
                $text = $text_en;
            }
        }
        $out[] = [
            'id' => $post->ID,
            'title' => $title,
            'text' => $text,
            'image' => get_the_post_thumbnail_url($post->ID, 'large') ?: '',
            'fuente_label' => (string) get_post_meta($post->ID, 'fuente_label', true),
            'fuente_url' => (string) get_post_meta($post->ID, 'fuente_url', true),
            'maps' => (string) get_post_meta($post->ID, 'maps', true),
            'sector' => (string) get_post_meta($post->ID, 'sector', true),
            'lat' => (string) get_post_meta($post->ID, 'lat', true),
            'lon' => (string) get_post_meta($post->ID, 'lon', true),
        ];
    }
    return $out;
}

function catucop_seed_contenido() {
    if (get_option('catucop_contenido_seeded')) {
        return;
    }
    $base = get_stylesheet_directory() . '/assets/seed';
    if (!is_dir($base)) {
        return;
    }
    if (!function_exists('catucop_import_image')) {
        return;
    }

    $hitos = [
        ['Biodiversidad récord', 'Record-breaking biodiversity', 'Concentra el 2,5% de la biodiversidad del planeta y más del 50% de la de Costa Rica en apenas 1.740 km². Lapas rojas, tapires, jaguares y cuatro especies de monos habitan sus bosques.', 'Home to 2.5% of the planet’s biodiversity and over 50% of Costa Rica’s, in just 1,740 km². Scarlet macaws, tapirs, jaguars and four monkey species live in its forests.', 'Instituto Costarricense de Turismo (ICT) · National Geographic', 'https://www.ict.go.cr/flipbook/guias/PDF/OSA.pdf'],
        ['Parque Nacional Corcovado', 'Corcovado National Park', 'El corazón del turismo de aventura en Osa: caminatas guiadas, la estación Sirena y la mayor extensión de bosque lluvioso primario de la costa pacífica de Centroamérica.', 'The heart of adventure tourism in Osa: guided hikes, the Sirena station and the largest stretch of primary rainforest on Central America’s Pacific coast.', 'National Geographic · Guía Turística de Osa (ICT)', 'https://www.ict.go.cr/flipbook/guias/PDF/OSA.pdf'],
        ['Golfo Dulce, fiordo tropical', 'Golfo Dulce, a tropical fjord', 'Uno de los únicos cuatro fiordos tropicales del planeta. Cuna de ballenas jorobadas, hogar de tiburones martillo y delfines, y escenario de noches de bioluminiscencia.', 'One of only four tropical fjords on Earth. A humpback whale nursery, home to hammerhead sharks and dolphins, and the setting for bioluminescent nights.', 'Revista de Biología Tropical (UCR / SciELO)', 'https://www.scielo.sa.cr/pdf/rbt/v63s1/0034-7744-rbt-63-s1-351.pdf'],
        ['Puerto Jiménez, la puerta de entrada', 'Puerto Jiménez, the gateway', 'El pueblo desde donde se organiza todo: vuelos directos desde San José, botes a Golfito, tours certificados, hospedajes sostenibles y gastronomía local.', 'The town where every trip begins: direct flights from San José, boats to Golfito, certified tours, sustainable lodging and local cuisine.', 'Guía Turística Cultural de Osa (ICT)', 'https://www.ict.go.cr/flipbook/guias/PDF/OSA.pdf'],
    ];
    foreach ($hitos as $i => $row) {
        $id = wp_insert_post([
            'post_type' => 'hito',
            'post_status' => 'publish',
            'post_title' => $row[0],
            'post_content' => $row[2],
            'menu_order' => $i + 1,
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'titulo_en', $row[1]);
            update_post_meta($id, 'descripcion_en', $row[3]);
            update_post_meta($id, 'fuente_label', $row[4]);
            update_post_meta($id, 'fuente_url', $row[5]);
        }
    }

    $acts = [
        ['Cabo Matapalo', 'Cabo Matapalo', 'Punta sur de la península, donde el bosque primario llega hasta el acantilado.', 'Southern point of the peninsula, where primary forest meets the cliff.', 'playas/cabo-matapalo.jpg'],
        ['Playa Preciosa', 'Playa Preciosa', 'Arena oscura y aguas del Golfo Dulce, a pocos kilómetros de Puerto Jiménez.', 'Dark sand and Golfo Dulce water, a few kilometres from Puerto Jiménez.', 'playas/playa-preciosa.jpg'],
        ['Playa Blanca', 'Playa Blanca', 'Una de las playas de Osa, entre el bosque y el mar.', 'One of Osa’s beaches, between the forest and the sea.', 'playas/playa-blanca.jpg'],
    ];
    foreach ($acts as $i => $row) {
        $id = wp_insert_post([
            'post_type' => 'actividad',
            'post_status' => 'publish',
            'post_title' => $row[0],
            'post_content' => $row[2],
            'menu_order' => $i + 1,
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'titulo_en', $row[1]);
            update_post_meta($id, 'descripcion_en', $row[3]);
            catucop_import_image($base . '/' . $row[4], $id);
        }
    }

    $events = [
        ['Festival Cuna de las Ballenas', 'Cradle of the Whales Festival', 'Vive el avistamiento de ballenas jorobadas en el Golfo Dulce, con actividades culturales y de conservación marina.', 'Experience humpback whale watching in the Golfo Dulce, with cultural and marine conservation activities.', 'eventos/ballenas.jpg'],
        ['Feria del Ceviche', 'Ceviche Fair', 'Saborea la tradición en la Feria del Ceviche de Puerto Jiménez: gastronomía local, mariscos frescos y cultura de Osa.', 'Taste tradition at the Puerto Jiménez Ceviche Fair: local cuisine, fresh seafood and Osa culture.', 'eventos/ceviche.jpg'],
        ['Megalimpieza anual de Corcovado', 'Annual Corcovado Cleanup', 'Como parte de su compromiso ambiental, la Cámara apoya la mega limpieza de Corcovado. Cuidamos juntos nuestro entorno.', 'As part of its environmental commitment, the Chamber supports the Corcovado mega cleanup. Together we care for our home.', 'eventos/limpieza.jpg'],
    ];
    foreach ($events as $i => $row) {
        $id = wp_insert_post([
            'post_type' => 'evento',
            'post_status' => 'publish',
            'post_title' => $row[0],
            'post_content' => $row[2],
            'menu_order' => $i + 1,
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'titulo_en', $row[1]);
            update_post_meta($id, 'descripcion_en', $row[3]);
            catucop_import_image($base . '/' . $row[4], $id);
        }
    }

    $contacts = [
        ['Fuerza Pública', 'contactos/fuerza-publica.png'],
        ['Cruz Roja', 'contactos/cruz-roja.png'],
        ['Policía Turística', 'contactos/policia-turistica.png'],
        ['Bomberos', 'contactos/bomberos.png'],
    ];
    foreach ($contacts as $i => $row) {
        $id = wp_insert_post([
            'post_type' => 'contacto',
            'post_status' => 'publish',
            'post_title' => $row[0],
            'menu_order' => $i + 1,
        ]);
        if ($id && !is_wp_error($id)) {
            catucop_import_image($base . '/' . $row[1], $id);
        }
    }

    if (!get_option('catucop_horarios')) {
        update_option('catucop_horarios', catucop_horarios_defaults());
    }
    if (!get_option('catucop_home_order')) {
        update_option('catucop_home_order', ['hero', 'hitos', 'actividades', 'eventos', 'contactos']);
    }
    catucop_sync_destinos_json();
    update_option('catucop_contenido_seeded', 1);
}
