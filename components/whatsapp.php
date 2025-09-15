<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: WhatsApp Button ==========
 * Lets editors configure a WhatsApp button: phone, prefilled text, label, classes, and injection mode.
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // ---------- Section ----------
    $wp_customize->add_section('soype_whatsapp_section', [
        'title'       => __('SoyPe: Botón de WhatsApp', 'soype'),
        'priority'    => 30,
        'description' => __('Configura número, texto inicial, etiqueta, clases y dónde inyectar el botón.', 'soype'),
    ]);

    // ---------- Sanitizers (re-use) ----------
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
    if ( ! function_exists('soypecc_sanitize_phone_digits') ) {
        /** Keep only digits for wa.me usage (international format, no +) */
        function soypecc_sanitize_phone_digits($value){
            $digits = preg_replace('/\D+/', '', (string)$value);
            return $digits;
        }
    }

    // ---------- Enabled ----------
    $wp_customize->add_setting('soype_whatsapp_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_whatsapp_enabled', [
        'label'   => __('Mostrar botón de WhatsApp', 'soype'),
        'section' => 'soype_whatsapp_section',
        'type'    => 'checkbox',
    ]);

    // ---------- Phone ----------
    $wp_customize->add_setting('soype_whatsapp_phone', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_phone_digits',
    ]);
    $wp_customize->add_control('soype_whatsapp_phone', [
        'label'       => __('Número (formato internacional, solo dígitos)', 'soype'),
        'description' => __('Ej: +54 9 11 1234-5678 → 5491112345678', 'soype'),
        'section'     => 'soype_whatsapp_section',
        'type'        => 'text',
    ]);

    // ---------- Prefilled text ----------
    $wp_customize->add_setting('soype_whatsapp_text', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('soype_whatsapp_text', [
        'label'       => __('Texto inicial del mensaje', 'soype'),
        'description' => __('Se prellenará en WhatsApp. Ej: "Hola, quisiera más información."', 'soype'),
        'section'     => 'soype_whatsapp_section',
        'type'        => 'textarea',
    ]);

    // ---------- Visible label on button ----------
    $wp_customize->add_setting('soype_whatsapp_label', [
        'default'           => __('Escribinos por WhatsApp', 'soype'),
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('soype_whatsapp_label', [
        'label'       => __('Etiqueta del botón', 'soype'),
        'section'     => 'soype_whatsapp_section',
        'type'        => 'text',
    ]);

    // ---------- Extra CSS classes ----------
    $wp_customize->add_setting('soype_whatsapp_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_whatsapp_class', [
        'label'       => __('Clases CSS (separadas por espacio)', 'soype'),
        'description' => __('Ej: btn btn--whatsapp', 'soype'),
        'section'     => 'soype_whatsapp_section',
        'type'        => 'text',
    ]);

    // ---------- Injection mode ----------
    $wp_customize->add_setting('soype_whatsapp_injection', [
        'default'           => 'theme_hook', // theme_hook | content | shortcode_only
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_whatsapp_injection', [
        'label'   => __('Ubicación del botón', 'soype'),
        'section' => 'soype_whatsapp_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Hook del tema (recomendado)', 'soype'),
            'content'        => __('Al inicio del contenido de la portada', 'soype'),
            'shortcode_only' => __('Solo mediante shortcode/bloque (manual)', 'soype'),
        ],
    ]);

    // ---------- Theme hook ----------
    $wp_customize->add_setting('soype_whatsapp_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){ return sanitize_key($v); },
    ]);
    $wp_customize->add_control('soype_whatsapp_theme_hook', [
        'label'       => __('Nombre del hook del tema', 'soype'),
        'description' => __('Ej.: shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_whatsapp_section',
        'type'        => 'text',
    ]);

    // ---------- Front page only ----------
    $wp_customize->add_setting('soype_whatsapp_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_whatsapp_only_front', [
        'label'   => __('Mostrar solo en la portada', 'soype'),
        'section' => 'soype_whatsapp_section',
        'type'    => 'checkbox',
    ]);
});

/**
 * ========== Assets ==========
 * Loads CSS/JS if the component is active and auto-injected (not shortcode_only).
 */
