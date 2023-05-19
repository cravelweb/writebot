<?php
 /**
 * Writebot AI WordPress Plugin
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
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
    register_setting(
      'cravel-writebot-options',
      CRAVEL_WRITEBOT_OPTION,
      array($this, 'validate_options')
    );
  }

  public function add_plugin_menu()
  {
    if (empty($GLOBALS['admin_page_hooks']['cravel-writebot-settings'])) {
      add_submenu_page(
        'options-general.php',
        CRAVEL_WRITEBOT_NAME_LOCAL ." ". __('settings', CRAVEL_WRITEBOT_DOMAIN),
        CRAVEL_WRITEBOT_NAME_LOCAL ." ". __('settings', CRAVEL_WRITEBOT_DOMAIN),
        'administrator',
        'cravel-writebot-plugin',
        array($this, 'plugin_settings_html')
      );
    }
  }

  public function plugin_settings_html()
  {
    $html = CravelChatGptAutoPostAdminView::plugin_settings_page();
    echo $html;
  }

  function validate_options($input)
  {
    if (isset($input['openai_api_key'])) {
      $input['openai_api_key'] = trim($input['openai_api_key']);
    } else {
      add_settings_error(CRAVEL_WRITEBOT_OPTION, 'missing_openai_api_key', __('You Need ChatGPT API Key.', CRAVEL_WRITEBOT_DOMAIN), 'error');
      $options = get_option(CRAVEL_WRITEBOT_OPTION);
      $input['openai_api_key'] = $options['openai_api_key'];
    }

    return $input;
  }

  static function get_option($key)
  {
    $options = get_option(CRAVEL_WRITEBOT_OPTION);
    if (!is_array($options) || !array_key_exists($key, $options)) {
      error_log("Key $key does not exist in options.");
      return null;
    }
    return $options[$key];
  }
}

$CravelPluginAdmin = CravelChatGptAutoPostAdmin::getInstance();