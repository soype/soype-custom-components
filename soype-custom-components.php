<?php
/**
 * Plugin Name: SoyPe Custom components
 * Description: Colection of small custom components.
 * Version: 1.0.0
 * Author: soype
 * Text Domain: soype-custom-components
 */

if ( ! defined('ABSPATH') ) exit;

// Useful constants early
define('SOYPECC_PATH', plugin_dir_path(__FILE__));
define('SOYPECC_URL',  plugin_dir_url(__FILE__));
if ( ! defined('SOYPECC_VERSION') ) {
  define('SOYPECC_VERSION', '1.0.0');
}

if ( ! defined('SOYPECC_LICENSE_ENDPOINT') ) {
  define('SOYPECC_LICENSE_ENDPOINT', 'https://plugins.soype.ar/verify.php'); 
}
if ( ! defined('SOYPECC_PLUGIN_SLUG') ) {
  define('SOYPECC_PLUGIN_SLUG', 'soype-custom-components');
}
// MUST MATCH the server SECRET (client-side copy). Goal: authenticity / tamper-evidence.
if ( ! defined('SOYPECC_CLIENT_SHARED_SECRET') ) {
  define('SOYPECC_CLIENT_SHARED_SECRET', 'change-this-to-a-long-random-secret-rotate-often');
}

// ===== Internals =====
/** Perform remote verification and return array|WP_Error */
function soypecc_remote_verify_call() {
  $body = [
    'site'    => home_url(),
    'plugin'  => SOYPECC_PLUGIN_SLUG,
    'version' => defined('SOYPECC_VERSION') ? SOYPECC_VERSION : 'dev',
  ];

  $res = wp_remote_post( SOYPECC_LICENSE_ENDPOINT, [
    'timeout' => 8,
    'body'    => $body,
  ]);
  if ( is_wp_error($res) ) return $res;

  $code = wp_remote_retrieve_response_code($res);
  $json = json_decode( wp_remote_retrieve_body($res), true );

  if ( $code !== 200 || ! is_array($json) ) {
    return new WP_Error('soypecc_http', 'Invalid response from license server');
  }
  return $json;
}

/** Verify server HMAC signature */
function soypecc_verify_signature( $payload ) {
  $site   = isset($payload['site'])   ? (string)$payload['site']   : '';
  $ts     = isset($payload['ts'])     ? (string)$payload['ts']     : '';
  $plugin = isset($payload['plugin']) ? (string)$payload['plugin'] : '';
  $ok     = ! empty($payload['ok']) ? '1' : '0';
  $sig    = isset($payload['sig'])    ? (string)$payload['sig']    : '';

  if ( $site === '' || $ts === '' || $plugin === '' || $sig === '' ) return false;

  $expected = hash_hmac('sha256', $site . '|' . $ts . '|' . $plugin . '|' . $ok, SOYPECC_CLIENT_SHARED_SECRET);

  return hash_equals($expected, $sig);
}

function soypecc_is_authorized_remote_only() {
  $cache_key   = 'soypecc_license_ok';
  $last_ok_key = 'soypecc_license_last_ok';

  $cached = get_transient($cache_key);
  if ( $cached === '1' ) return true;

  $resp = soypecc_remote_verify_call();

  if ( is_wp_error($resp) ) {
    // Server unreachable → grace mode (24h since last OK)
    $last_ok = (int) get_option($last_ok_key, 0);
    if ( $last_ok && ( time() - $last_ok ) <= DAY_IN_SECONDS ) {
      return true; // within grace window
    }
    return false;
  }

  if ( soypecc_verify_signature($resp) && ! empty($resp['ok']) ) {
    set_transient($cache_key, '1', DAY_IN_SECONDS); 
    update_option($last_ok_key, time(), false);  
    return true;
  }
  
  $last_ok = (int) get_option($last_ok_key, 0);
  if ( $last_ok && ( time() - $last_ok ) <= DAY_IN_SECONDS ) {
    return true;
  }

  return false;
}

/** Admin notice (only for admins) */
function soypecc_admin_auth_notice_remote() {
  if ( current_user_can('manage_options') && ! soypecc_is_authorized_remote_only() ) {
    echo '<div class="notice notice-error"><p><strong>SoyPe Custom Components:</strong> este sitio no está autorizado por el servidor de licencias (<code>plugins.soype.ar</code>). Revisa la allowlist o el secreto compartido.</p></div>';
  }
}


function soypecc_load_components() {
  require_once SOYPECC_PATH . 'components/hero.php';
  require_once SOYPECC_PATH . 'components/twocolumnblock.php';
  require_once SOYPECC_PATH . 'components/slider.php';
  require_once SOYPECC_PATH . 'components/richtext.php';
  require_once SOYPECC_PATH . 'components/carousel.php';
  require_once SOYPECC_PATH . 'components/accordion.php';
  require_once SOYPECC_PATH . 'components/whatsapp.php';
}

add_action('plugins_loaded', function(){
  if ( soypecc_is_authorized_remote_only() ) {
    soypecc_load_components();
  } else {
    add_action('admin_notices', 'soypecc_admin_auth_notice_remote');
  }
}, 1);

register_deactivation_hook(__FILE__, function(){
  delete_transient('soypecc_license_ok');
  delete_option('soypecc_license_last_ok');
});
