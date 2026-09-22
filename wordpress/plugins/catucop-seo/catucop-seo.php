<?php
/**
 * Plugin Name: CATUCOP SEO
 * Description: Metatítulo y metadescripción por página, en español e inglés, con una vista previa de cómo se verá el resultado en buscadores.
 * Version: 1.0.0
 * Author: CATUCOP
 * Text Domain: catucop-seo
 */

if (!defined('ABSPATH')) {
    exit;
}

function catucop_seo_lang() {
    return function_exists('catucop_lang') ? catucop_lang() : 'es';
}

function catucop_seo_id() {
    if (is_singular()) {
        return (int) get_queried_object_id();
    }
    return 0;
}

function catucop_seo_field($key) {
    $id = catucop_seo_id();
    if (!$id) {
        return '';
    }
    $value = get_post_meta($id, '_catucop_seo_' . $key . '_' . catucop_seo_lang(), true);
    return is_string($value) ? trim($value) : '';
}

add_filter('document_title_parts', function ($parts) {
    $title = catucop_seo_field('title');
    if ($title !== '') {
        $parts['title'] = $title;
        unset($parts['tagline'], $parts['site']);
    }
    return $parts;
});

add_filter('get_canonical_url', function ($url) {
    $url = remove_query_arg('lang', $url);
    if (catucop_seo_lang() === 'en') {
        $url = add_query_arg('lang', 'en', $url);
    }
    return $url;
});

add_action('wp_head', function () {
    if (!is_singular()) {
        return;
    }
    $desc = catucop_seo_field('desc');
    if ($desc === '' && function_exists('catucop_t')) {
        $desc = catucop_t('seo.fallback');
    }
    $title = catucop_seo_field('title');
    if ($title === '') {
        $title = wp_get_document_title();
    }
    $urls = [
        'es' => remove_query_arg('lang', get_permalink()),
        'en' => add_query_arg('lang', 'en', remove_query_arg('lang', get_permalink())),
    ];
    $current = catucop_seo_lang() === 'en' ? $urls['en'] : $urls['es'];
    if ($desc !== '') {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
    echo '<link rel="alternate" hreflang="es" href="' . esc_url($urls['es']) . '">' . "\n";
    echo '<link rel="alternate" hreflang="en" href="' . esc_url($urls['en']) . '">' . "\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($urls['es']) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc !== '') {
        echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url($current) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:locale" content="' . (catucop_seo_lang() === 'en' ? 'en_US' : 'es_CR') . '">' . "\n";

    if (is_front_page()) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TouristInformationCenter',
            'name' => 'CATUCOP',
            'description' => $desc,
            'url' => $urls['es'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Puerto Jiménez',
                'addressRegion' => 'Puntarenas',
                'addressCountry' => 'CR',
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}, 1);

add_action('add_meta_boxes', function () {
    add_meta_box(
        'catucop_seo',
        'SEO CATUCOP — cómo se verá en buscadores',
        'catucop_seo_box',
        'page',
        'normal',
        'high'
    );
});

function catucop_seo_box($post) {
    wp_nonce_field('catucop_seo', 'catucop_seo_nonce');
    $fields = [
        'title_es' => 'Metatítulo en español',
        'desc_es' => 'Metadescripción en español',
        'title_en' => 'Metatítulo en inglés',
        'desc_en' => 'Metadescripción en inglés',
    ];
    echo '<p>Estos textos son los que lee Google. El metatítulo conviene dejarlo cerca de 60 caracteres y la descripción cerca de 150. La vista previa se actualiza mientras escribe. El idioma visible del sitio no cambia el contenido de la página: solo elige cuál de estos textos se imprime en el HTML.</p>';
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:980px">';
    foreach (['es' => 'Español', 'en' => 'Inglés'] as $lang => $label) {
        $title = get_post_meta($post->ID, '_catucop_seo_title_' . $lang, true);
        $desc = get_post_meta($post->ID, '_catucop_seo_desc_' . $lang, true);
        echo '<div>';
        echo '<p><label for="catucop_seo_title_' . esc_attr($lang) . '"><strong>' . esc_html($fields['title_' . $lang]) . '</strong></label><br>';
        echo '<input style="width:100%" type="text" id="catucop_seo_title_' . esc_attr($lang) . '" name="catucop_seo_title_' . esc_attr($lang) . '" value="' . esc_attr($title) . '" data-fallback="' . esc_attr(get_the_title($post)) . '"></p>';
        echo '<p><label for="catucop_seo_desc_' . esc_attr($lang) . '"><strong>' . esc_html($fields['desc_' . $lang]) . '</strong></label><br>';
        echo '<textarea style="width:100%" rows="4" id="catucop_seo_desc_' . esc_attr($lang) . '" name="catucop_seo_desc_' . esc_attr($lang) . '">' . esc_textarea($desc) . '</textarea></p>';
        echo '<div style="font-family:Arial,sans-serif;background:#fff;border:1px solid #dadce0;border-radius:8px;padding:12px">';
        echo '<div id="catucop_preview_title_' . esc_attr($lang) . '" style="color:#1a0dab;font-size:18px;line-height:1.3"></div>';
        echo '<div style="color:#006621;font-size:13px;margin:2px 0 4px">' . esc_html(get_permalink($post)) . '</div>';
        echo '<div id="catucop_preview_desc_' . esc_attr($lang) . '" style="color:#4d5156;font-size:13px;line-height:1.4"></div>';
        echo '<p id="catucop_preview_count_' . esc_attr($lang) . '" style="margin:8px 0 0;color:#666;font-size:12px"></p>';
        echo '</div></div>';
    }
    echo '</div>';
    echo '<script>
    (function(){
      ["es","en"].forEach(function(lang){
        var title = document.getElementById("catucop_seo_title_"+lang);
        var desc = document.getElementById("catucop_seo_desc_"+lang);
        var pt = document.getElementById("catucop_preview_title_"+lang);
        var pd = document.getElementById("catucop_preview_desc_"+lang);
        var pc = document.getElementById("catucop_preview_count_"+lang);
        function cut(value, max){ return value.length > max ? value.slice(0, max - 1) + "…" : value; }
        function sync(){
          pt.textContent = cut(title.value || title.dataset.fallback || "", 60);
          pd.textContent = cut(desc.value || "", 160);
          pc.textContent = "Título: " + title.value.length + " caracteres · Descripción: " + desc.value.length + " caracteres";
        }
        title.addEventListener("input", sync);
        desc.addEventListener("input", sync);
        sync();
      });
    })();
    </script>';
}

add_action('save_post_page', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['catucop_seo_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['catucop_seo_nonce'])), 'catucop_seo')) {
        return;
    }
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    foreach (['title_es', 'title_en', 'desc_es', 'desc_en'] as $key) {
        if (!isset($_POST['catucop_seo_' . $key])) {
            continue;
        }
        $value = sanitize_text_field(wp_unslash($_POST['catucop_seo_' . $key]));
        update_post_meta($post_id, '_catucop_seo_' . $key, $value);
    }
});
