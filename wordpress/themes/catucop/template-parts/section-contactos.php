<?php
$items = function_exists('catucop_posts_bilingual') ? catucop_posts_bilingual('contacto') : [];
?>
<section class="section">
    <div class="wrap center emergency">
        <h2 class="headline section-title"><?php echo esc_html(catucop_t('home.emergency.title')); ?></h2>
        <p class="lead"><?php echo esc_html(catucop_t('home.emergency.body')); ?></p>
        <p class="call-911">911</p>
        <?php if ($items) : ?>
            <div class="contact-grid">
                <?php foreach ($items as $item) : ?>
                    <div class="contact">
                        <div class="contact-logo">
                            <?php if ($item['image']) : ?>
                                <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <span><?php echo esc_html($item['title']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
