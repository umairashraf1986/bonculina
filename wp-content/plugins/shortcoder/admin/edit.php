<?php

if( ! defined( 'ABSPATH' ) ) exit;

class SC_Admin_Edit{

    public static function init(){

        add_action( 'edit_form_after_title', array( __CLASS__, 'after_title' ) );

        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );

        add_action( 'save_post_' . SC_POST_TYPE, array( __CLASS__, 'save_post' ) );

        add_filter( 'wp_insert_post_data' , array( __CLASS__, 'before_insert_post' ) , 99, 1 );

        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

        add_filter( 'admin_footer_text', array( __CLASS__, 'footer_text' ) );

    }

    public static function after_title( $post ){

        if( $post->post_type != SC_POST_TYPE ){
            return;
        }

        $settings = Shortcoder::get_sc_settings( $post->ID );

        echo '<div id="sc_name">';
        echo '<input type="text" class="widefat" title="' . __( 'Name of the shortcode. Allowed characters are alphabets, numbers, hyphens and underscore.', 'shortcoder' ) . '" value="' . $post->post_name . '" name="post_name" id="post_name" pattern="[a-zA-z0-9\-_]+" required placeholder="' . __( 'Enter shortcode name', 'shortcoder' ) . '" />';
        echo '</div>';

        echo '<div id="edit-slug-box">';
        echo '<strong>' . __( 'Your shortcode', 'shortcoder' ) . ': </strong>';
        echo '<code class="sc_preview_text">' . Shortcoder::get_sc_tag( $post->ID ) . '</code>';
        echo '<span id="edit-slug-buttons"><button type="button" class="sc_copy button button-small"><span class="dashicons dashicons-yes"></span> ' . __( 'Copy', 'shortcoder' ) . '</button></span>';
        echo '</div>';

        // Editor
        self::editor( $post, $settings );

        // Hidden section
        self::hidden_section( $post, $settings );

    }

    public static function add_meta_boxes(){

        add_meta_box( 'sc_mb_settings', __( 'Shortcode settings', 'shortcoder' ), array( __CLASS__, 'settings_form' ), SC_POST_TYPE, 'normal', 'default' );

        add_meta_box( 'sc_mb_links', __( 'Get updates', 'shortcoder' ), array( __CLASS__, 'feedback' ), SC_POST_TYPE, 'side', 'default' );

        remove_meta_box( 'slugdiv', SC_POST_TYPE, 'normal' );

        remove_meta_box( 'commentstatusdiv', SC_POST_TYPE, 'normal' );

        remove_meta_box( 'commentsdiv', SC_POST_TYPE, 'normal' );

    }

    public static function settings_form( $post ){

        wp_nonce_field( 'sc_post_nonce', 'sc_nonce' );

        $settings = Shortcoder::get_sc_settings( $post->ID );

        $fields = array(

            array( __( 'Display name', 'shortcoder' ), SC_Admin_Form::field( 'text', array(
                'value' => $post->post_title,
                'name' => 'post_title',
                'class' => 'widefat',
                'helper' => __( 'Name of the shortcode to display when it is listed', 'shortcoder' )
            ))),

            array( __( 'Temporarily disable shortcode', 'shortcoder' ), SC_Admin_Form::field( 'select', array(
                'value' => $settings[ '_sc_disable_sc' ],
                'name' => '_sc_disable_sc',
                'list' => array(
                    'yes' => 'Yes',
                    'no' => 'No'
                ),
                'helper' => __( 'Select to disable the shortcode from executing in all the places where it is used.', 'shortcoder' )
            ))),

            array( __( 'Disable shortcode for administrators', 'shortcoder' ), SC_Admin_Form::field( 'select', array(
                'value' => $settings[ '_sc_disable_admin' ],
                'name' => '_sc_disable_admin',
                'list' => array(
                    'yes' => 'Yes',
                    'no' => 'No'
                ),
                'helper' => __( 'Select to disable the shortcode from executing for administrators.', 'shortcoder' )
            ))),

            array( __( 'Execute shortcode in devices', 'shortcoder' ), SC_Admin_Form::field( 'select', array(
                'value' => $settings[ '_sc_allowed_devices' ],
                'name' => '_sc_allowed_devices',
                'list' => array(
                    'all' => 'All devices',
                    'desktop_only' => 'Desktop only',
                    'mobile_only' => 'Mobile only'
                ),
                'helper' => __( 'Select the devices where the shortcode should be executed. Note: If any caching plugin is used, a separate caching for desktop and mobile might be required.', 'shortcoder' )
            ))),

        );

        echo SC_Admin_Form::table($fields);

    }

    public static function save_post( $post_id ){

        // Checks save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ 'sc_nonce' ] ) && wp_verify_nonce( $_POST[ 'sc_nonce' ], 'sc_post_nonce' ) );

        // Exits script depending on save status
        if ( $is_autosave || $is_revision || !$is_valid_nonce ){
            return;
        }

        $default_settings = Shortcoder::default_sc_settings();

        foreach( $default_settings as $key => $val ){

            if( array_key_exists( $key, $_POST ) ){
                $val = sanitize_text_field( $_POST[ $key ] );
                update_post_meta( $post_id, $key, $val );
            }

        }

    }

    public static function before_insert_post( $post ){
        
        if( $post[ 'post_type' ] != SC_POST_TYPE ){
            return $post;
        }

        $post_title = trim( $post[ 'post_title' ] );
        if( empty( $post_title ) ){
            $post[ 'post_title' ] = $post[ 'post_name' ];
        }

        if( $_POST && isset( $_POST[ 'sc_content' ] ) ){
            $post[ 'post_content' ] = $_POST[ 'sc_content' ];
        }

        return $post;
    }

    public static function editor_props( $settings ){

        $g = SC_Admin::clean_get();

        $list = array(
            'text' => __( 'Text editor', 'shortcoder' ),
            'visual' => __( 'Visual editor', 'shortcoder' ),
            'code' => __( 'Code editor', 'shortcoder' )
        );

        $editor = ( isset( $g[ 'editor' ] ) && array_key_exists( $g[ 'editor' ], $list ) ) ? $g[ 'editor' ] : $settings[ '_sc_editor' ];

        $switch = '<span class="sc_editor_list sc_editor_icon_' . $editor . '">';
        $switch .= '<select name="_sc_editor" class="sc_editor" title="' . __( 'Switch editor', 'shortcoder' ) . '">';
        foreach( $list as $id => $name ){
            $switch .= '<option value="' . $id . '" ' . selected( $editor, $id, false ) . '>' . $name . '</option>';
        }
        $switch .= '</select>';
        $switch .= '</span>';

        return array(
            'active' => $editor,
            'switch_html' => $switch
        );

    }

    public static function editor( $post, $settings ){

        $editor = self::editor_props( $settings );

        echo '<div class="hidden">';
        echo '<div class="sc_editor_toolbar">';
        echo '<button class="button button-primary sc_insert_param"><span class="dashicons dashicons-plus"></span>' . __( 'Insert shortcode parameters', 'shortcoder' ) . '<span class="dashicons dashicons-arrow-down"></span></button>';
        echo $editor[ 'switch_html' ];
        echo '</div>';
        echo '</div>';

        if( $editor[ 'active' ] == 'code' ){
            echo '<div class="sc_cm_menu"></div>';
            $content = user_can_richedit() ? esc_textarea( $post->post_content ) : $post->post_content;
            echo '<textarea name="sc_content" id="sc_content" class="sc_cm_content">' . $content . '</textarea>';
        }else{
            wp_editor( $post->post_content, 'sc_content', array(
                'wpautop'=> false,
                'textarea_rows'=> 20,
                'tinymce' => ( $editor[ 'active' ] == 'visual' )
            ));
        }

    }

    public static function enqueue_scripts( $hook ){

        global $post;

        if( !SC_Admin::is_sc_admin_page() || $hook == 'edit.php' || $hook == 'edit-tags.php' || $hook == 'term.php' ){
            return false;
        }

        $settings = Shortcoder::get_sc_settings( $post->ID );
        $editor = self::editor_props( $settings );

        wp_localize_script( 'sc-admin-js', 'SC_EDITOR', $editor[ 'active' ] );

        if( $editor[ 'active' ] != 'code' ){
            return false;
        }

        $cm_cdn_url = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.58.2/';
        $cm_files = array(
            'css' => array(
                'codemirror.min.css'
            ),
            'js' => array(
                'codemirror.min.js',
                'mode/htmlmixed/htmlmixed.min.js',
                'mode/css/css.min.js',
                'mode/xml/xml.min.js',
                'mode/javascript/javascript.min.js',
                'addon/selection/active-line.min.js',
                'addon/mode/overlay.min.js'
            )
        );

        foreach( $cm_files as $type => $files ){
            foreach( $files as $index => $file ){
                $url = $cm_cdn_url . $file;
                $id = 'sc-cm-' . $index;
                if( $type == 'css' ){
                    wp_enqueue_style( $id, $url, array(), SC_VERSION );
                }else{
                    wp_enqueue_script( $id, $url, array( 'sc-admin-js' ), SC_VERSION );
                }
            }
        }

    }

    public static function custom_params_list(){

        $sc_wp_params = Shortcoder::wp_params_list();
        
        echo '<ul class="sc_params_list">';

        foreach( $sc_wp_params as $group => $group_info ){
            echo '<li><span class="dashicons dashicons-' . $group_info['icon'] . '"></span>';
            echo $group_info[ 'name' ];
            echo '<ul class="sc_wp_params">';
            foreach( $group_info[ 'params' ] as $param_id => $param_name ){
                echo '<li data-id="' . $param_id . '">' . $param_name . '</li>';
            }
            echo '</ul></li>';
        }

        echo '<li><span class="dashicons dashicons-list-view"></span>' . __( 'Custom parameter', 'shortcoder' ) . '<ul>';
        echo '<li class="sc_params_form"><h4>' . __( 'Enter custom parameter name', 'shortcoder' ) . '</h4>';
            echo '<input type="text" class="sc_cp_box widefat" pattern="[a-zA-Z0-9_]+"/>';
            echo '<h4>' . __( 'Default value', 'shortcoder' ) . '</h4>';
            echo '<input type="text" class="sc_cp_default widefat"/>';
            echo '<button class="button sc_cp_btn">' . __( 'Insert parameter', 'shortcoder' ) . '</button>';
            echo '<p class="sc_cp_info"><small>' . __( 'Only alphabets, numbers and underscores are allowed. Custom parameters are case insensitive', 'shortcoder' ) . '</small></p></li>';
        echo '</ul></li>';

        echo '<li><span class="dashicons dashicons-screenoptions"></span>' . __( 'Custom Fields', 'shortcoder' ) . '<ul>';
        echo '<li class="sc_params_form"><h4>' . __( 'Enter custom field name', 'shortcoder' ) . '</h4>';
            echo '<input type="text" class="sc_cf_box widefat" pattern="[a-zA-Z0-9_-]+"/>';
            echo '<button class="button sc_cf_btn">' . __( 'Insert custom field', 'shortcoder' ) . '</button>';
            echo '<p class="sc_cf_info"><small>' . __( 'Only alphabets, numbers, underscore and hyphens are allowed. Cannot be empty.', 'shortcoder' ) . '</small></p></li>';
        echo '</ul></li>';

        echo '</ul>';

    }

    public static function hidden_section( $post, $settings ){

        self::custom_params_list();

    }

    public static function feedback( $post ){
        echo '<div class="feedback">';

        echo '<p>Get updates on the WordPress plugins, tips and tricks to enhance your WordPress experience. No spam.</p>';

        echo '<div class="subscribe_form" data-action="https://aakashweb.us19.list-manage.com/subscribe/post-json?u=b7023581458d048107298247e&id=ef5ab3c5c4&c=">
        <input type="email" class="subscribe_email_box" placeholder="Your email address">
        <p class="subscribe_confirm">Thanks for subscribing !</p>
        <button class="button subscribe_btn"><span class="dashicons dashicons-email"></span> Subscribe</button>
        </div>';

        echo '<ul>
        <li><a href="https://www.facebook.com/aakashweb" target="_blank">Follow on Facebook <span class="dashicons dashicons-arrow-right-alt2"></span></a></li>
        <li><a href="https://twitter.com/aakashweb" target="_blank">Follow on Twitter <span class="dashicons dashicons-arrow-right-alt2"></span></a></li>
        </ul>
        ';

        echo '<div class="ufw"><h4><a href="https://www.aakashweb.com/wordpress-plugins/ultimate-floating-widgets/?utm_source=shortcoder&utm_medium=sidebar&utm_campaign=ufw" target="_blank">
        <i>Create <span class="dashicons dashicons-arrow-right-alt2"></span></i>
        <br/> Floating/sticky widgets</a></h4>
        <img src="' . SC_ADMIN_URL . 'images/ufw.png" class="balloon" />
        <p>If you want to create floating/sticky sidebar widgets, then Ultimate floating widgets plugins lets you do that. Check it out.</p></div>';

        echo '<a class="rate_review" href="https://wordpress.org/support/plugin/shortcoder/reviews/?rate=5#new-post" target="_blank">
        <h4>Rate &amp; Review</h4>
        <span class="dashicons dashicons-star-filled"></span>
        <p>Like this plugin ? please do rate and review.</p>
        </a>';

        echo '<div class="cfe_bottom">';
        echo '<img src="' . SC_ADMIN_URL . '/images/coffee.svg" />';
        echo '<h3>Buy me a Coffee !</h3><p>If you like this plugin, buy me a coffee and help support this plugin !</p>';
        echo '<div class="cfe_form">';
        echo '<select class="cfe_amt">';
        for($i = 5; $i <= 15; $i++){
            echo '<option value="' . $i . '" ' . ($i == 6 ? 'selected="selected"' : '') . '>$' . $i . '</option>';
        }
        echo '<option value="">Custom</option>';
        echo '</select>';
        echo '<a class="button button-primary cfe_btn" href="https://www.paypal.me/vaakash/6" data-link="https://www.paypal.me/vaakash/" target="_blank">' . __( 'Buy me a coffee !', 'shortcoder' ) . '</a>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
    }

    public static function footer_text( $text ){

        if( SC_Admin::is_sc_admin_page() ){
            return '<span class="footer_thanks">Thanks for using <a href="https://www.aakashweb.com/wordpress-plugins/shortcoder/" target="_blank">Shortcoder</a> &bull; Please <a href="https://wordpress.org/support/plugin/shortcoder/reviews/?rate=5#new-post" target="_blank">rate 5 stars</a> and spread the word.</span>';
        }

        return $text;

    }

}

SC_Admin_Edit::init();

?>