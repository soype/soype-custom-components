<?php
/** @var string $class */
/** @var array  $hero */
/**
 * Minimal Hero template
 * - Mirrors one column of the Two Column Block
 * - Safe escaping throughout
 * - Uses <a> for CTA (button semantics via class)
 */
?>

<section class="soype-hero <?php echo esc_attr($class); ?>" style="background:<?php echo esc_attr($hero['bg'] ?? '#ffffff'); ?>">
    <div class="soype-hero__inner">
        <?php if ( ! empty($hero['image_src']) ): ?>
            <div class="soype-hero__media">
                <img src="<?php echo esc_url($hero['image_src']); ?>" alt="" style="max-width:100%;height:auto;display:block;">
            </div>
        <?php endif; ?>

        <div class="soype-hero__content">
            <?php if ( ! empty($hero['title']) ): ?>
                <h2 class="soype-hero__title"><?php echo esc_html($hero['title']); ?></h2>
            <?php endif; ?>

            <?php if ( ! empty($hero['desc']) ): ?>
                <div class="soype-hero__desc"><?php echo wp_kses_post( wpautop($hero['desc']) ); ?></div>
            <?php endif; ?>

            <?php if ( ! empty($hero['cta_text']) && ! empty($hero['cta_link']) ): ?>
                <p class="soype-hero__cta">
                    <a class="soype-button" href="<?php echo esc_url($hero['cta_link']); ?>">
                        <?php echo esc_html($hero['cta_text']); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>
