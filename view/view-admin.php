<?php

/**
 * Writebot AI WordPress Plugin
 * @author Cravel <cravel@crabelweb.com>
 * @link https://cravelweb.com
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

namespace CravelPlugins\ChatGptAutoPost;

if (!defined('ABSPATH')) exit;

class CravelChatGptAutoPostAdminView
{
  public static function plugin_settings_page()
  {
    ob_start();
?>
    <div class="wrap writebox-settings">
      <h2><?= CRAVEL_WRITEBOT_NAME ?> <?php _e('settings', CRAVEL_WRITEBOT_DOMAIN); ?></h2>
      <form method="post" action="options.php">
        <?php submit_button(); ?>
        <?php
        settings_fields('cravel-writebot-options');
        do_settings_sections('cravel-writebot-options');
        $options = get_option(CRAVEL_WRITEBOT_OPTION);
        //var_dump($options);
        ?>
        <div class="nav-tab-wrapper">
          <div class="tab-area">
            <label class="nav-tab nav-tab-active" for="tab1"><?php _e('Settings', CRAVEL_WRITEBOT_DOMAIN); ?></label>
            <label class="nav-tab" for="tab2"><?php _e('Info', CRAVEL_WRITEBOT_DOMAIN); ?></label>
          </div>
          <div class="panel-area">
            <div id="panel1" class="tab-panel nav-tab-active">
              <?php
              echo self::ext_tab_content_settings($options);
              ?>
            </div>
            <?php /*
            <div id="panel2" class="tab-panel">
              <?php
              echo self::ext_tab_content_info($options);
              ?>
            </div>
*/ ?>
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
      <div class="inside">
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php _e('OpenAI API Key', CRAVEL_WRITEBOT_DOMAIN) ?></th>
            <td>
              <div><label><input type="text" name="<?= CRAVEL_WRITEBOT_OPTION ?>[openai_api_key]" value="<?= esc_attr(@$options['openai_api_key']) ?>" />
                </label></div>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('API Model', CRAVEL_WRITEBOT_DOMAIN) ?></th>
            <td>
              <div><label>
                  <select name="<?= CRAVEL_WRITEBOT_OPTION ?>[openai_api_model]">
                    <?php
                    $models = CravelOpenAI::get_models();
                    if (empty($models)) {
                      echo '<option value="">' . __('Set [OpenAI API Key] first.', CRAVEL_WRITEBOT_DOMAIN) . '</option>';
                    } else {
                      foreach ($models as $model_name => $model_details) {
                        echo '<option value="' . $model_name . '" ' . (@$options['openai_api_model'] == $model_name ? 'selected' : '') . '>' . $model_details['displayName'] . '</option>';
                      }
                    }
                    ?>
                  </select>
                </label></div>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('Ghostwriter', CRAVEL_WRITEBOT_DOMAIN) ?></th>
            <td>
              <div>
                <?php
                $ghost_list = CravelGhosts::get_ghost_list();
                //var_dump($ghost_list);
                if (empty($ghost_list)) {
                  echo '<p>' . __('No ghostwriter found.', CRAVEL_WRITEBOT_DOMAIN) . '</p>';
                } else {
                  foreach ($ghost_list as $ghost) {
                    echo '<div class="radio-item">';
                    echo '<label><input type="radio" name="' . CRAVEL_WRITEBOT_OPTION . '[ghost]" value="' . $ghost['filename'] . '" ' . (@$options['ghost'] == $ghost['filename'] ? 'checked' : '') . ' />' . $ghost['name'];

                    echo '</label>';
                    echo '<p class="desctiption">' . $ghost['description'] . '<br>';
                    if (!empty($ghost['version'])) echo 'Ver.' . $ghost['version'];
                    if (!empty($ghost['author'])) {
                      echo ' by ' . (empty($ghost['url']) ? $ghost['author'] : '<a href="' . $ghost['url'] . '" target="_blank">' . $ghost['author'] . '</a>');
                    }
                    echo  '</p>';
                    echo '</div>';
                  }
                  echo '<div class="radio-item">';
                  echo '<label><input type="radio" name="' . CRAVEL_WRITEBOT_OPTION . '[ghost]" value="_url" ' . (@$options['ghost'] == '_url' ? 'checked' : '') . ' />' . __('インターネットから取得', CRAVEL_WRITEBOT_DOMAIN) . '</label>';
                  echo '<input type="text" name="' . CRAVEL_WRITEBOT_OPTION . '[ghost_url]" value="' . esc_attr(@$options['ghost_url']) . '" style="width:100%;" placeholder="' . __('https:// ... ゴーストライター設定ファイルのURL (*.json)', CRAVEL_WRITEBOT_DOMAIN) . '" />';
                }
                ?>
              </div>
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
      <h3 class="hndle"><?php _e('Plugin information', CRAVEL_WRITEBOT_DOMAIN) ?></h3>
      <div class="inside">
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php _e('Options', CRAVEL_WRITEBOT_DOMAIN) ?></th>
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
