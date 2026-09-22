<?php
/**
 * Template Name: Lugares y horarios
 */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$horarios = function_exists('catucop_get_horarios') ? catucop_get_horarios() : [];
$destinos = function_exists('catucop_posts_bilingual') ? catucop_posts_bilingual('destino') : [];
$map = function_exists('catucop_map_url') ? catucop_map_url() : '';
?>
<section class="map-hero">
    <img class="map-bg" src="<?php echo esc_url(catucop_img('bg-mapa.jpg')); ?>" alt="">
    <div class="map-scrim"></div>
    <div class="wrap map-inner">
        <h1 class="headline page-title"><?php echo esc_html(catucop_t('places.map.title')); ?></h1>
        <div id="map-preview">
            <img class="map-preview" src="<?php echo esc_url(catucop_img('mapa-preview.png')); ?>" alt="<?php echo esc_attr(catucop_lang() === 'es' ? 'Península de Osa' : 'Osa Peninsula'); ?>">
            <p class="map-hint"><?php echo esc_html(catucop_t('places.map.hint')); ?></p>
            <p class="map-mobile-note"><?php echo esc_html(catucop_t('places.map.mobile')); ?></p>
            <button class="btn-lime" type="button" id="map-open" data-src="<?php echo esc_url($map); ?>"><?php echo esc_html(catucop_t('places.map.load')); ?></button>
        </div>
        <div class="map-frame" id="map-frame" hidden>
            <div class="map-frame-bar">
                <span><?php echo esc_html(catucop_t('places.map.title')); ?></span>
                <button type="button" id="map-close"><?php echo esc_html(catucop_t('places.map.close')); ?></button>
            </div>
            <iframe id="map-iframe" title="<?php echo esc_attr(catucop_t('places.map.title')); ?>" hidden></iframe>
        </div>
    </div>
</section>

<section class="section transport">
    <img class="map-bg" src="<?php echo esc_url(catucop_img('bg-transporte.jpg')); ?>" alt="">
    <div class="transport-scrim"></div>
    <div class="wrap transport-inner">
        <h2 class="headline section-title center"><?php echo esc_html(catucop_t('places.schedules.title')); ?></h2>
        <?php if ($horarios) : ?>
            <div class="schedule-grid">
                <article class="schedule-card">
                    <div class="schedule-head">
                        <h3><?php echo esc_html(catucop_t('places.buses')); ?></h3>
                        <span><?php echo esc_html($horarios['bus_operador']); ?></span>
                    </div>
                    <img src="<?php echo esc_url($horarios['bus_image_url']); ?>" alt="<?php echo esc_attr($horarios['bus_operador']); ?>" loading="lazy">
                    <div class="time-list">
                        <?php foreach ($horarios['rutas'] as $ruta) : ?>
                            <div class="time-block">
                                <p><?php echo esc_html(catucop_field($ruta, 'titulo')); ?></p>
                                <strong><?php echo esc_html($ruta['hora']); ?></strong>
                                <span><?php echo esc_html(catucop_field($ruta, 'nota')); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ubic-row">
                        <?php foreach ($horarios['bus_ubic'] as $ubic) : ?>
                            <?php if (empty($ubic['url'])) { continue; } ?>
                            <a class="ubic" href="<?php echo esc_url($ubic['url']); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html(catucop_field($ubic, 'label')); ?></a>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="schedule-card">
                    <div class="schedule-head">
                        <h3><?php echo esc_html(catucop_t('places.boats')); ?></h3>
                        <span><?php echo esc_html($horarios['bote_operador']); ?></span>
                    </div>
                    <img src="<?php echo esc_url($horarios['bote_image_url']); ?>" alt="<?php echo esc_attr($horarios['bote_operador']); ?>" loading="lazy">
                    <div class="boat-stack">
                        <?php foreach ($horarios['tablas'] as $tabla) : ?>
                            <div class="boat-table">
                                <p class="boat-title"><?php echo esc_html(catucop_field($tabla, 'titulo')); ?></p>
                                <table>
                                    <tbody>
                                        <?php
                                        $rows = [
                                            'places.days.lv' => $tabla['lv'],
                                            'places.days.sab' => $tabla['sab'],
                                            'places.days.dom' => $tabla['dom'],
                                        ];
                                        foreach ($rows as $label => $hours) :
                                            ?>
                                            <tr>
                                                <th><?php echo esc_html(catucop_t($label)); ?></th>
                                                <td>
                                                    <?php foreach (catucop_hours($hours) as $hour) : ?>
                                                        <span><?php echo esc_html($hour); ?></span>
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ubic-row">
                        <?php foreach ($horarios['bote_ubic'] as $ubic) : ?>
                            <?php if (empty($ubic['url'])) { continue; } ?>
                            <a class="ubic" href="<?php echo esc_url($ubic['url']); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html(catucop_field($ubic, 'label')); ?></a>
                        <?php endforeach; ?>
                    </div>
                </article>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($destinos) : ?>
<section class="section section-soft">
    <div class="wrap">
        <div class="center narrow">
            <h2 class="headline section-title"><?php echo esc_html(catucop_t('places.dest.title')); ?></h2>
            <p class="lead"><?php echo esc_html(catucop_t('places.dest.lead')); ?></p>
        </div>
        <div class="dest-grid">
            <?php foreach ($destinos as $item) :
                $maps = $item['maps'];
                if (!$maps && $item['lat'] && $item['lon']) {
                    $maps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($item['lat'] . ',' . $item['lon']);
                }
                ?>
                <article class="dest-card">
                    <h3><?php echo esc_html($item['title']); ?></h3>
                    <?php if ($item['sector']) : ?><p class="aff-cat"><?php echo esc_html($item['sector']); ?></p><?php endif; ?>
                    <p><?php echo esc_html(wp_strip_all_tags($item['text'])); ?></p>
                    <?php if ($maps) : ?>
                        <a class="ubic" href="<?php echo esc_url($maps); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html(catucop_t('places.dest.go')); ?></a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php
get_footer();
