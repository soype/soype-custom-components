<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Two Column Block ==========
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // Section
    $wp_customize->add_section('soype_two_columns_section', [
        'title'       => __('SoyPe: Two Column Block', 'soype'),
        'priority'    => 30,
        'description' => __('Configure the two side-by-side content blocks (titles, descriptions, images, and background colors).', 'soype'),
    ]);

    // --- Sanitizers (idempotent) ---
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

    // ====== General toggles ======
    // Enabled
    $wp_customize->add_setting('soype_twocol_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_twocol_enabled', [
        'label'   => __('Show Two Column Block', 'soype'),
        'section' => 'soype_two_columns_section',
        'type'    => 'checkbox',
    ]);

    // Front page only
    $wp_customize->add_setting('soype_twocol_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_twocol_only_front', [
        'label'   => __('Show only on the front page', 'soype'),
        'section' => 'soype_two_columns_section',
        'type'    => 'checkbox',
    ]);

    // Optional custom CSS classes on wrapper
    $wp_customize->add_setting('soype_twocol_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_twocol_class', [
        'label'       => __('Wrapper CSS classes', 'soype'),
        'description' => __('Space-separated classes, e.g. "container my-8"', 'soype'),
        'section'     => 'soype_two_columns_section',
        'type'        => 'text',
    ]);

    // ====== Columns (2) ======
    for ($i = 1; $i <= 2; $i++) {
        // Title
        $wp_customize->add_setting("soype_twocol_{$i}_title", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_twocol_{$i}_title", [
            'label'   => sprintf(__('Column %d: Title', 'soype'), $i),
            'section' => 'soype_two_columns_section',
            'type'    => 'text',
        ]);

        // Description (allows basic HTML)
        $wp_customize->add_setting("soype_twocol_{$i}_desc", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'wp_kses_post',
        ]);
        $wp_customize->add_control("soype_twocol_{$i}_desc", [
            'label'   => sprintf(__('Column %d: Description', 'soype'), $i),
            'section' => 'soype_two_columns_section',
            'type'    => 'textarea',
        ]);

        // CTA Text
        $wp_customize->add_setting("soype_twocol_{$i}_cta_text", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_twocol_{$i}_cta_text", [
            'label'   => sprintf(__('Column %d: CTA Text', 'soype'), $i),
            'section' => 'soype_two_columns_section',
            'type'    => 'text',
        ]);

        // CTA Link
        $wp_customize->add_setting("soype_twocol_{$i}_cta_link", [
            'default'           => '',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("soype_twocol_{$i}_cta_link", [
            'label'   => sprintf(__('Column %d: CTA Link', 'soype'), $i),
            'section' => 'soype_two_columns_section',
            'type'    => 'text',
        ]);

        // Image (attachment id)
        $wp_customize->add_setting("soype_twocol_{$i}_image", [
            'default'           => 0,
            'type'              => 'theme_mod',
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control(new WP_Customize_Media_Control(
            $wp_customize,
            "soype_twocol_{$i}_image",
            [
                'label'     => sprintf(__('Column %d: Image', 'soype'), $i),
                'section'   => 'soype_two_columns_section',
                'mime_type' => 'image',
            ]
        ));

        // Background color (HEX) — native color picker
        $wp_customize->add_setting("soype_twocol_{$i}_bg", [
            'default'           => '#ffffff',
            'type'              => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control(
            $wp_customize,
            "soype_twocol_{$i}_bg",
            [
                'label'       => sprintf(__('Column %d: Background color', 'soype'), $i),
                'description' => __('Pick a HEX color for the column background.', 'soype'),
                'section'     => 'soype_two_columns_section',
            ]
        ));
    }

    // Injection mode: theme_hook | content | shortcode_only
    $wp_customize->add_setting('soype_twocol_injection', [
        'default'           => 'theme_hook',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_twocol_injection', [
        'label'   => __('Injection location', 'soype'),
        'section' => 'soype_two_columns_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Theme hook (recommended)', 'soype'),
            'content'        => __('Prepend to home content', 'soype'),
            'shortcode_only' => __('Shortcode/block only (manual)', 'soype'),
        ],
    ]);

    // Theme hook name (only used if theme_hook mode)
    $wp_customize->add_setting('soype_twocol_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_key',
    ]);
    $wp_customize->add_control('soype_twocol_theme_hook', [
        'label'       => __('Theme hook name', 'soype'),
        'description' => __('E.g., shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_two_columns_section',
        'type'        => 'text',
    ]);

});

/**
 * ========== Assets (only when active and relevant) ==========
 */
