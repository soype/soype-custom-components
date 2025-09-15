<?php
/**
 * Plugin Name: SoyPe Custom components
 * Description: Colection of small custom components.
 * Version: 1.0.0
 * Author: soype
 * Text Domain: https://soype.ar
 */

if ( ! defined('ABSPATH') ) exit;

define('SOYPECC_PATH', plugin_dir_path(__FILE__));
define('SOYPECC_URL',  plugin_dir_url(__FILE__));

/**
 * ========== Loader ==========
 */
require_once SOYPECC_PATH . 'components/hero.php';
require_once SOYPECC_PATH . 'components/twocolumnblock.php';
require_once SOYPECC_PATH . 'components/slider.php';
require_once SOYPECC_PATH . 'components/richtext.php';
require_once SOYPECC_PATH . 'components/carousel.php';
require_once SOYPECC_PATH . 'components/accordion.php';
require_once SOYPECC_PATH . 'components/whatsapp.php';