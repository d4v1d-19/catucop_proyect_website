<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
foreach (catucop_section_order() as $section) {
    get_template_part('template-parts/section', $section);
}
get_footer();
