<?php

/**
 * Support JetPack Testimonial
 */
class Essential_Content_Jetpack_Testimonial {
    const CUSTOM_POST_TYPE       = 'jetpack-testimonial';
    const OPTION_NAME            = 'jetpack_testimonial';
    const OPTION_READING_SETTING = 'jetpack_testimonial_posts_per_page';

    public $version = '0.1';

    static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Essential_Content_Jetpack_Testimonial;
        }

        return $instance;
    }

    /**
     * Conditionally hook into WordPress.
     *
     * Setup user option for enabling CPT.
     * If user has CPT enabled, show in admin.
     */
    function __construct() {
        // Make sure the post types are loaded for imports
        add_action( 'import_start',                array( $this, 'register_post_types' ) );

        // If called via REST API, we need to register later in lifecycle
        add_action( 'restapi_theme_init',          array( $this, 'maybe_register_cpt' ) );

        // Add to REST API post type whitelist
        add_filter( 'rest_api_allowed_post_types', array( $this, 'allow_cpt_rest_api_type' ) );

        $this->maybe_register_cpt();
    }

    /**
     * Registers the custom post types and adds action/filter handlers, but
     * only if the site supports it
     */
    function maybe_register_cpt() {
        // Add an option to enable the CPT
        add_action( 'admin_init', array( $this, 'settings_api_init' ) );

        // Check on theme switch if theme supports CPT and setting is disabled
        add_action( 'after_switch_theme', array( $this, 'activation_post_type_support' ) );

        $setting = 1;

        if ( class_exists( 'Jetpack_Options' ) ) {
            $setting = Jetpack_Options::get_option_and_ensure_autoload( self::OPTION_NAME, '0' );
        }

        // Bail early if Testimonial option is not set and the theme doesn't declare support
        if ( empty( $setting ) && ! $this->site_supports_custom_post_type() ) {
            return;
        }

        // Enable Omnisearch for CPT.
        if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
            new Jetpack_Omnisearch_Posts( self::CUSTOM_POST_TYPE );
        }

        // CPT magic
        $this->register_post_types();
        add_action( sprintf( 'add_option_%s', self::OPTION_NAME ),               array( $this, 'flush_rules_on_enable' ), 10 );
        add_action( sprintf( 'update_option_%s', self::OPTION_NAME ),            array( $this, 'flush_rules_on_enable' ), 10 );
        add_action( sprintf( 'publish_%s', self::CUSTOM_POST_TYPE ),             array( $this, 'flush_rules_on_first_testimonial' ) );
        add_action( 'after_switch_theme',                                        array( $this, 'flush_rules_on_switch' ) );

        // Admin Customization
        add_filter( 'enter_title_here',                                          array( $this, 'change_default_title'    ) );
        add_filter( sprintf( 'manage_%s_posts_columns', self::CUSTOM_POST_TYPE), array( $this, 'edit_title_column_label' ) );
        add_filter( 'post_updated_messages',                                     array( $this, 'updated_messages'        ) );
        add_action( 'customize_register',                                        array( $this, 'customize_register'      ) );

        // Only add the 'Customize' sub-menu if the theme supports it.
        $num_testimonials = self::count_testimonials();
        if ( ! empty( $num_testimonials ) && current_theme_supports( self::CUSTOM_POST_TYPE ) ) {
            add_action( 'admin_menu',                                            array( $this, 'add_customize_page' ) );
        }

        // Add to Jetpack XML sitemap
        add_filter( 'jetpack_sitemap_post_types',                                  array( $this, 'add_to_sitemap' ) );

        // Adjust CPT archive and custom taxonomies to obey CPT reading setting
        add_filter( 'pre_get_posts',                                             array( $this, 'query_reading_setting' ), 20 );
        add_filter( 'infinite_scroll_settings',                                  array( $this, 'infinite_scroll_click_posts_per_page' ) );

        // Register [jetpack_testimonials] always and
        // register [testimonials] if [testimonials] isn't already set
        add_shortcode( 'jetpack_testimonials',                                   array( $this, 'jetpack_testimonial_shortcode' ) );

        if ( ! shortcode_exists( 'testimonials' ) ) {
            add_shortcode( 'testimonials',                                       array( $this, 'jetpack_testimonial_shortcode' ) );
        }

        // If CPT was enabled programatically and no CPT items exist when user switches away, disable
        if ( $setting && $this->site_supports_custom_post_type() ) {
            add_action( 'switch_theme',                                          array( $this, 'deactivation_post_type_support' ) );
        }
    }

    /**
     * Add a checkbox field in 'Settings' > 'Writing'
     * for enabling CPT functionality.
     *
     * @return null
     */
    function settings_api_init() {
        add_settings_field(
            self::OPTION_NAME,
            '<span class="cpt-options">' . __( 'Testimonials', 'essential-content-types' ) . '</span>',
            array( $this, 'setting_html' ),
            'writing',
            'jetpack_cpt_section'
        );

        register_setting(
            'writing',
            self::OPTION_NAME,
            'intval'
        );

        // Check if CPT is enabled first so that intval doesn't get set to NULL on re-registering
        if ( $this->site_supports_custom_post_type() ) {
            register_setting(
                'writing',
                self::OPTION_READING_SETTING,
                'intval'
            );
        }
    }

    /**
     * HTML code to display a checkbox true/false option
     * for the CPT setting.
     *
     * @return html
     */
    function setting_html() {
        if ( current_theme_supports( self::CUSTOM_POST_TYPE ) ) : ?>
            <p><?php printf( __( 'Your theme supports Testimonials', 'essential-content-types' ) ); ?></p>
        <?php else : ?>
            <label for="<?php echo esc_attr( self::OPTION_NAME ); ?>">
                <input name="<?php echo esc_attr( self::OPTION_NAME ); ?>" id="<?php echo esc_attr( self::OPTION_NAME ); ?>" <?php echo checked( get_option( self::OPTION_NAME, '0' ), true, false ); ?> type="checkbox" value="1" />
                <?php esc_html_e( 'Enable Testimonials for this site.', 'essential-content-types' ); ?>
                <a target="_blank" href="http://en.support.wordpress.com/testimonials/"><?php esc_html_e( 'Learn More', 'essential-content-types' ); ?></a>
            </label>
        <?php endif;

        if ( $this->site_supports_custom_post_type() ) :
            printf( '<p><label for="%1$s">%2$s</label></p>',
                esc_attr( self::OPTION_READING_SETTING ),
                /* translators: %1$s is replaced with an input field for numbers */
                sprintf( __( 'Testimonial pages display at most %1$s testimonials', 'essential-content-types' ),
                    sprintf( '<input name="%1$s" id="%1$s" type="number" step="1" min="1" value="%2$s" class="small-text" />',
                        esc_attr( self::OPTION_READING_SETTING ),
                        esc_attr( get_option( self::OPTION_READING_SETTING, '10' ) )
                    )
                )
            );
        endif;
    }

    /**
     * Should this Custom Post Type be made available?
     */
    function site_supports_custom_post_type() {
        // If the current theme requests it.
        if ( current_theme_supports( self::CUSTOM_POST_TYPE ) || get_option( self::OPTION_NAME, '0' ) ) {
            return true;
        }

        // Otherwise, say no unless something wants to filter us to say yes.
        /** This action is documented in modules/custom-post-types/nova.php */
        return (bool) apply_filters( 'jetpack_enable_cpt', false, self::CUSTOM_POST_TYPE );
    }

    /**
     * Add to REST API post type whitelist
     */
    function allow_cpt_rest_api_type( $post_types ) {
        $post_types[] = self::CUSTOM_POST_TYPE;

        return $post_types;
    }


    /*
     * Flush permalinks when CPT option is turned on/off
     */
    function flush_rules_on_enable() {
        flush_rewrite_rules();
    }

    /*
     * Count published testimonials and flush permalinks when first testimonial is published
     */
    function flush_rules_on_first_testimonial() {
        $testimonials = get_transient( 'jetpack-testimonial-count-cache' );

        if ( false === $testimonials ) {
            flush_rewrite_rules();
            $testimonials = (int) wp_count_posts( self::CUSTOM_POST_TYPE )->publish;

            if ( ! empty( $testimonials ) ) {
                set_transient( 'jetpack-testimonial-count-cache', $testimonials, HOUR_IN_SECONDS * 12 );
            }
        }
    }

    /*
     * Flush permalinks when CPT supported theme is activated
     */
    function flush_rules_on_switch() {
        if ( current_theme_supports( self::CUSTOM_POST_TYPE ) ) {
            flush_rewrite_rules();
        }
    }

    /**
     * On plugin/theme activation, check if current theme supports CPT
     */
    static function activation_post_type_support() {
        if ( current_theme_supports( self::CUSTOM_POST_TYPE ) ) {
            update_option( self::OPTION_NAME, '1' );
        }
    }

    /**
     * On theme switch, check if CPT item exists and disable if not
     */
    function deactivation_post_type_support() {
        $testimonials = get_posts( array(
            'fields'           => 'ids',
            'posts_per_page'   => 1,
            'post_type'        => self::CUSTOM_POST_TYPE,
            'suppress_filters' => false
        ) );

        if ( empty( $testimonials ) ) {
            update_option( self::OPTION_NAME, '0' );
        }
    }

    /**
     * Register Post Type
     */
    function register_post_types() {
        if ( post_type_exists( self::CUSTOM_POST_TYPE ) ) {
            return;
        }

        register_post_type( self::CUSTOM_POST_TYPE, array(
            'description' => __( 'Customer Testimonials', 'essential-content-types' ),
            'labels' => array(
                'name'                  => esc_html__( 'Testimonials',                   'essential-content-types' ),
                'singular_name'         => esc_html__( 'Testimonial',                    'essential-content-types' ),
                'menu_name'             => esc_html__( 'Testimonials',                   'essential-content-types' ),
                'all_items'             => esc_html__( 'All Testimonials',               'essential-content-types' ),
                'add_new'               => esc_html__( 'Add New',                        'essential-content-types' ),
                'add_new_item'          => esc_html__( 'Add New Testimonial',            'essential-content-types' ),
                'edit_item'             => esc_html__( 'Edit Testimonial',               'essential-content-types' ),
                'new_item'              => esc_html__( 'New Testimonial',                'essential-content-types' ),
                'view_item'             => esc_html__( 'View Testimonial',               'essential-content-types' ),
                'search_items'          => esc_html__( 'Search Testimonials',            'essential-content-types' ),
                'not_found'             => esc_html__( 'No Testimonials found',          'essential-content-types' ),
                'not_found_in_trash'    => esc_html__( 'No Testimonials found in Trash', 'essential-content-types' ),
                'filter_items_list'     => esc_html__( 'Filter Testimonials list',       'essential-content-types' ),
                'items_list_navigation' => esc_html__( 'Testimonial list navigation',    'essential-content-types' ),
                'items_list'            => esc_html__( 'Testimonials list',              'essential-content-types' ),
            ),
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'page-attributes',
                'revisions',
            ),
            'rewrite' => array(
                'slug'       => 'testimonial',
                'with_front' => false,
                'feeds'      => false,
                'pages'      => true,
            ),
            'public'          => true,
            'show_ui'         => true,
            'menu_position'   => 20, // below Pages
            'menu_icon'       => 'dashicons-testimonial',
            'capability_type' => 'page',
            'map_meta_cap'    => true,
            'has_archive'     => true,
            'query_var'       => 'testimonial',
            'show_in_rest'    => true,
        ) );
    }

    /**
     * Update messages for the Testimonial admin.
     */
    function updated_messages( $messages ) {
        global $post;

        $messages[ self::CUSTOM_POST_TYPE ] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf( __( 'Testimonial updated. <a href="%s">View testimonial</a>', 'essential-content-types'), esc_url( get_permalink( $post->ID ) ) ),
            2  => esc_html__( 'Custom field updated.', 'essential-content-types' ),
            3  => esc_html__( 'Custom field deleted.', 'essential-content-types' ),
            4  => esc_html__( 'Testimonial updated.', 'essential-content-types' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Testimonial restored to revision from %s', 'essential-content-types'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( 'Testimonial published. <a href="%s">View testimonial</a>', 'essential-content-types' ), esc_url( get_permalink( $post->ID ) ) ),
            7  => esc_html__( 'Testimonial saved.', 'essential-content-types' ),
            8  => sprintf( __( 'Testimonial submitted. <a target="_blank" href="%s">Preview testimonial</a>', 'essential-content-types'), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
            9  => sprintf( __( 'Testimonial scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview testimonial</a>', 'essential-content-types' ),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'essential-content-types' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post->ID) ) ),
            10 => sprintf( __( 'Testimonial draft updated. <a target="_blank" href="%s">Preview testimonial</a>', 'essential-content-types' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
        );

        return $messages;
    }

    /**
     * Change ‘Enter Title Here’ text for the Testimonial.
     */
    function change_default_title( $title ) {
        $screen = get_current_screen();

        if ( self::CUSTOM_POST_TYPE == $screen->post_type )
            $title = esc_html__( "Enter the customer's name here", 'essential-content-types' );

        return $title;
    }

    /**
     * Change ‘Title’ column label on all Testimonials page.
     */
    function edit_title_column_label( $columns ) {
        $columns['title'] = esc_html__( 'Customer Name', 'essential-content-types' );

        return $columns;
    }

    /**
     * Follow CPT reading setting on CPT archive page
     */
    function query_reading_setting( $query ) {
        if ( ! is_admin()
            && $query->is_main_query()
            && $query->is_post_type_archive( self::CUSTOM_POST_TYPE )
        ) {
            $query->set( 'posts_per_page', get_option( self::OPTION_READING_SETTING, '10' ) );
        }
    }

    /*
     * If Infinite Scroll is set to 'click', use our custom reading setting instead of core's `posts_per_page`.
     */
    function infinite_scroll_click_posts_per_page( $settings ) {
        global $wp_query;

        if ( ! is_admin() && true === $settings['click_handle'] && $wp_query->is_post_type_archive( self::CUSTOM_POST_TYPE ) ) {
            $settings['posts_per_page'] = get_option( self::OPTION_READING_SETTING, $settings['posts_per_page'] );
        }

        return $settings;
    }

    /**
     * Add CPT to Dotcom sitemap
     */
    function add_to_sitemap( $post_types ) {
        $post_types[] = self::CUSTOM_POST_TYPE;

        return $post_types;
    }

    function set_testimonial_option() {
        $testimonials = wp_count_posts( self::CUSTOM_POST_TYPE );
        $published_testimonials = $testimonials->publish;

        update_option( self::OPTION_NAME, $published_testimonials );
    }

    function count_testimonials() {
        $testimonials = get_transient( 'jetpack-testimonial-count-cache' );

        if ( false === $testimonials ) {
            $testimonials = (int) wp_count_posts( self::CUSTOM_POST_TYPE )->publish;

            if ( ! empty( $testimonials ) ) {
                set_transient( 'jetpack-testimonial-count-cache', $testimonials, 60*60*12 );
            }
        }

        return $testimonials;
    }

    /**
     * Adds a submenu link to the Customizer.
     */
    function add_customize_page() {
        add_submenu_page(
            'edit.php?post_type=' . self::CUSTOM_POST_TYPE,
            esc_html__( 'Customize Testimonials Archive', 'essential-content-types' ),
            esc_html__( 'Customize', 'essential-content-types' ),
            'edit_theme_options',
            add_query_arg( array(
                'url' => urlencode( home_url( '/testimonial/' ) ),
                'autofocus[section]' => 'jetpack_testimonials'
            ), 'customize.php' )
        );
    }

    /**
     * Adds testimonial section to the Customizer.
     */
    function customize_register( $wp_customize ) {
        essential_content_testimonial_custom_control_classes();

        $wp_customize->add_panel( 'ect_plugin_options', array(
            'title'    => esc_html__( 'Essential Content Types Plugin Options', 'essential-content-types' ),
            'priority' => 1,
        ) );

        $wp_customize->add_section( 'jetpack_testimonials', array(
            'title'          => esc_html__( 'Testimonials', 'essential-content-types' ),
            'theme_supports' => self::CUSTOM_POST_TYPE,
            'priority'       => 130,
            'panel'          => 'ect_plugin_options',
        ) );

        $wp_customize->add_setting( 'jetpack_testimonials[page-title]', array(
            'default'              => esc_html__( 'Testimonials', 'essential-content-types' ),
            'sanitize_callback'    => array( 'Essential_Content_Jetpack_Testimonial_Title_Control', 'sanitize_content' ),
            'sanitize_js_callback' => array( 'Essential_Content_Jetpack_Testimonial_Title_Control', 'sanitize_content' ),
        ) );
        $wp_customize->add_control( 'jetpack_testimonials[page-title]', array(
            'section' => 'jetpack_testimonials',
            'label'   => esc_html__( 'Testimonial Archive Title', 'essential-content-types' ),
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( 'jetpack_testimonials[page-content]', array(
            'default'              => '',
            'sanitize_callback'    => array( 'Essential_Content_Jetpack_Testimonial_Textarea_Control', 'sanitize_content' ),
            'sanitize_js_callback' => array( 'Essential_Content_Jetpack_Testimonial_Textarea_Control', 'sanitize_content' ),
        ) );
        $wp_customize->add_control( new Essential_Content_Jetpack_Testimonial_Textarea_Control( $wp_customize, 'jetpack_testimonials[page-content]', array(
            'section'  => 'jetpack_testimonials',
            'settings' => 'jetpack_testimonials[page-content]',
            'label'    => esc_html__( 'Testimonial Archive Content', 'essential-content-types' ),
        ) ) );

        $wp_customize->add_setting( 'jetpack_testimonials[featured-image]', array(
            'default'              => '',
            'sanitize_callback'    => 'attachment_url_to_postid',
            'sanitize_js_callback' => 'attachment_url_to_postid',
            'theme_supports'       => 'post-thumbnails',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'jetpack_testimonials[featured-image]', array(
            'section' => 'jetpack_testimonials',
            'label'   => esc_html__( 'Testimonial Archive Featured Image', 'essential-content-types' ),
        ) ) );

        // The featured image control doesn't display properly in the Customizer unless we coerce
        // it back into a URL sooner, since that's what WP_Customize_Upload_Control::to_json() expects
        if ( is_admin() ) {
            add_filter( 'theme_mod_jetpack_testimonials', array( $this, 'coerce_testimonial_image_to_url' ) );
        }
    }

    public function coerce_testimonial_image_to_url( $opt ) {
        if ( ! $opt || ! is_array( $opt ) ) {
            return $opt;
        }
        if ( ! isset( $opt['featured-image'] ) || ! is_scalar( $opt['featured-image'] ) ) {
            return $opt;
        }
        $url = wp_get_attachment_url( $opt['featured-image'] );
        if ( $url ) {
            $opt['featured-image'] = $url;
        }
        return $opt;
    }

    /**
     * Our [testimonial] shortcode.
     * Prints Testimonial data styled to look good on *any* theme.
     *
     * @return jetpack_testimonial_shortcode_html
     */
    static function jetpack_testimonial_shortcode( $atts ) {
        // Default attributes
        $atts = shortcode_atts( array(
            'display_content' => true,
            'image'           => true,
            'columns'         => 1,
            'showposts'       => -1,
            'order'           => 'asc',
            'orderby'         => 'date',
        ), $atts, 'testimonial' );

        // A little sanitization
        if ( $atts['display_content'] && 'true' != $atts['display_content'] && 'full' != $atts['display_content'] ) {
            $atts['display_content'] = false;
        }

        if ( $atts['image'] && 'true' != $atts['image'] ) {
            $atts['image'] = false;
        }

        // Check if column value is set to valid numbers or else set default value as 1
        if( 1 == $atts['columns'] || 2 == $atts['columns'] ) {
            $atts['columns'] = absint( $atts['columns'] );
        } else {
            $atts['columns'] = 1;
        }

        $atts['showposts'] = intval( $atts['showposts'] );

        if ( $atts['order'] ) {
            $atts['order'] = urldecode( $atts['order'] );
            $atts['order'] = strtoupper( $atts['order'] );
            if ( 'DESC' != $atts['order'] ) {
                $atts['order'] = 'ASC';
            }
        }

        if ( $atts['orderby'] ) {
            $atts['orderby'] = urldecode( $atts['orderby'] );
            $atts['orderby'] = strtolower( $atts['orderby'] );
            $allowed_keys = array('author', 'date', 'title', 'rand');

            $parsed = array();
            foreach ( explode( ',', $atts['orderby'] ) as $testimonial_index_number => $orderby ) {
                if ( ! in_array( $orderby, $allowed_keys ) ) {
                    continue;
                }
                $parsed[] = $orderby;
            }

            if ( empty( $parsed ) ) {
                unset($atts['orderby']);
            } else {
                $atts['orderby'] = implode( ' ', $parsed );
            }
        }

        // enqueue shortcode styles when shortcode is used
        wp_enqueue_style( 'jetpack-testimonial-style', plugins_url( 'css/testimonial-shortcode.css', __FILE__ ), array(), '20140326' );

        return self::jetpack_testimonial_shortcode_html( $atts );
    }

    /**
     * The Testimonial shortcode loop.
     *
     * @todo add theme color styles
     * @return html
     */
    
    static function jetpack_testimonial_shortcode_html( $atts ) {

        // Default query arguments
        $defaults = array(
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby'],
            'posts_per_page' => $atts['showposts'],
        );

        $args              = wp_parse_args( $atts, $defaults );
        $args['post_type'] = self::CUSTOM_POST_TYPE; // Force this post type
        $query             = new WP_Query( $args );

        ob_start();

        // If we have posts, create the html
        // with testimonial markup
        if ( $query->have_posts() ) {
            

            /**
             * Hook: ect_before_testimonial_loop.
             *
             * @hooked 
             */
            $layout = ect_get_layout();
            do_action( 'ect_before_testimonial_loop', $layout[$atts['columns']] );
            ?>
            <?php 
                while ( $query->have_posts() ) {
                    
                    $query->the_post();
                    ect_get_template_part( 'content', 'testimonial', $atts );
                    
                }
                wp_reset_postdata();
            ?>
            <?php 

            /**
             * Hook: ect_after_testimonial_loop.
             *
             * @hooked
             */
            do_action( 'ect_after_testimonial_loop' );

        } else {
            /**
             * Hook: ect_no_testimonial_found.
             *
             * @hooked ect_no_testimonial_found
             */
            do_action( 'ect_no_testimonial_found' );
        }

        $html = ob_get_clean();

        // If there is a [testimonials] within a [testimonials], remove the shortcode
        if ( has_shortcode( $html, 'testimonials' ) ){
            remove_shortcode( 'testimonials' );
        }

        // Return the HTML block
        return $html;
    }
}
add_action( 'init', array( 'Essential_Content_Jetpack_Testimonial', 'init' ) );


function essential_content_testimonial_custom_control_classes() {
    class Essential_Content_Jetpack_Testimonial_Title_Control extends WP_Customize_Control {
        public static function sanitize_content( $value ) {
            if ( '' != $value )
                $value = trim( convert_chars( wptexturize( $value ) ) );

            return $value;
        }
    }

    class Essential_Content_Jetpack_Testimonial_Textarea_Control extends WP_Customize_Control {
        public $type = 'textarea';

        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
        <?php
        }

        public static function sanitize_content( $value ) {
            if ( ! empty( $value ) )
                /** This filter is already documented in core. wp-includes/post-template.php */
                $value = apply_filters( 'the_content', $value );

            $value = preg_replace( '@<div id="jp-post-flair"([^>]+)?>(.+)?</div>@is', '', $value ); // Strip WPCOM and Jetpack post flair if included in content

            return $value;
        }
    }
}

/**
 * Add Testimonial support
 */
function essential_content_testimonial_support() {
    /*
     * Adding theme support for Jetpack Testimonial CPT.
     */
    add_theme_support( 'jetpack-testimonial' );
}
add_action( 'after_setup_theme', 'essential_content_testimonial_support' );


/**
 * Class to Renders and save metabox options
 *
  */
class Essential_Content_Jetpack_Testimonial_Metabox {
    private $meta_box;

    private $fields;

    /**
    * Constructor
    *
        *
    * @access public
    *
    */
    public function __construct( $meta_box_id, $meta_box_title, $post_type ) {

        $this->meta_box = array (
            'id'        => $meta_box_id,
            'title'     => $meta_box_title,
            'post_type' => $post_type,
        );

        $this->fields = array(
            'ect-testimonials',
        );


        // Add metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add' ) );

        add_action( 'save_post', array( $this, 'save' ) );
    }

    /**
    * Add Meta Box for multiple post types.
    *
        *
    * @access public
    */
    public function add($postType) {
        if( in_array( $postType, $this->meta_box['post_type'] ) ) {
            add_meta_box( $this->meta_box['id'], $this->meta_box['title'], array( $this, 'show' ), $postType );
        }
    }

    /**
    * Renders metabox
    *
        *
    * @access public
    */
    public function show() {
        global $post;

        // Use nonce for verification
        wp_nonce_field( basename( __FILE__ ), 'ect_custom_meta_box_nonce' );

        $position = get_post_meta( $post->ID, 'ect_testimonial_position', true );

        // Begin the form table?>
        <table class="form-table">
            <tbody>
                <tr>
                    <td>
                        <label for="ect_testimonial_position"><?php esc_html_e( 'Position', 'essential-content-types' ); ?></label>
                    </td>

                    <td>
                        <input type="text" class="regular-text" name="ect_testimonial_position" value="<?php echo esc_attr( $position ); ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    <?php
    }

    /**
     * Save custom metabox data
     *
     * @action save_post
     *
     * @access public
     */
    public function save( $post_id ) {
        global $post_type;

        $post_type_object = get_post_type_object( $post_type );

        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                      // Check Autosave
        || ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
        || ( ! in_array( $post_type, $this->meta_box['post_type'] ) )                  // Check if current post type is supported.
        || ( ! check_admin_referer( basename( __FILE__ ), 'ect_custom_meta_box_nonce') )    // Check nonce - Security
        || ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) )  // Check permission
        {
          return $post_id;
        }

        if ( ! update_post_meta ( $post_id, 'ect_testimonial_position', sanitize_text_field( $_POST['ect_testimonial_position'] ) ) ) {
            add_post_meta( $post_id, 'ect_testimonial_position', sanitize_text_field( $_POST['ect_testimonial_position'] ), true );
        }
    }
}

