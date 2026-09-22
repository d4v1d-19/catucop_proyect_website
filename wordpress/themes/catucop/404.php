<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<div class="wrap prose-page">
    <h1 class="headline"><?php echo esc_html(catucop_lang() === 'es' ? 'Página no encontrada' : 'Page not found'); ?></h1>
    <p><a class="btn-lime" href="<?php echo esc_url(catucop_url(home_url('/'))); ?>"><?php echo esc_html(catucop_t('nav.inicio')); ?></a></p>
</div>
<?php
get_footer();
