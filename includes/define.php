<?php

define('WP_DEBUG', true);
define('JIBRES_VERSION', '1.1');
define('JIBRES_INC', JIBRES_DIR. 'includes/');



// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook(JIBRES_INC. 'activate', 'wp_jibres_activate');
register_deactivation_hook(JIBRES_INC. 'deactivate', 'wp_jibres_deactivate');


// load translations
$locale = apply_filters( 'plugin_locale', get_locale(), 'jibres' );
load_textdomain( 'jibres', trailingslashit( WP_LANG_DIR ) . 'jibres' . '/' . 'wp_jibres' . '-' . $locale . '.mo' );
load_plugin_textdomain('jibres', false, basename(JIBRES_DIR).'/languages');


?>