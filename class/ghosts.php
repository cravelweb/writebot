<?php

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/json.php';

class CravelGhosts
{
  static function get_styles()
  {
    $json = CravelJson::get_json('ghost');
    return $json['styles'];
  }

  static function get_types()
  {
    $json = CravelJson::get_json('ghost');
    return $json['types'];
  }

  static function get_markups()
  {
    $json = CravelJson::get_json('ghost');
    return $json['markups'];
  }

  static function get_ghosts()
  {
    $json = CravelJson::get_json('ghost');
    return $json;
  }
  static function get_current_ghost()
  {
    $ghost = CravelChatGptAutoPostAdmin::get_option('ghost');
    return $ghost;
  }
}

