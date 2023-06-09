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

require_once CRAVEL_WRITEBOT_DIR . '/class/openai.php';
require_once CRAVEL_WRITEBOT_DIR . '/class/ghosts.php';


class CravelChatGptAutoPostWriting
{
  static $instance = false;

  static $ghost_name = 'ghost';

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
    add_action('admin_notices', array($this, 'display_gpt_error_message'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_resources'));
    add_action('wp_ajax_generate_content', array($this, 'handle_ajax_generate_content'));
  }

  function enqueue_custom_resources()
  {
    $script_handle = 'cravel_chatgpt_autopost_script';
    wp_enqueue_script($script_handle, CRAVEL_WRITEBOT_URL . 'js/script.js', array('jquery'), '1.0', true);
    wp_localize_script($script_handle, 'CravelChatGptAutopostAjax', array(
      'ajaxurl'  => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('cravel_chatgpt_autopost_nonce'),
      'ghostUrl' => CravelGhosts::get_current_ghost_url()
    ));
    $translation_array = array(
      'constraints' => __("Constraints", CRAVEL_WRITEBOT_DOMAIN),
      'theme'       => __("Theme", CRAVEL_WRITEBOT_DOMAIN),
      'keywords'    => __("Keywords", CRAVEL_WRITEBOT_DOMAIN),
      'output'      => __("Output", CRAVEL_WRITEBOT_DOMAIN),
      'generating'  => __("generating...", CRAVEL_WRITEBOT_DOMAIN),
      'generated'   => __("generated", CRAVEL_WRITEBOT_DOMAIN),
      'error'       => __("error", CRAVEL_WRITEBOT_DOMAIN)
    );
    wp_localize_script($script_handle, 'text_label', $translation_array);
    wp_enqueue_style('cravel_chatgpt_autopost_style', CRAVEL_WRITEBOT_URL . 'css/style.css');
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
      wp_send_json_error(__("Failed to generate content.", CRAVEL_WRITEBOT_DOMAIN));
    }
    wp_die();
  }

  private function get_options()
  {
    $options = get_option(CRAVEL_WRITEBOT_OPTION);
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
      CRAVEL_WRITEBOT_NAME,
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

    $ghost_data = CravelGhosts::get_current_ghost();
    if (empty($ghost_data)) {
      echo '<div class="error"><p>' . __('Please select a ghost from the settings page.', CRAVEL_WRITEBOT_DOMAIN) . '</p></div>';
      return;
    }

    $ghost_writer_values = array();
    foreach ($ghost_data as $ghost_item_name => $ghost_item_details) {
      $ghost_writer_values[$ghost_item_name] = get_post_meta($post->ID, '_' . $ghost_item_name, true);
    }
    $selected_language = $this->get_selected_language($post->ID);
    echo '<div id="ghost-writer-settings" class="ghost-writer-settings">';
    echo '<h3>' . CRAVEL_WRITEBOT_NAME . __('settings', CRAVEL_WRITEBOT_DOMAIN) . '</h3>';
    echo '<div>' . $this->get_ghosts_html($ghost_writer_values, $selected_language) . '</div>';

    $theme = get_post_meta($post->ID, '_post_theme', true);
    echo '<h4>' . __('Theme', CRAVEL_WRITEBOT_DOMAIN) . '</h4>';
    echo '<p class="description" id="theme-description"></p>';
    echo '<textarea name="post_theme" rows="5" style="width:100%;">' . esc_html($theme) . '</textarea>';

    $keywords = get_post_meta($post->ID, '_post_keywords', true);
    echo '<h4>' . __('Keywords', CRAVEL_WRITEBOT_DOMAIN) . '</h4>';
    echo '<textarea name="post_keywords" rows="2" style="width:100%;">' . esc_html($keywords) . '</textarea>';
    echo '<p class="description" id="keyword-description">' . __('When you input keywords, an article is generated with content that emphasizes those keywords. Please enter the key points you want to touch upon as the core of the theme, and any SEO-focused keywords.', CRAVEL_WRITEBOT_DOMAIN) . '</p>';

    echo '<button id="generate_content" class="button button-primary">' . __('Generate Content', CRAVEL_WRITEBOT_DOMAIN) . '</button>';
    echo '<div id="openai-api-status"></div><div class="spinner"></div>';
    echo '</div>';

    $generated_content = get_post_meta($post->ID, '_generated_content', true);
    echo '<h3>' . __('Generated Content', CRAVEL_WRITEBOT_DOMAIN) . '</h3>';
    echo '<textarea name="generated_content" rows="10" style="width:100%;">' . esc_html($generated_content) . '</textarea>';
    echo '<p class="description">' . __('This text is generated by AI. Please copy and paste the above content into the post body if you wish to publish it as the content of the post.', CRAVEL_WRITEBOT_DOMAIN) . '</p>';
    echo '</div>';

    wp_nonce_field(basename(__FILE__), 'gpt_nonce');
  }

