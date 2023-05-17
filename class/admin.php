<?php

/**
 * 管理画面
 *
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * 
 * @version 1.0.0
 */

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/view/view-admin.php';
require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/openai.php';

class CravelChatGptAutoPostAdmin
{
  static $instance = false;

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function __construct()
  {
    add_action('admin_menu', array($this, 'add_plugin_menu'));
    add_action('admin_init', array($this, 'register_plugin_settings'));

    register_setting(
      'cravel-chatgpt-autopost-options',
      CRAVEL_CHATGPT_AUTOPOST_OPTION,
      array($this, 'validate_options')
    );
  }

  public function add_plugin_menu()
  {
    if (empty($GLOBALS['admin_page_hooks']['cravel-chatgpt-autopost-settings'])) {
      add_menu_page(
        'Ghostwriter',
        'Ghostwriter',
        'administrator',
        'cravel-chatgpt-autopost-plugin',
        array($this, 'plugin_settings_html'),
        'dashicons-format-audio',
        1000,
      );
    }
  }

  function register_plugin_settings()
  {
    register_setting('cravel-chatgpt-autopost-options', 'cravel_chatgpt_autopost_option');
  }

  public function plugin_settings_html()
  {
    $html = CravelChatGptAutoPostAdminView::plugin_settings_page();
    echo $html;
  }

  function validate_options($input)
  {
    if (isset($input['openai_api_key']) && !empty($input['openai_api_key'])) {
      $input['openai_api_key'] = trim($input['openai_api_key']);
    } else {
      add_settings_error('cravel_chatgpt_autopost_option', 'missing_openai_api_key', 'ChatGPT API Keyが必要です。', 'error');
      $options = get_option('cravel_chatgpt_autopost_option');
      $input['openai_api_key'] = $options['openai_api_key'];
    }

    return $input;
  }

  static function get_option($key)
  {
    $options = get_option(CRAVEL_CHATGPT_AUTOPOST_OPTION);
    return $options[$key];
  }
}

$CravelPluginAdmin = CravelChatGptAutoPostAdmin::getInstance();