add_action('wp_enqueue_scripts', function () {
    if ( ! get_theme_mod('soype_twocol_enabled', 1) ) return;

    $mode = get_theme_mod('soype_twocol_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return;
    if ( get_theme_mod('soype_twocol_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/twocolumnblock/twocolumnblock.css';
    $js  = SOYPECC_PATH . 'assets/twocolumnblock/twocolumnblock.js';

    // Safe enqueue with mtime as version when file exists
    if ( file_exists($css) ) {
        wp_enqueue_style('soypecc-twocolumnblock', SOYPECC_URL . 'assets/twocolumnblock/twocolumnblock.css', [], filemtime($css));
    }
    if ( file_exists($js) ) {
        wp_enqueue_script('soypecc-twocolumnblock', SOYPECC_URL . 'assets/twocolumnblock/twocolumnblock.js', [], filemtime($js), true);
    }
});

/**
 * ========== Data helper ==========
 */
if ( ! function_exists('soypecc_get_two_columns') ) {
    function soypecc_get_two_columns() {
        // Build both columns; if both are completely empty, caller can decide not to render.
        $cols = [];
        for ($i = 1; $i <= 2; $i++) {
            $cols[] = [
                'title'   => (string) get_theme_mod("soype_twocol_{$i}_title", ''),
                'desc'    => (string) get_theme_mod("soype_twocol_{$i}_desc", ''),
                'cta-text'   => (string) get_theme_mod("soype_twocol_{$i}_cta_text", ''),
                'cta-link'   => (string) get_theme_mod("soype_twocol_{$i}_cta_link", ''),
                'image'   => (int)    get_theme_mod("soype_twocol_{$i}_image", 0),
                'bg'      => (string) get_theme_mod("soype_twocol_{$i}_bg", '#ffffff'),
                // Derived values
                'image_src' => ( $id = (int) get_theme_mod("soype_twocol_{$i}_image", 0) ) ? wp_get_attachment_image_src($id, 'full')[0] ?? '' : '',
            ];
        }
        return $cols;
    }
}

/**
 * ========== Render helper ==========
 */
if ( ! function_exists('soypecc_render_two_columns') ) {
    function soypecc_render_two_columns($args = []) {

        if ( ! get_theme_mod('soype_twocol_enabled', 1) ) {
            return '';
        }

        $columns = soypecc_get_two_columns();

        // Check if at least one column has meaningful content
        $has_content = false;
        foreach ($columns as $c) {
            if ( ! empty($c['title']) || ! empty($c['desc']) || ! empty($c['image']) ) {
                $has_content = true; break;
            }
        }
        if ( ! $has_content ) return '';

        $args = wp_parse_args($args, [
            'class'   => get_theme_mod('soype_twocol_class', ''),
            'columns' => $columns,
        ]);

        ob_start();
        extract($args, EXTR_OVERWRITE);

        // Template path: /templates/twocolumnblock.php
        // The template should expect $class and $columns.
        $tpl = SOYPECC_PATH . 'templates/twocolumnblock.php';
        if ( file_exists($tpl) ) {
            include $tpl;
        } else {
            // Minimal fallback markup (kept simple)
            ?>
            <section class="soype-twocolumnblock <?php echo esc_attr($class); ?>">
                <div class="soype-twocolumnblock__inner" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                    <?php foreach ($columns as $col): ?>
                        <div class="soype-twocolumnblock__col" style="background:<?php echo esc_attr($col['bg']); ?>;padding:16px;border-radius:8px;">
                            <?php if (!empty($col['image_src'])): ?>
                                <div class="soype-twocolumnblock__media" style="margin-bottom:12px;">
                                    <img src="<?php echo esc_url($col['image_src']); ?>" alt="" style="max-width:100%;height:auto;display:block;">
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($col['title'])): ?>
                                <h3 class="soype-twocolumnblock__title"><?php echo esc_html($col['title']); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($col['desc'])): ?>
                                <div class="soype-twocolumnblock__desc"><?php echo wp_kses_post(wpautop($col['desc'])); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php
        }

        return ob_get_clean();
    }
}

/**
 * ========== Shortcode ==========
 * Usage: [soype_two_columns]
 */
add_shortcode('soype_two_columns', function($atts){
    if ( ! get_theme_mod('soype_twocol_enabled', 1) ) return '';

    // Ensure styles/scripts if files exist
    $css = SOYPECC_PATH . 'assets/twocolumnblock/twocolumnblock.css';
    $js  = SOYPECC_PATH . 'assets/twocolumnblock/twocolumnblock.js';
    if ( file_exists($css) ) wp_enqueue_style('soypecc-twocolumnblock', SOYPECC_URL . 'assets/twocolumnblock/twocolumnblock.css', [], filemtime($css));
    if ( file_exists($js) )  wp_enqueue_script('soypecc-twocolumnblock', SOYPECC_URL . 'assets/twocolumnblock/twocolumnblock.js', [], filemtime($js), true);

    return soypecc_render_two_columns();
});

/**
 * ========== Template tag ==========
 * Call with: <?php if (function_exists('soypecc_the_two_columns')) soypecc_the_two_columns(); ?>
 */
if ( ! function_exists('soypecc_the_two_columns') ) {
    function soypecc_the_two_columns($args = []) { echo soypecc_render_two_columns($args); }
}

/**
 * ========== Automatic injection ==========
 * Mode 1: Theme hook (default: shopire_site_main_header)
 * Mode 2: Prepend to front page content (the_content)
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_twocol_enabled', 1) ) return;

    $mode       = get_theme_mod('soype_twocol_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_twocol_only_front', 1);

    // Visibility helper
    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_twocol_theme_hook', 'shopire_site_main_header');
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_two_columns();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_two_columns() . $content;
        }, 5);
    }
});
