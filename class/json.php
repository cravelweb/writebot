<?php

namespace CravelPlugins\ChatGptAutoPost;

class CravelJson
{
  const TRANSIENT_KEY_PREFIX = 'cravel_json_';
  const EXPIRATION = 60 * 5;  // 5 minutes 

  static function load_json($name, $dir = 'json')
  {
    if (empty($name)) {
      return null;
    }
    $transient_key = self::TRANSIENT_KEY_PREFIX . $name;
    $config = get_transient($transient_key);
    //$config = false;

    if ($config === false) {
      $json = file_get_contents(CRAVEL_WRITEBOT_DIR . '/' . $dir . '/' . $name . '.json');
      $config = json_decode($json, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to parse config.json: ' . json_last_error_msg());
        return null;
      }
      set_transient($transient_key, $config, self::EXPIRATION);
    }
    return $config;
  }

  static function get_json($name, $dir = 'json')
  {
    return self::load_json($name, $dir);
  }

  static function get_json_file($file)
  {
    $json = file_get_contents($file);
    $config = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      error_log('Failed to parse config.json: ' . json_last_error_msg());
      return null;
    }
    return $config;
  }

  static function get_json_value($name, $key)
  {
    $json = self::get_json($name);
    return isset($json[$key]) ? $json[$key] : null;
  }
}
