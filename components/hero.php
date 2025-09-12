<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * ========== CUSTOMIZER: Hero ==========
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // Section
    $wp_customize->add_section('soype_hero_section', [
        'title'       => __('SoyPe: Hero', 'soype'),
        'priority'    => 29,
        'description' => __('Configure the hero (title, description, CTA, image, and background color).', 'soype'),
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
    $wp_customize->add_setting('soype_hero_enabled', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_hero_enabled', [
        'label'   => __('Show Hero', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'checkbox',
    ]);

    // Front page only
    $wp_customize->add_setting('soype_hero_only_front', [
        'default'           => 1,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_checkbox',
    ]);
    $wp_customize->add_control('soype_hero_only_front', [
        'label'   => __('Show only on the front page', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'checkbox',
    ]);

    // Optional custom CSS classes on wrapper
    $wp_customize->add_setting('soype_hero_class', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'soypecc_sanitize_css_class',
    ]);
    $wp_customize->add_control('soype_hero_class', [
        'label'       => __('Wrapper CSS classes', 'soype'),
        'description' => __('Space-separated classes, e.g. "container my-12"', 'soype'),
        'section'     => 'soype_hero_section',
        'type'        => 'text',
    ]);

    // ====== Hero fields (mirrors a single Two Column item) ======
    // Title
    $wp_customize->add_setting('soype_hero_title', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('soype_hero_title', [
        'label'   => __('Title', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'text',
    ]);

    // Description (allows basic HTML)
    $wp_customize->add_setting('soype_hero_desc', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('soype_hero_desc', [
        'label'   => __('Description', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'textarea',
    ]);

    // CTA Text
    $wp_customize->add_setting('soype_hero_cta_text', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('soype_hero_cta_text', [
        'label'   => __('CTA Text', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'text',
    ]);

    // CTA Link
    $wp_customize->add_setting('soype_hero_cta_link', [
        'default'           => '',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('soype_hero_cta_link', [
        'label'       => __('CTA Link (URL)', 'soype'),
        'description' => __('E.g., https://yourdomain.com/page', 'soype'),
        'section'     => 'soype_hero_section',
        'type'        => 'url',
    ]);

    // Image (attachment id)
    $wp_customize->add_setting('soype_hero_image', [
        'default'           => 0,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control(
        $wp_customize,
        'soype_hero_image',
        [
            'label'     => __('Image', 'soype'),
            'section'   => 'soype_hero_section',
            'mime_type' => 'image',
        ]
    ));

    // Background color (HEX) — native color picker
    $wp_customize->add_setting('soype_hero_bg', [
        'default'           => '#ffffff',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control(
        $wp_customize,
        'soype_hero_bg',
        [
            'label'       => __('Background color', 'soype'),
            'description' => __('Pick a HEX color for the hero background.', 'soype'),
            'section'     => 'soype_hero_section',
        ]
    ));

    // ====== Injection mode: theme_hook | content | shortcode_only ======
    $wp_customize->add_setting('soype_hero_injection', [
        'default'           => 'theme_hook',
        'type'              => 'theme_mod',
        'sanitize_callback' => function($v){
            return in_array($v, ['theme_hook','content','shortcode_only'], true) ? $v : 'theme_hook';
        },
    ]);
    $wp_customize->add_control('soype_hero_injection', [
        'label'   => __('Injection location', 'soype'),
        'section' => 'soype_hero_section',
        'type'    => 'select',
        'choices' => [
            'theme_hook'     => __('Theme hook (recommended)', 'soype'),
            'content'        => __('Prepend to home content', 'soype'),
            'shortcode_only' => __('Shortcode/block only (manual)', 'soype'),
        ],
    ]);

    // Theme hook name (only used if theme_hook mode)
    $wp_customize->add_setting('soype_hero_theme_hook', [
        'default'           => 'shopire_site_main_header',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_key',
    ]);
    $wp_customize->add_control('soype_hero_theme_hook', [
        'label'       => __('Theme hook name', 'soype'),
        'description' => __('E.g., shopire_site_main_header, wp_body_open, etc.', 'soype'),
        'section'     => 'soype_hero_section',
        'type'        => 'text',
    ]);
});

/**
 * ========== Assets (only when active and relevant) ==========
 */
add_action('wp_enqueue_scripts', function () {
    if ( ! get_theme_mod('soype_hero_enabled', 1) ) return;

    $mode = get_theme_mod('soype_hero_injection', 'theme_hook');
    if ( $mode === 'shortcode_only' ) return;
    if ( get_theme_mod('soype_hero_only_front', 1) && ! is_front_page() ) return;

    $css = SOYPECC_PATH . 'assets/hero/hero.css';
    $js  = SOYPECC_PATH . 'assets/hero/hero.js';

    if ( file_exists($css) ) {
        wp_enqueue_style('soypecc-hero', SOYPECC_URL . 'assets/hero/hero.css', [], filemtime($css));
    }
    if ( file_exists($js) ) {
        wp_enqueue_script('soypecc-hero', SOYPECC_URL . 'assets/hero/hero.js', [], filemtime($js), true);
    }
});

/**
 * ========== Data helper ==========
 */
if ( ! function_exists('soypecc_get_hero') ) {
    function soypecc_get_hero() {
        $id = (int) get_theme_mod('soype_hero_image', 0);
        $image_src = $id ? ( wp_get_attachment_image_src($id, 'full')[0] ?? '' ) : '';

        return [
            'title'     => (string) get_theme_mod('soype_hero_title', ''),
            'desc'      => (string) get_theme_mod('soype_hero_desc', ''),
            'cta_text'  => (string) get_theme_mod('soype_hero_cta_text', ''),
            'cta_link'  => (string) get_theme_mod('soype_hero_cta_link', ''),
            'image_id'  => $id,
            'image_src' => $image_src,
            'bg'        => (string) get_theme_mod('soype_hero_bg', '#ffffff'),
        ];
    }
}

/**
 * ========== Render helper ==========
 */
if ( ! function_exists('soypecc_render_hero') ) {
    function soypecc_render_hero($args = []) {

        if ( ! get_theme_mod('soype_hero_enabled', 1) ) {
            return '';
        }

        $hero = soypecc_get_hero();

        // If everything is empty (no title, desc, image), don't render
        if ( empty($hero['title']) && empty($hero['desc']) && empty($hero['image_src']) ) {
            return '';
        }

        $args = wp_parse_args($args, [
            'class' => get_theme_mod('soype_hero_class', ''),
            'hero'  => $hero,
        ]);

        ob_start();
        extract($args, EXTR_OVERWRITE);

        // Template path: /templates/hero.php (expects $class and $hero)
        $tpl = SOYPECC_PATH . 'templates/hero.php';
        if ( file_exists($tpl) ) {
            include $tpl;
        } else {
            // Minimal fallback markup
            ?>
            <section class="soype-hero <?php echo esc_attr($class); ?>" style="background:<?php echo esc_attr($hero['bg']); ?>;">
                <div class="soype-hero__inner" style="display:grid;grid-template-columns:1fr;gap:20px;align-items:center;padding:24px;">
                    <?php if (!empty($hero['image_src'])): ?>
                        <div class="soype-hero__media" style="margin-bottom:12px;">
                            <img src="<?php echo esc_url($hero['image_src']); ?>" alt="" style="max-width:100%;height:auto;display:block;">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($hero['title'])): ?>
                        <h2 class="soype-hero__title"><?php echo esc_html($hero['title']); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($hero['desc'])): ?>
                        <div class="soype-hero__desc"><?php echo wp_kses_post(wpautop($hero['desc'])); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($hero['cta_text']) && !empty($hero['cta_link'])): ?>
                        <p class="soype-hero__cta">
                            <a class="soype-button" href="<?php echo esc_url($hero['cta_link']); ?>">
                                <?php echo esc_html($hero['cta_text']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </section>
            <?php
        }

        return ob_get_clean();
    }
}

/**
 * ========== Shortcode ==========
 * Usage: [soype_hero]
 */
add_shortcode('soype_hero', function($atts){
    if ( ! get_theme_mod('soype_hero_enabled', 1) ) return '';

    // Ensure styles/scripts if files exist
    $css = SOYPECC_PATH . 'assets/hero/hero.css';
    $js  = SOYPECC_PATH . 'assets/hero/hero.js';
    if ( file_exists($css) ) wp_enqueue_style('soypecc-hero', SOYPECC_URL . 'assets/hero/hero.css', [], filemtime($css));
    if ( file_exists($js) )  wp_enqueue_script('soypecc-hero', SOYPECC_URL . 'assets/hero/hero.js', [], filemtime($js), true);

    return soypecc_render_hero();
});

/**
 * ========== Template tag ==========
 * Call with: <?php if (function_exists('soypecc_the_hero')) soypecc_the_hero(); ?>
 */
if ( ! function_exists('soypecc_the_hero') ) {
    function soypecc_the_hero($args = []) { echo soypecc_render_hero($args); }
}

/**
 * ========== Automatic injection ==========
 * Mode 1: Theme hook (default: shopire_site_main_header)
 * Mode 2: Prepend to front page content (the_content)
 */
add_action('init', function(){

    if ( ! get_theme_mod('soype_hero_enabled', 1) ) return;

    $mode       = get_theme_mod('soype_hero_injection', 'theme_hook');
    $only_front = get_theme_mod('soype_hero_only_front', 1);

    // Visibility helper
    $should_show = function() use ($only_front) {
        return $only_front ? is_front_page() : true;
    };

    if ( $mode === 'theme_hook' ) {
        $hook = get_theme_mod('soype_hero_theme_hook', 'shopire_site_main_header');
        add_action($hook, function() use ($should_show) {
            if ( ! is_admin() && $should_show() ) {
                echo soypecc_render_hero();
            }
        }, 99);
    }
    elseif ( $mode === 'content' ) {
        add_filter('the_content', function($content) use ($should_show) {
            if ( is_admin() || ! in_the_loop() || ! is_main_query() ) return $content;
            if ( ! $should_show() ) return $content;
            return soypecc_render_hero() . $content;
        }, 5);
    }
});
