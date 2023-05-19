<?php

/**
 * Writebot AI WordPress Plugin
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

require_once CRAVEL_WRITEBOT_DIR . '/class/json.php';

class CravelGhosts
{
  static function load_ghost($ghost_filename = 'ghost')
  {
    if ($ghost_filename === '_url') {
      $ghost_url = CravelChatGptAutoPostAdmin::get_option('ghost_url');
      $json_data = CravelJson::load_json_from_url($ghost_url);
    } else {
      $json_data = CravelJson::load_json_from_file($ghost_filename, 'ghosts');
    }
    if (self::is_json_valid($json_data)) {
      return $json_data;
    }
    return null;
  }

  static function get_ghost_files()
  {
    $json_files = glob(CRAVEL_WRITEBOT_DIR . '/ghosts/*.json');
    $ghost_file_list = [];
    foreach ($json_files as $json_file) {
      $ghost_file_name = pathinfo($json_file, PATHINFO_FILENAME);
      $json = CravelJson::load_json_from_file($ghost_file_name, $dir = 'ghosts');
      if (self::is_json_valid($json)) {
        $ghost_file_list[] = $ghost_file_name;
      }
    }
    return $ghost_file_list;
  }

  static function get_ghost_info($ghost_file_name, $key)
  {
    $ghost_data = self::load_ghost($ghost_file_name);
    return $ghost_data[$key] ?? null;
  }

  static function get_ghost_list()
  {
    $ghost_files = self::get_ghost_files();
    //var_dump($ghost_files);
    $ghost_list = [];
    foreach ($ghost_files as $ghost_file_name) {
      $ghost_list[] = [
        'name'        => self::get_ghost_info($ghost_file_name, 'name'),
        'description' => self::get_ghost_info($ghost_file_name, 'description'),
        'version'     => self::get_ghost_info($ghost_file_name, 'version'),
        'filename'    => $ghost_file_name,
        'author'      => self::get_ghost_info($ghost_file_name, 'author'),
        'url'         => self::get_ghost_info($ghost_file_name, 'url'),
      ];
    }
    return $ghost_list;
  }

  static function get_current_ghost_name()
  {
    $ghost_file_name = CravelChatGptAutoPostAdmin::get_option('ghost');
    if (empty($ghost_file_name)) {
      $ghost_file_name = 'ghost';
    }
    return $ghost_file_name;
  }

  static function get_current_ghost_url()
  {
    $ghost_file_name = CravelChatGptAutoPostAdmin::get_option('ghost');
    if (empty($ghost_file_name)) {
      $ghost_file_name = 'ghost';
    }
    if ($ghost_file_name === '_url') {
      $ghost_file_url = CravelChatGptAutoPostAdmin::get_option('ghost_url');
    } else {
      $ghost_file_url = CRAVEL_WRITEBOT_URL . 'ghosts/' . $ghost_file_name . '.json';
    }
    return $ghost_file_url;
  }

  static function get_current_ghost()
  {
    $ghost_data = self::load_ghost(self::get_current_ghost_name());
    return $ghost_data['ghost'] ?? null;
  }

  static function is_json_valid($json)
  {
    if (!(isset($json['name']) && isset($json['description']) && isset($json['ghost']))) {
      return false;
    }
    /*
    if (!(isset($json['ghost']['persona']) && isset($json['ghost']['types']) && isset($json['ghost']['length']) && isset($json['ghost']['markups']))) {
      return false;
    }

    foreach (['persona', 'types', 'length', 'markups'] as $section) {
      if (!isset($json['ghost'][$section]['name']) || !isset($json['ghost'][$section]['items'])) {
        return false;
      }

      foreach ($json['ghost'][$section]['items'] as $item) {
        if (!(isset($item['name']) && isset($item['prompt']))) {
          return false;
        }
      }
    }
    */
    return true;
  }
}
