<?php

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/json.php';

class CravelOpenAI
{
  static function get_options()
  {
    return CravelJson::get_json('config');
  }

  static function get_option($key)
  {
    $options = CravelJson::get_json('config');
    $value = $options[$key];
    return $value;
  }

  static function get_models()
  {
    $config = CravelJson::get_json('config');
    return $config['models'];
  }

  static function get_endpoint($model)
  {
    $models = CravelOpenAI::get_models();
    if (isset($models[$model])) {
      return $models[$model]['endpoint'];
    } else {
      return null;
    }
  }

  static function get_display_name($model)
  {
    $models = CravelOpenAI::get_models();
    if (isset($models[$model])) {
      return $models[$model]['displayName'];
    } else {
      return null;
    }
  }

  static function get_endpoint_uri($model)
  {
    $models = CravelOpenAI::get_models();
    $base_url = CravelOpenAI::get_option('baseUrl');
    $endpoint = CravelOpenAI::get_endpoint($model);
    $uri = $base_url . $endpoint;
    return $uri;
  }

  static function get_current_model()
  {
    $model = CravelChatGptAutoPostAdmin::get_option('openai_api_model');
    return $model;
  }

  static function get_current_endpoint_uri()
  {
    $model = CravelChatGptAutoPostAdmin::get_option('openai_api_model');
    $uri = CravelOpenAI::get_endpoint_uri($model);
    return $uri;
  }
}
