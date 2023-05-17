<?php
/*
  Plugin Name: Ghostwriter (ChatGPT AutoPost)
  Plugin URI : https://knowten.jp
  Description: ChatGPT AutoPost
  Version: 1.0.0
  Author: Cravel
  Author URI: https://cravelweb.com
 */

namespace CravelPlugins\ChatGptAutoPost;


if (!defined('ABSPATH')) exit;

define('CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR', __DIR__);
define('CRAVEL_CHATGPT_AUTOPOST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CRAVEL_CHATGPT_AUTOPOST_PLUGIN_NAME', 'Ghostwriter');

// オプション名設定
define('CRAVEL_CHATGPT_AUTOPOST_OPTION', 'cravel_chatgpt_autopost_option');


require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/admin.php';
require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/autopost.php';
require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/writing.php';
