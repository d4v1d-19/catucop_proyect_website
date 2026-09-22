<?php
/**
 * CATUCOP child theme.
 *
 * Understrap queda como tema padre (jerarquía de plantillas, menús, soporte de
 * WordPress). Sus hojas Bootstrap no se cargan: el HTML público usa el CSS de
 * este tema para que la página pese menos y respete el diseño.
 */

if (!defined('ABSPATH')) {
    exit;
}

require get_stylesheet_directory() . '/inc/lang.php';
require get_stylesheet_directory() . '/inc/setup.php';
require get_stylesheet_directory() . '/inc/seed.php';