$ect_metabox = new Essential_Content_Jetpack_Testimonial_Metabox(
    'ect-options', //metabox id
    esc_html__( 'Testmonial Options', 'essential-content-types' ), //metabox title
    array( 'jetpack-testimonial' ) //metabox post types
);


if( ! function_exists( 'essential_content_get_testimonial_thumbnail_link' ) ):
/**
 * Display the featured image if it's available
 *
 * @return html
 */
function essential_content_get_testimonial_thumbnail_link( $post_id, $size ) {
    if ( has_post_thumbnail( $post_id ) ) {
        /**
         * Change the Testimonial thumbnail size.
         *
         * @module custom-content-types
         *
         * @since 3.4.0
         *
         * @param string|array $var Either a registered size keyword or size array.
         */
        //return '<a class="testimonial-featured-image testimonial-thumbnail post-thumbnail" href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_post_thumbnail( $post_id, apply_filters( 'testimonial_thumbnail_size', $size ) ) . '</a>';
        return '<div class="testimonial-featured-image testimonial-thumbnail post-thumbnail">' . get_the_post_thumbnail( $post_id, apply_filters( 'testimonial_thumbnail_size', $size ) ) . '</div>';
    }
}
endif;


if( ! function_exists( 'essential_content_no_testimonial_found' ) ):
/**
 * No items found text
 *
 * @return html
 */
