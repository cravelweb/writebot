<?php

/**
 * ChatGPT AutoPost
 *
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * 
 * @version 1.0.0
 */

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/openai.php';

class CravelChatGptAutoPostMain
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
  }

  private function get_options()
  {
    $options = get_option(CRAVEL_CHATGPT_AUTOPOST_OPTION);
    return $options;
  }

  public function get_option($key)
  {
    $options = $this->get_options();
    return $options[$key];
  }
}

$CravelChatGptAutoPostMain = CravelChatGptAutoPostMain::getInstance();
