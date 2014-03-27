<?php
/*
Plugin Name: Schedulicity - Easy Online Scheduling
Plugin URI: www.schedulicity.com
Description: Wordpress Plugin that allows you to easily integrate schedulicity with one command. Activate the plugin, and navigate to the "Settings" tab on the Wordpress dashboard. Then click Schedulicity Setup. Set your business key and select which plugin type you want. Then place the [schedule_now] shortcode on any page/post and your booking calendar will automatically appear.
Version: 2.0.0
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
class Schedulicity_Plugin {

	/**
	 * Construct.
	 */
	function __construct() {

		add_action('admin_init', array( &$this, 'schedulicityplugin_init'), 0 );
		add_action('admin_menu', array( &$this, 'schedulicity_add_page'), 0 );
		add_action('plugins_loaded', array( &$this, 'schedulicity_widgets'), 0 );
		
		// Admin Notices
		if ( ! empty( $_GET['hide_sched_check'] ) ) {
			update_option( 'sched_hide_check', 'hide' );
		}
		if ( ! empty( $_GET['hide_sched_check_bizkey'] ) ) {
			update_option( 'sched_hide_check_bizkey', 'hide' );
		}
		$sched_hide_check = get_option( 'sched_hide_check' );
		$sched_hide_check_bizkey = get_option( 'sched_hide_check_bizkey' );
		$sched_bizkey = get_option( 'user_bizkey' );
		if ($sched_hide_check != 'hide') {
			add_action( 'admin_notices', array( &$this, 'sched_time_to_update' ), 0);
		}
		$sched_bizkey = array_filter($sched_bizkey);
		if (($sched_hide_check_bizkey != 'hide') && (empty($sched_bizkey))){
			add_action( 'admin_notices', array( &$this, 'missing_your_bizkey' ), 0);
		}
	}

	// Init plugin options to white list our options
	function schedulicityplugin_init(){
	
		register_setting( 'schedulicity_options', 'user_bizkey');
		
		//Deprecated Settings
		//register_setting( 'schedulicity_options', 'user_maxheight');
		//register_setting( 'schedulicity_options', 'user_minheight');
		//register_setting( 'schedulicity_options', 'widget_type');
	}

	// Add menu page
	function schedulicity_add_page() {
		add_options_page('Schedulicity Plugin Setup', 'Schedulicity Setup', 'manage_options', 'schedulicity_options_page', array( &$this, 'schedulicity_options_do_page'));
	}

	// Draw the menu page itself
	function schedulicity_options_do_page() {
	?>
		<div class="wrap" style="font-size: 18px">	
			<?php
			if (isset($_GET['tab'])) {
				$active_tab = $_GET['tab'];
			} else {
				$active_tab = 'standard_setup';
			}
			?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=schedulicity_options_page&tab=standard_setup" class="nav-tab <?php echo $active_tab == 'standard_setup' ? 'nav-tab-active' : ''; ?>">Setup</a>  
				<a href="?page=schedulicity_options_page&tab=advanced_setup" class="nav-tab <?php echo $active_tab == 'advanced_setup' ? 'nav-tab-active' : ''; ?>">Advanced</a> 
			</h2>
			<?php
			if ($active_tab == 'standard_setup') {
			?>
			<div style="background: #FFF;-moz-border-radius: 3px;border-radius: 3px;margin:5%;margin-top: 30px;padding: 20px;-moz-box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888;">
				
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
						Business Key: <input type="text" name="user_bizkey[bizkey]" value="<?php echo $options['bizkey']; ?>" /><input type="submit" class="button-primary" value="<?php _e('Save Business Key') ?>" style="margin-left: 20px" /><span style="margin-left: 20px;font-size: 14px"><a href="?page=schedulicity_options_page&tab=advanced_setup#bizkey">What's my Business Key?</a></span>
						<p> 
							If you don't have a Schedulicity account <a href="http://www.schedulicity.com/?anic=wordpress" target="_blank">click here</a> to get 30 days free.
						</p>
						</ul>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Two - Set Up</li>
						<ul style="font-size: 16px">
							Use the [schedule_now] shortcode to add scheduling widgets. Use the [schedule_now_button] shortcode to add schedule now buttons. Refer to the <a href="?page=schedulicity_options_page&tab=advanced_setup#shortcodeinstruction">advanced options page</a> for more info.
						</ul>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Three - Start Scheduling!</li>
						<ul style="font-size: 16px">
						Once you've added the shortcode to a page or post, just give it a quick test to make sure it works. If you have any issues, email <a href="mailto:support@schedulicity.com">support@schedulicity.com</a> or call <strong>877-582-0494</strong>. When you're ready, send your customers to your site to start booking their appointments!
						</ul>
				</ol>
				
			</form>
			</div>
			<?php
			}
			else {
			?>
			<div style="background: #FFF;-moz-border-radius: 3px;border-radius: 3px;margin:5%;margin-top: 30px;padding: 20px;-moz-box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888;">
				<img src="<?php echo plugins_url( 'schedulicitylogo.jpg', __FILE__ ); ?>" style="width: 200px; margin-bottom: 10px" />
				<h2>Schedulicity Advanced Setup</h2>
				<div id="bizkey">
					<h4>1. Finding Your Business Key</h4>
					<p style="margin-left: 20px; font-size: 14px">
						To find your business key: 
						<p style="margin-left: 20px; font-size: 14px">1. Make sure you have an active Schedulicity account. If you don't have one you can create one here with a 30 day free trial: <a href="http://www.schedulicity.com/?anic=wordpress" target="_blank">https://schedulicity.com</a></p>
						<p style="margin-left: 20px; font-size: 14px">2. Login to your Schedulicity account. Click the 'Marketing' tab and then click 'Access Your Widgets'. In the middle of the page you'll see the Facebook Widget info. Your business key is listed there.</p>
					</p>
				</div>
				<div id="shortcodeinstruction">
					<h4>2. Customizing Your Shortcode</h4>
					<p style="margin-left: 20px; font-size: 14px">The [schedule_now] shortcode can be customized to meet your needs. By default the embedded widget is used, but you can easily change it to the overlay widget using the method below. As well you can add as many embedded widgets as you want to a page, but can only have one overlay widget per page. See section <a href="?page=schedulicity_options_page&tab=advanced_setup#multipleaccounts">4. Use with Multiple Schedulicity Accounts</a> for more info.</p>
					<div>
					<ul style="font-size: 16px">	
					<li><span style="margin-right: 20px"><strong>Embedded Widget</strong></span></li>
					<ul style="font-size: 14px; margin-left: 20px; margin-bottom: 10px ">
					Shortcode: <span style="background: #b0f26d">[schedule_now widget="embedded"]</span> This widget is built right into a page on your site. You'll need to set aside 652 x 479 pixels for it to work. See
					an <a href="http://schedulicity.wpovernight.com/embedded-widget/" target="_blank">example here.</a>
					</ul>
					<li>
						<span style="margin-right: 46px"><strong>Overlay Widget</strong></span>
					</li>
					<ul style="font-size: 14px; margin-left: 20px; margin-bottom: 10px">
					Shortcode: <span style="background: #b0f26d">[schedule_now widget="overlay"]</span> A schedule now button will hang on the side of your screen. Your schedule will pop up when the user clicks the button. See
					an <a href="http://schedulicity.wpovernight.com/overlay-widget/" target="_blank">example here.</a>
					</ul>
					</ul>
					<h4>3. Adding Schedule Now Buttons</h4>
					<ul style="font-size: 16px">
					<li><span style="margin-right: 45px"><strong>Schedule Now Buttons</strong></span></li>
					</ul>
					</div>
					<div>
					<ul style="font-size: 14px; margin-left: 20px">
					Just insert the shortcode <span style="background: #b0f26d">[schedule_now_button]</span> on any page or post. A Schedule Now button linking to your Schedulicity account will automatically appear. <a href="http://schedulicity.wpovernight.com/responsive-button/" target="_blank">See example</a>
					<br /><br />
					You can also customize the button to have it align left, align right, or align center (default) just add the align attribute to the shortcode. Example: <span style="background: #b0f26d">[schedule_now_button align="center"]</span>. The align attribute can be any of the following: align="left", align="center", align="right".
					<br /><br />
					Finally, you can customize the button style by adding the style attribute to the [schedule_now_button] shortcode. Example:
					<span style="background: #b0f26d">[schedule_now_button align="center" style="button10"]</span>. See chart below for the style attribute value of each button.
					</ul>	
					</div>
					<div style="text-align:center">
					<img src="<?php echo plugins_url( '/images/schedule_now_button_layout.png', __FILE__ ); ?>" style="max-width: 100%;" />
					</div>
					
				</div>
				<div id="multipleaccounts">
					<h4>4. Use With Multiple Schedulicity Accounts</h4>
					<p style="margin-left: 20px; font-size: 14px">Using the Schedulicity plugin with multiple accounts is easy! Just add 
					<span style="color: #4b9500">bizkey=" "</span> to the [schedule_now] or [schedule_now_button] shortcodes and place your bizkey between the quotes. 
					Examples: <span style="background: #ffef73">[schedule_now <span style="color: #4b9500">bizkey="SSTJP8"</span>]</span> or 
					<span style="background: #ffef73">[schedule_now_button align="left" <span style="color: #4b9500">bizkey="SSTJP8"</span>]</span>. With this method you can add as many booking calendars or buttons
					to your site as needed.</p>
				</div>
				<div id="supportinfo">
					<h4>5. Support Issues</h4>
					<p style="margin-left: 20px; font-size: 14px">
						If you have any questions please feel free to reach out to the Schedulicity support team.
						<br /><br />
						Email: <a href="mailto:support@schedulicity.com">support@schedulicity.com</a>
						<br />
						Phone: <strong>877-582-0494</strong>
					</p>
				</div>
			</div>	
			<?php
			}
			?>
			
		</div>
	<?php	
	}

	function schedulicity_widgets() {
		// Retrieve Widget Type
		$widget = get_option('widget_type');
		$sched_widget = $widget['embedded'];
		// Functions in this if statement are deprecated
		if (isset($sched_widget)){
		
			if ($sched_widget==2) {
				function overlay_widget($atts) {
					$bizkey = get_option('user_bizkey');
					$sched_bizkey = $bizkey['bizkey'];
					extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
					$content ='';
					$content .= '<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/popupjs.aspx?business=' . $bizkey . '"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=' . $bizkey . '"" title="Online Scheduling">Schedule Now</a></noscript>';
					return $content;		
				}
				add_shortcode('schedule_now', 'overlay_widget');
			}
			
			else {
				function embedded_widget($atts) {
					$bizkey = get_option('user_bizkey');
					$sched_bizkey = $bizkey['bizkey'];
					extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
					$content ='';
					$content .= '<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/embedjs.aspx?business=' . $bizkey . '"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=' . $bizkey . '"" title="Online Scheduling">Schedule Now</a></noscript>';
					return $content;
				}
				add_shortcode('schedule_now', 'embedded_widget');
			}
			
		}
		else {
		
			function standard_widget($atts) {
				$bizkey = get_option('user_bizkey');
				$sched_bizkey = $bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey, 'widget' => 'embedded') , $atts));
				$content = '';
				if ($widget == 'overlay') {
					$content .= '<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/popupjs.aspx?business=' . $bizkey . '"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=' . $bizkey . '"" title="Online Scheduling">Schedule Now</a></noscript>';
				}
				else {
					$content .= $return .= '<script type="text/javascript" src="http://www.schedulicity.com/Scheduling/Embed/embedjs.aspx?business=' . $bizkey . '"></script><noscript><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business=' . $bizkey . '"" title="Online Scheduling">Schedule Now</a></noscript>';
				}
				return $content;		
			}
			add_shortcode('schedule_now', 'standard_widget');
		
		}
		function sched_button_left($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_left_sc = '<div style="text-align: left"><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business='.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				return $sched_button_left_sc;
		}
				
		function sched_button_center($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_center_sc = '<div style="text-align: center"><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business='.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				return $sched_button_center_sc;
		}
		
		function sched_button_right($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_right_sc = '<div style="text-align: right"><a href="http://www.schedulicity.com/Scheduling/Default.aspx?business='.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				return $sched_button_right_sc;
		}
		
		function sched_button($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey, 'align' =>'center', 'style' => '', 'size' => 'lg') , $atts));
				$sched_button = '';
				$alignment = '';
				if ($align == 'right') {
					$alignment = 'right';
				}
				else if ($align == 'left') {
					$alignment = 'left';
				}
				else {
					$alignment = 'center';
				}
				if (!empty($style)) {
					$image_url = '';
					$style = str_replace('button','', $style);
					if ($style < 10){
						$style = sprintf('%02d', $style);
					}
					$image_url = '//d2k394ztg01v3m.cloudfront.net/images/schedulenow_'.$style.'_'.$size.'.png';
					$sched_button = '<div style="text-align: '.$alignment.'"><a href="https://www.schedulicity.com/Scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="'.$image_url.'" alt="Schedule online now" border="0" /></a></div>';
				}
				else {
					$sched_button = '<div style="text-align: '.$alignment.'"><a href="https://www.schedulicity.com/Scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				}
				/*
				if ($align == 'right') {
					$sched_button = '<div style="text-align: right"><a href="https://www.schedulicity.com/Scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				}
				else if ($align == 'left') {
					$sched_button = '<div style="text-align: left"><a href="https://www.schedulicity.com/Scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				}
				else {
					$sched_button = '<div style="text-align: center"><a href="https://www.schedulicity.com/Scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				}
				*/
				$sched_button .='<script>if (window.innerWidth <= 600){document.getElementById("schednowlink").href="https://m.schedulicity.com/Scheduling/'.$bizkey.'"}</script>';
				return $sched_button;
		}
		
		add_shortcode('btn_left' , 'sched_button_left');
		add_shortcode('btn_center' , 'sched_button_center');
		add_shortcode('btn_right' , 'sched_button_right');
		add_shortcode('schedule_now', 'responsive_widget');
		add_shortcode('schedule_now_button' , 'sched_button');
	}
	
	function sched_time_to_update() {
		$error = 'The Schedulicity Plugin has undergone significant changes. Please update your shortcodes according to the directions on the settings page';
		$message = sprintf('<div class="error"><p>%1$s. <a href="%2$s">%3$s</a></p></div>', $error, add_query_arg( 'hide_sched_check', 'true' ), 'Hide this notice' );
		echo $message;
	}
	
	function missing_your_bizkey() {
		$error = 'The Schedulicity Plugin needs a valid business key to work. Please update your business key from the settings page';
		$message = sprintf('<div class="error"><p>%1$s. <a href="%2$s">%3$s</a></p></div>', $error, add_query_arg( 'hide_sched_check_bizkey', 'true' ), 'Hide this notice' );
		echo $message;
	}

}
$Schedulicity_Plugin = new Schedulicity_Plugin();
?>