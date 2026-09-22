<?php
/**
 * Idioma de la interfaz: español por defecto, inglés con ?lang=en.
 * El contenido editable vive en los plugins; aquí solo están los textos fijos.
 */

if (!defined('ABSPATH')) {
    exit;
}

function catucop_lang() {
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }
    $get = isset($_GET['lang']) ? sanitize_key(wp_unslash($_GET['lang'])) : '';
    if ($get === 'es' || $get === 'en') {
        $lang = $get;
        return $lang;
    }
    $cookie = isset($_COOKIE['catucop_lang']) ? sanitize_key(wp_unslash($_COOKIE['catucop_lang'])) : '';
    $lang = ($cookie === 'en') ? 'en' : 'es';
    return $lang;
}

function catucop_set_lang_cookie($lang) {
    if ($lang !== 'es' && $lang !== 'en') {
        return;
    }
    $paths = ['/'];
    if (defined('COOKIEPATH') && COOKIEPATH) {
        $paths[] = COOKIEPATH;
    }
    if (defined('SITECOOKIEPATH') && SITECOOKIEPATH) {
        $paths[] = SITECOOKIEPATH;
    }
    $paths = array_unique($paths);
    foreach ($paths as $path) {
        $args = [
            'expires' => time() + YEAR_IN_SECONDS,
            'path' => $path,
            'secure' => is_ssl(),
            'httponly' => false,
            'samesite' => 'Lax',
        ];
        if (defined('COOKIE_DOMAIN') && COOKIE_DOMAIN) {
            $args['domain'] = COOKIE_DOMAIN;
        }
        setcookie('catucop_lang', $lang, $args);
    }
    $_COOKIE['catucop_lang'] = $lang;
}

add_action('init', function () {
    if (!isset($_GET['lang'])) {
        return;
    }
    $get = sanitize_key(wp_unslash($_GET['lang']));
    if ($get !== 'es' && $get !== 'en') {
        return;
    }
    catucop_set_lang_cookie($get);
}, 1);

add_filter('language_attributes', function () {
    return 'lang="' . esc_attr(catucop_lang()) . '"';
});

function catucop_dictionary() {
    static $dict = null;
    if ($dict !== null) {
        return $dict;
    }
    $dict = [
        'nav.inicio' => ['es' => 'Inicio', 'en' => 'Home'],
        'nav.afiliados' => ['es' => 'Afiliados', 'en' => 'Affiliates'],
        'nav.lugares' => ['es' => 'Lugares / Horarios', 'en' => 'Places / Schedules'],
        'brand.sub' => [
            'es' => 'Cámara de Turismo de Puerto Jiménez',
            'en' => 'Puerto Jiménez Chamber of Tourism',
        ],
        'home.places.title' => ['es' => 'Playas y paisajes', 'en' => 'Beaches & landscapes'],
        'home.places.lead' => [
            'es' => 'Arena oscura y aguas calmas del Golfo Dulce, o el oleaje salvaje del Pacífico. Estas son algunas de las playas de Osa.',
            'en' => 'Dark sand and the calm waters of the Golfo Dulce, or the wild Pacific surf. These are some of Osa’s beaches.',
        ],
        'home.events.title' => ['es' => 'Eventos y comunidad', 'en' => 'Events & community'],
        'home.events.social' => [
            'es' => 'Para más información síganos en redes sociales',
            'en' => 'For more information follow us on social media',
        ],
        'home.emergency.title' => [
            'es' => 'En caso de emergencia, sepa a quién acudir',
            'en' => 'In an emergency, know who to call',
        ],
        'home.emergency.body' => [
            'es' => 'Si llegara a ocurrir una emergencia, lo mejor es llamar al 911. Se encargarán de abordar la situación de la mejor manera posible, canalizando el caso a la autoridad correspondiente.',
            'en' => 'If an emergency occurs, the best option is to call 911. They will handle the situation and route your case to the appropriate authority.',
        ],
        'home.hitos.title' => [
            'es' => '¿Por qué visitar la Península de Osa?',
            'en' => 'Why visit the Osa Peninsula?',
        ],
        'home.source' => ['es' => 'Fuente', 'en' => 'Source'],
        'aff.title' => ['es' => 'Nuestros afiliados', 'en' => 'Our affiliates'],
        'aff.lead' => [
            'es' => 'Hospedajes, tours, transporte y comercios comprometidos con el turismo sostenible de la Península de Osa. Haz clic en un afiliado para conocer más.',
            'en' => 'Lodging, tours, transport and businesses committed to sustainable tourism on the Osa Peninsula. Click an affiliate to learn more.',
        ],
        'aff.filter.all' => ['es' => 'Todos', 'en' => 'All'],
        'aff.visit' => ['es' => 'Visitar sitio web', 'en' => 'Visit website'],
        'aff.close' => ['es' => 'Cerrar', 'en' => 'Close'],
        'aff.location' => ['es' => 'Ubicación', 'en' => 'Location'],
        'aff.phone' => ['es' => 'Teléfono', 'en' => 'Phone'],
        'aff.email' => ['es' => 'Correo', 'en' => 'Email'],
        'aff.empty' => ['es' => 'Todavía no hay afiliados publicados.', 'en' => 'No affiliates published yet.'],
        'places.map.title' => [
            'es' => 'Ubique lugares con nuestro mapa interactivo de la Península',
            'en' => 'Find places with our interactive map of the Peninsula',
        ],
        'places.map.load' => ['es' => 'Cargar mapa interactivo', 'en' => 'Load interactive map'],
        'places.map.hint' => [
            'es' => '42 sitios documentados: playas, cataratas, reservas y destinos comunitarios de Osa.',
            'en' => '42 documented sites: beaches, waterfalls, reserves and community destinations across Osa.',
        ],
        'places.map.close' => ['es' => 'Cerrar mapa', 'en' => 'Close map'],
        'places.map.mobile' => [
            'es' => 'En el teléfono el mapa se abre en otra pestaña, para no cargar el relieve 3D dentro de esta página.',
            'en' => 'On a phone the map opens in another tab, so the 3D relief is not loaded inside this page.',
        ],
        'places.schedules.title' => ['es' => 'Horarios y rutas', 'en' => 'Schedules & routes'],
        'places.buses' => ['es' => 'Buses', 'en' => 'Buses'],
        'places.boats' => ['es' => 'Botes', 'en' => 'Boats'],
        'places.days.lv' => ['es' => 'Lunes a viernes', 'en' => 'Mon – Fri'],
        'places.days.sab' => ['es' => 'Sábado', 'en' => 'Saturday'],
        'places.days.dom' => ['es' => 'Domingo', 'en' => 'Sunday'],
        'places.dest.title' => ['es' => 'Destinos de la cámara', 'en' => 'Chamber destinations'],
        'places.dest.lead' => [
            'es' => 'Sitios que la cámara agrega o actualiza. También aparecen en el listado del mapa. La ruta se abre en Google Maps.',
            'en' => 'Sites the chamber adds or updates. They also appear in the map list. Directions open in Google Maps.',
        ],
        'places.dest.go' => ['es' => 'Cómo llegar', 'en' => 'Directions'],
        'footer.rights' => [
            'es' => 'Cámara de Turismo de Puerto Jiménez. Todos los derechos reservados.',
            'en' => 'Puerto Jiménez Chamber of Tourism. All rights reserved.',
        ],
        'footer.tagline' => [
            'es' => 'Turismo sostenible en la Península de Osa',
            'en' => 'Sustainable tourism on the Osa Peninsula',
        ],
        'footer.place' => ['es' => 'Puerto Jiménez, Osa · Costa Rica', 'en' => 'Puerto Jiménez, Osa · Costa Rica'],
        'seo.fallback' => [
            'es' => 'Cámara de Turismo de Puerto Jiménez. Turismo sostenible en la Península de Osa y Corcovado.',
            'en' => 'Puerto Jiménez Chamber of Tourism. Sustainable tourism on the Osa Peninsula and Corcovado.',
        ],
    ];
    return $dict;
}

