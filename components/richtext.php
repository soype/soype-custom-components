<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Slider adjustments ==========
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // Section
    $wp_customize->add_section('soype_richtext_section', [
        'title'       => __('SoyPe: Richtext', 'soype'),
        'priority'    => 30,
        'description' => __('Configura título, descripción, clases CSS y dónde inyectar el richtext.', 'soype'),
    ]);

    // Sanitizers
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
    if ( ! function_exists('soypecc_sanitize_choice') ) {
        function soypecc_sanitize_choice( $value, $setting ) {
            $allowed = ['left','center','right','justify',''];
            return in_array($value, $allowed, true) ? $value : $setting->default;
        }
    }

    // Enabled
    $wp_customize->add_setting('soype_richtext_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_richtext_enabled', [
        'label'   => __('Mostrar richtext', 'soype'),
        'section' => 'soype_richtext_section',
        'type'    => 'checkbox',
    ]);

    // Title (plain text)
    $wp_customize->add_setting('soype_richtext_title', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('soype_richtext_title', [
        'label'       => __('Título', 'soype'),
        'description' => __('Título del richtext (texto plano).', 'soype'),
        'section'     => 'soype_richtext_section',
        'type'        => 'text',
    ]);

    // Description (rich text permitido)
    $wp_customize->add_setting('soype_richtext_description', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('soype_richtext_description', [
        'label'       => __('Descripción', 'soype'),
        'description' => __('Puedes usar HTML básico (p, a, strong, em, br, etc.).', 'soype'),
        'section'     => 'soype_richtext_section',
        'type'        => 'textarea',
    ]);

    // Alignment
    $wp_customize->add_setting('soype_richtext_alignment', [
        'default'           => 'center',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_choice',
        'transport'         => 'refresh', // o 'postMessage' si querés live preview
    ]);

    $wp_customize->add_control('soype_richtext_alignment', [
        'label'       => __('Alinear texto', 'soype'),
        'description' => __('Seleccionar alineación (centro por defecto).', 'soype'),
        'section'     => 'soype_richtext_section',
        'type'        => 'select',
        'choices'     => [
            'left'    => __('Izquierda', 'soype'),
            'center'  => __('Centro', 'soype'),
            'right'   => __('Derecha', 'soype'),
            'justify' => __('Justificado', 'soype'),
        ],
    ]);

    // Background color
    $wp_customize->add_setting('soype_richtext_color', [
        'default'           => '#ffffff', 
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color', 
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'soype_richtext_color', [
        'label'       => __('Color de fondo', 'soype'),
        'description' => __('Elegí un color en formato HEX', 'soype'),
        'section'     => 'soype_richtext_section',
    ]));


    // Custom class
    $wp_customize->add_setting('soype_richtext_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_richtext_class', [
        'label'       => __('Clases CSS (separadas por espacio)', 'soype'),
        'description' => __('Ej: hero dark text-lg', 'soype'),
        'section'     => 'soype_richtext_section',
        'type'        => 'text',
    ]);

    // Injection
    $wp_customize->add_setting('soype_richtext_injection', [
        'default'           => 'theme_hook',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_richtext_injection', [
        'label'   => __('Ubicación del richtext', 'soype'),
        'section' => 'soype_richtext_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Hook del tema (recomendado)', 'soype'),
            'content'        => __('Al inicio del contenido de la portada', 'soype'),
            'shortcode_only' => __('Solo mediante shortcode/bloque (manual)', 'soype'),
        ],
    ]);

    // Hook theme
    $wp_customize->add_setting('soype_richtext_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){ return sanitize_key($v); },
    ]);
    $wp_customize->add_control('soype_richtext_theme_hook', [
        'label'       => __('Nombre del hook del tema', 'soype'),
        'description' => __('Ej.: shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_richtext_section',
        'type'        => 'text',
    ]);

    // Front only
    $wp_customize->add_setting('soype_richtext_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_richtext_only_front', [
        'label'   => __('Mostrar solo en la portada', 'soype'),
        'section' => 'soype_richtext_section',
        'type'    => 'checkbox',
    ]);

});

