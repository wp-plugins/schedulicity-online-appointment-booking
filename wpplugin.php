<?php
/*
Plugin Name: Schedulicity - Easy Online Scheduling
Plugin URI: www.schedulicity.com
Description: Wordpress Plugin that allows you to easily integrate schedulicity with one command. Activate the plugin, and navigate to the "Settings" tab on the Wordpress dashboard. Then click Schedulicity Setup. Set your business key and select which plugin type you want. Then place the [schedule_now] shortcode on any page/post and your booking calendar will automatically appear.
Version: 1.1
Author: Schedulicity Inc.
Author URI: www.schedulicity.com
License: GPL2
*/
/*  Copyright 2012 Jeremiah Prummer, Schedulicity Inc.  (email : jeremiah@schedulicity.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
add_action('admin_init', 'schedulicityplugin_init' );
add_action('admin_menu', 'schedulicity_add_page');

// Init plugin options to white list our options
function schedulicityplugin_init(){
	register_setting( 'schedulicity_options', 'widget_type', 'schedulicity_options_validate' );
	register_setting( 'schedulicity_options', 'user_bizkey', 'schedulicity_options_validate' );
	register_setting( 'schedulicity_options', 'user_maxheight', 'schedulicity_options_validate' );
	register_setting( 'schedulicity_options', 'user_minheight', 'schedulicity_options_validate' );
}

// Add menu page
function schedulicity_add_page() {
	add_options_page('Schedulicity Plugin Setup', 'Schedulicity Setup', 'manage_options', 'schedulicity_options_page', 'schedulicity_options_do_page');
}

// Draw the menu page itself
function schedulicity_options_do_page() {
?>
	<div class="wrap">
		
		<div style="width: 90%; margin:0px auto; margin-top: 20px; margin-bottom: 20px;border: 1px solid #888;padding: 20px;-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888;">
		
		<div style="margin-bottom: 20px">
		<img src="<?php echo plugins_url( 'schedulicitylogo.jpg', __FILE__ ); ?>" style="width: 200px; margin-bottom: 10px" />
		<h2>Schedulicity Plugin Setup</h2>
		</div>
		<form method="post" action="options.php">
			<?php settings_fields('schedulicity_options'); ?>
			
			<ol>
			<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step One - Insert Your Biz Key</li>
			<?php $options = get_option('user_bizkey'); ?>			
			<ul style="font-size: 16px">
			Biz Key: <input type="text" name="user_bizkey[bizkey]" value="<?php echo $options['bizkey']; ?>" /> <span style="margin-left: 20px">
			If you don't have a Schedulicity account <a href="http://www.schedulicity.com/?anic=wordpress" target="_blank">click here</a> to get 30 days free.
			</span>
			</ul>
			<ul style="font-size: 14px; margin-left: 20px; margin-bottom: 10px; margin-top: 10px ">
			To find biz key: Login to your Schedulicity account. Click the 'Marketing' tab and then click "edit my listing". You'll see a link that looks like <span style="color: green">http://www.schedulicity.com/Scheduling/BusinessInfo.aspx?business=<span style="font-weight:bold">AAAAAA</span></span>. Your biz key is the bolded six character key after ?business=. You can also contact <a href="http://www.schedulicity.com/About/Customer-Support.aspx">Schedulicity support</a> for help.
			</ul>
			<?php $options = get_option('widget_type'); ?>
			<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Two - Select Your Widget Type</li>
			<div style="width: 100%;">
			<div>
			<ul style="font-size: 16px">	
			<li><span style="margin-right: 20px">Embedded</span><input name="widget_type[embedded]" type="radio" value="1" <?php checked('1', $options['embedded']); ?> /></li>
			<ul style="font-size: 14px; margin-left: 20px; margin-bottom: 10px ">
			This widget is built right into a page on your site. You'll need to set aside 652 x 479 pixels for it to work. See
			an <a href="http://www.wpovernight.com/schedulicity/embedded-widget/" target="_blank">example here.</a>
			</ul>
			<li><span style="margin-right: 46px">Overlay</span><input name="widget_type[embedded]" type="radio" value="2" <?php checked('2', $options['embedded']); ?> /></li>
			<ul style="font-size: 14px; margin-left: 20px; margin-bottom: 10px">
			A schedule now button will hang on the side of your screen. Your schedule will pop up when the user clicks the button. See
			an <a href="http://www.wpovernight.com/schedulicity/overlay-widget/" target="_blank">example here.</a>
			</ul>
			<li><span style="margin-right: 16px">Responsive</span><input name="widget_type[embedded]" type="radio" value="3" <?php checked('3', $options['embedded']); ?> /></li>
			</ul>
			</div>
			<div>
			<ul style="font-size: 14px; margin-left: 20px">
			The responsive design will automatically fit the width of any of your pages, posts or widgets. Just set the minimum/maximum height and you're ready to go. See an <a href="http://www.wpovernight.com/schedulicity/responsive-design/" target="_blank">example here.</a>
			</ul>	
			<?php $options = get_option('user_maxheight'); ?>
			<ul style="font-size: 14px; margin-left: 20px">
			Max Height (In Pixels)<input type="text" name="user_maxheight[maxheight]" value="<?php echo $options['maxheight']; ?>" />
			</ul>
			<?php $options = get_option('user_minheight'); ?>
			<ul style="font-size: 14px; margin-left: 20px">
			Min Height (In Pixels) <input type="text" name="user_minheight[minheight]" value="<?php echo $options['minheight']; ?>" />
			</ul>
			</div>
			</div>
			<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Three - Start Scheduling!</li>
			<ul style="font-size: 16px">
			Just insert the shortcode <span style="background: yellow">[schedule_now]</span> 
			on any page or post. Your booking calendar will automatically appear.
			</ul>	
			</ol>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div>
	</div>
<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function schedulicity_options_validate($input) {
	// Our first value is either 0 or 1
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
	
	// Say our second option must be safe text with no HTML tags
	$input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
	
	return $input;
}
//Retrieve Widget Type
$widget_type = get_option('widget_type');
$schedulicity_widget = $widget_type['embedded'];
if ($schedulicity_widget==1) {
	function embedded_widget() {
		$user_bizkey = get_option('user_bizkey');
		$sched_bizkey = $user_bizkey['bizkey'];
		$embedded_shortcode = <<<HTML
		<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/embedjs.aspx?business=$sched_bizkey"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=$sched_bizkey" title="Online Scheduling">Schedule Now</a></noscript>
HTML;
		return $embedded_shortcode;
				}
		add_shortcode('schedule_now', 'embedded_widget');
}
elseif ($schedulicity_widget==2) {
function overlay_widget() {
		$user_bizkey = get_option('user_bizkey');
		$sched_bizkey = $user_bizkey['bizkey'];
		echo '<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/popupjs.aspx?business=';
		echo $sched_bizkey;
		echo '"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=';
		echo $sched_bizkey;
		echo '" title="Online Scheduling">Schedule Now</a></noscript>';
				}
		add_shortcode('schedule_now', 'overlay_widget');
	}
else {
function responsive_widget() {
		$user_bizkey = get_option('user_bizkey');
		$sched_bizkey = $user_bizkey['bizkey'];
		$user_maxheight = get_option('user_maxheight');
		$maxheight = $user_maxheight['maxheight'];
		$user_minheight = get_option('user_minheight');
		$minheight = $user_minheight['minheight'];
		$unit = 'px';
		$responsive_shortcode = <<<HTML
		<iframe src="https://m.schedulicity.com/Scheduling/SelectService/$sched_bizkey" style="width:100%; height: 100%; max-height: $maxheight$unit;min-height: $minheight$unit"></iframe>
HTML;
		return $responsive_shortcode;
				}
		
		add_shortcode('schedule_now', 'responsive_widget');
}
?>