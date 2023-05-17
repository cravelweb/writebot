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
require_once CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DIR . '/class/ghosts.php';


class CravelChatGptAutoPostWriting
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
    add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
    add_action('save_post', array($this, 'save_custom_meta_data'));
    //add_action('save_post', array($this, 'generate_and_save_post_content'));
    add_action('admin_notices', array($this, 'display_gpt_error_message'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_resources'));
    add_action('wp_ajax_generate_content', array($this, 'handle_ajax_generate_content'));
  }

  function enqueue_custom_resources()
  {
    wp_enqueue_script('cravel_chatgpt_autopost_script', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_URL . 'js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('cravel_chatgpt_autopost_style', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_URL . 'css/style.css');
    wp_localize_script('cravel_chatgpt_autopost_script', 'CravelChatGptAutopostAjax', array(
      'ajaxurl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('cravel_chatgpt_autopost_nonce'),
    ));
  }

  function handle_ajax_generate_content()
  {
    check_ajax_referer('cravel_chatgpt_autopost_nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
    $keywords = isset($_POST['keywords']) ? sanitize_text_field($_POST['keywords']) : '';

    update_post_meta($post_id, '_prompt', $prompt);
    update_post_meta($post_id, '_keywords', $keywords);

    $content = $this->generate_content($prompt . "\n" . $keywords);

    if (!empty($content)) {
      update_post_meta($post_id, '_generated_content', $content);
      wp_send_json_success($content);
    } else {
      wp_send_json_error('文書生成に失敗しました');
    }
    wp_die();
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

  function add_custom_meta_box()
  {
    add_meta_box(
      'custom_meta_box',
      'Ghostwriter',
      array($this, 'display_custom_meta_box'),
      'post',
      'normal',
      'high'
    );
  }

  function display_custom_meta_box($post)
  {
    //echo CravelOpenAI::get_current_model();
    //echo CravelOpenAI::get_current_endpoint_uri();

    $ghosts = CravelGhosts::get_ghosts();
    $ghost_writer_values = array();
    foreach ($ghosts as $ghost_name => $ghost_details) {
      $ghost_writer_values[$ghost_name] = get_post_meta($post->ID, '_' . $ghost_name, true);
    }
    $selected_language = $this->get_selected_language($post->ID);
    echo '<div id="ghost-writer-settings" class="ghost-writer-settings">';
    echo '<h3>ゴーストライター設定</h3>';
    echo '<div>' . $this->get_ghosts_html($ghost_writer_values, $selected_language) . '</div>';

    $theme = get_post_meta($post->ID, '_post_theme', true);
    echo '<h4>執筆の指示・テーマの指定</h4>';
    echo '<p class="description" id="theme-description"></p>';
    echo '<textarea name="post_theme" rows="5" style="width:100%;">' . esc_html($theme) . '</textarea>';

    $keywords = get_post_meta($post->ID, '_post_keywords', true);
    echo '<h4>キーワード（カンマ区切り）</h4>';
    echo '<textarea name="post_keywords" rows="2" style="width:100%;">' . esc_html($keywords) . '</textarea>';
    echo '<p class="description" id="keyword-description">キーワードを入力すると、それらのキーワードを重視した内容で記事が生成されます。テーマの核として触れてほしい内容や、SEOの重点キーワードなどを入力してください。</p>';

    echo '<button id="generate_content" class="button button-primary">上記の条件で生成する</button>';
    echo '<div id="openai-api-status"></div><div class="spinner"></div>';
    echo '</div>';

    $generated_content = get_post_meta($post->ID, '_generated_content', true);
    echo '<h3>生成されたテキスト</h3>';
    echo '<textarea name="generated_content" rows="10" style="width:100%;">' . esc_html($generated_content) . '</textarea>';
    echo '<p class="description">上記に生成された文章が格納されます。投稿本文として公開する場合は上記の内容をコピーして投稿本文に貼り付けて使用してください。</p>';

    echo '</div>';

    wp_nonce_field(basename(__FILE__), 'gpt_nonce');
  }

  function get_ghosts_html($selected_ghosts = array(), $selected_language = 'ja')
  {
    $html = "";
    $ghosts = CravelGhosts::get_ghosts();
    $html .= '<dl>';
    foreach ($ghosts as $ghost_name => $ghost_details) {
      $html .= '<dt>' . $ghost_details['name'] . '</dt>';
      $html .= '<dd>';
      $html .= '<select class="ghost" name="' . $ghost_name . '">';
      $html .= '<option value="">指定しない</option>';
      foreach ($ghost_details['items'] as $option_name => $option_details) {
        $selected = ($option_name == $selected_ghosts[$ghost_name]) ? 'selected' : '';
        $html .= '<option value="' . esc_attr($option_name) . '" ' . $selected . '>' . esc_html($option_details['name']) . '</option>';
      }
      $html .= '</select>';
      $html .= '</dd>';
    }
    //$html .=  '<dt>記述する言語</dt>';
    //$html .=  '<dd>' . $this->get_languages_html($selected_language) . '</dd>';

    $user_prompt = get_option('user_prompt', '');
    $html .= '<dt>サイト共通プロンプト</dt>';
    $html .= '<dd>';
    $html .= '<textarea name="user_prompt" rows="3" style="width:100%;">' . esc_html($user_prompt) . '</textarea>';
    $html .= '<p class="description">生成する文章を調整するための追加プロンプトを入力してください。この項目はWordPressサイト全体の文体や書式を整えるためのサイト共通の設定項目のため、内容を変更すると他の投稿にも反映されます。</p>';
    $html .= '<dd>';
    $html .= '</dl>';
    return $html;
  }

  function get_languages_html($selected_language = null)
  {
    $html = "";
    $languages = get_available_languages();
    array_unshift($languages, 'en_US');
    $current_locale = get_locale();
    $html .= '<select name="selected_language">';
    foreach ($languages as $language) {
      $selected = ($language == $selected_language) ? 'selected' : '';
      $display_language = locale_get_display_language($language, $language);
      $html .=  '<option value="' . esc_attr($language) . '" ' . $selected . '>' . esc_html($display_language) . '</option>';
    }
    $html .=  '</select>';
    return $html;
  }

  function get_selected_ghost($post_id)
  {
    return get_post_meta($post_id, '_selected_ghost', true);
  }

  function get_selected_language($post_id)
  {
    return get_post_meta($post_id, '_selected_language', true);
  }

  function save_custom_meta_data($post_id)
  {
    if (!$this->can_save_post($post_id)) return;

    $new_value = isset($_POST['user_prompt']) ? $_POST['user_prompt'] : '';
    update_option('user_prompt', $new_value);

    $this->update_meta($post_id, 'post_theme');
    $this->update_meta($post_id, 'generated_content');
    $this->update_meta($post_id, 'post_keywords');
    $this->update_meta($post_id, 'selected_language');
    $ghosts = CravelGhosts::get_ghosts();
    foreach ($ghosts as $ghost_name => $ghost_details) {
      $this->update_meta($post_id, $ghost_name);
    }
  }



  function update_meta($post_id, $field_name)
  {
    if ($field_name == 'user_prompt') return;
    $old_value = get_post_meta($post_id, '_' . $field_name, true);
    $new_value = isset($_POST[$field_name]) ? $_POST[$field_name] : '';
    if ($new_value === "") {
      if ($old_value) {
        update_post_meta($post_id, '_' . $field_name, $new_value);
      }
    } else if ($new_value && $new_value !== $old_value) {
      update_post_meta($post_id, '_' . $field_name, $new_value);
    } else if (!$new_value && $old_value) {
      delete_post_meta($post_id, '_' . $field_name, $old_value);
    }
  }




  function generate_and_save_post_content($post_id)
  {
    if (!$this->can_save_post($post_id)) return;
    remove_action('save_post', array($this, 'generate_and_save_post_content'));
    $metadata = get_post_meta($post_id, '_metadata', true);
    $content = $this->generate_content($metadata);
    $post = array(
      'ID'           => $post_id,
      'post_content' => $content,
    );
    wp_update_post($post);

    add_action('save_post', array($this, 'generate_and_save_post_content'));
  }

  function sanitize_metadata($metadata)
  {
    $sanitized_metadata = sanitize_text_field($metadata);
    return $sanitized_metadata;
  }

  function can_save_post($post_id)
  {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    if (!current_user_can('edit_post', $post_id))  return false;
    if (!isset($_POST['gpt_nonce'])) return false;
    if (!wp_verify_nonce($_POST['gpt_nonce'], basename(__FILE__))) return false;
    return true;
  }

  function generate_content($prompt)
  {
    update_option('gpt_error_message', "");
    $openai_api_key = $this->get_option('openai_api_key');
    if (!$openai_api_key) {
      error_log("OpenAI API key is not set.");
      update_option('gpt_error_message', "OpenAI API key is not set.");
      return "";
    }

    $endpoint_uri = CravelOpenAI::get_current_endpoint_uri();
    $ch = curl_init($endpoint_uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    if (strpos($endpoint_uri, '/chat') !== false) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
        'model' => CravelOpenAI::get_current_model(),
        'messages' => [
          [
            "role" => "user",
            "content" => $prompt
          ]
        ],
        'max_tokens' => 3000,
        'temperature' => 0.9,
        //'stream' => true,
        'stop' => ['\n', 'Human:', 'AI:'],
      )));
    } else {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
        'model' => CravelOpenAI::get_current_model(),
        'prompt' => $prompt,
        'max_tokens' => 1500,
      )));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $openai_api_key,
    ));

    $response = curl_exec($ch);
    error_log('OpenAI API response: ' . $response);

    if (curl_errno($ch)) {
      $error_msg = curl_error($ch);
      error_log("OpenAI API request failed: " . $error_msg);
      update_option('gpt_error_message', "OpenAI API request failed: " . $error_msg);
      return "";
    }

    $response_data = json_decode($response, true);

    if (strpos($endpoint_uri, '/chat') !== false) {
      if (!isset($response_data['choices'][0]['message']['content'])) {
        error_log("Invalid response from OpenAI API");
        update_option('gpt_error_message', "Invalid response from OpenAI API");
        return "";
      }
      update_option('gpt_error_message', "");
      return $response_data['choices'][0]['message']['content'];
    } else {
      if (!isset($response_data['choices'][0]['text'])) {
        error_log("Invalid response from OpenAI API");
        update_option('gpt_error_message', "Invalid response from OpenAI API");
        return "";
      }
      update_option('gpt_error_message', "");
      return $response_data['choices'][0]['text'];
    }
  }

  function display_gpt_error_message()
  {
    $message = get_option('gpt_error_message', "");
    if (!empty($message)) {
      echo "<div class='notice notice-error is-dismissible'><p>$message</p></div>";
    }
  }
}

$CravelChatGptAutoPostWriting = CravelChatGptAutoPostWriting::getInstance();
