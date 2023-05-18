<?php
/*
  Plugin Name: WriteBot AI (ChatGPT Text Generator)
  Description: Use ChatGPT to create articles automatically.
  Version: 0.5.1
  Author: Cravel
  Author URI: https://cravelweb.com
 */

namespace CravelPlugins\ChatGptAutoPost;


if (!defined('ABSPATH')) exit;

define('CRAVEL_WRITEBOT_DIR', __DIR__);
define('CRAVEL_WRITEBOT_URL', plugin_dir_url(__FILE__));
define('CRAVEL_WRITEBOT_NAME', 'WriteBot AI');
define('CRAVEL_WRITEBOT_DOMAIN', 'cravel-writebot');
define('CRAVEL_WRITEBOT_NAME_LOCAL', __('WriteBot AI', CRAVEL_WRITEBOT_DOMAIN));
define('CRAVEL_WRITEBOT_OPTION', 'cravel_writebot_option');

require_once CRAVEL_WRITEBOT_DIR . '/class/admin.php';
require_once CRAVEL_WRITEBOT_DIR . '/class/writing.php';

add_action('plugins_loaded', 'CravelPlugins\ChatGptAutoPost\load_textdomain');
function load_textdomain()
{
  load_plugin_textdomain(CRAVEL_WRITEBOT_DOMAIN, FALSE, basename(dirname(__FILE__)) . '/languages/');
}