add_action('wp_enqueue_scripts', function(){
    if ( ! get_theme_mod('soype_whatsapp_enabled', 1) ) return;

    $mode = get_theme_mod('soype_whatsapp_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return;

    if ( get_theme_mod('soype_whatsapp_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/whatsapp/whatsapp.css';
    $js  = SOYPECC_PATH . 'assets/whatsapp/whatsapp.js';

    wp_enqueue_style(
        'soypecc-whatsapp',
        SOYPECC_URL . 'assets/whatsapp/whatsapp.css',
        [],
        file_exists($css) ? filemtime($css) : '1.0'
    );
    wp_enqueue_script(
        'soypecc-whatsapp',
        SOYPECC_URL . 'assets/whatsapp/whatsapp.js',
        [],
        file_exists($js) ? filemtime($js) : '1.0',
        true
    );
});

/**
 * ========== Helpers ==========
 */

/** Build wa.me URL with sanitized phone and encoded text. */
if ( ! function_exists('soypecc_build_whatsapp_url') ) {
    function soypecc_build_whatsapp_url($phone_digits, $text) {
        $p = preg_replace('/\D+/', '', (string)$phone_digits);
        $t = rawurlencode((string)$text);
        if (empty($p)) return '';
        return "https://wa.me/{$p}" . ($t !== '' ? "?text={$t}" : '');
    }
}

/**
 * Render helper: returns HTML string for the WhatsApp button.
 * Exposes $class, $label, $href to the template (templates/whatsapp-button.php).
 */
if ( ! function_exists('soypecc_render_whatsapp_button') ) {
    function soypecc_render_whatsapp_button($args = []) {

        if ( ! get_theme_mod('soype_whatsapp_enabled', 1) ) {
            return '';
        }

        $defaults = [
            'class' => get_theme_mod('soype_whatsapp_class', ''),
            'phone' => get_theme_mod('soype_whatsapp_phone', ''),
            'text'  => get_theme_mod('soype_whatsapp_text', ''),
            'label' => get_theme_mod('soype_whatsapp_label', __('Escribinos por WhatsApp', 'soype')),
            'target'=> '_blank',
        ];
        $args = wp_parse_args($args, $defaults);

        // Build URL; if no valid phone, do not render.
        $href = soypecc_build_whatsapp_url($args['phone'], $args['text']);
        if ( $href === '' ) return '';

        ob_start();
        $class  = $args['class'];
        $label  = $args['label'];
        $target = $args['target'];
        $href   = $href; // expose to template
        include SOYPECC_PATH . 'templates/whatsapp.php';
        return ob_get_clean();
    }
}

/**
 * ========== Shortcode ==========
 * Usage: [soype_whatsapp phone="5491112345678" text="Hola!" label="Escribinos" class="btn btn--whatsapp" target="_blank"]
 * If attributes are omitted, values are taken from the Customizer.
 */
add_shortcode('soype_whatsapp', function($atts){

    if ( ! get_theme_mod('soype_whatsapp_enabled', 1) ) {
        return '';
    }

    $atts = shortcode_atts([
        'class'  => '',
        'phone'  => '',
        'text'   => '',
        'label'  => '',
        'target' => '_blank',
    ], $atts, 'soype_whatsapp');

    // Enqueue assets when shortcode is used
    $css = SOYPECC_PATH . 'assets/whatsapp/whatsapp.css';
    $js  = SOYPECC_PATH . 'assets/whatsapp/whatsapp.js';
    wp_enqueue_style('soypecc-whatsapp', SOYPECC_URL . 'assets/whatsapp/whatsapp.css', [], file_exists($css) ? filemtime($css) : '1.0');
    wp_enqueue_script('soypecc-whatsapp', SOYPECC_URL . 'assets/whatsapp/whatsapp.js', [], file_exists($js) ? filemtime($js) : '1.0', true);

    // Fallback to Customizer values where shortcode attrs are empty
    $args = [
        'class' => $atts['class'] !== '' ? $atts['class'] : get_theme_mod('soype_whatsapp_class', ''),
        'phone' => $atts['phone'] !== '' ? $atts['phone'] : get_theme_mod('soype_whatsapp_phone', ''),
        'text'  => $atts['text']  !== '' ? $atts['text']  : get_theme_mod('soype_whatsapp_text', ''),
        'label' => $atts['label'] !== '' ? $atts['label'] : get_theme_mod('soype_whatsapp_label', __('Escribinos por WhatsApp', 'soype')),
        'target'=> $atts['target'] ?: '_blank',
    ];

    return soypecc_render_whatsapp_button($args);
});

/**
 * ========== Template tag ==========
 * Example: <?php if (function_exists('soypecc_the_whatsapp_button')) soypecc_the_whatsapp_button(); ?>
 */
if ( ! function_exists('soypecc_the_whatsapp_button') ) {
    function soypecc_the_whatsapp_button($args = []) { echo soypecc_render_whatsapp_button($args); }
}

/**
 * ========== Automatic injection ==========
 * Mode 1: via theme hook (default: shopire_site_main_header)
 * Mode 2: prepend to front page content (the_content)
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_whatsapp_enabled', 1) ) return;

    $mode       = get_theme_mod('soype_whatsapp_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_whatsapp_only_front', 1);

    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_whatsapp_theme_hook', 'shopire_site_main_header');
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_whatsapp_button();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_whatsapp_button() . $content;
        }, 5);
    }
});
