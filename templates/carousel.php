<?php
/**
 * Template: Carousel (Testimonials)
 *
 * Expects:
 * - string $class  Extra CSS classes for the root element.
 * - array  $items  Each item: ['title' => string, 'text' => string (HTML allowed)]
 *
 * Accessibility notes:
 * - Uses ARIA roles/labels to describe a carousel of testimonial cards.
 * - Buttons have aria-controls pointing to the track.
 * - Each slide is a "group" with an "aria-label" like "1 of N".
 */
if ( ! isset($items) || ! is_array($items) || empty($items) ) {
    return; // nothing to render
}

$root_classes = trim('soype-carousel ' . (isset($class) ? (string)$class : ''));
// Unique IDs for track/control relationships
$uid   = 'soype-carousel-' . uniqid();
$track = $uid . '-track';
$total = count($items);
?>
<div class="<?php echo esc_attr($root_classes); ?>"
     id="<?php echo esc_attr($uid); ?>"
     role="region"
     aria-roledescription="carousel"
     aria-label="<?php echo esc_attr__('Testimoniales', 'soype'); ?>">
  <div class="soype-carousel__title">
    <h2><?php echo esc_attr($title) ?></h2>
  </div>
  <!-- Track: JS will translateX this element to create the infinite effect -->
  <div class="soype-carousel__viewport" aria-live="polite">
    <div class="soype-carousel__track" id="<?php echo esc_attr($track); ?>">
      <?php foreach ($items as $i => $it):
          $t_title = isset($it['title']) ? (string)$it['title'] : '';
          $t_text  = isset($it['text'])  ? (string)$it['text']  : '';

          // Compute accessible "x of y" label
          $aria_label = sprintf(
              /* translators: 1: current index, 2: total */
              esc_attr__('%1$d de %2$d', 'soype'),
              $i + 1,
              $total
          );

          // Basic emptiness checks to avoid rendering empty shells
          $has_title = mb_strlen(trim(wp_strip_all_tags($t_title))) > 0;
          $has_text  = mb_strlen(trim(wp_strip_all_tags($t_text)))  > 0;

          if ( ! $has_title && ! $has_text ) {
              continue;
          }
      ?>
      <article class="soype-carousel__slide"
               role="group"
               aria-roledescription="slide"
               aria-label="<?php echo $aria_label; ?>">

        <div class="soype-card soype-card--testimonial">
          <?php if ( $has_title ) : ?>
            <h3 class="soype-card__title">
              <?php echo esc_html($t_title); ?>
            </h3>
          <?php endif; ?>

          <?php if ( $has_text ) : ?>
            <blockquote class="soype-card__text">
              <?php
                // Allow basic HTML as configured in Customizer (wp_kses_post)
                // Wrap plain text in <p> if desired via CSS later.
                echo wp_kses_post($t_text);
              ?>
            </blockquote>
          <?php endif; ?>
        </div>

      </article>
      <?php endforeach; ?>
    </div>
  </div>

    <!-- Controls: centered dots between prev/next -->
  <div class="soype-carousel__controls" role="toolbar" aria-label="<?php echo esc_attr__('Controles del carrusel', 'soype'); ?>">
    <button class="soype-carousel-prev"
            type="button"
            aria-label="<?php echo esc_attr__('Anterior', 'soype'); ?>"
            aria-controls="<?php echo esc_attr($track); ?>">
      <span aria-hidden="true">‹</span>
    </button>

    <div class="soype-carousel__dots"
         role="tablist"
         aria-label="<?php echo esc_attr__('Paginación', 'soype'); ?>">
      <!-- JS will populate dots here -->
    </div>

    <button class="soype-carousel-next"
            type="button"
            aria-label="<?php echo esc_attr__('Siguiente', 'soype'); ?>"
            aria-controls="<?php echo esc_attr($track); ?>">
      <span aria-hidden="true">›</span>
    </button>
  </div>


  <?php /* Optional pagination dots container (hook with JS later)
  <div class="soype-carousel__dots" role="tablist" aria-label="<?php echo esc_attr__('Paginación', 'soype'); ?>"></div>
  */ ?>

  <?php /* Optional: noscript fallback to show as a simple list
  <noscript>
    <ul class="soype-carousel__list-fallback">
      <?php foreach ($items as $it): ?>
        <li>
          <?php if (!empty($it['title'])): ?>
            <strong><?php echo esc_html($it['title']); ?>:</strong>
          <?php endif; ?>
          <?php echo wp_kses_post($it['text']); ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </noscript>
  */ ?>

</div>
