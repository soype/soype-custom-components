<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Accordion (FAQs) ==========
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // -------- Config básica ----------
    $HARD_MAX = 20; // Límite superior absoluto para evitar sobrecargar el Customizer

    // Section
    $wp_customize->add_section('soype_accordion_section', [
        'title'       => __('SoyPe: Accordion (FAQs)', 'soype'),
        'priority'    => 30,
        'description' => __('Configura el acordeón de Preguntas Frecuentes (FAQs): cantidad, items, clases y ubicación.', 'soype'),
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

    // Enabled
    $wp_customize->add_setting('soype_accordion_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_accordion_enabled', [
        'label'   => __('Mostrar accordion (FAQs)', 'soype'),
        'section' => 'soype_accordion_section',
        'type'    => 'checkbox',
    ]);

    // Sólo uno abierto a la vez
    $wp_customize->add_setting('soype_accordion_single', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_accordion_single', [
        'label'       => __('Abrir sólo una pregunta a la vez', 'soype'),
        'description' => __('Si está activo, al abrir una se cierran las demás.', 'soype'),
        'section'     => 'soype_accordion_section',
        'type'        => 'checkbox',
    ]);

    // Cantidad de FAQs (definida por el usuario)
    $wp_customize->add_setting('soype_accordion_count', [
        'default'           => 6,
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v) use ($HARD_MAX) {
            $n = absint($v);
            if ($n < 1) $n = 1;
            if ($n > $HARD_MAX) $n = $HARD_MAX;
            return $n;
        },
        'transport'         => 'postMessage', // permite que active_callback reaccione en vivo
    ]);
    $wp_customize->add_control('soype_accordion_count', [
        'label'       => __('Cantidad de FAQs', 'soype'),
        'description' => sprintf(__('Define cuántos ítems mostrar (1–%d).', 'soype'), $HARD_MAX),
        'section'     => 'soype_accordion_section',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 1,
            'max'  => $HARD_MAX,
            'step' => 1,
        ],
    ]);

    // Injection
    $wp_customize->add_setting('soype_accordion_injection', [
        'default'           => 'theme_hook', // theme_hook | content | shortcode_only
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_accordion_injection', [
        'label'   => __('Ubicación del accordion', 'soype'),
        'section' => 'soype_accordion_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Hook del tema (recomendado)', 'soype'),
            'content'        => __('Al inicio del contenido de la portada', 'soype'),
            'shortcode_only' => __('Solo mediante shortcode/bloque (manual)', 'soype'),
        ],
    ]);

    // Hook del tema
    $wp_customize->add_setting('soype_accordion_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){ return sanitize_key($v); },
    ]);
    $wp_customize->add_control('soype_accordion_theme_hook', [
        'label'       => __('Nombre del hook del tema', 'soype'),
        'description' => __('Ej.: shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_accordion_section',
        'type'        => 'text',
    ]);

    // Sólo portada
    $wp_customize->add_setting('soype_accordion_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_accordion_only_front', [
        'label'   => __('Mostrar solo en la portada', 'soype'),
        'section' => 'soype_accordion_section',
        'type'    => 'checkbox',
    ]);

    // Clases CSS
    $wp_customize->add_setting('soype_accordion_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_accordion_class', [
        'label'       => __('Clases CSS (separadas por espacio)', 'soype'),
        'description' => __('Ej: faq one-col spacing-md', 'soype'),
        'section'     => 'soype_accordion_section',
        'type'        => 'text',
    ]);

    // ===== Items FAQ (pre-registrados hasta HARD_MAX) =====
    for ($i = 1; $i <= $HARD_MAX; $i++) {

        // Helper: mostrar control sólo si $i <= count actual
        $active_cb = function( $control ) use ( $i, $HARD_MAX ) {
            $setting = $control->manager->get_setting('soype_accordion_count');
            $count   = $setting ? absint( $setting->value() ) : 0;
            if ($count < 1) $count = 1;
            if ($count > $HARD_MAX) $count = $HARD_MAX;
            return $i <= $count;
        };

        // Etiqueta interna (sólo para ordenar visualmente)
        $wp_customize->add_setting("soype_accordion_item_{$i}_label", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_accordion_item_{$i}_label", [
            'label'           => sprintf(__('Item %d', 'soype'), $i),
            'description'     => __('(Etiqueta interna; no se muestra en el front)', 'soype'),
            'section'         => 'soype_accordion_section',
            'type'            => 'text',
            'active_callback' => $active_cb,
        ]);

        // Pregunta
        $wp_customize->add_setting("soype_faq_{$i}_question", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_faq_{$i}_question", [
            'label'           => sprintf(__('Pregunta %d', 'soype'), $i),
            'section'         => 'soype_accordion_section',
            'type'            => 'text',
            'active_callback' => $active_cb,
        ]);

        // Respuesta (HTML permitido)
        $wp_customize->add_setting("soype_faq_{$i}_answer", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'wp_kses_post',
        ]);
        $wp_customize->add_control("soype_faq_{$i}_answer", [
            'label'           => sprintf(__('Respuesta %d', 'soype'), $i),
            'description'     => __('Permite HTML básico (p, a, strong, em, br, listas...).', 'soype'),
            'section'         => 'soype_accordion_section',
            'type'            => 'textarea',
            'active_callback' => $active_cb,
        ]);

        // Abierto por defecto
        $wp_customize->add_setting("soype_faq_{$i}_open", [
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'soypecc_sanitize_checkbox',
        ]);
        $wp_customize->add_control("soype_faq_{$i}_open", [
            'label'           => sprintf(__('Abrir por defecto (item %d)', 'soype'), $i),
            'section'         => 'soype_accordion_section',
            'type'            => 'checkbox',
            'active_callback' => $active_cb,
        ]);
    }
});

