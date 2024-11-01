<?php
/*
Plugin Name: UGC Comments
Plugin URI: https://wordpress.org/plugins/ugc-comments/
Description: The plugin allows you to manage the values of the "rel" attribute in comment links ("ugc", "nofollow").
Version: 1.00
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: ugc-comments
*/ 

//проверка версии плагина (запуск функции установки новых опций) begin
function ugcc_check_version() {
    $ugcc_options = get_option('ugcc_options');
    if ( $ugcc_options['version'] != '1.00' ) {
        ugcc_set_new_options();
    }    
}
add_action('plugins_loaded', 'ugcc_check_version');
//проверка версии плагина (запуск функции установки новых опций) end

//функция установки новых опций при обновлении плагина у пользователей begin
function ugcc_set_new_options() { 
    $ugcc_options = get_option('ugcc_options');

    //если нет опции при обновлении плагина - записываем ее
    //if (!isset($ugcc_options['new_option'])) {$ugcc_options['new_option']='value';}
    
    //если необходимо переписать уже записанную опцию при обновлении плагина
    //$ugcc_options['old_option'] = 'new_value';
    
    $ugcc_options['version'] = '1.00';
    update_option('ugcc_options', $ugcc_options);
}
//функция установки новых опций при обновлении плагина у пользователей end

