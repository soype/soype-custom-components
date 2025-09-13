<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Carousel (Testimonials) ==========
 * Users can choose how many cards (max 20) and then fill each with "Título" + "Testimonio".
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // -------- Hard cap to protect Customizer ----------
    $HARD_MAX = 20;

    // -------- Section ----------
    $wp_customize->add_section('soype_carousel_section', [
        'title'       => __('SoyPe: Carousel (Testimoniales)', 'soype'),
        'priority'    => 30,
        'description' => __('Configura el carrusel de testimoniales: cantidad de tarjetas, contenido y opciones de inyección.', 'soype'),
    ]);

    // -------- Sanitizers (re-use if present) ----------
    if ( ! function_exists('soypecc_sanitize_checkbox') ) {
        function soypecc_sanitize_checkbox($v){ return $v ? 1 : 0; }
    }
    if ( ! function_exists('soypecc_sanitize_css_class') ) {
        function soypecc_sanitize_css_class($value) {
            $value = wp_strip_all_tags($value);
            $value = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $value);
            return preg_replace('/\s+/', ' ', trim($value));
        }
    }

    // -------- Enable/disable ----------
    $wp_customize->add_setting('soype_carousel_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_carousel_enabled', [
        'label'   => __('Mostrar carousel de testimoniales', 'soype'),
        'section' => 'soype_carousel_section',
        'type'    => 'checkbox',
    ]);

    // -------- Front page only ----------
    $wp_customize->add_setting('soype_carousel_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_carousel_only_front', [
        'label'   => __('Mostrar solo en la portada', 'soype'),
        'section' => 'soype_carousel_section',
        'type'    => 'checkbox',
    ]);

    // -------- Card count (user-defined) ----------
    $wp_customize->add_setting('soype_carousel_count', [
        'default'           => 6,
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v) use ($HARD_MAX){
            $n = absint($v);
            if ($n < 1) $n = 1;
            if ($n > $HARD_MAX) $n = $HARD_MAX;
            return $n;
        },
        'transport'         => 'postMessage', // allows live active_callback reactions
    ]);
    $wp_customize->add_control('soype_carousel_count', [
        'label'       => __('Cantidad de tarjetas', 'soype'),
        'description' => sprintf(__('Define cuántas tarjetas mostrar (1–%d).', 'soype'), $HARD_MAX),
        'section'     => 'soype_carousel_section',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 1,
            'max'  => $HARD_MAX,
            'step' => 1,
        ],
    ]);

    // -------- Optional: CSS classes ----------
    $wp_customize->add_setting('soype_carousel_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_carousel_class', [
        'label'       => __('Clases CSS (separadas por espacio)', 'soype'),
        'description' => __('Ej: testimonials wrap spacing-lg', 'soype'),
        'section'     => 'soype_carousel_section',
        'type'        => 'text',
    ]);

    // -------- Injection mode ----------
    $wp_customize->add_setting('soype_carousel_injection', [
        'default'           => 'theme_hook', // theme_hook | content | shortcode_only
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_carousel_injection', [
        'label'   => __('Ubicación del carousel', 'soype'),
        'section' => 'soype_carousel_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Hook del tema (recomendado)', 'soype'),
            'content'        => __('Al inicio del contenido de la portada', 'soype'),
            'shortcode_only' => __('Solo mediante shortcode/bloque (manual)', 'soype'),
        ],
    ]);

    // -------- Theme hook name ----------
    $wp_customize->add_setting('soype_carousel_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){ return sanitize_key($v); },
    ]);
    $wp_customize->add_control('soype_carousel_theme_hook', [
        'label'       => __('Nombre del hook del tema', 'soype'),
        'description' => __('Ej.: shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_carousel_section',
        'type'        => 'text',
    ]);

    // -------- Cards (pre-register up to HARD_MAX) ----------
    for ($i = 1; $i <= $HARD_MAX; $i++) {

        // Active callback: show only if $i <= current count
        $active_cb = function( $control ) use ( $i, $HARD_MAX ) {
            $setting = $control->manager->get_setting('soype_carousel_count');
            $count   = $setting ? absint( $setting->value() ) : 0;
            if ($count < 1) $count = 1;
            if ($count > $HARD_MAX) $count = $HARD_MAX;
            return $i <= $count;
        };

        // Internal label (helps group fields visually; not rendered on front)
        $wp_customize->add_setting("soype_carousel_item_{$i}_label", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_carousel_item_{$i}_label", [
            'label'           => sprintf(__('Tarjeta %d (etiqueta interna)', 'soype'), $i),
            'description'     => __('Solo para orden interno; no se muestra en el sitio.', 'soype'),
            'section'         => 'soype_carousel_section',
            'type'            => 'text',
            'active_callback' => $active_cb,
        ]);

        // Title
        $wp_customize->add_setting("soype_carousel_{$i}_title", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_carousel_{$i}_title", [
            'label'           => sprintf(__('Título %d', 'soype'), $i),
            'section'         => 'soype_carousel_section',
            'type'            => 'text',
            'active_callback' => $active_cb,
        ]);

        // Testimonial body (HTML allowed)
        $wp_customize->add_setting("soype_carousel_{$i}_text", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'wp_kses_post',
        ]);
        $wp_customize->add_control("soype_carousel_{$i}_text", [
            'label'           => sprintf(__('Testimonio %d', 'soype'), $i),
            'description'     => __('Permite HTML básico (p, br, strong, em, a...).', 'soype'),
            'section'         => 'soype_carousel_section',
            'type'            => 'textarea',
            'active_callback' => $active_cb,
        ]);
    }
});