/**
 * ========== Accordion Assets ==========
 * Sólo carga si el componente está activo
 */
add_action('wp_enqueue_scripts', function(){
    if ( ! get_theme_mod('soype_accordion_enabled', 1) ) return;

    $mode = get_theme_mod('soype_accordion_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return;

    if ( get_theme_mod('soype_accordion_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/accordion/accordion.css';
    $js  = SOYPECC_PATH . 'assets/accordion/accordion.js';

    wp_enqueue_style(
        'soypecc-accordion',
        SOYPECC_URL . 'assets/accordion/accordion.css',
        [],
        file_exists($css) ? filemtime($css) : '1.0'
    );
    wp_enqueue_script(
        'soypecc-accordion',
        SOYPECC_URL . 'assets/accordion/accordion.js',
        [],
        file_exists($js) ? filemtime($js) : '1.0',
        true
    );
});

/**
 * ========== Helpers ==========
 */
if ( ! function_exists('soypecc_get_faqs') ) {
    function soypecc_get_faqs() {
        $HARD_MAX = 20;
        $count = absint( get_theme_mod('soype_accordion_count', 6) );
        if ($count < 1) $count = 1;
        if ($count > $HARD_MAX) $count = $HARD_MAX;

        $faqs = [];
        for ($i = 1; $i <= $count; $i++) {
            $q = (string) get_theme_mod("soype_faq_{$i}_question", '');
            $a = (string) get_theme_mod("soype_faq_{$i}_answer", '');
            if ( $q === '' && trim(wp_strip_all_tags($a)) === '' ) {
                continue; // vacío => ignorar
            }
            $open = (bool) get_theme_mod("soype_faq_{$i}_open", 0);
            $faqs[] = [
                'question' => $q,
                'answer'   => $a,
                'open'     => $open,
            ];
        }
        return $faqs;
    }
}

/**
 * ========== Render helper ==========
 */
if ( ! function_exists('soypecc_render_accordion') ) {
    function soypecc_render_accordion($args = []) {

        if ( ! get_theme_mod('soype_accordion_enabled', 1) ) {
            return '';
        }

        $faqs = soypecc_get_faqs();
        if ( empty($faqs) ) {
            return ''; // no hay items, no se renderiza
        }

        $defaults = [
            'class'  => get_theme_mod('soype_accordion_class', ''),
            'faqs'   => $faqs,
            'single' => (bool) get_theme_mod('soype_accordion_single', 1),
        ];
        $args = wp_parse_args($args, $defaults);

        // JSON-LD (FAQPage) para SEO
        $json_entities = array_map(function($it){
            return [
                '@type'          => 'Question',
                'name'           => wp_strip_all_tags( $it['question'] ),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( $it['answer'] ),
                ],
            ];
        }, $args['faqs']);

        $jsonld = '';
        if ( ! empty($json_entities) ) {
            $schema = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $json_entities,
            ];
            $jsonld = '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) . '</script>';
        }

        ob_start();
        $class  = $args['class'];
        $faqs   = $args['faqs'];
        $single = $args['single'];
        include SOYPECC_PATH . 'templates/accordion.php';
        $html = ob_get_clean();

        return $html . $jsonld;
    }
}

/**
 * ========== Shortcode ==========
 * Uso: [soype_accordion]
 */
add_shortcode('soype_accordion', function($atts){

    if ( ! get_theme_mod('soype_accordion_enabled', 1) ) {
        return '';
    }

    // Encola assets cuando se usa el shortcode
    $css = SOYPECC_PATH . 'assets/accordion/accordion.css';
    $js  = SOYPECC_PATH . 'assets/accordion/accordion.js';
    wp_enqueue_style('soypecc-accordion', SOYPECC_URL . 'assets/accordion/accordion.css', [], file_exists($css) ? filemtime($css) : '1.0');
    wp_enqueue_script('soypecc-accordion', SOYPECC_URL . 'assets/accordion/accordion.js', [], file_exists($js) ? filemtime($js) : '1.0', true);

    return soypecc_render_accordion();
});

/**
 * ========== Template tag ==========
 * Ej.: <?php if (function_exists('soypecc_the_accordion')) soypecc_the_accordion(); ?>
 */
if ( ! function_exists('soypecc_the_accordion') ) {
    function soypecc_the_accordion($args = []) { echo soypecc_render_accordion($args); }
}

/**
 * ========== Inyección automática ==========
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_accordion_enabled', 1) ) return;

    $mode = get_theme_mod('soype_accordion_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_accordion_only_front', 1);

    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_accordion_theme_hook', 'shopire_site_main_header');
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_accordion();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_accordion() . $content;
        }, 5);
    }
});
