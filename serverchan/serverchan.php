<?php
// require_once __DIR__ .'/helper.php';
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://dpii.club/wecomchan-wordpress-notice
 * @since             1.0.2
 * @package           WeComchan_Wordpress_notice
 *
 * @wordpress-plugin
 * Plugin Name:       WeComchan通知
 * Plugin URI:        https://dpii.club/wecomchan-wordpress-notice
 * Description:       将WordPress通知推送到微信（目前支持：WeComchan-GO云函数）感谢：方糖-easychen site：http://ftqq.com/
 * Version:           1.0.2
 * Author:            DefiedParty
 * Author URI:        https://dpii.club/
 * Text Domain:       wecomchan-wordpress-notice
 * Domain Path:       /languages
 * Thanks:            方糖-easychen site：http://ftqq.com/ https://github.com/easychen/serverchan-wordpress-comments-notice
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('Serverchan_VERSION', '1.0.2');


function ftqq_serverchan_settings_init()
{
    // 为 阅读 页面注册新设置
    register_setting('discussion', 'ftqq_serverchan_settings');

    // 在阅读页面上注册新分节
    add_settings_section(
        'ftqq_serverchan_settings_section',
        'WeCom酱',
        'ftqq_serverchan_settings_section_cb',
        'discussion'
    );

    add_settings_field(
        'ftqq_serverchan_settings_is_on',
        '是否开启微信通知',
        'ftqq_serverchan_settings_is_on_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_accesspath',
        '访问路径',
        'ftqq_serverchan_settings_accesspath_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_sendkey',
        'Sendkey',
        'ftqq_serverchan_settings_sendkey_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_author_not_send',
        '不发送作者自己的评论通知',
        'ftqq_serverchan_settings_author_not_send_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );
}

function ftqq_serverchan_settings_section_cb()
{
    echo "<p>通过WeCom酱向微信发送通知</p>";
}

function ftqq_serverchan_settings_is_on_cb()
{
    $setting = get_option('ftqq_serverchan_settings');

    $html = '<input type="checkbox" id="ftqq_serverchan_is_on" name="ftqq_serverchan_settings[is_on]" value="1"' . checked(1, @$setting['is_on'], false) . '/>';

    echo $html;
}

function ftqq_serverchan_settings_accesspath_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[accesspath]" value=<?php echo isset($setting['accesspath']) ? esc_attr($setting['accesspath']) : ''; ?>>
<?php
}

function ftqq_serverchan_settings_sendkey_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[sendkey]" value=<?php echo isset($setting['sendkey']) ? esc_attr($setting['sendkey']) : ''; ?>>
<?php
}

function ftqq_serverchan_settings_author_not_send_cb()
{
    $setting = get_option('ftqq_serverchan_settings');

    $html = '<input type="checkbox" id="ftqq_serverchan_author_send" name="ftqq_serverchan_settings[author_not_send]" value="1"' . checked(1, @$setting['author_not_send'], false) . '/>';

    echo $html;
}

/**
 * 注册 wporg_settings_init 到 admin_init Action 钩子
 */
add_action('admin_init', 'ftqq_serverchan_settings_init');

function ftqq_serverchan_comment_send($comment_id)
{
    // 读取配置
    $setting = get_option('ftqq_serverchan_settings');
    // 检查配置
    if (intval(@$setting['is_on']) != 1) {
        return false;
    }
    if (!isset($setting['sendkey'])) {
        return false;
    }
    if (!isset($setting['accesspath'])) {
        return false;
    }

    $comment = get_comment($comment_id);

    // 配置开关：如果文章作者就是评论作者，那么不发送评论
    if (intval(@$setting['author_not_send']) == 1 && (get_post($comment->comment_post_ID)->post_author == $comment->user_id)) {
        return false;
    }

    $text = "博客 [ " . get_bloginfo('name') . " ] 《".get_post($comment->comment_post_ID)->post_title."》有新的留言%0a";
    $desp = $comment->comment_author." ： ".$comment->comment_content .'%0a<a href="'.site_url()."/?page_id=".$comment->comment_post_ID."%23comment-".$comment_id.'">去博客查看</a>';

    $msg = $text . $desp;
    return $result = file_get_contents($setting['accesspath'] . '?sendkey=' . $setting['sendkey'] . '&msg_type=text&msg=' . $msg);

}
add_action('comment_post', 'ftqq_serverchan_comment_send', 19, 2);