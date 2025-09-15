<?php
/**
 * Template: WhatsApp button
 * Receives: $class (string), $label (string), $href (string), $target (string)
 * Note: keep markup minimal; style via assets/whatsapp/whatsapp.css
 */
?>
<a class="soype-whatsapp-btn <?php echo esc_attr($class); ?>"
   href="<?php echo esc_url($href); ?>"
   target="<?php echo isset($target) ? esc_attr($target) : '_blank'; ?>"
   rel="noopener noreferrer"
   aria-label="<?php echo esc_attr($label); ?>">
  <svg class="soype-whatsapp-icon" viewBox="0 0 32 32" width="24" height="24" aria-hidden="true" focusable="false">
    <path fill="currentColor" d="M19.1 17.3c-.3-.2-1.8-.9-2.1-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7.1c-.3-.2-1.2-.5-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6s.3-.3.4-.5c.1-.2.2-.4.3-.6.1-.2 0-.4 0-.6s-.7-1.7-1-2.3c-.3-.5-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4s-1.2 1.2-1.2 2.9 1.3 3.3 1.5 3.5c.2.3 2.6 4 6.4 5.5.9.4 1.7.6 2.3.8 1 .3 1.9.3 2.6.2.8-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.1-.2-.1-.5-.3z"/>
    <path fill="currentColor" d="M27.7 4.3C24.8 1.5 21 0 16.9 0 8 0 .9 7.1.9 16c0 2.8.7 5.5 2.1 7.9L0 32l8.4-2.7c2.3 1.3 4.9 2 7.6 2h0c8.9 0 16-7.1 16-15.9 0-4.2-1.6-8-4.3-11.1zM16 28.7c-2.4 0-4.7-.6-6.7-1.8l-.5-.3-4.9 1.6 1.6-4.8-.3-.5c-1.3-2.1-2-4.6-2-7.1C3.5 8 9 2.7 16 2.7c3.5 0 6.7 1.4 9.1 3.7 2.4 2.4 3.7 5.6 3.7 9s-1.3 6.6-3.7 8.9c-2.5 2.5-5.7 3.7-9.1 3.7z"/>
  </svg>
</a>
