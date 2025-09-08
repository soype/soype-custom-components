<?php
/** @var string $title */
/** @var string $class */
/** @var array $slides ['image_id','title','link','cta_enabled','cta_text'] **/
?>
<div class="soype-slider <?php echo esc_attr($class); ?>">

  <div class="soype-slider__track">
    <?php foreach ($slides as $idx => $s): 
        $img_id      = (int) $s['image_id'];
        $s_title     = isset($s['title']) ? (string)$s['title'] : '';
        $s_link      = isset($s['link']) ? (string)$s['link'] : '';
        $cta_enabled = !empty($s['cta_enabled']);
        $cta_text    = isset($s['cta_text']) ? (string)$s['cta_text'] : '';

        // Eager loading for the first slide, lazy for the rest
        $loading = ($idx === 0) ? 'eager' : 'lazy';
        $img_html = wp_get_attachment_image(
            $img_id,
            'full',
            false,
            ['decoding' => 'async', 'loading' => $loading, 'class' => 'soype-slide__img']
        );
    ?>
      <div class="soype-slide">
        <div class="soype-slide__media">
          <?php if ( $cta_enabled ) : ?>
            <?php echo $img_html; ?>
          <?php else: ?>
            <?php if ( ! empty($s_link) ) : ?>
              <a class="soype-slide__link" href="<?php echo esc_url($s_link); ?>">
                <?php echo $img_html; ?>
              </a>
            <?php else: ?>
              <?php echo $img_html; ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class='soype-slide__backdrop'></div>
        <div class="soype-slide__content">
          <?php if ( mb_strlen( trim( wp_strip_all_tags($s_title) ) ) > 0 ) : ?>
            <h3 class="soype-slide__title"><?php echo esc_html($s_title); ?></h3>
          <?php endif; ?>

          <?php if ( $cta_enabled && ! empty($s_link) && mb_strlen(trim($cta_text)) > 0 ) : ?>
            <a class="soype-slide__btn" href="<?php echo esc_url($s_link); ?>">
              <?php echo esc_html($cta_text); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="soype-slider__controls">
      <button class='soype-slider-prev' aria-label='Slide anterior'><</button>
      <button class='soype-slider-next' aria-label='Siguiente Slide'>></button>
    </div>

</div>