function essential_content_no_testimonial_found() {
    echo "<p><em>" . esc_html__( 'Your Testimonial Archive currently has no entries. You can start creating them on your dashboard.', 'essential-content-types-pro' ) . "</em></p>";
}
add_action( 'ect_no_testimonial_found', 'essential_content_no_testimonial_found', 10 );
endif;


if( ! function_exists( 'essential_content_testimonial_section_open' ) ):
/**
 * Open section
 *
 * @return html
 */
function essential_content_testimonial_section_open( $layout = null ) {
    echo '<div class="ect-testimonial-content-section ect-section ' . $layout . '">';
        echo '<div class="ect-wrapper">';
}   
endif;
add_action( 'ect_before_testimonial_loop', 'essential_content_testimonial_section_open', 10, 1 );


if( ! function_exists( 'essential_content_testimonial_loop_start' ) ):
/**
 * open wrapper before loop
 *
 */
function essential_content_testimonial_loop_start( $layout = null ){
    echo '<div class="section-content-wrapper testimonial-content-wrapper">';
}
endif;
add_action( 'ect_before_testimonial_loop', 'essential_content_testimonial_loop_start', 30 );


if( ! function_exists( 'essential_content_testimonial_slider_open' ) ):
function essential_content_testimonial_slider_open() { ?>
    <div class="cycle-slideshow"
                data-cycle-log="false"
                data-cycle-pause-on-hover="true"
                data-cycle-swipe="true"
                data-cycle-auto-height=container
                data-cycle-loader=false
                data-cycle-slides=".testimonial_slider_wrap"
                data-cycle-pager=".testimonial-slider-pager"
                data-cycle-prev=".testimonial-slider-prev"
                data-cycle-next=".testimonial-slider-next"
                >

                <div class="controller">
                    <!-- prev/next links -->
                    <button class="cycle-prev testimonial-slider-prev" aria-label="Previous">
                        <span class="screen-reader-text"><?php esc_html_e( 'Previous Slide', 'essential-content-types-pro' ); ?></span><?php echo essential_content_get_svg( array( 'icon' => 'angle-down' ) ); ?>
                    </button>

                    <!-- empty element for pager links -->
                    <div class="cycle-pager testimonial-slider-pager"></div>

                    <button class="cycle-next testimonial-slider-next" aria-label="Next">
                        <span class="screen-reader-text"><?php esc_html_e( 'Next Slide', 'essential-content-types-pro' ); ?></span><?php echo essential_content_get_svg( array( 'icon' => 'angle-down' ) ); ?>
                    </button>
                </div><!-- .controller -->

                <div class="testimonial_slider_wrap">

<?php }
endif;
//add_action( 'ect_before_testimonial_loop', 'essential_content_testimonial_slider_open', 40 );


