<?php
/**
 * Plugin Name: soype-custom-components
 * Description: Colección de componentes agnósticos (p.ej. slider) editables desde el Personalizador. Incluye shortcode y opciones de inyección.
 * Version: 1.0.0
 * Author: soype
 * Text Domain: soype
 */

if ( ! defined('ABSPATH') ) exit;

define('SOYPECC_PATH', plugin_dir_path(__FILE__));
define('SOYPECC_URL',  plugin_dir_url(__FILE__));

/**
 * ========== Loader de componentes ==========
 */
require_once SOYPECC_PATH . 'components/slider.php';