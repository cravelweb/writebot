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

require_once CRAVEL_WRITEBOT_DIR . '/view/view-admin.php';
require_once CRAVEL_WRITEBOT_DIR . '/class/openai.php';

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
      'cravel-writebot-options',
      cravel_writebot_option,
      array($this, 'validate_options')
    );
  }




  public function add_plugin_menu()
  {
    if (empty($GLOBALS['admin_page_hooks']['cravel-writebot-settings'])) {
      add_menu_page(
        CRAVEL_WRITEBOT_NAME . __('settings', CRAVEL_WRITEBOT_DOMAIN),
        CRAVEL_WRITEBOT_NAME . __('settings', CRAVEL_WRITEBOT_DOMAIN),
        'administrator',
        'cravel-writebot-plugin',
        array($this, 'plugin_settings_html'),
        'dashicons-format-audio',
        1000,
      );
    }
  }

  function register_plugin_settings()
  {
    register_setting('cravel-writebot-options', CRAVEL_WRITEBOT_DOMAIN);
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
      add_settings_error('cravel_writebot_option', 'missing_openai_api_key', __('You Need ChatGPT API Key.', CRAVEL_WRITEBOT_DOMAIN), 'error');
      $options = get_option('cravel_writebot_option');
      $input['openai_api_key'] = $options['openai_api_key'];
    }

    return $input;
  }

  static function get_option($key)
  {
    $options = get_option(cravel_writebot_option);
    return $options[$key];
  }
}

$CravelPluginAdmin = CravelChatGptAutoPostAdmin::getInstance();
