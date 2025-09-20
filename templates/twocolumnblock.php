<?php
/** @var string $title */
/** @var array  $columns */
/** @var array  $rows */
/** @var string $class */
?>

<section class="soype-twocolumnblock <?php echo esc_attr($class); ?>">
  <div class="soype-twocolumnblock__inner">

    <?php foreach ($columns as $i => $col): ?>
      <?php
        // Determine current row index: items 0–1 => row 1, 2–3 => row 2
        $row_index = ($i < 2) ? 1 : 2;
        $row_bg    = isset($rows[$row_index]['bg']) ? (string) $rows[$row_index]['bg'] : '';

        // Open row wrappers at the start of each row
        if ($i === 0 || $i === 2) {
            // Full-bleed color on the row; inner keeps the content constrained
            echo '<div class="soype-twocolumnblock__row" style="'
               . ( $row_bg !== '' ? 'background:' . esc_attr($row_bg) . ';' : '' )
               . '">';
            echo '<div class="soype-twocolumnblock__row-inner">';
        }

        // Decide if media needs box treatment (non-white-ish backgrounds)
        $non_white_row = ($row_bg !== '' && strtolower($row_bg) !== '#ffffff');
      ?>

      <div class="soype-twocolumnblock__col soype-twocolumnblock__col-<?php echo ($i+1); ?>">
        <?php if (!empty($col['image_src'])): ?>
          <div class="soype-twocolumnblock__media <?php echo $non_white_row ? 'soype-twocolumnblock__media--box' : ''; ?>">
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

          <?php if (!empty($col['cta-link'])): ?>
            <a class="soype-twocolumnblock__cta soype-button" href="<?php echo esc_url($col['cta-link']); ?>">
              <?php echo esc_html($col['cta-text'] ?: __('Learn more', 'soype')); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <?php
        // Close row wrappers at the end of each row
        if ($i === 1 || $i === 3) {
            echo '</div></div>';
        }
      ?>
    <?php endforeach; ?>

  </div>
</section>
