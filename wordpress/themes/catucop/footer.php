<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div>
            <div class="brand">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <img class="footer-logo" src="<?php echo esc_url(catucop_img('logo.png')); ?>" alt="CATUCOP" width="48" height="48">
                <?php endif; ?>
                <div>
                    <p class="brand-name">CATUCOP</p>
                    <p class="muted"><?php echo esc_html(catucop_t('brand.sub')); ?></p>
                </div>
            </div>
            <p class="footer-tagline muted"><?php echo esc_html(catucop_t('footer.tagline')); ?></p>
        </div>
        <nav class="footer-nav" aria-label="Footer">
            <?php catucop_menu('footer', 'footer'); ?>
        </nav>
        <div>
            <div class="footer-social"><?php catucop_social_links(); ?></div>
            <p class="muted footer-place"><?php echo esc_html(catucop_t('footer.place')); ?></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p class="wrap muted">© <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(catucop_t('footer.rights')); ?></p>
    </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
