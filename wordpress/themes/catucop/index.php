<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<div class="wrap prose-page">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            echo '<h1 class="headline">' . esc_html(get_the_title()) . '</h1>';
            the_content();
        }
    }
    ?>
</div>
<?php
get_footer();
