<?php
$items = function_exists('catucop_posts_bilingual') ? catucop_posts_bilingual('actividad') : [];
if (!$items) {
    return;
}
$rots = ['rot-a', 'rot-b', 'rot-c'];
?>
<section class="section">
    <div class="wrap">
        <div class="center narrow">
            <h2 class="headline section-title"><?php echo esc_html(catucop_t('home.places.title')); ?></h2>
            <p class="lead"><?php echo esc_html(catucop_t('home.places.lead')); ?></p>
        </div>
        <div class="polaroid-grid">
            <?php foreach ($items as $i => $item) : ?>
                <figure class="polaroid <?php echo esc_attr($rots[$i % 3]); ?>">
                    <?php if ($item['image']) : ?>
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    <?php endif; ?>
                    <figcaption><?php echo esc_html($item['title']); ?></figcaption>
                    <?php if (trim(wp_strip_all_tags($item['text'])) !== '') : ?>
                        <p><?php echo esc_html(wp_strip_all_tags($item['text'])); ?></p>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
