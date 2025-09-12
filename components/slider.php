<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Slider adjustments ==========
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // Section
    $wp_customize->add_section('soype_slider_section', [
        'title'       => __('SoyPe: Slider', 'soype'),
        'priority'    => 30,
        'description' => __('Configura título, clases CSS y dónde inyectar el slider.', 'soype'),
    ]);

    // Sanitize
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

    // Enabled
    $wp_customize->add_setting('soype_slider_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_slider_enabled', [
        'label'   => __('Mostrar slider', 'soype'),
        'section' => 'soype_slider_section',
        'type'    => 'checkbox',
    ]);

    // Front only
    $wp_customize->add_setting('soype_slider_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_slider_only_front', [
        'label'   => __('Mostrar solo en la portada', 'soype'),
        'section' => 'soype_slider_section',
        'type'    => 'checkbox',
    ]);

    // === Slides (3) ===
    for ($i = 1; $i <= 3; $i++) {
        // Image
        $wp_customize->add_setting("soype_slide_{$i}_image", [
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control(new WP_Customize_Media_Control(
            $wp_customize,
            "soype_slide_{$i}_image",
            [
                'label'     => sprintf(__('Slide %d: Imagen', 'soype'), $i),
                'section'   => 'soype_slider_section',
                'mime_type' => 'image',
            ]
        ));

        // Slide Title
        $wp_customize->add_setting("soype_slide_{$i}_title", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_slide_{$i}_title", [
            'label'   => sprintf(__('Slide %d: Título (opcional)', 'soype'), $i),
            'section' => 'soype_slider_section',
            'type'    => 'text',
        ]);

        // Link
        $wp_customize->add_setting("soype_slide_{$i}_link", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control("soype_slide_{$i}_link", [
            'label'       => sprintf(__('Slide %d: URL', 'soype'), $i),
            'description' => __('Ej: https://tudominio.com/pagina', 'soype'),
            'section'     => 'soype_slider_section',
            'type'        => 'url',
        ]);

        // Enable CTA
        $wp_customize->add_setting("soype_slide_{$i}_cta_enabled", [
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'soypecc_sanitize_checkbox',
        ]);
        $wp_customize->add_control("soype_slide_{$i}_cta_enabled", [
            'label'   => sprintf(__('Slide %d: Usar CTA (no linkear imagen)', 'soype'), $i),
            'section' => 'soype_slider_section',
            'type'    => 'checkbox',
        ]);

        // CTA Text
        $wp_customize->add_setting("soype_slide_{$i}_cta_text", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_slide_{$i}_cta_text", [
            'label'       => sprintf(__('Slide %d: Texto del CTA', 'soype'), $i),
            'description' => __('Solo se usa si el CTA está activado', 'soype'),
            'section'     => 'soype_slider_section',
            'type'        => 'text',
        ]);
    }


    // Custom class
    $wp_customize->add_setting('soype_slider_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_slider_class', [
        'label'       => __('Clases CSS (separadas por espacio)', 'soype'),
        'description' => __('Ej: hero dark text-lg', 'soype'),
        'section'     => 'soype_slider_section',
        'type'        => 'text',
    ]);

    // Injection
    $wp_customize->add_setting('soype_slider_injection', [
        'default'           => 'theme_hook', // theme_hook | content | shortcode_only
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_slider_injection', [
        'label'   => __('Ubicación del slider', 'soype'),
        'section' => 'soype_slider_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Hook del tema (recomendado)', 'soype'),
            'content'        => __('Al inicio del contenido de la portada', 'soype'),
            'shortcode_only' => __('Solo mediante shortcode/bloque (manual)', 'soype'),
        ],
    ]);

    // Hook theme
    $wp_customize->add_setting('soype_slider_theme_hook', [
        'default'           => 'shopire_site_main_header', // por tu tema actual
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){ return sanitize_key($v); },
    ]);
    $wp_customize->add_control('soype_slider_theme_hook', [
        'label'       => __('Nombre del hook del tema', 'soype'),
        'description' => __('Ej.: shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_slider_section',
        'type'        => 'text',
    ]);
});

