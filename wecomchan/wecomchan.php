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
 * @since             2.0.0
 * @package           WeComchan_Wordpress_notice
 *
 * @wordpress-plugin
 * Plugin Name:       WeCom酱,WeComchan-企业微信WordPress博客评论微信通知
 * Plugin URI:        https://dpii.club/wecomchan-wordpress-notice
 * Description:       将WordPress通知推送到微信（目前支持直接调用企业微信API）感谢：方糖-easychen site：http://ftqq.com/
 * Version:           2.0.0
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
define('WeComchan_VERSION', '2.0.0');


function ftqq_serverchan_settings_init()
{
    // 为 阅读 页面注册新设置
    register_setting('discussion', 'ftqq_serverchan_settings');

    // 在阅读页面上注册新分节
    add_settings_section(
        'ftqq_serverchan_settings_section',
        'WeCom酱,WeComchan-企业微信WordPress博客评论微信通知',
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
        'ftqq_serverchan_settings_corpid',
        '企业ID',
        'ftqq_serverchan_settings_corpid_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_agentid',
        '内部应用ID',
        'ftqq_serverchan_settings_agentid_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_appsecret',
        '内部应用Secret',
        'ftqq_serverchan_settings_appsecret_cb',
        'discussion',
        'ftqq_serverchan_settings_section'
    );

    add_settings_field(
        'ftqq_serverchan_settings_touser',
        '指定接收成员（填写ID，多个接收者用"|"分隔；填"@all"，则向该企业应用的全部成员发送）',
        'ftqq_serverchan_settings_touser_cb',
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
    echo "<p>通过WeCom酱向企业微信发送通知</p>";
}

function ftqq_serverchan_settings_is_on_cb()
{
    $setting = get_option('ftqq_serverchan_settings');

    $html = '<input type="checkbox" id="ftqq_serverchan_is_on" name="ftqq_serverchan_settings[is_on]" value="1"' . checked(1, @$setting['is_on'], false) . '/>';

    echo $html;
}

function ftqq_serverchan_settings_corpid_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[corpid]" value=<?php echo isset($setting['corpid']) ? esc_attr($setting['corpid']) : ''; ?>>
<?php
}

function ftqq_serverchan_settings_agentid_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[agentid]" value=<?php echo isset($setting['agentid']) ? esc_attr($setting['agentid']) : ''; ?>>
<?php
}

function ftqq_serverchan_settings_appsecret_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[appsecret]" value=<?php echo isset($setting['appsecret']) ? esc_attr($setting['appsecret']) : ''; ?>>
<?php
}

function ftqq_serverchan_settings_touser_cb()
{
    $setting = get_option('ftqq_serverchan_settings');
    // 输出字段
?>
    <input type="text" name="ftqq_serverchan_settings[touser]" value=<?php echo isset($setting['touser']) ? esc_attr($setting['touser']) : ''; ?>>
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
    if (!isset($setting['corpid'])) {
        return false;
    }
    if (!isset($setting['agentid'])) {
        return false;
    }
    if (!isset($setting['appsecret'])) {
        return false;
    }
    if (!isset($setting['touser'])) {
        return false;
    }

    $comment = get_comment($comment_id);

    // 配置开关：如果文章作者就是评论作者，那么不发送评论
    if (intval(@$setting['author_not_send']) == 1 && (get_post($comment->comment_post_ID)->post_author == $comment->user_id)) {
        return false;
    }

    $text = "博客 [ " . get_bloginfo('name') . " ] 《".get_post($comment->comment_post_ID)->post_title."》有新的留言\n";
    $desp = $comment->comment_author.' ：' . addslashes($comment->comment_content) . "\n" . '<a href=\"'.site_url()."/?page_id=".$comment->comment_post_ID."#comment-".$comment_id.'\">去博客查看</a>';

    $msg = $text . $desp;
    $postbody = '{"msgtype":"text","touser":"' . $setting["touser"]. '","agentid":' . $setting['agentid'] . ',"text":{"content":"' . $msg . '"}}';
    $acc_tok = json_decode((string)file_get_contents('https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $setting['corpid'] .'&corpsecret=' . $setting['appsecret']),true)['access_token'];
    $buildpost = stream_context_create(array(  

        'http' => array(  
    
          'method' => 'POST',  
    
          'header' => 'Content-type:application/json',
      
          'content' => $postbody,  
    
          'timeout' => 20  
    
        )  
    
    ));  
    return $result = file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $acc_tok, false, $buildpost);
}
add_action('comment_post', 'ftqq_serverchan_comment_send', 19, 2);