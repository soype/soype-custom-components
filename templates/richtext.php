<?php
/** @var string $title */
/** @var string $description */
/** @var string $class */
/** @var string $alignment **/
?>
<div class="soype-richtext <?php echo esc_attr($class); ?>">
    <div class="soype-richtext__container soype-richtext--align-<?php echo esc_attr($alignment) ?>">
        <?php if ( ! empty($title) ): ?>
            <h3 class="soype-richtext__title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>

        <?php if ( ! empty($description) ): ?>
            <div class="soype-richtext__description">
                <?php echo wp_kses_post( $description ); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
