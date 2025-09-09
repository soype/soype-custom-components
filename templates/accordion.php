<?php
/**
 * Template: Accordion (FAQs)
 * Variables esperadas:
 * - string $class   Clases extra para el wrapper
 * - array  $faqs    Lista de FAQs: [ [question, answer, open], ... ]
 * - bool   $single  Si true, sólo un panel abierto a la vez (lo usa el JS via data-single)
 */
if ( ! defined('ABSPATH') ) exit;

// Aseguro tipos básicos
$class  = isset($class)  ? (string) $class : '';
$faqs   = isset($faqs)   && is_array($faqs) ? $faqs : [];
$single = ! empty($single);

if ( empty($faqs) ) {
    return; // Nada que renderizar
}

// ID base único para evitar colisiones en páginas con múltiples acordeones
$accordion_uid = uniqid('soype_acc_', false);
?>
<section class="soype-accordion <?php echo esc_attr($class); ?>" data-single="<?php echo $single ? 'true' : 'false'; ?>">
    <div class="soype-accordion__list">
        <?php foreach ($faqs as $index => $item):
            $q = isset($item['question']) ? (string) $item['question'] : '';
            $a = isset($item['answer'])   ? (string) $item['answer']   : '';
            $is_open = ! empty($item['open']);

            // IDs accesibles únicos por ítem
            $uid      = $accordion_uid . '_' . ($index + 1);
            $btn_id   = $uid . '_btn';
            $panel_id = $uid . '_panel';
        ?>
        <div class="soype-accordion__item<?php echo $is_open ? ' is-open' : ''; ?>">
            <h3 class="soype-accordion__heading">
                <button
                    id="<?php echo esc_attr($btn_id); ?>"
                    class="soype-accordion__button"
                    type="button"
                    aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr($panel_id); ?>"
                >
                    <span class="soype-accordion__button-text"><?php echo esc_html($q); ?></span>
                    <span class="soype-accordion__icon" aria-hidden="true"></span>
                </button>
            </h3>
            <div
                id="<?php echo esc_attr($panel_id); ?>"
                class="soype-accordion__panel"
                role="region"
                aria-labelledby="<?php echo esc_attr($btn_id); ?>"
                <?php echo $is_open ? '' : 'hidden'; ?>
            >
                <div class="soype-accordion__panel-inner">
                    <?php echo wp_kses_post($a); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
