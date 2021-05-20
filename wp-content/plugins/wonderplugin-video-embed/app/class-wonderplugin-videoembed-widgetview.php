<?php 

class WonderPlugin_Videoembed_Widgetview {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_defaults() {
		
		return array(
			'videotype' => 'iframe',
			'iframe' => '',
			'mp4' => '',
			'webm' => '',
			'poster' => '',
				
			'videowidth' => 480,
			'videoheight' => 270,
			'keepaspectratio' => 1,
			'autoplay' => 0,
			'loop' => 0,
			'videocss' => 'position:relative;display:block;background-color:#000;overflow:hidden;max-width:100%;margin:0 auto;',
			'playbutton' => WONDERPLUGIN_VIDEOEMBED_URL . 'engine/playvideo-64-64-0.png',
				
			'lightbox' => 0,
			'lightboxsize' => 1,
			'lightboxwidth' => 960,
			'lightboxheight' => 540,
			'autoopen' => 0,
			'autoopendelay' => 1000,
			'autoclose' => 0,
			'lightboxtitle' => '',
			'lightboxgroup' => '',
			'lightboxshownavigation' => 0,
			'lightboxoptions' => '',
			'showimage' => ''
		);		
	}
	
	function get_boolparams() {

		return array('lightbox', 'lightboxsize', 'autoopen', 'autoclose', 'lightboxshownavigation', 'autoplay', 'loop', 'keepaspectratio');
	}
	
	function get_htmlparams() {
		
		return array('lightboxtitle');
	}
	
	function double_escape_html($str) {
		
		$str = str_replace('"', '\\"', $str);
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('<', '&lt;', $str);
		$str = str_replace('>', '&gt;', $str);
		$str = str_replace('"', '&quot;', $str);
		return str_replace('&', '&amp;', $str);
	}
	
	function un_double_escape_html($str) {
	
		$str = str_replace('&amp;', '&', $str);
		return str_replace('&amp;', '&', $str);
	}
	
	function un_double_escape_html_for_display($str) {
		
		$str = $this->un_double_escape_html($str);
		return str_replace('&quot;', '"', $str);
	}
	
	function unescape_html_for_edit($str) {
		
		$str = str_replace('&amp;', '&', $str);
		return str_replace('\\&quot;', '&quot;', $str);		
	}
		
	function process_wistiaurl($url, $autoplay, $loop) {
		
		$glue = (strpos($url, '?') !== false) ? '&' : '?';
				
		if ($autoplay)
		{
			$url .= $glue . 'autoPlay=true';
			$glue = '&';
		}
		
		if ($loop)
		{
			$url .= $glue . 'endVideoBehavior=loop';
		}
		
		return $url;
			
	}
	
	function process_vimeourl($url, $autoplay, $loop) {
		
		$id = null; $qs = null; $result = $url;
		
		preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $url, $matches);
		
		if (!empty($matches) && !empty($matches[5]))
			$id = $matches[5];
		
		if (!empty($id))
		{
			$result = 'https://player.vimeo.com/video/' . $id;
			
			$query_array = array();
				
			if ($autoplay)
				$query_array[] = 'autoplay=1';
				
			if ($loop)
				$query_array[] = 'loop=1';
				
			$parts = parse_url($url);
			if (isset($parts['query']))
				parse_str($parts['query'], $qs);
				
			if (!empty($qs))
			{
				foreach( $qs as $key => $value )
					$query_array[] = urlencode( $key ) . '=' . urlencode( $value );
			}
				
			if (!empty($query_array))
				$result .= '?' . implode( '&', $query_array );
		}
		