/**
 * ========== Slider Assets ==========
 * Only loads if the component is active
 */
add_action('wp_enqueue_scripts', function(){
    if ( ! get_theme_mod('soype_richtext_enabled', 1) ) return;

    $mode = get_theme_mod('soype_richtext_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return; 

    if ( get_theme_mod('soype_richtext_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/richtext/richtext.css';
    $js  = SOYPECC_PATH . 'assets/richtext/richtext.js';

    wp_enqueue_style(
        'soypecc-richtext',
        SOYPECC_URL . 'assets/richtext/richtext.css',
        [],
        file_exists($css) ? filemtime($css) : '1.0'
    );
    wp_enqueue_script(
        'soypecc-richtext',
        SOYPECC_URL . 'assets/richtext/richtext.js',
        [],
        file_exists($js) ? filemtime($js) : '1.0',
        true
    );
});

/**
 * ========== Render helper ==========
 */


if ( ! function_exists('soypecc_render_richtext') ) {
    function soypecc_render_richtext($args = []) {

        if ( ! get_theme_mod('soype_richtext_enabled', 1) ) {
            return '';
        }

        $defaults = [
            'class'       => get_theme_mod('soype_richtext_class', ''),
            'title'       => get_theme_mod('soype_richtext_title', ''),
            'description' => get_theme_mod('soype_richtext_description', ''),
            'alignment'   => get_theme_mod('soype_richtext_alignment', 'center'),
            'color' => get_theme_mod('soype_richtext_color', '#ffffff')
        ];
        $args = wp_parse_args($args, $defaults);

        // No renders vacíos
        if ( empty($args['title']) && empty($args['description']) ) {
            return '';
        }

        ob_start();
        // $class, $title, $description disponibles en el template
        $class = $args['class'];
        $title = $args['title'];
        $description = $args['description'];
        $alignment = $args['alignment'];
        $color = $args['color'];

        include SOYPECC_PATH . 'templates/richtext.php';
        return ob_get_clean();
    }
}


/**
 * ========== Shortcode ==========
 * Usage: [soype_richtext]
 */
add_shortcode('soype_richtext', function($atts){

    if ( ! get_theme_mod('soype_richtext_enabled', 1) ) {
        return '';
    }

    $atts = shortcode_atts([
        'class'       => '',
        'title'       => '',
        'description' => '',
    ], $atts, 'soype_richtext');

    // Enqueues
    $css = SOYPECC_PATH . 'assets/richtext/richtext.css';
    $js  = SOYPECC_PATH . 'assets/richtext/richtext.js';
    wp_enqueue_style('soypecc-richtext', SOYPECC_URL . 'assets/richtext/richtext.css', [], file_exists($css) ? filemtime($css) : '1.0');
    wp_enqueue_script('soypecc-richtext', SOYPECC_URL . 'assets/richtext/richtext.js', [], file_exists($js) ? filemtime($js) : '1.0', true);

    // Si el shortcode no pasa nada, se toma del Customizer en render()
    return soypecc_render_richtext(array_filter($atts, function($v){ return $v !== ''; }));
});


/**
 * ========== Template tag ==========
 * In case you wish to call it: <?php if (function_exists('soypecc_the_richtext')) soypecc_the_richtext(); ?>
 */
if ( ! function_exists('soypecc_the_richtext') ) {
    function soypecc_the_richtext($args = []) { echo soypecc_render_richtext($args); }
}

/**
 * ========== Automatic injection ==========
 * Modo 1: Hook del tema (por defecto: shopire_site_main_header)
 * Modo 2: Al inicio del contenido de la portada (the_content)
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_richtext_enabled', 1) ) return;

    $mode = get_theme_mod('soype_richtext_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_richtext_only_front', 1);

    // Helper visibilidad
    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_richtext_theme_hook', 'shopire_site_main_header');
        // After header
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_richtext();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_richtext() . $content;
        }, 5);
    }
});