/**
 * ========== Carousel Assets ==========
 * Only load if component is active and being injected automatically.
 */
add_action('wp_enqueue_scripts', function(){
    if ( ! get_theme_mod('soype_carousel_enabled', 1) ) return;

    $mode = get_theme_mod('soype_carousel_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return;

    if ( get_theme_mod('soype_carousel_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/carousel/carousel.css';
    $js  = SOYPECC_PATH . 'assets/carousel/carousel.js';

    wp_enqueue_style(
        'soypecc-carousel',
        SOYPECC_URL . 'assets/carousel/carousel.css',
        [],
        file_exists($css) ? filemtime($css) : '1.0'
    );
    wp_enqueue_script(
        'soypecc-carousel',
        SOYPECC_URL . 'assets/carousel/carousel.js',
        [],
        file_exists($js) ? filemtime($js) : '1.0',
        true
    );
});

/**
 * ========== Data helper ==========
 * Collect only non-empty cards, preserve order, strip fully-empty entries.
 */
if ( ! function_exists('soypecc_get_testimonials') ) {
    function soypecc_get_testimonials() {
        $HARD_MAX = 20;
        $count = absint( get_theme_mod('soype_carousel_count', 6) );
        if ($count < 1) $count = 1;
        if ($count > $HARD_MAX) $count = $HARD_MAX;

        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $title = (string) get_theme_mod("soype_carousel_{$i}_title", '');
            $text  = (string) get_theme_mod("soype_carousel_{$i}_text", '');
            // Skip if both fields are effectively empty
            if ( $title === '' && trim( wp_strip_all_tags( $text ) ) === '' ) continue;

            $items[] = [
                'title' => $title,
                'text'  => $text,
            ];
        }
        return $items;
    }
}

/**
 * ========== Render helper ==========
 * Renders via a template file (templates/carousel.php). Provides $class and $items to the template.
 * You can extend later with JSON-LD (e.g., Review) if needed.
 */
if ( ! function_exists('soypecc_render_carousel') ) {
    function soypecc_render_carousel($args = []) {

        if ( ! get_theme_mod('soype_carousel_enabled', 1) ) {
            return '';
        }

        $items = soypecc_get_testimonials();
        if ( empty($items) ) {
            return ''; // No content, no output
        }

        $defaults = [
            'class' => get_theme_mod('soype_carousel_class', ''),
            'items' => $items,
        ];
        $args = wp_parse_args($args, $defaults);

        ob_start();
        // Expose $class and $items to template scope
        $class = $args['class'];
        $items = $args['items'];
        include SOYPECC_PATH . 'templates/carousel.php';
        return ob_get_clean();
    }
}

/**
 * ========== Shortcode ==========
 * Usage: [soype_carousel]
 * Enqueues assets when used via shortcode as well.
 */
add_shortcode('soype_carousel', function($atts){

    if ( ! get_theme_mod('soype_carousel_enabled', 1) ) {
        return '';
    }

    $css = SOYPECC_PATH . 'assets/carousel/carousel.css';
    $js  = SOYPECC_PATH . 'assets/carousel/carousel.js';
    wp_enqueue_style('soypecc-carousel', SOYPECC_URL . 'assets/carousel/carousel.css', [], file_exists($css) ? filemtime($css) : '1.0');
    wp_enqueue_script('soypecc-carousel', SOYPECC_URL . 'assets/carousel/carousel.js', [], file_exists($js) ? filemtime($js) : '1.0', true);

    return soypecc_render_carousel();
});

/**
 * ========== Template tag ==========
 * Example usage inside themes: <?php if (function_exists('soypecc_the_carousel')) soypecc_the_carousel(); ?>
 */
if ( ! function_exists('soypecc_the_carousel') ) {
    function soypecc_the_carousel($args = []) { echo soypecc_render_carousel($args); }
}

/**
 * ========== Automatic injection ==========
 * Mode 1: theme hook (default: shopire_site_main_header)
 * Mode 2: prepend to front page content
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_carousel_enabled', 1) ) return;

    $mode       = get_theme_mod('soype_carousel_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_carousel_only_front', 1);

    // Visibility helper
    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_carousel_theme_hook', 'shopire_site_main_header');
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_carousel();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_carousel() . $content;
        }, 5);
    }
});
