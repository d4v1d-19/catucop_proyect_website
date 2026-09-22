<?php
/**
 * Activa el tema hijo, los plugins y carga el contenido inicial.
 * Uso: php wordpress/tools/activate-catucop.php
 */

define('WP_USE_THEMES', false);
require 'C:/xampp/htdocs/catucop/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugins = [
    'catucop-seo/catucop-seo.php',
    'catucop-afiliados/catucop-afiliados.php',
    'catucop-contenido/catucop-contenido.php',
];

foreach ($plugins as $plugin) {
    $result = activate_plugin($plugin);
    if (is_wp_error($result)) {
        fwrite(STDERR, $result->get_error_message() . PHP_EOL);
        exit(1);
    }
    echo "plugin $plugin\n";
}

switch_theme('catucop');
require get_theme_root() . '/catucop/functions.php';
echo 'stylesheet ' . get_stylesheet() . PHP_EOL;

catucop_register_afiliados();
catucop_register_contenido();
catucop_seed_site();
catucop_seed_afiliados();
catucop_seed_contenido();
flush_rewrite_rules(true);

echo 'pages ' . (int) get_option('page_on_front') . PHP_EOL;
echo 'afiliados ' . (int) wp_count_posts('afiliado')->publish . PHP_EOL;
echo 'actividades ' . (int) wp_count_posts('actividad')->publish . PHP_EOL;
echo 'eventos ' . (int) wp_count_posts('evento')->publish . PHP_EOL;
echo 'hitos ' . (int) wp_count_posts('hito')->publish . PHP_EOL;
echo 'contactos ' . (int) wp_count_posts('contacto')->publish . PHP_EOL;
echo "ok\n";
