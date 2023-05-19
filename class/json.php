<?php

/**
 * Writebot AI WordPress Plugin
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

namespace CravelPlugins\ChatGptAutoPost;

class CravelJson
{
  const TRANSIENT_KEY_PREFIX = 'cravel_json_';
  const EXPIRATION = 60 * 5;  // 5 minutes 

  static function load_json_from_file($name, $dir = 'json')
  {
    if (empty($name)) {
      return null;
    }
    $transient_key = self::TRANSIENT_KEY_PREFIX . $name;
    //$config = get_transient($transient_key);
    $config = false;

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

  static function load_json_from_url($url)
  {
    $transient_key = self::TRANSIENT_KEY_PREFIX . md5($url);
    $data = get_transient($transient_key);

    if ($data === false) {
      $response = wp_remote_get($url);

      if (is_wp_error($response)) {
        error_log('Failed to fetch data from url: ' . $url);
        return null;
      }

      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to parse json from url: ' . $url . ' error: ' . json_last_error_msg());
        return null;
      }

      set_transient($transient_key, $data, self::EXPIRATION);
    }

    return $data;
  }
}
