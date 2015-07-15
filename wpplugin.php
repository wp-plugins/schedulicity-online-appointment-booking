<?php
/*
Plugin Name: Schedulicity - Easy Online Scheduling
Plugin URI: www.schedulicity.com
Description: Wordpress Plugin that allows you to easily integrate schedulicity with one command. Activate the plugin, and navigate to the "Settings" tab on the Wordpress dashboard. Then click Schedulicity Setup. Set your business key and select which plugin type you want. Then place the [schedule_now] shortcode on any page/post and your booking calendar will automatically appear.
Version: 2.1.1
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

	private static $add_script;
	/**
	 * Construct.
	 */
	function __construct() {

		add_action('admin_init', array( &$this, 'schedulicityplugin_init'), 0 );
		add_action('admin_menu', array( &$this, 'schedulicity_add_page'), 0 );
		add_action('plugins_loaded', array( &$this, 'schedulicity_widgets'), 0 );
		
		add_action('init', array( &$this, 'register_script'));
		add_action('wp_footer', array( &$this, 'print_script'));

		add_action( 'admin_enqueue_scripts', array(&$this,'load_styles'));
		
		// Admin Notices
		if ( ! empty( $_GET['hide_sched_check'] ) ) {
			update_option( 'sched_hide_check', 'hide' );
		}
		if ( ! empty( $_GET['hide_sched_check_bizkey'] ) ) {
			update_option( 'sched_hide_check_bizkey', 'hide' );
		}
		$sched_hide_check = get_option( 'sched_hide_check' );
		$sched_hide_check_bizkey = get_option( 'sched_hide_check_bizkey' );
		$sched_bizkey = array();
		$sched_bizkey = get_option( 'user_bizkey' );
		if ($sched_hide_check != 'hide') {
			add_action( 'admin_notices', array( &$this, 'sched_time_to_update' ), 0);
		}
		if (is_array($sched_bizkey)) {
			$sched_bizkey = array_filter($sched_bizkey);
		}

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
	
	/**
	 * Load JS
	 */
	static function register_script() {
		wp_register_script('schedulicity-js', plugins_url( '/js/schedulicity.js' , __FILE__ ), array('jquery'),null,true);
	}

	/**
	 * Load CSS
	 */
	public function load_styles() {
		
		$css = file_exists( get_stylesheet_directory() . '/schedulicity-admin.css' )
			? get_stylesheet_directory_uri() . '/schedulicity-admin.css'
			: plugins_url( '/css/schedulicity-admin.css', __FILE__ );
			
		wp_register_style( 'schedulicity-admin', $css, array(), '', 'all' );
		wp_enqueue_style( 'schedulicity-admin' );
	}

	static function print_script() {
		if ( ! self::$add_script ) {
			return;
		} else {
			wp_print_scripts('schedulicity-js');
		}
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
			<span style="float:right;">
				Like Us? <a href="https://wordpress.org/support/view/plugin-reviews/schedulicity-online-appointment-booking?filter=5#postform" style="color:green;font-weight:bold" target="_blank">Rate Us on WordPress.org!</a>
			</span>
			<img src="<?php echo plugins_url( 'schedulicitylogo.jpg', __FILE__ ); ?>" style="width: 200px; margin-bottom: 10px" />
			<h2>Schedulicity Plugin Setup</h2>
			</div>
			<form method="post" action="options.php">
				<?php settings_fields('schedulicity_options'); ?>
				
				<ol>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step One - Insert Your Biz Key</li>
						<?php $options = get_option('user_bizkey'); ?>			
						<ul style="font-size: 16px">
						Business Key: <input type="text" name="user_bizkey[bizkey]" id="bizkey_field" value="<?php echo $options['bizkey']; ?>" /><input type="submit" class="button-primary" value="<?php _e('Save Business Key') ?>" style="margin-left: 20px" /><span style="margin-left: 20px;font-size: 14px"><a href="?page=schedulicity_options_page&tab=advanced_setup#bizkey">What's my Business Key?</a></span>
						<p> 
							If you don't have a Schedulicity account <a href="http://www.schedulicity.com/?anic=wordpress" target="_blank">click here</a> to get 30 days free.
						</p>
						</ul>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Two - Create Your Shortcode</li>
							<ul style="font-size: 16px">
							<p>
								Choose from 97 button types and 2 widgets. Just click the button size you want and the style and watch the button shortcode change to the right.
								If you want to add a Scheduling widget click either the embedded or overlay widget below.
							</p>
							<div style="margin-left: -4%;margin-right: -1%">	
								<div class="type-panel">
				                    <span data-value="lg" class="selected"><img ng-src="//cdn.schedulicity.com/images/schedulenow_lt_green3_lg.png" src="//cdn.schedulicity.com/images/schedulenow_lt_green3_lg.png"></span>
				                    <span data-value="md"><img ng-src="//cdn.schedulicity.com/images/schedulenow_lt_green3_md.png" src="//cdn.schedulicity.com/images/schedulenow_lt_green3_md.png"></span>
				                    <span data-value="sm"><img ng-src="//cdn.schedulicity.com/images/schedulenow_lt_green3_sm.png" src="//cdn.schedulicity.com/images/schedulenow_lt_green3_sm.png"></span>
				                    <span data-value="url">URL only</span><br>
				                </div>
								<div class="button-panel" ng-show="showButtons">
				                    <div>
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_blue1_md.png" data-id="lt_blue1">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_blue4_md.png" data-id="lt_blue4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_blue7_md.png" data-id="lt_blue7">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_darktone3_md.png" data-id="lt_darktone3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_darktone4_md.png" data-id="lt_darktone4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_green2_md.png" data-id="lt_green2">
				                        <img class="button-img ng-scope selected" src="//cdn.schedulicity.com/images/schedulenow_lt_green3_md.png" data-id="lt_green3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_green4_md.png" data-id="lt_green4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_green8_md.png" data-id="lt_green8">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_red2_md.png" data-id="lt_red2">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_red3_md.png" data-id="lt_red3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_red7_md.png" data-id="lt_red7">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_yellow2_md.png" data-id="lt_yellow2">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_yellow3_md.png" data-id="lt_yellow3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_yellow5_md.png" data-id="lt_yellow5">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_lt_yellow7_md.png" data-id="lt_yellow7">
				                    </div>
				                    <div>
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_blue1_md.png" data-id="dk_blue1">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_blue4_md.png" data-id="dk_blue4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_blue7_md.png" data-id="dk_blue7">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_darktone1_md.png" data-id="dk_darktone1">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_darktone4_md.png" data-id="dk_darktone4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_green2_md.png" data-id="dk_green2">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_green3_md.png" data-id="dk_green3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_green4_md.png" data-id="dk_green4">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_green8_md.png" data-id="dk_green8">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_red2_md.png" data-id="dk_red2">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_red3_md.png" data-id="dk_red3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_red7_md.png" data-id="dk_red7">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_yellow2_md.png" data-id="dk_yellow2">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_yellow3_md.png" data-id="dk_yellow3">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_yellow5_md.png" data-id="dk_yellow5">
				                        <img class="button-img ng-scope" src="//cdn.schedulicity.com/images/schedulenow_dk_yellow7_md.png" data-id="dk_yellow7">
				                    </div>
				                </div>
				                <div class="shortcode-updater" id="button-shortcode-updater">
				                	<h2>Button ShortCode</h2>
				                	<p>Copy the below shortcode and paste it into any page or post.</p>
				                	<img src="http://cdn.schedulicity.com/images/schedulenow_lt_green3_lg.png" />
				                	<input type="text" name="sched_shortcode" id="sched_shortcode" value="[schedule_now_button style='lt_green3_md']" />
				                </div>
				                <div class="schedclear first"></div>
				                <div class="formsection primary button-panel">
					                <span id="widgetheader">
					                	<h2>Website - scheduling widget</h2>
					                	<p>Clients can easily book an appointment without ever leaving your website.</p>
					                </span>
				                	<span id="embeddedwidget" class="widgetselector">
				                		<div class="checkbox"></div>
				                		<h3>Embedded scheduling widget</h3>
				                		<p>Embed the scheduling widget on your site so that clients can book from their desktop, tablet, or mobile phone.</p>
				                	</span>
				                	<span id="overlaywidget" class="widgetselector">
				                		<div class="checkbox"></div>
				                		<h3>Overlay scheduling widget</h3>
				                		<p>Add the overlay widget to every page of your website for quick, accessible scheduling for your clients</p>
				                	</span>
					            </div>
					            <div class="shortcode-updater" id="widget-shortcode-updater">
				                	<h2>Widget ShortCode</h2>
				                	<p>Copy the below shortcode and paste it into any page or post.</p>
				                	<!--<img src="http://cdn.schedulicity.com/images/schedulenow_lt_green3_lg.png" />-->
				                	<input type="text" name="sched_shortcode" id="sched_widgetshortcode" value="[schedule_now widget='embedded']" />
				                </div>
				                <div class="schedclear"></div>
				            </div>
				            </ul>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Three - Start Scheduling!</li>
						<ul style="font-size: 16px">
						Once you've added the shortcode to a page or post, just give it a quick test to make sure it works. If you have any issues, email <a href="mailto:support@schedulicity.com">support@schedulicity.com</a> or call <strong>877-582-0494</strong>. When you're ready, send your customers to your site to start booking their appointments!
						</ul>
				</ol>
                <script type="text/javascript">
                	jQuery( document ).ready(function( $ ) {
						$('.button-img').click(function(){
							$('.button-img.selected').removeClass('selected');
							$(this).addClass('selected');
							button_show_shortcode();
						});
						$('div.checkbox').click(function(){
							$sibling = $(this).parent().siblings(".widgetselector").find(".checkbox");
							if($(this).hasClass('selected')){
								$(this).removeClass('selected');
								widget_show_shortcode();
							} else {
								$(this).addClass('selected');
								$sibling.removeClass('selected');
								widget_show_shortcode();
							}
						});

						$('.type-panel span').click(function(){
							$('.type-panel span.selected').removeClass('selected');
							$(this).addClass('selected');
							button_show_shortcode();
						});
						$('#sched_shortcode, #sched_widgetshortcode').click(function(){
							$(this).select();
						});
						$bizkey = '<?php echo $options["bizkey"] ?>';
						function button_show_shortcode(){
							$style = $('.button-img.selected').data('id');
							$size = $('.type-panel .selected').data('value');
							if($style == null){
								$style = 'lt_green3';
							}
							if($size == null){
								$size = 'md';
							}
							$url = '//cdn.schedulicity.com/images/schedulenow_'+$style+'_'+$size+'.png';
							$('#button-shortcode-updater #sched_shortcode').fadeTo(700, 0.5, function() { $('#button-shortcode-updater #sched_shortcode').fadeTo(600, 1); });
							$('#button-shortcode-updater img').attr('src',$url);
							$('#button-shortcode-updater #sched_shortcode').val("[schedule_now_button style='"+$style+"_"+$size+"']");
							$('#button-shortcode-updater > img').show();
							if($size != 'url'){
								$('#button-shortcode-updater > img').show();
								$('#button-shortcode-updater > img').fadeTo(700, 0.5, function() { $('#button-shortcode-updater > img').fadeTo(600, 1); });
							}
							if($size == 'url'){
								$('#sched_shortcode').val('https://www.schedulicity.com/scheduling/'+$bizkey);
								$('#button-shortcode-updater > img').hide();
							}
						}

						function widget_show_shortcode(){
							$('#sched_widgetshortcode').fadeTo(700, 0.5, function() { $('#sched_widgetshortcode').fadeTo(600, 1); });
							if($('#overlaywidget div.checkbox').hasClass('selected')){
								$('#sched_widgetshortcode').val('[schedule_now widget="overlay"]');
							} else {
								$('#sched_widgetshortcode').val('[schedule_now widget="embedded"]');
							}
						}
					});
                </script>
			</form>
			</div>
			<?php
			}
			else {
			?>
			<div style="background: #FFF;-moz-border-radius: 3px;border-radius: 3px;margin:5%;margin-top: 30px;padding: 20px;-moz-box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888;">
				<span style="float:right;">
					Like Us? <a href="https://wordpress.org/support/view/plugin-reviews/schedulicity-online-appointment-booking?filter=5#postform" style="color:green;font-weight:bold" target="_blank">Rate Us on WordPress.org!</a>
				</span>
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
				<div id="multipleaccounts">
					<h4>2. Use With Multiple Schedulicity Accounts</h4>
					<p style="margin-left: 20px; font-size: 14px">Using the Schedulicity plugin with multiple accounts is easy! Just add 
					<span style="color: #4b9500">bizkey=" "</span> to the [schedule_now] or [schedule_now_button] shortcodes and place your bizkey between the quotes. 
					Examples: <span style="background: #ffef73">[schedule_now <span style="color: #4b9500">bizkey="SSTJP8"</span>]</span> or 
					<span style="background: #ffef73">[schedule_now_button align="left" <span style="color: #4b9500">bizkey="SSTJP8"</span>]</span>. With this method you can add as many booking calendars or buttons
					to your site as needed.</p>
				</div>
				<div id="supportinfo">
					<h4>3. Support Issues</h4>
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
		self::$add_script = true;
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
					$content = '<script type="text/javascript" src="https://www.schedulicity.com/scheduling/widget/';
					$content .= $bizkey;
					$content .= '/popupscript"></script>';
					return $content;		
				}
				add_shortcode('schedule_now', 'overlay_widget');
			}
			
			else {
				function embedded_widget($atts) {
					$bizkey = get_option('user_bizkey');
					$sched_bizkey = $bizkey['bizkey'];
					extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
					$content = '<script type="text/javascript" src="https://www.schedulicity.com/scheduling/widget/';
					$content .= $bizkey;
					$content .= '/embedscript"></script>';
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
				if(empty($bizkey)){
					$bizkey = $sched_bizkey;
				}
				if ($widget == 'overlay') {
					$content = '<script type="text/javascript" src="https://www.schedulicity.com/scheduling/widget/';
					$content .= $bizkey;
					$content .= '/popupscript"></script>';
				}
				else {
					$content = '<script type="text/javascript" src="https://www.schedulicity.com/scheduling/widget/';
					$content .= $bizkey;
					$content .= '/embedscript"></script>';
				}
				return $content;	
			}
			add_shortcode('schedule_now', 'standard_widget');
		
		}
		function sched_button_left($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_left_sc = '<div style="text-align: left"><a href="http://www.schedulicity.com/scheduling/'.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				return $sched_button_left_sc;
		}
				
		function sched_button_center($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_center_sc = '<div style="text-align: center"><a href="http://www.schedulicity.com/scheduling/'.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				return $sched_button_center_sc;
		}
		
		function sched_button_right($atts) {
				$user_bizkey = get_option('user_bizkey');
				$sched_bizkey = $user_bizkey['bizkey'];
				extract(shortcode_atts( array('bizkey' => $sched_bizkey) , $atts));
				$sched_button_right_sc = '<div style="text-align: right"><a href="http://www.schedulicity.com/scheduling/'.$bizkey.'" title="Online scheduling" target="_blank"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
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
					$oldstyle = strpos($style, 'button');
					if($oldstyle !== false){
						$style = str_replace('button','', $style);
						if ($style < 10){
							$style = sprintf('%02d', $style);
						}
						$image_url = '//d2k394ztg01v3m.cloudfront.net/images/schedulenow_'.$style.'_'.$size.'.png';
					} elseif ($oldstyle === false){
						$image_url = '//cdn.schedulicity.com/images/schedulenow_'.$style.'.png';
					}					
					
					$sched_button = '<div style="text-align: '.$alignment.'"><a href="https://www.schedulicity.com/scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="'.$image_url.'" alt="Schedule online now" border="0" /></a></div>';
				}
				else {
					$sched_button = '<div style="text-align: '.$alignment.'"><a href="https://www.schedulicity.com/scheduling/'.$bizkey.'" title="Online scheduling" target="_blank" id="schednowlink"><img src="http://www.schedulicity.com/Business/Images/ScheduleNow_LG.png" alt="Schedule online now" border="0" /></a></div>';
				}
				
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