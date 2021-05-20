<?php 

class WonderPlugin_Videoembed_View {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function add_metaboxes() {
		add_meta_box('overview_features', __('Wonder Video Embed Features', 'wonderplugin_videoembed'), array($this, 'show_features'), 'wonderplugin_videoembed_overview', 'features', '');
		add_meta_box('overview_news', __('WonderPlugin News', 'wonderplugin_videoembed'), array($this, 'show_news'), 'wonderplugin_videoembed_overview', 'news', '');
	}
	
	function show_upgrade_to_commercial() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Use on commercial websites</li>
			<li>Remove the wonderplugin.com watermark</li>
			<li>Techincal support</li>
			<li><a href="http://www.wonderplugin.com/wordpress-video-player/order/" target="_blank">Upgrade to Commercial Version</a></li>
		</ul>
		<?php
	}
	
	function show_news() {
		
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		$rss = fetch_feed( 'http://www.wonderplugin.com/feed/' );
		
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) )
		{
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
		}
		?>
		
		<ul class="wonderplugin-feature-list">
		    <?php if ( $maxitems > 0 ) {
		        foreach ( $rss_items as $item )
		        {
		        	?>
		        	<li>
		                <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" 
		                    title="<?php printf( __( 'Posted %s', 'wonderplugin_videoembed' ), $item->get_date('j F Y | g:i a') ); ?>">
		                    <?php echo esc_html( $item->get_title() ); ?>
		                </a>
		                <p><?php echo esc_html( $item->get_description() ); ?></p>
		            </li>
		        	<?php 
		        }
		    } ?>
		</ul>
		<?php
	}
	
	function show_features() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Support YouTube, Vimeo, Wistia, iFrame and self-hosted MP4/WebM videos</li>
			<li>Work on mobile, tablets and all major web browsers, including iPhone, iPad, Android, Firefox, Safari, Chrome, Opera, Internet Explorer and Microsoft Edge</li>
			<li>Fully responsive</li>
			<li>Insert videos to sidebar widget</li>
			<li>Insert videos into pages and posts</li>
			<li>Play in lightbox popup (working together with our premium plugin <a href="https://www.wonderplugin.com/wordpress-lightbox/?product=videoembed" target="_blank">Wonder Lightbox</a>)</li>
			<li>Auto lightbox popup on page load</li>
			<li>Auto close video popup when the video ends (support YouTube, Vimeo and MP4/WebM videos)</li>
		</ul>
		<?php
	}
	
	function show_contact() {
		?>
		<p>Technical support is available for Commercial Version users at support@wonderplugin.com. Please include your license information, WordPress version, link to your webpage, all related error messages in your email.</p> 
		<?php
	}
		
	function print_frontend() {
		
		
	}
	
	function print_overview() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-videoembed" class="icon32"><br /></div>
		
		<h2><?php echo __( 'Wonder Video Embed', 'wonderplugin_videoembed' ) . (WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE == "L" ? "" : ((WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE == "C") ? " Commercial Version" : " Free Version")) . " " . WONDERPLUGIN_VIDEOEMBED_VERSION; ?> </h2>
		 
		<div id="welcome-panel" class="welcome-panel">
			<div class="welcome-panel-content">
				<h3>WordPress Video Embed Plugin & Widget</h3>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4>Get Started</h4>
						<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=wonderplugin_videoembed_show_quick_start'); ?>">Quick Start</a>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						
						<h4>More Actions</h4>
						<ul>
							<li><a href="http://www.wonderplugin.com/wordpress-video-player/help/" target="_blank" class="welcome-icon welcome-learn-more">Help Document</a></li>
							<?php  if (WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE == "F") { ?>
							<li><a href="http://www.wonderplugin.com/wordpress-video-player/order/" target="_blank" class="welcome-icon welcome-view-site">Upgrade to Commercial Version</a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder columns-2">
	 
	                 <div class="postbox-container">
	                    <?php 
	                    do_meta_boxes( 'wonderplugin_videoembed_overview', 'features', '' ); 
	                    ?>
	                </div>
	 
	                <div class="postbox-container">
	                    <?php 
	                    do_meta_boxes( 'wonderplugin_videoembed_overview', 'news', ''); 
	                    ?>
	                </div>
	 
	        </div>
        </div>
            
		<?php
	}
	
	function print_quick_start() {

		?>
		<div class="wrap">
		<div id="icon-wonderplugin-videoembed" class="icon32"><br /></div>
		
		<h2><?php _e( 'Quick Start Guide', 'wonderplugin_videoembed' ); ?> </h2>
		
		<div style="margin:8px 0px 24px 24px;">
		<ul style="list-style-type: square;">
		<?php if (WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE == "F") { ?>
		<li><a href="#removewatermark">Remove Free Version Watermark</a></li>
		<?php } ?>
		<li><a href="#videowidget">Insert Video to Widget</a></li>
		<li><a href="#videopost">Insert Video to Post/Page</a></li>
		<li><a href="#youtubevideo">Add YouTub Video</a></li>
		<li><a href="#vimeovideo">Add Vimeo Video</a></li>
		<li><a href="#wistiavideo">Add Wistia Video</a></li>
		<li><a href="#mp4video">Add MP4/WebM Video</a></li>
		<li><a href="#videolightbox">Play in Lightbox Popup</a></li>
		<li><a href="#lightboxgroup">Lightbox Group</a></li>
		<li><a href="#lightboxoptions">Lightbox Advanced Options</a></li>
		</ul>
		</div>
		
		<?php if (WONDERPLUGIN_VIDEOEMBED_VERSION_TYPE == "F") { ?>
		<h3 id="removewatermark">Remove Free Version Watermark</h3>
		<p>To remove the Free Version watermark, please <a href="https://www.wonderplugin.com/wordpress-video-player/order/" target="_blank">Upgrade to Commercial Version</a>.</p>
		<?php } ?>
		
		<h3 id="videowidget">Insert Video to Widget</h3>
		<p>To insert a video to WordPress sidebar widget, log into WordPress backend, click menu Appearance -> Widget. Drag the widget Wonder Video Embed to the Widget Area.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-video-widget.png" /></div>
		
		<h3 id="videopost">Insert Video to Post/Page</h3>
		<p>To insert a video to post or page, in WordPress post editor or page editor, switch to Visual mode, then click the button Wonder Video Embed.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-post-video.png" /></div>
		
		<h3 id="youtubevideo">Add YouTube Video</h3>
		<p>To add YouTube, copy the YouTube video url from the web browser and add it to the plugin.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-add-youtube.png" /></div>
		
		<h3 id="vimeovideo">Add Vimeo Video</h3>
		<p>On the Vimeo video, click the Share button, in the Share this Video popup, copy the src value from the embed code and add it to the plugin.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-add-vimeo.png" /></div>
		
		<h3 id="wistiavideo">Add Wistia Video</h3>
		<p>Log into your Wistia account, edit the video, click the button Video Actions, then click Embed & Share from the drop-down menu. In the popup, copy the src value from the Inline Embed code and add it to the plugin.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-add-wistia.png" /></div>
		
		<h3 id="mp4video">Add MP4/WebM Video</h3>
		<ul style="list-style-type: square;padding-left:18px;">
		<li>To play MP4/WebM video, in the plugin, Video tab, select the option MP4/WebM Video, then click the button Select an MP4 file to upload or select an MP4 file from WordPress Media Library.</li>
		<li>Firefox and Opera do not support MP4 format. To play with HTML5 in Firefox and Opera, an extra WebM format video must be provided. If the WebM video is not provided, in Firefox and Opera, Flash will be used for playing the video.</li>
		<li>Your vidoe must be HTML5 compatible. If your video is not playing or not playing correctly, please view the tutorial for how to convert the video: <a href="https://www.wonderplugin.com/wordpress-tutorials/how-to-convert-video-to-html5-compatible/" target="_blank">https://www.wonderplugin.com/wordpress-tutorials/how-to-convert-video-to-html5-compatible/</a></li>
		<li>To play a video file hosted on a remote website, you can directly enter the video URL to the text box.</li>
		</ul>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-add-mp4.png" /></div>
		
		<h3 id="videolightbox">Play in Lightbox Popup</h3>
		<ul style="list-style-type: square;padding-left:18px;">
		<li style="color:#ff0000;">To play video in Lightbox, the plugin <a href="https://www.wonderplugin.com/wordpress-lightbox/" target="_blank">Wonder Lightbox</a> must be installed and activated.</li>
		<li>In the plugin, switch to Lightbox tab, check the option Play in Lightbox Popup.</li>
		<li>To select an anchor display image for the Lightbox, click the button Select an image file for the text box Display Image URL.</li>
		</ul>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-video-lightbox.png" /></div>
		
		<h3 id="lightboxgroup">Lightbox Group</h3>
		<p>If you have multiple videos belonging to the same group, you can enter a group name for the Lightbox and display all the videos in a Lightbox gallery. You can also check the option Show thumbnail navigation to display thumbnails at the bottom of the Lightbox.</p>
		<p>The group name can be any text.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-lightbox-group-name.png" /></div>
		
		<h3 id="lightboxoptions">Lightbox Advanced Options</h3>
		<p>By entering data tags to the text box Lightbox Advanced Options, you can further customise the Lightbox. For example, the data tags in the following image will change the overlay background color to red #ff0000 and change the Lightbox background color to dark grey #333.</p>
		<div class="wonderplugin-tutorial-image"><img src="<?php echo WONDERPLUGIN_VIDEOEMBED_URL; ?>images/wordpress-lightbox-advanced-options.png" /></div>
		<p>The options are as following. For each option, you need to add a data tag prefix data-, and the value need to enclosed with quotes.</p>
		<table class="wonderplugin-tutorial-table">
			<thead>
				<tr>
					<th width="18%">Name</th>
					<th width="39%">Description</th>
					<th width="43%">Default value</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>overlaybgcolor</code></td>
					<td>Overlay background color</td>
					<td><code>#000</code></td>
				</tr>
				<tr>
					<td><code>overlayopacity</code></td>
					<td>Overlay transparency, from 0 to 1.</td>
					<td><code>0.9</code></td>
				</tr>
				<tr>
					<td><code>bgcolor</code></td>
					<td>Background color of the lightbox</td>
					<td><code>#fff</code></td>
				</tr>
				<tr>
					<td><code>bordersize</code></td>
					<td>Border width of the lightbox</td>
					<td><code>8</code></td>
				</tr>
				<tr>
					<td><code>closeonoverlay</code></td>
					<td>Whent the value is true, the lightbox can be closed by clicking
						the overlay background. Otherwise, it can only be closed by
						clicking the close button.</td>
					<td><code>true</code></td>
				</tr>
				<tr>
					<td><code>alwaysshownavarrows</code></td>
					<td>Always show the navigation arrows, otherwise it will show the
						navigation arrows on mouse over (on desktop)</td>
					<td><code>false</code></td>
				</tr>
				<tr>
					<td><code>autoslide</code></td>
					<td>Automatically slideshow on popup</td>
					<td><code>false</code></td>
				</tr>
				<tr>
					<td><code>showplaybutton</code></td>
					<td>Display the play slideshow button</td>
					<td><code>true</code></td>
				</tr>
				<tr>
					<td><code>slideinterval</code></td>
					<td>Interval (ms) when auto slideshow</td>
					<td><code>5000</code></td>
				</tr>
				<tr>
					<td><code>showtimer</code></td>
					<td>Show the line timer for image slideshow</td>
					<td><code>true</code></td>
				</tr>
				<tr>
					<td><code>timerposition</code></td>
					<td>The position of the line timer: top or bottom</td>
					<td><code>bottom</code></td>
				</tr>
				<tr>
					<td><code>timercolor</code></td>
					<td>The color of line timer</td>
					<td><code>#dc572e</code></td>
				</tr>
				<tr>
					<td><code>fullscreenmode</code></td>
					<td>Whether to display the lightbox in fullscreen mode. In
						fullscreen mode, the close button will be displayed on the top
						right corner of the web browser.</td>
					<td><code>false</code></td>
				</tr>
				<tr>
					<td><code>titlestyle</code></td>
					<td>Define title position. The available options are <code>bottom</code>,
						<code>inside</code>, <code>left</code>, <code>right</code>.
					</td>
					<td><code>bottom</code></td>
				</tr>
				<tr>
					<td><code>imagepercentage</code></td>
					<td>When the option titlestyle is right or left, the option defines
						the percentage of the image or video width.</td>
					<td><code>75</code></td>
				</tr>
				<tr>
					<td><code>titleprefix</code></td>
					<td>When displaying in a gallery, the prefix will be added to the
						beginning of the titles. The macro variables %NUM and %TOTAL will
						be replaced by the index of the current image/video and the total
						number of the gallery.</td>
					<td><code>%NUM / %TOTAL</code></td>
				</tr>
				<tr>
					<td><code>titlecss</code></td>
					<td>Title CSS</td>
					<td><code>color:#333; font-size:16px;
							font-family:Arial,Helvetica,sans-serif; overflow:hidden;
							text-align:left;</code></td>
				</tr>
				<tr>
					<td><code>descriptioncss</code></td>
					<td>Description CSS</td>
					<td><code>color:#333; font-size:12px;
							font-family:Arial,Helvetica,sans-serif; overflow:hidden;
							text-align:left; margin:4px 0px 0px; padding: 0px;</code></td>
				</tr>
				<tr>
					<td><code>titleinsidecss</code></td>
					<td>Title CSS when titlestyle is inside</td>
					<td><code>color:#fff; font-size:16px;
							font-family:Arial,Helvetica,sans-serif; overflow:hidden;
							text-align:left;</code></td>
				</tr>
				<tr>
					<td><code>descriptioninsidecss</code></td>
					<td>Description CSS when titlestyle is inside</td>
					<td><code>color:#fff; font-size:12px;
							font-family:Arial,Helvetica,sans-serif; overflow:hidden;
							text-align:left; margin:4px 0px 0px; padding: 0px;</code></td>
				</tr>
			</tbody>

		</table>

		<?php 
	}
	
	function print_edit_settings() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-videoembed" class="icon32"><br /></div>
			
		<h2><?php _e( 'Settings', 'wonderplugin_videoembed' ); ?></h2>
		
		<?php
		
		if ( isset($_POST['save-videoembed-options']) && check_admin_referer('wonderplugin-videoembed', 'wonderplugin-videoembed-settings'))
		{		
			unset($_POST['save-videoembed-options']);
			$this->controller->save_settings($_POST);
			echo '<div class="updated"><p>Settings saved.</p></div>';
		}
								
		$settings = $this->controller->get_settings();
		$keepdata = $settings['keepdata'];
		$disableupdate = $settings['disableupdate'];
		$addjstofooter = $settings['addjstofooter'];
		$zindex = $settings['zindex'];
		
		?>
				
        <form method="post">
        
        <?php wp_nonce_field('wonderplugin-videoembed', 'wonderplugin-videoembed-settings'); ?>
        
        <table class="form-table">
		
		<tr>
			<th>Data option</th>
			<td><label><input name='keepdata' type='checkbox' id='keepdata' <?php echo ($keepdata == 1) ? 'checked' : ''; ?> /> Keep data when deleting the plugin</label>
			</td>
		</tr>
		
	
		<tr>
			<th>Scripts position</th>
			<td><label><input name='addjstofooter' type='checkbox' id='addjstofooter' <?php echo ($addjstofooter == 1) ? 'checked' : ''; ?> /> Add plugin js scripts to the footer (wp_footer hook must be implemented by the WordPress theme)</label>
			</td>
		</tr>
		
		<tr>
			<th>z-index of the dialog in the post/page editor</th>
			<td><label><input name='zindex' type='number' id='zindex' value='<?php echo $zindex; ?>' /></label>
			</td>
		</tr>
		
        </table>
        
        <p class="submit"><input type="submit" name="save-videoembed-options" id="save-videoembed-options" class="button button-primary" value="Save Changes"  /></p>
        
        </form>
        
		</div>
		<?php
	}
		
	function print_register() {
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-videoembed" class="icon32"><br /></div>
				
		<script>	
		function validateLicenseForm() {
			
			if (jQuery.trim(jQuery("#wonderplugin-videoembed-key").val()).length <= 0)
			{
				jQuery("#license-form-message").html("<p>Please enter your license key</p>").show();
				return false;
			}

			return true;
		}
		</script>

		<h2><?php _e( 'Register', 'wonderplugin_videoembed' ); ?></h2>
		<?php
				
		if (isset($_POST['save-videoembed-license']) && check_admin_referer('wonderplugin-videoembed', 'wonderplugin-videoembed-register'))
		{		
			unset($_POST['save-videoembed-license']);

			$ret = $this->controller->check_license($_POST);
			
			if ($ret['status'] == 'valid')
				echo '<div class="updated"><p>The key has been saved.</p><p>WordPress caches the update information. If you still see the message "Automatic update is unavailable for this plugin", please wait for some time, then click the below button "Force WordPress To Check For Plugin Updates".</p></div>';
			else if ($ret['status'] == 'expired')
				echo '<div class="error"><p>Your free upgrade period has expired, please renew your license.</p></div>';
			else if ($ret['status'] == 'invalid')
				echo '<div class="error"><p>The key is invalid.</p></div>';
			else if ($ret['status'] == 'abnormal')
				echo '<div class="error"><p>You have reached the maximum website limit of your license key. Please log into the membership area and upgrade to a higher license.</p></div>';
			else if ($ret['status'] == 'misuse')
				echo '<div class="error"><p>There is a possible misuse of your license key, please contact support@wonderplugin.com for more information.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>Please enter your license key.</p></div>';
			else if (isset($ret['message']))
				echo '<div class="error"><p>' . $ret['message'] . '</p></div>';
		}
		else if (isset($_POST['deregister-videoembed-license']) && check_admin_referer('wonderplugin-videoembed', 'wonderplugin-videoembed-register'))
		{	
			$ret = $this->controller->deregister_license($_POST);
			
			if ($ret['status'] == 'success')
				echo '<div class="updated"><p>The key has been deregistered.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>The license key is empty.</p></div>';
		}
		
		$settings = $this->controller->get_settings();
		$disableupdate = $settings['disableupdate'];
		
		$key = '';
		$info = $this->controller->get_plugin_info();
		if (!empty($info->key) && ($info->key_status == 'valid' || $info->key_status == 'expired'))
			$key = $info->key;
		
		?>
		
		<?php 
		if ($disableupdate == 1)
		{
			echo "<h3 style='padding-left:10px;'>The plugin version check and update is currently disabled. You can enable it in the Settings menu.</h3>";
		}
		else
		{
		?> <div style="padding-left:10px;padding-top:12px;"> <?php
			if (empty($key)) { ?>
				<form method="post" onsubmit="return validateLicenseForm()">
				<?php wp_nonce_field('wonderplugin-videoembed', 'wonderplugin-videoembed-register'); ?>
				<div class="error" style="display:none;" id="license-form-message"></div>
				<table class="form-table">
				<tr>
					<th>Enter Your License Key:</th>
					<td><input name="wonderplugin-videoembed-key" type="text" id="wonderplugin-videoembed-key" value="" class="regular-text" /> <input type="submit" name="save-videoembed-license" id="save-videoembed-license" class="button button-primary" value="Register"  />
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
					<p><strong>By entering your license key and registering your website, you agree to the following terms:</strong></p>
					<ul style="list-style-type:square;margin-left:20px;">
						<li>The key is unique to your account. You may not distribute, give away, lend or re-sell it. We reserve the right to monitor levels of your key usage activity and take any necessary action in the event of abnormal usage being detected.</li>
						<li>By entering your license key and clicking the button "Register", your domain name, the plugin name and the key will be sent to the plugin website <a href="https://www.wonderplugin.com" target="_blank">https://www.wonderplugin.com</a> for verification and registration.</li>
						<li>You can view all your registered domain name(s) and plugin(s) by logging into <a href="https://www.wonderplugin.com/members/" target="_blank">WonderPlugin Members Area</a>, left menu "License Key and Register".</li>
						<li>For more information, please view <a href="https://www.wonderplugin.com/terms-of-use/" target="_blank">Terms of Use</a>.</li>
					</ul>
					<p style="margin:8px 0;">To find your license key, please log into <a href="https://www.wonderplugin.com/members/" target="_blank">WonderPlugin Members Area</a>, then click "License Key and Register" on the left menu.</p>
					<p style="margin:8px 0;">After registration, when there is a new version available and you are in the free upgrade period, you can directly upgrade the plugin in your WordPress dashboard. If you do not register, you can still upgrade the plugin manually: <a href="https://www.wonderplugin.com/wordpress-carousel-plugin/how-to-upgrade-to-a-new-version-without-losing-existing-work/" target="_blank">How to upgrade to a new version without losing existing work</a>.</p>
					</td>
				</tr>
				</table>
				</form>
			<?php } else { ?>
				<form method="post">
				<?php wp_nonce_field('wonderplugin-videoembed', 'wonderplugin-videoembed-register'); ?>
				<p>You have entered your license key and this domain has been successfully registered. &nbsp;&nbsp;<input name="wonderplugin-videoembed-key" type="hidden" id="wonderplugin-videoembed-key" value="<?php echo esc_html($key); ?>" class="regular-text" /><input type="submit" name="deregister-videoembed-license" id="deregister-videoembed-license" class="button button-primary" value="Deregister"  /></p>
				</form>
				<?php if ($info->key_status == 'expired') { ?>
				<p><strong>Your free upgrade period has expired.</strong> To get upgrades, please <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>.</p>
				<?php } ?>
			<?php } ?>
			</div>
		<?php } ?>
		
		<div style="padding-left:10px;padding-top:30px;">
		<a href="<?php echo admin_url('update-core.php?force-check=1'); ?>"><button class="button-primary">Force WordPress To Check For Plugin Updates</button></a>
		</div>
					
		<div style="padding-left:10px;padding-top:20px;">
        <ul style="list-style-type:square;font-size:16px;line-height:28px;margin-left:24px;">
		<li><a href="https://www.wonderplugin.com/how-to-upgrade-a-commercial-version-plugin-to-the-latest-version/" target="_blank">How to upgrade to the latest version</a></li>
	    <li><a href="https://www.wonderplugin.com/register-faq/" target="_blank">Where can I find my license key and other frequently asked questions</a></li>
	    </ul>
        </div>
        
		</div>
		
		<?php
	}
}