if( ! function_exists( 'essential_content_testimonial_slider_close' ) ):
function essential_content_testimonial_slider_close() {
        echo '</div><!-- .testimonial_slider_wrap -->';
    echo '</div><!-- .cycle-slideshow -->';
}
endif;
//add_action( 'ect_after_testimonial_loop', 'essential_content_testimonial_slider_close', 10 );


if( ! function_exists( 'essential_content_testimonial_loop_end' ) ):
/**
 * close wrapper after loop
 *
 */
function essential_content_testimonial_loop_end(){
    echo '</div><!-- .testimonial-content-wrapper -->';
}
endif;
add_action( 'ect_after_testimonial_loop', 'essential_content_testimonial_loop_end', 20 );


if( ! function_exists( 'essential_content_testimonial_section_close' ) ):
/**
 * Close section
 *
 * @return html
 */
function essential_content_testimonial_section_close() {
        echo '</div><!-- .ect-wrapper -->';
    echo '</div><!-- .ect-section -->';
}   
endif;
add_action( 'ect_after_testimonial_loop', 'essential_content_testimonial_section_close', 30 );


if( ! function_exists( 'essential_content_get_svg' ) ):
/**
 * Return SVG markup.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 *     @type string $icon  Required SVG icon filename.
 *     @type string $title Optional SVG title.
 *     @type string $desc  Optional SVG description.
 * }
 * @return string SVG markup.
 */
