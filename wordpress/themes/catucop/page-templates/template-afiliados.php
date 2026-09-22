<?php
/**
 * Template Name: Afiliados
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$items = function_exists('catucop_get_afiliados') ? catucop_get_afiliados() : [];
$terms = function_exists('catucop_afiliado_cats') ? catucop_afiliado_cats() : [];
$lang = catucop_lang();
?>
<div class="wrap page-intro">
    <header class="center narrow">
        <h1 class="headline page-title"><?php echo esc_html(catucop_t('aff.title')); ?></h1>
        <p class="lead"><?php echo esc_html(catucop_t('aff.lead')); ?></p>
    </header>

    <?php if ($terms) : ?>
        <div class="filters" role="tablist">
            <button class="filter is-active" type="button" data-filter="all"><?php echo esc_html(catucop_t('aff.filter.all')); ?></button>
            <?php foreach ($terms as $term) :
                $label = $term->name;
                if ($lang === 'en') {
                    $en = get_term_meta($term->term_id, 'nombre_en', true);
                    if ($en) {
                        $label = $en;
                    }
                }
                ?>
                <button class="filter" type="button" data-filter="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($label); ?></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!$items) : ?>
        <p class="center muted"><?php echo esc_html(catucop_t('aff.empty')); ?></p>
    <?php else : ?>
        <div class="aff-grid">
            <?php foreach ($items as $item) : ?>
                <button class="aff-card" type="button" data-cat="<?php echo esc_attr($item['cat']); ?>" data-open="aff-<?php echo esc_attr((string) $item['id']); ?>" aria-label="<?php echo esc_attr($item['nombre']); ?>">
                    <?php if ($item['logo']) : ?>
                        <img src="<?php echo esc_url($item['logo']); ?>" alt="<?php echo esc_attr($item['nombre']); ?>" loading="lazy">
                    <?php else : ?>
                        <span><?php echo esc_html($item['nombre']); ?></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="aff-modal" id="aff-modal" hidden>
            <div class="aff-dialog" role="dialog" aria-modal="true">
                <?php foreach ($items as $item) : ?>
                    <article class="aff-panel" id="aff-<?php echo esc_attr((string) $item['id']); ?>" hidden>
                        <div class="aff-top">
                            <div class="aff-links">
                                <?php
                                $icons = [
                                    'facebook' => 'facebook.png',
                                    'instagram' => 'instagram.png',
                                    'tiktok' => 'tiktok.png',
                                    'tripadvisor' => 'tripadvisor.png',
                                ];
                                if (!empty($item['enlaces']['web'])) :
                                    ?>
                                    <a href="<?php echo esc_url($item['enlaces']['web']); ?>" target="_blank" rel="noreferrer noopener" aria-label="Web"><?php echo catucop_icon_globe(); ?></a>
                                <?php endif;
                                foreach ($icons as $key => $file) :
                                    if (empty($item['enlaces'][$key])) {
                                        continue;
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($item['enlaces'][$key]); ?>" target="_blank" rel="noreferrer noopener" aria-label="<?php echo esc_attr($key); ?>">
                                        <img src="<?php echo esc_url(catucop_img($file)); ?>" alt="" width="18" height="18">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <button class="aff-close" type="button" data-close-aff><?php echo esc_html(catucop_t('aff.close')); ?></button>
                        </div>
                        <h2><?php echo esc_html($item['nombre']); ?></h2>
                        <?php if ($item['cat_label']) : ?>
                            <p class="aff-cat"><?php echo esc_html($item['cat_label']); ?></p>
                        <?php endif; ?>
                        <div class="aff-desc"><?php echo wp_kses_post(wpautop($item['desc'])); ?></div>
                        <ul class="aff-meta">
                            <?php if ($item['ubicacion']) : ?>
                                <li><strong><?php echo esc_html(catucop_t('aff.location')); ?>:</strong> <?php echo esc_html($item['ubicacion']); ?></li>
                            <?php endif; ?>
                            <?php if ($item['telefono']) : ?>
                                <li><strong><?php echo esc_html(catucop_t('aff.phone')); ?>:</strong> <?php echo esc_html($item['telefono']); ?></li>
                            <?php endif; ?>
                            <?php if ($item['email']) : ?>
                                <li><strong><?php echo esc_html(catucop_t('aff.email')); ?>:</strong> <a href="mailto:<?php echo esc_attr($item['email']); ?>"><?php echo esc_html($item['email']); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <?php if (!empty($item['enlaces']['web'])) : ?>
                            <a class="btn-lime" href="<?php echo esc_url($item['enlaces']['web']); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html(catucop_t('aff.visit')); ?></a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
get_footer();
