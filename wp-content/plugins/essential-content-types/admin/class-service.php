<?php

/**
 * Support JetPack Service
 */
class Essential_Content_Service {
    const CUSTOM_POST_TYPE       = 'ect-service';
    const CUSTOM_TAXONOMY_TYPE   = 'ect-service-type';
    const CUSTOM_TAXONOMY_TAG    = 'ect-service-tag';
    const OPTION_NAME            = 'ect-service';
    const OPTION_READING_SETTING = 'ect_service_posts_per_page';

    public $version = '0.1';

    static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Essential_Content_Service;
        }

        return $instance;
    }

    /**
     * Conditionally hook into WordPress.
     *
     * Setup user option for enabling CPT
     * If user has CPT enabled, show in admin
     */
    function __construct() {
        // Make sure the post types are loaded for imports
        add_action( 'import_start',                                                    array( $this, 'register_post_types' ) );

        // Add to REST API post type whitelist
        add_filter( 'rest_api_allowed_post_types',                                     array( $this, 'allow_service_rest_api_type' ) );

        // CPT magic
        $this->register_post_types();
        add_action( sprintf( 'add_option_%s', self::OPTION_NAME ),                     array( $this, 'flush_rules_on_enable' ), 10 );
        add_action( sprintf( 'update_option_%s', self::OPTION_NAME ),                  array( $this, 'flush_rules_on_enable' ), 10 );
        add_action( sprintf( 'publish_%s', self::CUSTOM_POST_TYPE),                    array( $this, 'flush_rules_on_first_content' ) );
        add_action( 'after_switch_theme',                                              array( $this, 'flush_rules_on_switch' ) );

        // Admin Customization
        add_filter( 'post_updated_messages',                                           array( $this, 'updated_messages'   ) );
        add_filter( sprintf( 'manage_%s_posts_columns', self::CUSTOM_POST_TYPE),       array( $this, 'edit_admin_columns' ) );
        add_filter( sprintf( 'manage_%s_posts_custom_column', self::CUSTOM_POST_TYPE), array( $this, 'image_column'       ), 10, 2 );
        add_action( 'customize_register',                                              array( $this, 'customize_register' ) );

        add_image_size( 'service-admin-thumb', 50, 50, true );
        add_action( 'admin_enqueue_scripts',                                           array( $this, 'enqueue_admin_styles'  ) );

        // register service shortcode and services shortcode (legacy)
        add_shortcode( 'services',                                                    array( $this, 'service_shortcode' ) );
        add_shortcode( 'ect_services',                                            array( $this, 'service_shortcode' ) );

        // Adjust CPT archive and custom taxonomies to obey CPT reading setting
        add_filter( 'pre_get_posts',                                                   array( $this, 'query_reading_setting' ) );
    }

    /*
     * Flush permalinks when CPT option is turned on/off
     */
    function flush_rules_on_enable() {
        flush_rewrite_rules();
    }

    /*
     * Count published contents and flush permalinks when first contents is published
     */
    function flush_rules_on_first_content() {
        $contents = get_transient( 'service-count-cache' );

        if ( false === $contents ) {
            flush_rewrite_rules();
            $contents = (int) wp_count_posts( self::CUSTOM_POST_TYPE )->publish;

            if ( ! empty( $contents ) ) {
                set_transient( 'service-count-cache', $contents, HOUR_IN_SECONDS * 12 );
            }
        }
    }

    /*
     * Flush permalinks when CPT supported theme is activated
     */
    function flush_rules_on_switch() {
        flush_rewrite_rules();
    }

    /**
     * Register Post Type
     */
    function register_post_types() {
        if ( post_type_exists( self::CUSTOM_POST_TYPE ) ) {
            return;
        }

        register_post_type( self::CUSTOM_POST_TYPE, array(
            'description' => esc_html__( 'Service Items', 'essential-content-types' ),
            'labels' => array(
                'name'                  => esc_html__( 'Services',          'essential-content-types' ),
                'singular_name'         => esc_html__( 'Service',           'essential-content-types' ),
                'menu_name'             => esc_html__( 'Service',           'essential-content-types' ),
                'all_items'             => esc_html__( 'All Services',               'essential-content-types' ),
                'add_new'               => esc_html__( 'Add New',                    'essential-content-types' ),
                'add_new_item'          => esc_html__( 'Add New Service',            'essential-content-types' ),
                'edit_item'             => esc_html__( 'Edit Service',               'essential-content-types' ),
                'new_item'              => esc_html__( 'New Service',                'essential-content-types' ),
                'view_item'             => esc_html__( 'View Service',               'essential-content-types' ),
                'search_items'          => esc_html__( 'Search Services',            'essential-content-types' ),
                'not_found'             => esc_html__( 'No Services found',          'essential-content-types' ),
                'not_found_in_trash'    => esc_html__( 'No Services found in Trash', 'essential-content-types' ),
                'filter_items_list'     => esc_html__( 'Filter contents list',       'essential-content-types' ),
                'items_list_navigation' => esc_html__( 'Service list navigation',    'essential-content-types' ),
                'items_list'            => esc_html__( 'Services list',              'essential-content-types' ),
            ),
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'author',
                'comments',
            ),
            'rewrite' => array(
                'slug'       => 'service',
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
            ),
            'public'          => true,
            'show_ui'         => true,
            'menu_position'   => 20,                    // below Pages
            'menu_icon'       => 'dashicons-exerpt-view', // 3.8+ dashicon option
            'capability_type' => 'page',
            'map_meta_cap'    => true,
            'taxonomies'      => array( self::CUSTOM_TAXONOMY_TYPE, self::CUSTOM_TAXONOMY_TAG ),
            'has_archive'     => true,
            'query_var'       => 'service',
            'show_in_rest'    => true,
        ) );

        register_taxonomy( self::CUSTOM_TAXONOMY_TYPE, self::CUSTOM_POST_TYPE, array(
            'hierarchical'      => true,
            'labels'            => array(
                'name'                  => esc_html__( 'Service Types',                 'essential-content-types' ),
                'singular_name'         => esc_html__( 'Service Type',                  'essential-content-types' ),
                'menu_name'             => esc_html__( 'Service Types',                 'essential-content-types' ),
                'all_items'             => esc_html__( 'All Service Types',             'essential-content-types' ),
                'edit_item'             => esc_html__( 'Edit Service Type',             'essential-content-types' ),
                'view_item'             => esc_html__( 'View Service Type',             'essential-content-types' ),
                'update_item'           => esc_html__( 'Update Service Type',           'essential-content-types' ),
                'add_new_item'          => esc_html__( 'Add New Service Type',          'essential-content-types' ),
                'new_item_name'         => esc_html__( 'New Service Type Name',         'essential-content-types' ),
                'parent_item'           => esc_html__( 'Parent Service Type',           'essential-content-types' ),
                'parent_item_colon'     => esc_html__( 'Parent Service Type:',          'essential-content-types' ),
                'search_items'          => esc_html__( 'Search Service Types',          'essential-content-types' ),
                'items_list_navigation' => esc_html__( 'Service type list navigation',  'essential-content-types' ),
                'items_list'            => esc_html__( 'Service type list',             'essential-content-types' ),
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'service-type' ),
        ) );

        register_taxonomy( self::CUSTOM_TAXONOMY_TAG, self::CUSTOM_POST_TYPE, array(
            'hierarchical'      => false,
            'labels'            => array(
                'name'                       => esc_html__( 'Service Tags',                   'essential-content-types' ),
                'singular_name'              => esc_html__( 'Service Tag',                    'essential-content-types' ),
                'menu_name'                  => esc_html__( 'Service Tags',                   'essential-content-types' ),
                'all_items'                  => esc_html__( 'All Service Tags',               'essential-content-types' ),
                'edit_item'                  => esc_html__( 'Edit Service Tag',               'essential-content-types' ),
                'view_item'                  => esc_html__( 'View Service Tag',               'essential-content-types' ),
                'update_item'                => esc_html__( 'Update Service Tag',             'essential-content-types' ),
                'add_new_item'               => esc_html__( 'Add New Service Tag',            'essential-content-types' ),
                'new_item_name'              => esc_html__( 'New Service Tag Name',           'essential-content-types' ),
                'search_items'               => esc_html__( 'Search Service Tags',            'essential-content-types' ),
                'popular_items'              => esc_html__( 'Popular Service Tags',           'essential-content-types' ),
                'separate_items_with_commas' => esc_html__( 'Separate tags with commas',      'essential-content-types' ),
                'add_or_remove_items'        => esc_html__( 'Add or remove tags',             'essential-content-types' ),
                'choose_from_most_used'      => esc_html__( 'Choose from the most used tags', 'essential-content-types' ),
                'not_found'                  => esc_html__( 'No tags found.',                 'essential-content-types' ),
                'items_list_navigation'      => esc_html__( 'Service tag list navigation',    'essential-content-types' ),
                'items_list'                 => esc_html__( 'Service tag list',               'essential-content-types' ),
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'service-tag' ),
        ) );
    }

    /**
     * Update messages for the Services admin.
     */
    function updated_messages( $messages ) {
        global $post;

        $messages[self::CUSTOM_POST_TYPE] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf( __( 'Service updated. <a href="%s">View item</a>', 'essential-content-types'), esc_url( get_permalink( $post->ID ) ) ),
            2  => esc_html__( 'Custom field updated.', 'essential-content-types' ),
            3  => esc_html__( 'Custom field deleted.', 'essential-content-types' ),
            4  => esc_html__( 'Service updated.', 'essential-content-types' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Service restored to revision from %s', 'essential-content-types'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( 'Service published. <a href="%s">View content</a>', 'essential-content-types' ), esc_url( get_permalink( $post->ID ) ) ),
            7  => esc_html__( 'Service saved.', 'essential-content-types' ),
            8  => sprintf( __( 'Service submitted. <a target="_blank" href="%s">Preview content</a>', 'essential-content-types'), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
            9  => sprintf( __( 'Service scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview service</a>', 'essential-content-types' ),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i', 'essential-content-types' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
            10 => sprintf( __( 'Service item draft updated. <a target="_blank" href="%s">Preview content</a>', 'essential-content-types' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
        );

        return $messages;
    }

    /**
     * Change ‘Title’ column label
     * Add Featured Image column
     */
    function edit_admin_columns( $columns ) {
        // change 'Title' to 'Content'
        $columns['title'] = __( 'Service', 'essential-content-types' );
        if ( current_theme_supports( 'post-thumbnails' ) ) {
            // add featured image before 'Content'
            $columns = array_slice( $columns, 0, 1, true ) + array( 'thumbnail' => '' ) + array_slice( $columns, 1, NULL, true );
        }

        return $columns;
    }

    /**
     * Add featured image to column
     */
    function image_column( $column, $post_id ) {
        global $post;
        switch ( $column ) {
            case 'thumbnail':
                echo get_the_post_thumbnail( $post_id, 'service-admin-thumb' );
                break;
        }
    }

    /**
     * Adjust image column width
     */
    function enqueue_admin_styles( $hook ) {
        $screen = get_current_screen();

        if ( 'edit.php' == $hook && self::CUSTOM_POST_TYPE == $screen->post_type && current_theme_supports( 'post-thumbnails' ) ) {
            wp_add_inline_style( 'wp-admin', '.column-thumbnail img:nth-of-type(2) { display: none; } .manage-column.column-thumbnail { width: 50px; } @media screen and (max-width: 360px) { .column-thumbnail{ display:none; } }' );
        }
    }

    /**
     * Adds service section to the Customizer.
     */
    function customize_register( $wp_customize ) {
        $wp_customize->add_panel( 'ect_plugin_options', array(
            'title'    => esc_html__( 'Essential Content Types Plugin Options', 'essential-content-types' ),
            'priority' => 1,
        ) );

        $wp_customize->add_section( 'ect_service', array(
            'title'    => esc_html__( 'Services', 'essential-content-types' ),
            'priority' => 130,
            'panel'    => 'ect_plugin_options',
        ) );

        $wp_customize->add_setting( 'ect_service_title', array(
            'default'              => esc_html__( 'Services', 'essential-content-types' ),
            'type'                 => 'option',
            'sanitize_callback'    => 'sanitize_text_field',
            'sanitize_js_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'ect_service_title', array(
            'section'              => 'ect_service',
            'label'                => esc_html__( 'Service Archive Title', 'essential-content-types' ),
            'type'                 => 'text',
        ) );

        $wp_customize->add_setting( 'ect_service_content', array(
            'default'              => '',
            'type'                 => 'option',
            'sanitize_callback'    => 'wp_kses_post',
            'sanitize_js_callback' => 'wp_kses_post',
        ) );

        $wp_customize->add_control( 'ect_service_content', array(
            'section'              => 'ect_service',
            'label'                => esc_html__( 'Service Archive Content', 'essential-content-types' ),
            'type'                 => 'textarea',
        ) );

        $wp_customize->add_setting( 'ect_service_featured_image', array(
            'default'              => '',
            'type'                 => 'option',
            'sanitize_callback'    => 'attachment_url_to_postid',
            'sanitize_js_callback' => 'attachment_url_to_postid',
            'theme_supports'       => 'post-thumbnails',
        ) );

        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ect_service_featured_image', array(
            'section'              => 'ect_service',
            'label'                => esc_html__( 'Service Archive Featured Image', 'essential-content-types' ),
        ) ) );
    }

    /**
     * Follow CPT reading setting on CPT archive and taxonomy pages
     */
    function query_reading_setting( $query ) {
        if ( ! is_admin() &&
            $query->is_main_query() &&
            ( $query->is_post_type_archive( self::CUSTOM_POST_TYPE ) || $query->is_tax( self::CUSTOM_TAXONOMY_TYPE ) || $query->is_tax( self::CUSTOM_TAXONOMY_TAG ) )
        ) {
            $query->set( 'posts_per_page', get_option( self::OPTION_READING_SETTING, '10' ) );
        }
    }

    /**
     * Add to REST API post type whitelist
     */
    function allow_service_rest_api_type( $post_types ) {
        $post_types[] = self::CUSTOM_POST_TYPE;

        return $post_types;
    }

    /**
     * Our [services] shortcode.
     * Prints Service data styled to look good on *any* theme.
     *
     * @return service_shortcode_html
     */
    static function service_shortcode( $atts ) {
        // Default attributes
        $atts = shortcode_atts( array(
            'image'           => true,
            'display_types'   => true,
            'display_tags'    => true,
            'display_content' => true,
            'display_author'  => false,
            'show_filter'     => false,
            'include_type'    => false,
            'include_tag'     => false,
            'columns'         => 2,
            'showposts'       => -1,
            'order'           => 'asc',
            'orderby'         => 'date',
        ), $atts, 'services' );

        // A little sanitization
        if ( $atts['image'] && 'true' != $atts['image'] ) {
            $atts['image'] = false;
        }

        if ( $atts['display_types'] && 'true' != $atts['display_types'] ) {
            $atts['display_types'] = false;
        }

        if ( $atts['display_tags'] && 'true' != $atts['display_tags'] ) {
            $atts['display_tags'] = false;
        }

        if ( $atts['display_author'] && 'true' != $atts['display_author'] ) {
            $atts['display_author'] = false;
        }

        if ( $atts['display_content'] && 'true' != $atts['display_content'] && 'full' != $atts['display_content'] ) {
            $atts['display_content'] = false;
        }

        if ( $atts['include_type'] ) {
            $atts['include_type'] = explode( ',', str_replace( ' ', '', $atts['include_type'] ) );
        }

        if ( $atts['include_tag'] ) {
            $atts['include_tag'] = explode( ',', str_replace( ' ', '', $atts['include_tag'] ) );
        }

        // Check if column value is set to valid numbers or else set default value as 2
        if( 1 <= $atts['columns'] && 6 >= $atts['columns'] ) {
            $atts['columns'] = absint( $atts['columns'] );
        } else {
            $atts['columns'] = 2;
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
            $allowed_keys = array( 'author', 'date', 'title', 'rand' );

            $parsed = array();
            foreach ( explode( ',', $atts['orderby'] ) as $service_index_number => $orderby ) {
                if ( ! in_array( $orderby, $allowed_keys ) ) {
                    continue;
                }
                $parsed[] = $orderby;
            }

            if ( empty( $parsed ) ) {
                unset( $atts['orderby'] );
            } else {
                $atts['orderby'] = implode( ' ', $parsed );
            }
        }

        // enqueue shortcode styles when shortcode is used
        wp_enqueue_style( 'service-style', plugins_url( 'css/service-shortcode.css', __FILE__ ), array(), '20140326' );

        return self::service_shortcode_html( $atts );
    }

    /**
     * Query to retrieve entries from the Service post_type.
     *
     * @return object
     */
    static function service_query( $atts ) {
        // Default query arguments
        $default = array(
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby'],
            'posts_per_page' => $atts['showposts'],
        );

        $args = wp_parse_args( $atts, $default );
        $args['post_type'] = self::CUSTOM_POST_TYPE; // Force this post type

        if ( false != $atts['include_type'] || false != $atts['include_tag'] ) {
            $args['tax_query'] = array();
        }

        // If 'include_type' has been set use it on the main query
        if ( false != $atts['include_type'] ) {
            array_push( $args['tax_query'], array(
                'taxonomy' => self::CUSTOM_TAXONOMY_TYPE,
                'field'    => 'slug',
                'terms'    => $atts['include_type'],
            ) );
        }

        // If 'include_tag' has been set use it on the main query
        if ( false != $atts['include_tag'] ) {
            array_push( $args['tax_query'], array(
                'taxonomy' => self::CUSTOM_TAXONOMY_TAG,
                'field'    => 'slug',
                'terms'    => $atts['include_tag'],
            ) );
        }

        if ( false != $atts['include_type'] && false != $atts['include_tag'] ) {
            $args['tax_query']['relation'] = 'AND';
        }

        // Run the query and return
        $query = new WP_Query( $args );
        return $query;
    }

    /**
     * The Service shortcode loop.
     *
     * @todo add theme color styles
     * @return html
     */
    
    static function service_shortcode_html( $atts ) {

        $query = self::service_query( $atts );
        $atts['service_index_number'] = 0;

        ob_start();

        // If we have posts, create the html
        // with service markup
        if ( $query->have_posts() ) {
            
            /**
             * Hook: ect_before_service_loop.
             *
             * @hooked 
             */
            $layout = ect_get_layout();
            do_action( 'ect_before_service_loop', $layout[$atts['columns']] );

            ?>
            <?php 
                while ( $query->have_posts() ) {
                    $query->the_post();
                    ect_get_template_part( 'content', 'service', $atts );
                    /**
                     * Hook: ect_service_loop.
                     *
                     * @hooked
                     */
                }
                wp_reset_postdata(); 
            ?>  
            <?php 

            /**
             * Hook: ect_after_service_loop.
             *
             * @hooked
             */
            do_action( 'ect_after_service_loop' );

        } else {
            /**
             * Hook: ect_no_service_found.
             *
             * @hooked ect_no_service_found
             */
            do_action( 'ect_no_service_found' );
        }

        $html = ob_get_clean();

        // If there is a [services] within a [services], remove the shortcode
        if ( has_shortcode( $html, 'services' ) ){
            remove_shortcode( 'services' );
        }

        // Return the HTML block
        return $html;
    }

    /**
     * Displays the content type that a content belongs to.
     *
     * @return html
     */
    static function get_content_type( $post_id ) {
        $content_types = get_the_terms( $post_id, self::CUSTOM_TAXONOMY_TYPE );

        // If no types, return empty string
        if ( empty( $content_types ) || is_wp_error( $content_types ) ) {
            return;
        }

        $html = '<div class="content-types"><span>' . __( 'Types', 'essential-content-types' ) . ':</span>';
        $types = array();
        // Loop thorugh all the types
        foreach ( $content_types as $content_type ) {
            $content_type_link = get_term_link( $content_type, self::CUSTOM_TAXONOMY_TYPE );

            if ( is_wp_error( $content_type_link ) ) {
                return $content_type_link;
            }

            $types[] = '<a href="' . esc_url( $content_type_link ) . '" rel="tag">' . esc_html( $content_type->name ) . '</a>';
        }
        $html .= ' '.implode( ', ', $types );
        $html .= '</div>';

        return $html;
    }

    /**
     * Displays the content tags that a content belongs to.
     *
     * @return html
     */
    static function get_content_tags( $post_id ) {
        $content_tags = get_the_terms( $post_id, self::CUSTOM_TAXONOMY_TAG );

        // If no tags, return empty string
        if ( empty( $content_tags ) || is_wp_error( $content_tags ) ) {
            return false;
        }

        $html = '<div class="content-tags"><span>' . __( 'Tags', 'essential-content-types' ) . ':</span>';
        $tags = array();
        // Loop thorugh all the tags
        foreach ( $content_tags as $content_tag ) {
            $content_tag_link = get_term_link( $content_tag, self::CUSTOM_TAXONOMY_TYPE );

            if ( is_wp_error( $content_tag_link ) ) {
                return $content_tag_link;
            }

            $tags[] = '<a href="' . esc_url( $content_tag_link ) . '" rel="tag">' . esc_html( $content_tag->name ) . '</a>';
        }
        $html .= ' '. implode( ', ', $tags );
        $html .= '</div>';

        return $html;
    }

    /**
     * Displays the author of the current service content.
     *
     * @return html
     */
    static function get_content_author() {
        $html = '<div class="content-author">';
        /* translators: %1$s is link to author posts, %2$s is author display name */
        $html .= sprintf( __( '<span>Author:</span> <a href="%1$s">%2$s</a>', 'essential-content-types' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        );
        $html .= '</div>';

        return $html;
    }
}
add_action( 'init', array( 'Essential_Content_Service', 'init' ) );


/**
 * Add Service support
 */
function essential_content_service_support() {
    /*
     * Adding theme support for Jetpack Service CPT.
     */
    add_image_size( 'ect-service', 640, 640, true );
}
add_action( 'after_setup_theme', 'essential_content_service_support' );


if( ! function_exists( 'essential_content_get_service_thumbnail_link' ) ):
function essential_content_get_service_thumbnail_link( $post_id, $size ) {
    if ( has_post_thumbnail( $post_id ) ) {
        /**
         * Change the Service thumbnail size.
         *
         * @module custom-content-types
         *
         * @since 3.4.0
         *
         * @param string|array $var Either a registered size keyword or size array.
         */
        return '<a class="service-featured-image" href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_post_thumbnail( $post_id, apply_filters( 'service_thumbnail_size', $size ) ) . '</a>';
    }
}
endif;


if( ! function_exists( 'essential_content_no_service_found' ) ):
/**
 * No items found text
 *
 * @return html
 */
function essential_content_no_service_found() {
    echo "<p><em>" . esc_html__( 'Your Service Archive currently has no entries. You can start creating them on your dashboard.', 'essential-content-types-pro' ) . "</em></p>";
}
add_action( 'ect_no_service_found', 'essential_content_no_service_found', 10 );
endif;


if( ! function_exists( 'essential_content_service_section_open' ) ):
/**
 * Open section
 *
 * @return html
 */
function essential_content_service_section_open( $layout ) {
    echo '<div class="ect-service ect-section">';
        echo '<div class="ect-wrapper">';
}   
endif;
add_action( 'ect_before_service_loop', 'essential_content_service_section_open', 10, 1 );


if( ! function_exists( 'essential_content_service_loop_start' ) ):
/**
 * open wrapper before loop
 *
 */
function essential_content_service_loop_start( $layout = null ){
    echo '<div class="section-content-wrapper service-content-wrapper ' . $layout . '">';
}
endif;
add_action( 'ect_before_service_loop', 'essential_content_service_loop_start', 30 );


if( ! function_exists( 'essential_content_service_loop_end' ) ):
/**
 * close wrapper after loop
 *
 */
function essential_content_service_loop_end(){
    echo '</div><!-- .service-content-wrapper -->';
}
endif;
add_action( 'ect_after_service_loop', 'essential_content_service_loop_end', 10 );


if( ! function_exists( 'essential_content_service_section_close' ) ):
/**
 * Close section
 *
 * @return html
 */
function essential_content_service_section_close() {
        echo '</div><!-- .ect-wrapper -->';
    echo '</div><!-- .ect-section -->';
}   
endif;
add_action( 'ect_after_service_loop', 'essential_content_service_section_close', 20 );