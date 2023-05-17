<?php

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_WRITEBOT_DIR . '/class/json.php';

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

    $enable_models = CravelOpenAI::get_enable_models();
    $models = array();
    foreach ($config['models'] as $model_id => $model_detail) {
      if (in_array($model_id, $enable_models)) {
        $models[$model_id] = $model_detail;
      }
    }

    return $models;
  }

  static function get_enable_models()
  {
    $api_key = CravelChatGptAutoPostAdmin::get_option('openai_api_key');
    $options = [
      'http' => [
        'method' => 'GET',
        'header' => [
          'Content-Type: application/json',
          'Authorization: Bearer ' . $api_key
        ]
      ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents('https://api.openai.com/v1/models', false, $context);
    $responseData = json_decode($response, true);

    $models = array();
    // id を配列に追加
    foreach ($responseData['data'] as $model) {
      $models[] = $model['id'];
    }
    return $models;
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