//функция установки значений по умолчанию при активации плагина begin
function ugcc_init() {

    $ugcc_options = array();
    $ugcc_options['version'] = '1.00';
    $ugcc_options['ugc-author-link'] = 'enabled';
    $ugcc_options['nofollow-author-link'] = 'enabled';
    $ugcc_options['ugc-comments-links'] = 'enabled';
    $ugcc_options['nofollow-comments-links'] = 'enabled';
    $ugcc_options['noindex-author-link'] = 'disabled';
    $ugcc_options['noindex-comments-links'] = 'disabled';
   
    add_option('ugcc_options', $ugcc_options);
}
add_action('activate_ugc-comments/ugc-comments.php', 'ugcc_init');
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function ugcc_on_deactivation() {
	if ( ! current_user_can('activate_plugins') ) return;
}
register_deactivation_hook( __FILE__, 'ugcc_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function ugcc_on_uninstall() {
	if ( ! current_user_can('activate_plugins') ) return;
    delete_option('ugcc_options');
}
register_uninstall_hook( __FILE__, 'ugcc_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function ugcc_setup(){
    load_plugin_textdomain('ugc-comments');
}
add_action('init', 'ugcc_setup');
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function ugcc_actions($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=ugc-comments.php">' . __('Settings', 'ugc-comments') . '</a>'), $links);
}
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'ugcc_actions');
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function ugcc_files_admin($hook_suffix) {
	$purl = plugins_url('', __FILE__);
    if ( $hook_suffix == 'settings_page_ugc-comments' ) {
        if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}    
        wp_register_script('ugcc-lettering', $purl . '/inc/jquery.lettering.js');  
        wp_enqueue_script('ugcc-lettering');
        wp_register_script('ugcc-textillate', $purl . '/inc/jquery.textillate.js');
        wp_enqueue_script('ugcc-textillate');
        wp_register_style('ugcc-animate', $purl . '/inc/animate.min.css');
        wp_enqueue_style('ugcc-animate');
        wp_register_script('ugcc-script', $purl . '/inc/ugcc-script.js', array(), '1.00');  
        wp_enqueue_script('ugcc-script');
        wp_register_style('ugcc-css', $purl . '/inc/ugcc-css.css', array(), '1.00');
        wp_enqueue_style('ugcc-css');
    }
}
add_action('admin_enqueue_scripts', 'ugcc_files_admin');
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция вывода страницы настроек плагина begin
function ugcc_options_page() {
$purl = plugins_url('', __FILE__);

if (isset($_POST['submit'])) {
     
//проверка безопасности при сохранении настроек плагина begin       
if ( ! wp_verify_nonce( $_POST['ugcc_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'ugc-comments' ));
}
//проверка безопасности при сохранении настроек плагина end
        
    //проверяем и сохраняем введенные пользователем данные begin    
    $ugcc_options = get_option('ugcc_options');
    
    if(isset($_POST['ugc-author-link'])){$ugcc_options['ugc-author-link'] = sanitize_text_field($_POST['ugc-author-link']);}else{$ugcc_options['ugc-author-link'] = 'disabled';}
    if(isset($_POST['nofollow-author-link'])){$ugcc_options['nofollow-author-link'] = sanitize_text_field($_POST['nofollow-author-link']);}else{$ugcc_options['nofollow-author-link'] = 'disabled';}
    if(isset($_POST['ugc-comments-links'])){$ugcc_options['ugc-comments-links'] = sanitize_text_field($_POST['ugc-comments-links']);}else{$ugcc_options['ugc-comments-links'] = 'disabled';}
    if(isset($_POST['nofollow-comments-links'])){$ugcc_options['nofollow-comments-links'] = sanitize_text_field($_POST['nofollow-comments-links']);}else{$ugcc_options['nofollow-comments-links'] = 'disabled';}
    if(isset($_POST['noindex-author-link'])){$ugcc_options['noindex-author-link'] = sanitize_text_field($_POST['noindex-author-link']);}else{$ugcc_options['noindex-author-link'] = 'disabled';}
    if(isset($_POST['noindex-comments-links'])){$ugcc_options['noindex-comments-links'] = sanitize_text_field($_POST['noindex-comments-links']);}else{$ugcc_options['noindex-comments-links'] = 'disabled';}
    
    update_option('ugcc_options', $ugcc_options);
    //проверяем и сохраняем введенные пользователем данные end
}
$ugcc_options = get_option('ugcc_options');
?>
<?php   if (!empty($_POST) ) :
if ( ! wp_verify_nonce( $_POST['ugcc_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'ugc-comments' ));
}
?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'ugc-comments'); ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('&#8220;UGC Comments&#8221; Settings', 'ugc-comments'); ?></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<?php $lang = get_locale(); ?>
<?php if ($lang == 'ru_RU') { ?>
<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode">Вам нравится этот плагин ?</span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="Купить мне чашку кофе :)" style=" margin: 5px; float:left;" />
        <p>Привет, меня зовут <strong>Flector</strong>.</p>
        <p>Я потратил много времени на разработку этого плагина.<br />
		Поэтому не откажусь от небольшого пожертвования :)</p>
        <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a> 
        <p>Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.<br />
        Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target="_blank" href="https://www.wpuslugi.ru/?from=ugcc-plugin">https://www.wpuslugi.ru/</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } else { ?>
<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Do you like this plugin ?', 'ugc-comments'); ?></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="<?php _e('buy me a coffee', 'ugc-comments'); ?>" style=" margin: 5px; float:left;" />
        <p><?php _e('Hi! I\'m <strong>Flector</strong>, developer of this plugin.', 'ugc-comments'); ?></p>
        <p><?php _e('I\'ve spent many hours developing this plugin.', 'ugc-comments'); ?> <br />
		<?php _e('If you like and use this plugin, you can <strong>buy me a cup of coffee</strong>.', 'ugc-comments'); ?></p>
        <a target="_blank" href="https://www.paypal.me/flector"><img alt="" src="<?php echo $purl . '/img/donate.gif'; ?>" title="<?php _e('Donate with PayPal', 'ugc-comments'); ?>" /></a>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } ?>

<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Options', 'ugc-comments'); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">

            <tr>
                <th class="tdcheckbox"><?php _e('Comment Author\'s Links:', 'ugc-comments') ?></th>
                <td>
                    <label for="ugc-author-link"><input type="checkbox" value="enabled" name="ugc-author-link" id="ugc-author-link" <?php if ($ugcc_options['ugc-author-link'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Add "ugc" attribute', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to comment author\'s links.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th class="tdcheckbox"></th>
                <td style="padding-top: 0px;">
                    <label for="nofollow-author-link"><input type="checkbox" value="enabled" name="nofollow-author-link" id="nofollow-author-link" <?php if ($ugcc_options['nofollow-author-link'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Add "nofollow" attribute', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to comment author\'s links.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            
            <tr>
                <th class="tdcheckbox"><?php _e('Comments Links:', 'ugc-comments') ?></th>
                <td>
                    <label for="ugc-comments-links"><input type="checkbox" value="enabled" name="ugc-comments-links" id="ugc-comments-links" <?php if ($ugcc_options['ugc-comments-links'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Add "ugc" attribute', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to links within the comment.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th class="tdcheckbox"></th>
                <td style="padding-top: 0px;">
                    <label for="nofollow-comments-links"><input type="checkbox" value="enabled" name="nofollow-comments-links" id="nofollow-comments-links" <?php if ($ugcc_options['nofollow-comments-links'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Add "nofollow" attribute', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to links within the comment.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            
            <tr>
                <th class="tdcheckbox"><?php _e('Noindex for Yandex:', 'ugc-comments') ?></th>
                <td>
                    <label for="noindex-author-link"><input type="checkbox" value="enabled" name="noindex-author-link" id="noindex-author-link" <?php if ($ugcc_options['noindex-author-link'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Hide comment author\'s links', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to comment author\'s links.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th class="tdcheckbox"></th>
                <td style="padding-top: 0px;">
                    <label for="noindex-comments-links"><input type="checkbox" value="enabled" name="noindex-comments-links" id="noindex-comments-links" <?php if ($ugcc_options['noindex-comments-links'] == 'enabled') echo "checked='checked'"; ?> /><?php _e('Hide comments links', 'ugc-comments'); ?></label>
                    <br /><small><?php _e('This option only applies to links within the comment.', 'ugc-comments'); ?> </small>
                </td>
            </tr>
            
            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Update options &raquo;', 'ugc-comments'); ?>" />
                </td>
            </tr> 
        </table>
    </div>
</div>

<div class="postbox" style="margin-bottom:0;">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('About', 'ugc-comments'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
      <p><?php _e('If you liked my plugin, please <a target="_blank" href="https://wordpress.org/plugins/ugc-comments/"><strong>rate</strong></a> it.', 'ugc-comments'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('You may also like my other plugins:', 'ugc-comments'); ?></p>
      
      <div class="about">
        <ul>
            <?php if ($lang == 'ru_RU') : ?>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - создание RSS-ленты для сервиса Яндекс.Дзен.</li>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-turbo/">RSS for Yandex Turbo</a> - создание RSS-ленты для сервиса Яндекс.Турбо.</li>
            <?php endif; ?>
            <li><a target="_blank" href="https://wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('this plugin allows you to hide text using the tags [spoiler]your text[/spoiler].', 'ugc-comments'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('very beautiful text animations (shortcodes in posts and widgets or PHP code in theme files).', 'ugc-comments'); ?> </li>
            <li><a target="_blank" href="https://wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('this plugin adds social sharing icons to each image in your posts.', 'ugc-comments'); ?> </li>
            <li><a target="_blank" href="https://wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('this plugin changes the creation dates of posts to relative dates.', 'ugc-comments'); ?> </li>
            <li><a target="_blank" href="https://wordpress.org/plugins/truncate-comments/">Truncate Comments</a> - <?php _e('this plugin uses Javascript to hide long comments (Amazon-style comments).', 'ugc-comments'); ?> </li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-yandex-share/">Easy Yandex Share</a> - <?php _e('share buttons for WordPress from Yandex. ', 'ugc-comments'); ?> </li>
            <li style="margin: 3px 0px 3px 35px;"><a target="_blank" href="https://wordpress.org/plugins/html5-cumulus/">HTML5 Cumulus</a> <span class="new">new</span> - <?php _e('a modern (HTML5) version of the classic &#8220;WP-Cumulus&#8221; plugin.', 'rss-for-yandex-turbo'); ?></li>
            </ul>
      </div>     
    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'ugcc_nonce'); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function ugcc_menu() {
	add_options_page('UGC Comments', 'UGC Comments', 'manage_options', 'ugc-comments.php', 'ugcc_options_page');
}
add_action('admin_menu', 'ugcc_menu');
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end

//функция изменения атрибута rel для ссылок на автора комментария begin
function ugcc_author_link( $link ) {
    
    //выходим, если это админка
    if ( is_admin() ) return $link;
    
    $ugcc_options = get_option('ugcc_options');
    
    $pattern = "/rel='(.*?)'/i";
    $replacement = "rel=''";
    $link = preg_replace($pattern, $replacement, $link);
    
    $rel = array();
    $rel[] = 'external';

    if ( $ugcc_options['nofollow-author-link'] == 'enabled' ) {
        $rel[] = 'nofollow';
    }    
    if ( $ugcc_options['ugc-author-link'] == 'enabled' ) {
        $rel[] = 'ugc';          
    }
    
    $new_rel = implode(' ', $rel);
    $link = str_replace("rel=''", "rel='".$new_rel."'", $link);
    
    if ( $ugcc_options['noindex-author-link'] == 'enabled' ) {
        $link = '<!--noindex-->' . $link . '<!--/noindex-->';
    }
    
    return $link;
}
add_filter('get_comment_author_link', 'ugcc_author_link');
//функция изменения атрибута rel для ссылок на автора комментария end

//функция изменения атрибута rel для ссылок в тексте комментария begin
function ugcc_comments_links( $comment ) {
    
    //выходим, если это админка
    if ( is_admin() ) return $comment;
    
    $ugcc_options = get_option('ugcc_options');
    
    $pattern = '/rel="(.*?)"/i';
    $replacement = 'rel=""';
    $comment = preg_replace($pattern, $replacement, $comment);
    
    $rel = array();

    if ( $ugcc_options['nofollow-comments-links'] == 'enabled' ) {
        $rel[] = 'nofollow';
    }    
    if ( $ugcc_options['ugc-comments-links'] == 'enabled' ) {
        $rel[] = 'ugc';          
    }
    
    $new_rel = implode(' ', $rel);    
    if ( $new_rel != '' ) {
        $comment = str_replace('rel=""', 'rel="'.$new_rel.'"', $comment);
    } else {
        $comment = str_replace('rel=""', '', $comment);
    }
    
    if ( $ugcc_options['noindex-comments-links'] == 'enabled' ) {
        $pattern = '/<a(.*?)<\/a>/i';
        $replacement = '<!--noindex--><a$1</a><!--/noindex-->';
        $comment = preg_replace($pattern, $replacement, $comment);
    }
    
    return $comment;
}
add_filter('comment_text', 'ugcc_comments_links');
//функция изменения атрибута rel для ссылок в тексте комментария end