function catucop_t($key) {
    $dict = catucop_dictionary();
    if (!isset($dict[$key])) {
        return $key;
    }
    $lang = catucop_lang();
    return $dict[$key][$lang] ?? $dict[$key]['es'];
}

function catucop_url($url) {
    $url = remove_query_arg('lang', $url);
    if (catucop_lang() === 'en') {
        $url = add_query_arg('lang', 'en', $url);
    }
    return $url;
}

function catucop_switch_url($target) {
    $target = ($target === 'en') ? 'en' : 'es';
    return add_query_arg('lang', $target);
}

function catucop_page_url($key) {
    $id = (int) get_option('catucop_page_' . $key);
    $url = $id ? get_permalink($id) : home_url('/');
    return catucop_url($url);
}

function catucop_img($file) {
    $file = ltrim(str_replace('\\', '/', (string) $file), '/');
    $map = get_option('catucop_image_map', []);
    if (!is_array($map)) {
        $map = [];
    }
    $id = isset($map[$file]) ? (int) $map[$file] : 0;
    if ($id) {
        $url = wp_get_attachment_url($id);
        if ($url) {
            return $url;
        }
    }
    $path = get_stylesheet_directory() . '/assets/img/' . $file;
    if (!is_readable($path) || !function_exists('catucop_import_image')) {
        return '';
    }
    $id = catucop_import_image($path, 0);
    if (!$id) {
        return '';
    }
    $map[$file] = $id;
    update_option('catucop_image_map', $map, false);
    return (string) wp_get_attachment_url($id);
}

function catucop_icon_globe() {
    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a15 15 0 0 1 0 18a15 15 0 0 1 0-18Z"/></svg>';
}

function catucop_field($row, $key) {
    if (!is_array($row)) {
        return '';
    }
    if (catucop_lang() === 'en' && !empty($row[$key . '_en'])) {
        return $row[$key . '_en'];
    }
    if (isset($row[$key . '_es'])) {
        return $row[$key . '_es'];
    }
    return $row[$key] ?? '';
}

function catucop_hours($value) {
    $parts = preg_split('/\s*,\s*/', (string) $value);
    if (!is_array($parts)) {
        return [];
    }
    return array_values(array_filter(array_map('trim', $parts)));
}
