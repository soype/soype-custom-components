<?php
/** @var string $title */
/** @var object $col */
?>

<section class="soype-twocolumnblock <?php echo esc_attr($class); ?>">
    <div class="soype-twocolumnblock__inner">
        <?php foreach ($columns as $i => $col): ?>
            <div class="soype-twocolumnblock__col soype-twocolumnblock__col-<?php echo ($i+1); ?>" style="background:<?php echo esc_attr($col['bg']); ?>">
                <?php if (!empty($col['image_src'])): ?>
                    <div class="soype-twocolumnblock__media">
                        <img src="<?php echo esc_url($col['image_src']); ?>" alt="" style="max-width:100%;height:auto;display:block;">
                    </div>
                <?php endif; ?>
                <div class="soype-twocolumnblock__content">
                <?php if (!empty($col['title'])): ?>
                    <h3 class="soype-twocolumnblock__title"><?php echo esc_html($col['title']); ?></h3>
                <?php endif; ?>
                <?php if (!empty($col['desc'])): ?>
                    <div class="soype-twocolumnblock__desc"><?php echo wp_kses_post(wpautop($col['desc'])); ?></div>
                <?php endif; ?>
                <?php if (!empty($col["cta-link"])): ?>
                    <button href='<?php echo esc_attr($col['cta-link']) ?>'><?php echo esc_attr($col['cta-text']) ?></button>
                <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>