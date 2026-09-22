<?php
$items = function_exists('catucop_posts_bilingual') ? catucop_posts_bilingual('hito') : [];
if (!$items) {
    return;
}
?>
<section class="section section-soft">
    <div class="wrap center">
        <h2 class="headline section-title"><?php echo esc_html(catucop_t('home.hitos.title')); ?></h2>
        <div class="hito-grid">
            <?php foreach ($items as $item) : ?>
                <article class="hito-card">
                    <h3><?php echo esc_html($item['title']); ?></h3>
                    <p><?php echo esc_html(wp_strip_all_tags($item['text'])); ?></p>
                    <?php if (!empty($item['fuente_url'])) : ?>
                        <a class="source" href="<?php echo esc_url($item['fuente_url']); ?>" target="_blank" rel="noreferrer noopener">
                            <?php echo esc_html(catucop_t('home.source')); ?>: <?php echo esc_html($item['fuente_label']); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
