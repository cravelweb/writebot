<?php

/**
 * 管理画面HTMLビューテンプレート
 *
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * 
 * @version 1.0.0
 */

namespace CravelPlugins\ChatGptAutoPost;


if (!defined('ABSPATH')) exit;

class CravelChatGptAutoPostAdminView
{
  /**
   * WordPress設定ページを出力
   */
  public static function plugin_settings_page()
  {
    ob_start();
?>
    <div class="wrap">
      <h2><?= CRAVEL_CHATGPT_AUTOPOST_PLUGIN_NAME ?><?php _e('settings', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN); ?></h2>
      <?php
      if (true == @$_GET['settings-updated']) : ?>
        <div id="settings_updated" class="updated notice is-dismissible">
          <p><strong><?php _e('Settings saved.'); ?></strong></p>
        </div>
      <?php endif; ?>

      <form method="post" action="options.php">
        <?php submit_button(); ?>
        <?php
        // 設定読み込み
        settings_fields('cravel-chatgpt-autopost-options');
        do_settings_sections('cravel-chatgpt-autopost-options');
        $options = get_option(CRAVEL_CHATGPT_AUTOPOST_OPTION);
        //var_dump($options);
        ?>
        <div class="nav-tab-wrapper">
          <div class="tab-area">
            <label class="nav-tab nav-tab-active" for="tab1"><?php _e('Settings', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN); ?></label>
            <label class="nav-tab" for="tab2"><?php _e('Info', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN); ?></label>
          </div>
          <div class="panel-area">
            <div id="panel1" class="tab-panel nav-tab-active">
              <?php
              echo self::ext_tab_content_settings($options);
              ?>
            </div>
            <div id="panel2" class="tab-panel">
              <?php
              echo self::ext_tab_content_info($options);
              ?>
            </div>
          </div>
        </div>
        <?php submit_button(); ?>
      </form>
    </div>
  <?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }

  public static function ext_tab_content_settings($options)
  {
    ob_start();
  ?>
    <div class="postbox metabox-holder">
      <h3 class="hndle"><?php _e('ChatGPT API', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN) ?></h3>
      <div class="inside">
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php _e('OpenAI API Key', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN) ?></th>
            <td>
              <div><label><input type="text" name="<?= CRAVEL_CHATGPT_AUTOPOST_OPTION ?>[openai_api_key]" value="<?= esc_attr(@$options['openai_api_key']) ?>" />
                </label></div>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('API Model', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN) ?></th>
            <td>
              <div><label>
                  <select name="<?= CRAVEL_CHATGPT_AUTOPOST_OPTION ?>[openai_api_model]">
                    <?php
                    $models = CravelOpenAI::get_models();
                    foreach ($models as $model_name => $model_details) {
                      echo '<option value="' . $model_name . '" ' . (@$options['openai_api_model'] == $model_name ? 'selected' : '') . '>' . $model_details['displayName'] . '</option>';
                    }
                    ?>
                  </select>
                </label></div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  <?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }

  public static function ext_tab_content_info($options)
  {
    ob_start();
  ?>
    <div class="postbox metabox-holder">
      <h3 class="hndle"><?php _e('Plugin information', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN) ?></h3>
      <div class="inside">
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php _e('Options', CRAVEL_CHATGPT_AUTOPOST_PLUGIN_DOMAIN) ?></th>
            <td>
              <textarea rows="10" cols="50" readonly style="width:100%;"><?= json_encode($options) ?></textarea>
            </td>
          </tr>
        </table>
      </div>
    </div>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }
}