function essential_content_get_svg( $args = array() ) {
    // Make sure $args are an array.
    if ( empty( $args ) ) {
        return __( 'Please define default parameters in the form of an array.', 'starter-pro' );
    }

    // Define an icon.
    if ( false === array_key_exists( 'icon', $args ) ) {
        return __( 'Please define an SVG icon filename.', 'starter-pro' );
    }

    // Set defaults.
    $defaults = array(
        'icon'        => '',
        'title'       => '',
        'desc'        => '',
        'fallback'    => false,
    );

    // Parse args.
    $args = wp_parse_args( $args, $defaults );

    // Set aria hidden.
    $aria_hidden = ' aria-hidden="true"';

    // Set ARIA.
    $aria_labelledby = '';

    /*
     * Starter doesn't use the SVG title or description attributes; non-decorative icons are described with .screen-reader-text.
     *
     * However, child themes can use the title and description to add information to non-decorative SVG icons to improve accessibility.
     *
     * Example 1 with title: <?php echo starter_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ) ); ?>
     *
     * Example 2 with title and description: <?php echo starter_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ) ); ?>
     *
     * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
     */
    if ( $args['title'] ) {
        $aria_hidden     = '';
        $unique_id       = uniqid();
        $aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

        if ( $args['desc'] ) {
            $aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
        }
    }

    // Begin SVG markup.
    $svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

    // Display the title.
    if ( $args['title'] ) {
        $svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

        // Display the desc only if the title is already set.
        if ( $args['desc'] ) {
            $svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
        }
    }

    /*
     * Display the icon.
     *
     * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
     *
     * See https://core.trac.wordpress.org/ticket/38387.
     */
    $svg .= ' <use href="#icon-' . esc_html( $args['icon'] ) . '" xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use> ';

    // Add some markup to use as a fallback for browsers that do not support SVGs.
    if ( $args['fallback'] ) {
        $svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
    }

    $svg .= '</svg>';

    return $svg;
}
endif;