  function get_ghosts_html($selected_ghosts = array(), $selected_language = 'ja')
  {
    $html = "";
    $ghost_data = CravelGhosts::get_current_ghost();
    if (empty($ghost_data)) {
      return $html;
    }

    $html .= '<dl>';
    foreach ($ghost_data as $ghost_name => $ghost_details) {
      $html .= '<dt>' . $ghost_details['name'] . '</dt>';
      $html .= '<dd>';
      $html .= '<select class="ghost" name="' . $ghost_name . '">';
      $html .= '<option value="">' . __('Select', CRAVEL_WRITEBOT_DOMAIN) . '</option>';
      foreach ($ghost_details['items'] as $option_name => $option_details) {
        $selected = ($option_name == $selected_ghosts[$ghost_name]) ? 'selected' : '';
        $html .= '<option value="' . esc_attr($option_name) . '" ' . $selected . '>' . esc_html($option_details['name']) . '</option>';
      }
      $html .= '</select>';
      $html .= '</dd>';
    }
    //$html .=  '<dt>' . __('Language', CRAVEL_WRITEBOT_DOMAIN) . '</dt>';
    //$html .=  '<dd>' . $this->get_languages_html($selected_language) . '</dd>';

    $user_prompt = get_option('user_prompt', '');
    $html .= '<dt>' . __('User Prompt', CRAVEL_WRITEBOT_DOMAIN) . '</dt>';
    $html .= '<dd>';
    $html .= '<textarea name="user_prompt" rows="3" style="width:100%;">' . esc_html($user_prompt) . '</textarea>';
    $html .= '<p class="description">' . __('Enter the text that will be used as the starting point for the AI to generate the content.', CRAVEL_WRITEBOT_DOMAIN) . '</p>';
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
    $ghosts = CravelGhosts::get_current_ghost();
    if (!empty($ghosts)) {
      foreach ($ghosts as $ghost_name => $ghost_details) {
        $this->update_meta($post_id, $ghost_name);
      }
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

  function set_error($message)
  {
    error_log($message);
    update_option('gpt_error_message', $message);
    wp_send_json_error(__($message, CRAVEL_WRITEBOT_DOMAIN));
  }

  function generate_content($prompt)
  {
    update_option('gpt_error_message', "");
    $openai_api_key = $this->get_option('openai_api_key');
    if (!$openai_api_key) {
      $this->set_error("OpenAI API key is not set.");
    }

    $endpoint_uri = CravelOpenAI::get_current_endpoint_uri();

    $payload = strpos($endpoint_uri, '/chat') !== false ? $this->generate_chat_payload($prompt) : $this->generate_prompt_payload($prompt);

    $ch = curl_init($endpoint_uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $openai_api_key,
    ));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $this->set_error("OpenAI API request failed: " . curl_error($ch));
    }

    $response_data = json_decode($response, true);
    $field = strpos($endpoint_uri, '/chat') !== false ? 'message' : 'text';

    if (!isset($response_data['choices'][0][$field])) {
      if (isset($response_data['error'])) {
        $errormsg = $response_data['error']['message'];
      } else {
        $errormsg = __("Invalid response from OpenAI API", CRAVEL_WRITEBOT_DOMAIN);
      }
      $this->set_error($errormsg);
      wp_die();
    }
    update_option('gpt_error_message', "");

    if ($field == 'message') {
      $response_item =  $response_data['choices'][0][$field]['content'];
    } else {
      $response_item =  $response_data['choices'][0][$field];
    }
    return $response_item;
  }



  private function generate_chat_payload($prompt)
  {
    return array(
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
    );
  }

  private function generate_prompt_payload($prompt)
  {
    return array(
      'model' => CravelOpenAI::get_current_model(),
      'prompt' => $prompt,
      'max_tokens' => 1500,
    );
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