/**
 * ========== Slider Assets ==========
 * Only loads if the component is active
 */
add_action('wp_enqueue_scripts', function(){
    if ( ! get_theme_mod('soype_slider_enabled', 1) ) return;

    $mode = get_theme_mod('soype_slider_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return; 

    if ( get_theme_mod('soype_slider_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/slider/slider.css';
    $js  = SOYPECC_PATH . 'assets/slider/slider.js';

    wp_enqueue_style(
        'soypecc-slider',
        SOYPECC_URL . 'assets/slider/slider.css',
        [],
        file_exists($css) ? filemtime($css) : '1.0'
    );
    wp_enqueue_script(
        'soypecc-slider',
        SOYPECC_URL . 'assets/slider/slider.js',
        [],
        file_exists($js) ? filemtime($js) : '1.0',
        true
    );
});

/**
 * ========== Render helper ==========
 */

if ( ! function_exists('soypecc_get_slides') ) {
    function soypecc_get_slides() {
        $slides = [];
        for ($i = 1; $i <= 3; $i++) {
            $img_id = (int) get_theme_mod("soype_slide_{$i}_image", 0);
            if ( $img_id <= 0 ) {
                continue; // No image, no slide
            }
            $title       = (string) get_theme_mod("soype_slide_{$i}_title", '');
            $link        = (string) get_theme_mod("soype_slide_{$i}_link", '');
            $cta_enabled = (int) get_theme_mod("soype_slide_{$i}_cta_enabled", 0);
            $cta_text    = (string) get_theme_mod("soype_slide_{$i}_cta_text", '');

            $slides[] = [
                'image_id'    => $img_id,
                'title'       => $title,
                'link'        => $link,
                'cta_enabled' => (bool) $cta_enabled,
                'cta_text'    => $cta_text,
            ];
        }
        return $slides;
    }
}


if ( ! function_exists('soypecc_render_slider') ) {
    function soypecc_render_slider($args = []) {

        if ( !get_theme_mod('soype_slider_enabled', 1) ) {
            return '';
        }

        $slides = soypecc_get_slides();
        if ( empty($slides) ) {
            return ''; // No slides, no slider
        }

        $args = wp_parse_args($args, [
            'class' => get_theme_mod('soype_slider_class', ''),
            'slides' => $slides
        ]); 

        ob_start();
        extract($args, EXTR_OVERWRITE); 
        include SOYPECC_PATH . 'templates/slider.php';
        return ob_get_clean();
    }
}

/**
 * ========== Shortcode ==========
 * Usage: [soype_slider]
 */
add_shortcode('soype_slider', function($atts){

    if( !get_theme_mod('soype_slider_enabled', 1) ){
        return '';
    }
    
    $css = SOYPECC_PATH . 'assets/slider/slider.css';
    $js  = SOYPECC_PATH . 'assets/slider/slider.js';
    wp_enqueue_style('soypecc-slider', SOYPECC_URL . 'assets/slider/slider.css', [], file_exists($css) ? filemtime($css) : '1.0');
    wp_enqueue_script('soypecc-slider', SOYPECC_URL . 'assets/slider/slider.js', [], file_exists($js) ? filemtime($js) : '1.0', true);

    return soypecc_render_slider();
});

/**
 * ========== Template tag ==========
 * In case you wish to call it: <?php if (function_exists('soypecc_the_slider')) soypecc_the_slider(); ?>
 */
if ( ! function_exists('soypecc_the_slider') ) {
    function soypecc_the_slider($args = []) { echo soypecc_render_slider($args); }
}

/**
 * ========== Automatic injection ==========
 * Modo 1: Hook del tema (por defecto: shopire_site_main_header)
 * Modo 2: Al inicio del contenido de la portada (the_content)
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_slider_enabled', 1) ) return;

    $mode = get_theme_mod('soype_slider_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_slider_only_front', 1);

    // Helper visibilidad
    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_slider_theme_hook', 'shopire_site_main_header');
        // After header
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_slider();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_slider() . $content;
        }, 5);
    }
});