		return $result;
	}
	
	function process_youtubeurl($url, $autoplay, $loop) {
		
		$id = null; $qs = null; $result = $url;
		
		$parts = parse_url($url);
		
		if (isset($parts['query'])) 
		{
			parse_str($parts['query'], $qs);
				
			if (isset($qs['v']))
				$id =  $qs['v'];
			else if (isset($qs['vi']))
				$id = $qs['vi'];
			
			unset($qs['v']);
			unset($qs['vi']);
		}
		
		if (empty($id) && isset($parts['path']))
		{
			$path = explode('/', trim($parts['path'], '/'));
			$id = $path[count($path)-1];
		}

		if (!empty($id))
		{
			$result = 'https://www.youtube.com/embed/' . $id;
			
			$query_array = array();
			
			if ($autoplay)
				$query_array[] = 'autoplay=1';
			
			if ($loop)
				$query_array[] = 'loop=1&playlist=' . $id;
					
			if (!empty($qs))
			{
				foreach( $qs as $key => $value )
					$query_array[] = urlencode( $key ) . '=' . urlencode( $value );
			}
			
			if (!empty($query_array))
				$result .= '?' . implode( '&', $query_array );
		}
		
		return $result;
	}
	
	function process_iframeurl($url, $autoplay, $loop) {
						
		if ((strpos(strtolower($url), 'youtube.com') !== false) || (strpos(strtolower($url), 'youtu.be') !== false))
			$url = $this->process_youtubeurl($url, $autoplay, $loop);
		else if (strpos(strtolower($url), 'vimeo.com') !== false)
			$url = $this->process_vimeourl($url, $autoplay, $loop);
		else if (strpos(strtolower($url), 'wistia') !== false)
			$url = $this->process_wistiaurl($url, $autoplay, $loop);
		
		return $url;
	}
	
	function lightbox_data_tags($atts) {

		$content = ' data-autoplay="' . ((!empty($atts['autoplay'])) ? 'true' : 'false') . '"';
		
		if (!empty($atts['loop']))
			$content .= ' data-loopvideo="true"';
		
		if (!empty($atts['lightboxsize']))
			$content .= ' data-width=' . $atts['lightboxwidth'] . ' data-height=' . $atts['lightboxheight'];
		
		if (!empty($atts['autoopen']))
			$content .= ' data-autoopen="true" data-autoopendelay=' . $atts['autoopendelay'];
		
		if (!empty($atts['autoclose']))
			$content .= ' data-autoclose="true"';
		
		if (!empty($atts['lightboxgroup']))
			$content .= ' data-group="' . $atts['lightboxgroup'] . '"';
				
		if (!empty($atts['lightboxshownavigation']) && !empty($atts['showimage']))
			$content .= ' data-thumbnail="' . $atts['showimage'] . '"';
		
		if (!empty($atts['lightboxtitle']))
			$content .= ' title="' . $this->un_double_escape_html($atts['lightboxtitle']) . '"';
		
		if (!empty($atts['lightboxoptions']))
			$content .= ' ' . $this->un_double_escape_html_for_display($atts['lightboxoptions']);
			
		return $content;
	}
	
	function shortcode_handler($atts) {
		
		$content = '<div class="wonderplugin-video" style="width:' . $atts['videowidth'] . 'px;height:' . $atts['videoheight'] . 'px;' . $atts['videocss'] . '"';
		
		if (!empty($atts['keepaspectratio']) && !empty($atts['videowidth']) && !empty($atts['videoheight']))
			$content .= ' data-aspectratio="' . ($atts['videowidth'] / $atts['videoheight']) . '"';
		
		if (!empty($atts['embedtype']))
			$content .= ' data-embedtype="' . $atts['embedtype'] . '"';
		
		$content .= '>';
			
		if (!empty($atts['iframe']))
		{
			if (!empty($atts['lightbox']))
			{
				$iframe_url = $this->process_iframeurl($atts['iframe'], false, false);
				
				$content .= '<a class="wpve-lightbox wplightbox" href="' . $iframe_url . '"';
				
				$content .= $this->lightbox_data_tags($atts);
					
				$content .= '>';
				
				if (!empty($atts['showimage']))
					$content .= '<div class="wpve-poster"><img src="' . $atts['showimage'] . '" /></div>';
				
				if (!empty($atts['playbutton']))
					$content .= '<div class="wpve-playbutton" style="position:absolute;top:0;left:0;width:100%;height:100%;background-image:url(' . $atts['playbutton'] . ');background-position:center center;background-repeat:no-repeat;"></div>';
				
				$content .= '</a>';
			}
			else
			{
				$iframe_url = $this->process_iframeurl($atts['iframe'], (!empty($atts['autoplay']) ? true: false), (!empty($atts['loop']) ? true: false));
				
				$content .= '<iframe class="wpve-iframe" width="100%" height="100%" src="' . $iframe_url . '" frameborder="0" allowfullscreen></iframe>';
			}
		}
		else if (!empty($atts['videotype']) && $atts['videotype'] == 'mp4')
		{
			if (!empty($atts['lightbox']))
			{
				$content .= '<a class="wpve-lightbox wplightbox" href="' . $atts['mp4'] . '"';
				
				if (!empty($atts['webm']))
					$content .= ' data-webm="' . $atts['webm'] . '"';

				if (!empty($atts['poster']))
					$content .= ' data-html5videoposter="' . $atts['poster'] . '"';
				
				$content .= $this->lightbox_data_tags($atts);
				
				$content .= '>';
				
				if (!empty($atts['showimage']))
					$content .= '<div class="wpve-poster"><img src="' . $atts['showimage'] . '" /></div>';
				
				if (!empty($atts['playbutton']))
					$content .= '<div class="wpve-playbutton" style="position:absolute;top:0;left:0;width:100%;height:100%;background-image:url(' . $atts['playbutton'] . ');background-position:center center;background-repeat:no-repeat;"></div>';
				
				$content .= '</a>;';
			}
			else
			{				
				$content .= '<div class="wpve-videoplayer" style="display:block;position:relative;width:100%;height:100%;" data-mp4="' . $atts['mp4'] . '"';
				
				$content .= ' data-skinfolder="' . WONDERPLUGIN_VIDEOEMBED_URL . 'engine/"';
				
				if (!empty($atts['webm']))
					$content .= ' data-webm="' . $atts['webm'] . '"';
				
				if (!empty($atts['autoplay']))
					$content .= ' data-autoplay="true"';
				
				if (!empty($atts['loop']))
					$content .= ' data-loop="true"';
				
				if (!empty($atts['poster']))
					$content .= ' data-poster="' . $atts['poster'] . '"';
				
				if (!empty($atts['playbutton']))
					$content .= ' data-playbutton="' . $atts['playbutton'] . '"';
				
				$content .= '></div>';	
			}
		}
		
		$content .= '</div>';
		
					
		return $content;
	}
	
	function show_widgetfront($args, $instance) {
		
		$content = '[wonderplugin_video embedtype="widget"';
		
		if ($instance['videotype'] == 'iframe')
		{
			$content .= ' iframe="' . $instance['iframe'] . '"';
		}
		else
		{
			$content .= ' videotype="mp4"';
			
			if (!empty($instance['mp4']))
				$content .= ' mp4="' . $instance['mp4'] . '"';
			else
				$content .= ' mp4=""';
			
			if (!empty($instance['webm']))
				$content .= ' webm="' . $instance['webm'] . '"';
			else
				$content .= ' webm=""';
			
			if (!empty($instance['poster']))
				$content .= ' poster="' . $instance['poster'] . '"';
			else
				$content .= ' poster=""';
		}
		
		if ($instance['lightbox'])
			$content .= ' lightbox=1';
		else
			$content .= ' lightbox=0';

		if ($instance['lightboxsize'])
			$content .= ' lightboxsize=1';
		else
			$content .= ' lightboxsize=0';
		
		$content .= ' lightboxwidth=' . $instance['lightboxwidth'] . ' lightboxheight='  . $instance['lightboxheight'];
			
		if ($instance['autoopen'])
			$content .= ' autoopen=1';
		else
			$content .= ' autoopen=0';

		$content .= ' autoopendelay=' . $instance['autoopendelay'];
					
		if ($instance['autoclose'])
			$content .= ' autoclose=1';
		else
			$content .= ' autoclose=0';
		
		if (!empty($instance['lightboxtitle']))
			$content .= ' lightboxtitle="' . $instance['lightboxtitle'] . '"';
		else
			$content .= ' lightboxtitle=""';
		
		if (!empty($instance['lightboxgroup']))
			$content .= ' lightboxgroup="' . $instance['lightboxgroup'] . '"';
		else
			$content .= ' lightboxgroup=""';
		
		if ($instance['lightboxshownavigation'])
			$content .= ' lightboxshownavigation=1';
		else
			$content .= ' lightboxshownavigation=0';
		
		if (!empty($instance['showimage']))
			$content .= ' showimage="' . $instance['showimage'] . '"';
		else
			$content .= ' showimage=""';
		
		if (!empty($instance['lightboxoptions']))
			$content .= ' lightboxoptions="' . str_replace('"', '&quot;', $instance['lightboxoptions']) . '"';
		else
			$content .= ' lightboxoptions=""';
		
		$content .= ' videowidth=' . $instance['videowidth'];
		$content .= ' videoheight=' . $instance['videoheight'];
		
		if ($instance['keepaspectratio'])
			$content .= ' keepaspectratio=1';
		else
			$content .= ' keepaspectratio=0';
		
		if ($instance['autoplay'])
			$content .= ' autoplay=1';
		else
			$content .= ' autoplay=0';
		
		if ($instance['loop'])
			$content .= ' loop=1';
		else
			$content .= ' loop=0';
		
		if (!empty($instance['videocss']))
			$content .= ' videocss="' . str_replace('"', '\\"', $instance['videocss']) . '"';
		else
			$content .= ' videocss=""';
			
		if (!empty($instance['playbutton']))
			$content .= ' playbutton="' . $instance['playbutton'] . '"';
		else
			$content .= ' playbutton=""';
		
		$content .= ']';
		
		echo do_shortcode($content);
	}
	
	function show_widgetform($widget, $instance) {
		
		$instance = wp_parse_args((array) $instance, $this->get_defaults());
				
		$videotype = esc_attr( $instance['videotype'] );
		$iframe = esc_url( $instance['iframe'] );
		$mp4 = esc_url( $instance['mp4'] );
		$webm = esc_url( $instance['webm'] );
		$poster = esc_url( $instance['poster'] );
		
		$lightbox = esc_attr( $instance['lightbox'] );
		$lightboxsize = esc_attr( $instance['lightboxsize'] );
		$lightboxwidth = esc_attr( $instance['lightboxwidth'] );
		$lightboxheight = esc_attr( $instance['lightboxheight'] );
		$autoopen = esc_attr( $instance['autoopen'] );
		$autoclose = esc_attr( $instance['autoclose'] );
		$autoopendelay = esc_attr( $instance['autoopendelay'] );
		$lightboxtitle = esc_attr( $instance['lightboxtitle'] );
		$lightboxgroup = esc_attr( $instance['lightboxgroup'] );
		$lightboxshownavigation = esc_attr( $instance['lightboxshownavigation'] );
		$showimage = esc_url( $instance['showimage'] );
		$lightboxoptions = esc_attr( $instance['lightboxoptions'] );
		
		$lightboxtitle = $this->unescape_html_for_edit($lightboxtitle);
		
		$videowidth = esc_attr( $instance['videowidth'] );
		$videoheight = esc_attr( $instance['videoheight'] );
		$keepaspectratio = esc_attr( $instance['keepaspectratio'] );
		$autoplay = esc_attr( $instance['autoplay'] );
		$loop = esc_attr( $instance['loop'] );
		
		$videocss = esc_attr( $instance['videocss'] );
		$playbutton = esc_url( $instance['playbutton'] );
		
		$param_names = array();
		foreach($instance as $key => $value)
			$param_names[$key] = $widget->get_field_name($key);
				
	?>
		<div class="wpve-tab-container">
		
			<div class="wpve-tab-label wpve-tab-label-selected" data-contentclass="wpve-tab-content1">Video</div>
			<div class="wpve-tab-label" data-contentclass="wpve-tab-content2">Lightbox</div>
			<div class="wpve-tab-label" data-contentclass="wpve-tab-content3">Options</div>
			<div class="wpve-tab-help"><a href="<?php echo admin_url('admin.php?page=wonderplugin_videoembed_show_quick_start'); ?>" target="_blank">Help Document</a></div>
			
			<section class="wpve-tab-content1 wpve-tab-content wpve-tab-content-selected" >
			
			<p><label class="wpve-tab-label-primary"><input type="radio" class="wpve-videotype-input" name="<?php echo $param_names["videotype"]; ?>" value="iframe" <?php echo (($videotype == "iframe") ? "checked" : ""); ?> /> Enter YouTube, Vimeo, Wistia or iFrame Video URL:</label></p>
			<div class="wpve-video-iframe-container" style="display:<?php echo (($videotype == "iframe") ? "block" : "none"); ?>" >
				<input class="wpve-iframe-url widefat" type="text" name="<?php echo $param_names["iframe"]; ?>" value="<?php echo $iframe; ?>" />
			</div>
  			
  			<p><label class="wpve-tab-label-primary"><input type="radio" class="wpve-videotype-input" name="<?php echo $param_names["videotype"]; ?>" value="mp4" <?php echo (($videotype == "mp4") ? "checked" : ""); ?> /> MP4/WebM Video</label></p>
  			<div class="wpve-video-mp4-container" style="display:<?php echo (($videotype == "mp4") ? "block" : "none"); ?>">
  				<p>
  				<label>MP4 Video URL:</label>
  				<input type="text" class="wpve-mp4-url widefat" name="<?php echo $param_names["mp4"]; ?>" value="<?php echo $mp4; ?>" />
  				<input type="button" class="wpve-select-file wpve-select-mp4 button" data-textfield="wpve-mp4-url" data-texttype="video" value="Select an MP4 file" />
  				</p>
  				<p>
  				<label>WebM Video URL (For Firefox HTML5 Player):</label>
  				<input type="text" class="wpve-webm-url widefat" name="<?php echo $param_names["webm"]; ?>" value="<?php echo $webm; ?>" />
  				<input type="button" class="wpve-select-file wpve-select-webm button" data-textfield="wpve-webm-url" data-texttype="video" value="Select a WebM file" />
  				</p>
  				<p>
  				<label>HTML5 Poster Image URL:</label>
  				<input type="text" class="wpve-poster-url widefat" name="<?php echo $param_names["poster"]; ?>" value="<?php echo $poster; ?>" />
  				<input type="button" class="wpve-select-file wpve-select-poster button" data-textfield="wpve-poster-url" data-texttype="image" value="Select an image file" />
  				</p>
  			</div>

			</section>
			
			<section class="wpve-tab-content2 wpve-tab-content" >
				
			<p <?php if(!class_exists('WonderPlugin_Lightbox_Plugin')) echo 'style="color:#ff0000;"'; ?>>* To play in lightbox, <a href="https://www.wonderplugin.com/wordpress-lightbox/?product=videoembed" target="_blank">Wonder Lightbox</a> must be installed and activated.</p>
			<p><label class="wpve-tab-label-primary"><input type="checkbox" class="checkbox" name="<?php echo $param_names["lightbox"]; ?>" <?php checked($lightbox, 1); ?> value="on" /> Play in Lightbox Popup</label></p>
			<div class="wpve-play-lightbox-container">
			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["lightboxsize"]; ?>" <?php checked($lightboxsize, 1); ?> value="on" /> Set Lightbox size: </label><input type="number" name="<?php echo $param_names["lightboxwidth"]; ?>" value="<?php echo $lightboxwidth; ?>" class="small-text" /> / <input type="number" name="<?php echo $param_names["lightboxheight"]; ?>" value="<?php echo $lightboxheight; ?>" class="small-text" /></p>
			<p>
  			<label>Display Image / Thumbnail URL:</label>
  			<input type="text" class="wpve-showimage-url widefat" name="<?php echo $param_names["showimage"]; ?>" value="<?php echo $showimage; ?>" />
  			<input type="button" class="wpve-select-file wpve-select-showimage button" data-textfield="wpve-showimage-url" data-texttype="image" value="Select an image file" />
  			</p>
  			<p><label>Title:<input type="text" name="<?php echo $param_names["lightboxtitle"]; ?>" value="<?php echo $lightboxtitle; ?>" class="widefat" /></label></p>
			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["autoopen"]; ?>" <?php checked($autoopen, 1); ?> value="on" /> Auto popup on page load in milliseconds: </label><input type="number" name="<?php echo $param_names["autoopendelay"]; ?>" value="<?php echo $autoopendelay; ?>" class="small-text" /></p>
			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["autoclose"]; ?>" <?php checked($autoclose, 1); ?> value="on" /> Auto close on video end (YouTube, Vimeo and MP4/WebM)</label></p>
  			<p><label>Add to group:<input type="text" name="<?php echo $param_names["lightboxgroup"]; ?>" value="<?php echo $lightboxgroup; ?>" class="medium-text" /></label></p>
  			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["lightboxshownavigation"]; ?>" <?php checked($lightboxshownavigation, 1); ?> value="on" /> Show thumbnail navigation</label></p>
			<p><label>Lightbox Advanced Options:</label><textarea name="<?php echo $param_names["lightboxoptions"]; ?>" class="widefat" rows="4"><?php echo $lightboxoptions; ?></textarea></p>
			</div>
				
			</section>
			
			<section class="wpve-tab-content3 wpve-tab-content" >
				
			<p><label>Width / Height (px):</label><input type="number" name="<?php echo $param_names["videowidth"]; ?>" value="<?php echo $videowidth; ?>" class="small-text" /> / <input type="number" name="<?php echo $param_names["videoheight"]; ?>" value="<?php echo $videoheight; ?>" class="small-text" /></p>
			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["keepaspectratio"]; ?>" <?php checked($keepaspectratio, 1); ?> value="on" /> Keep aspect ratio</label></p>
			<p><label><input type="checkbox" class="checkbox" name="<?php echo $param_names["autoplay"]; ?>" <?php checked($autoplay, 1); ?> value="on" /> Autoplay</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="checkbox" class="checkbox" name="<?php echo $param_names["loop"]; ?>" <?php checked($loop, 1); ?> value="on" /> Loop play</label></p>
			<p>* Autoplay does not work on mobile and tablets.</p>
			
			<p><label>Video CSS:</label><input class="widefat" type="text" name="<?php echo $param_names["videocss"]; ?>" value="<?php echo $videocss; ?>" /></p>
			
  			<p>
  			<label>Play Button URL:</label>
  			<input type="text" class="wpve-playbutton-url widefat" name="<?php echo $param_names["playbutton"]; ?>" value="<?php echo $playbutton; ?>" />
			<input type="button" class="wpve-select-file wpve-select-playbutton button" data-textfield="wpve-playbutton-url" data-texttype="image" value="Select an image file" />
			OR <select class="wpve-select-playbutton-preloaded">
			  <option value=""></option>
			  <option value="<?php echo WONDERPLUGIN_VIDEOEMBED_URL . 'engine/playvideo-64-64-0.png'; ?>">White Button</option>
			  <option value="<?php echo WONDERPLUGIN_VIDEOEMBED_URL . 'engine/playvideo-64-64-1.png'; ?>">Red Button</option>
			  <option value="<?php echo WONDERPLUGIN_VIDEOEMBED_URL . 'engine/playvideo-64-64-2.png'; ?>">Blue Button</option>
			</select>
  			</p>
			
			</section>
			
		</div>
	<?php	
	}
}
