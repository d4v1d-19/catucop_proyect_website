<?php
$items = function_exists('catucop_posts_bilingual') ? catucop_posts_bilingual('evento') : [];
if (!$items) {
    return;
}
?>
<section class="section section-soft">
    <div class="wrap">
        <h2 class="headline section-title center"><?php echo esc_html(catucop_t('home.events.title')); ?></h2>
        <div class="event-grid">
            <?php foreach ($items as $item) : ?>
                <article class="event-card">
                    <?php if ($item['image']) : ?>
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="event-body">
                        <h3><?php echo esc_html($item['title']); ?></h3>
                        <p><?php echo esc_html(wp_strip_all_tags($item['text'])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="events-social"><?php echo esc_html(catucop_t('home.events.social')); ?></p>
    </div>
</section>
