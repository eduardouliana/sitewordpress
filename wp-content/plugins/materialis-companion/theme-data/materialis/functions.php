<?php

/** @var \Materialis\Companion $this */
require_once $this->themeDataPath("/updates.php");
require_once $this->themeDataPath("/custom-style.php");
require_once $this->themeDataPath("/options/overlap.php");
require_once $this->themeDataPath("/shortcodes/latest-news.php");


add_filter('materialis_can_show_demo_content', "__return_true");
add_filter('materialis_show_inactive_plugin_infos', "__return_false");


add_filter('materialis_full_width_page', function ($value) {

    if (Materialis\Companion::instance()->isMaintainable()) {
        $value = true;
    }

    return $value;
});

add_filter('materialis_page_content_wrapper_class', function ($class) {
    if (materialis_is_front_page() || apply_filters('materialis_full_width_page', false)) {
        $class = array_diff($class, array('gridContainer'));
    }

    return $class;
});


add_filter('materialis_page_content_class', function ($class) {
    if (Materialis\Companion::instance()->isMaintainable()) {
        $class[] = 'no-padding';
        $class   = array_diff($class, array('gridContainer'));
    }

    return $class;
});

function materialis_companion_get_post_thumbnail()
{
    ob_start();
    the_post_thumbnail('post-thumbnail', array('class' => 'blog-postimg'));
    $thumbnail = trim(ob_get_clean());

    if (empty($thumbnail)) {
        if (is_customize_preview() || 1) {
            return "<img src='https://placeholdit.imgix.net/~text?txtsize=38&bg=FF7F66&txtclr=FFFFFFe&w=400&h=250' class='blog-postimg'/>";
        } else {
            return $thumbnail;
        }
    }

    return $thumbnail;
}


function materialis_companion_blog_link()
{
    if ('page' == get_option('show_on_front')) {
        if (get_option('page_for_posts')) {
            return esc_url(get_permalink(get_option('page_for_posts')));
        } else {
            return esc_url(home_url('/?post_type=post'));
        }
    } else {
        return esc_url(home_url('/'));
    }
}


function materialis_companion_contact_form($attrs = array())
{
    $atts = shortcode_atts(
        array(
            'shortcode' => "",
        ),
        $attrs
    );

    $contact_shortcode = "";
    if ($atts['shortcode']) {
        $contact_shortcode = "[" . html_entity_decode(html_entity_decode($atts['shortcode'])) . "]";
    }
    ob_start();

    if ($contact_shortcode !== "") {
        echo do_shortcode($contact_shortcode);
    } else {
        echo '<p style="text-align:center;color:#ababab">' . __('Contact form will be displayed here. To activate it you have to set the "contact form shortcode" parameter in Customizer.',
                'materialis-companion') . '</p>';
    }

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode('materialis_contact_form', 'materialis_companion_contact_form');


add_filter('cloudpress\template\page_content',
    function ($content) {
        $content = str_replace('[materialis_blog_link]', materialis_companion_blog_link(), $content);
        $content = str_replace('[tag_companion_uri]', \Materialis\Companion::instance()->themeDataURL(), $content);

        return $content;
    });


add_filter('cloudpress\companion\cp_data',
    function ($data, $companion) {
        /** @var Materialis\Companion $companion */

//        $sectionsJSON             = $companion->themeDataPath("/sections/sections.inc");
        $contentSections          = $companion->loadConfig($companion->themeDataPath("/sections/sections.php"));
        $data['data']['sections'] = $contentSections;

        $showPro = apply_filters('materialis_show_info_pro_messages', true);

        if ($showPro) {
            $proSections              = $companion->loadConfig($companion->themeDataPath("/sections/pro-only-sections.php"));
            $data['data']['sections'] = array_merge($contentSections, $proSections);
        }


        if (apply_filters('materialis_show_custom_section', false)) {
            $customSectionContentFile = $companion->themeDataPath("/assets/custom-section.html");
            $customSectionContent     = file_get_contents($customSectionContentFile);

            $data['data']['sections'][] = array(
                "index"       => 1,
                "id"          => "custom-section",
                "elementId"   => "custom-section",
                "type"        => "section-available",
                "name"        => "Custom Section",
                "content"     => $customSectionContent,
                "thumb"       => "\/\/onepageexpress.com\/default-assets\/previews\/custom-section.png",
                "preview"     => "\/\/onepageexpress.com\/default-assets\/previews\/custom-section.png",
                "description" => "simple custom section",
                "category"    => "custom",
                "prepend"     => false,
                "pro"         => true,
            );
        }

        return $data;
    }, 10, 2);

add_action('cloudpress\template\load_assets',
    function ($companion) {
        $ver = $companion->version;
        wp_enqueue_style($companion->getThemeSlug() . '-common-css', $companion->themeDataURL('/assets/css/common.css'), array($companion->getThemeSlug() . '-style'), $ver);
        wp_enqueue_style('companion-page-css', $companion->themeDataURL('/sections/content.css'), array(), $ver);
//        wp_enqueue_style('companion-cotent-swap-css', $companion->themeDataURL('/assets/css/HoverFX.css'), array(), $ver);

//        wp_enqueue_script('companion-lib-hammer', $companion->themeDataURL('/assets/js/libs/hammer.js'), array(), $ver);
//        wp_enqueue_script('companion-lib-modernizr', $companion->themeDataURL('/assets/js/libs/modernizr.js'), array(), $ver);
        wp_register_script('companion-' . $companion->getThemeSlug(), null, array('jquery',), $ver);

        if ( ! is_customize_preview()) {
            wp_enqueue_script('companion-cotent-swap', $companion->themeDataURL('/assets/js/HoverFX.js'), array('companion-' . $companion->getThemeSlug()), $ver);
        }

        wp_enqueue_script('companion-countup', $companion->themeDataURL('/assets/js/countup.js'), array('companion-' . $companion->getThemeSlug()), $ver);
        wp_enqueue_script('companion-progressbar', $companion->themeDataURL('/assets/js/progressbar.js'), array('companion-' . $companion->getThemeSlug()), $ver);

        wp_enqueue_script('companion-scripts', $companion->themeDataURL('/sections/scripts.js'), array('companion-' . $companion->getThemeSlug(), $companion->getThemeSlug() . '-theme'), $ver, true);
    });

//add_action('cloudpress\customizer\preview_scripts',
//    function ($customizer) {
//        $ver = $customizer->companion()->version;
//        wp_enqueue_script(
//            $customizer->companion()->getThemeSlug() . "_preview-handle", $customizer->companion()->themeDataURL() . "/preview-handles.js", array('cp-customizer-preview'), $ver
//        );
//    });


//add_action('cloudpress\customizer\global_scripts',
//    function ($customizer) {
//        $ver = $customizer->companion()->version;
//        wp_enqueue_script(
//            $customizer->companion()->getThemeSlug() . "_companion_theme_customizer",
//            $customizer->companion()->themeDataURL() . "/customizer.js",
//            array('cp-customizer-base'),
//            $ver,
//            true
//        );
//    });

function materialis_companion_page_builder_get_css_value($value, $unit = false)
{
    $noUnitValues = array('inherit', 'auto', 'initial');
    if ( ! in_array($value, $noUnitValues)) {
        return $value . $unit;
    }

    return $value;
}


function materialis_companion_get_front_page_content($companion)
{
    $defaultSections = array(
        "overlappable-5-materialis",
        "about-4",
        "features-10-materialis",
        "content-7-materialis",
        "content-8-materialis",
        "portfolio-1-materialis",
        "testimonials-1-materialis",
        "cta-1-materialis",
        "team-8-materialis",
        "latest-news-1-materialis",
        "contact-1",
    );

    $alreadyColoredSections = array("contact-1", "cta-blue-section");

    /** @var Materialis\Companion $companion */
    $availableSections = $companion->loadConfig($companion->themeDataPath("/sections/sections.php"));

    $content = "";

    $colors     = array('#ffffff', '#f5fafd');
    $colorIndex = 0;

    foreach ($defaultSections as $ds) {
        foreach ($availableSections as $as) {
            if ($as['id'] == $ds) {
                $_content = $as['content'];

                if (in_array($ds, array("overlappable-5-materialis", "about-4"))) {
                    $colorIndex = 1;
                }

                if (strpos($_content, 'data-bg="transparent"') === false && ! in_array($ds, $alreadyColoredSections)) {
                    $_content   = preg_replace('/\<div/', '<div style="background-color:' . $colors[$colorIndex] . '" ', $_content, 1);
                    $colorIndex = $colorIndex ? 0 : 1;
                } else {
                    $colorIndex = 0;
                }

                $_content = preg_replace('/\<div/', '<div id="' . $as['elementId'] . '" ', $_content, 1);

                $content .= $_content;
                break;
            }
        }
    }

    return $content;
}

add_filter('cloudpress\companion\front_page_content',
    function ($content, $companion) {
        $content = materialis_companion_get_front_page_content($companion);

        return \Materialis\Companion::filterDefault($content);
    }, 10, 2);


add_filter('cloudpress\customizer\control\content_sections\data',
    function ($data) {
        $categories = array(
            'overlappable',
            'about',
            'features',
            'content',
            'cta',
            'counters',
            'FAQ',
            'gallery',
            'portfolio',
            'pricing',
            'promo',
            'testimonials',
            'clients',
            'team',
            'latest_news',
            'contact',
            'woocommerce',
        );

        $result = array();

        foreach ($categories as $cat) {
            if (isset($data[$cat])) {
                $result[$cat] = $data[$cat];
                unset($data[$cat]);
            }
        }

        $result = array_merge($result, $data);

        return $result;
    });

add_filter('cloudpress\customizer\control\content_sections\category_label',
    function ($label, $category) {

        switch ($category) {
            case 'latest_news':
                $label = __("Latest News", 'materialis-companion');
                break;

            case 'cta':
                $label = __("Call to action", 'materialis-companion');
                break;

            default:
                $label = __($label, 'materialis-companion');
                break;
        }

        return $label;
    }, 10, 2);


add_action('edit_form_after_title', 'materialis_companion_add_maintainable_filter');

function materialis_companion_add_maintainable_filter($post)
{
    $companion    = \Materialis\Companion::instance();
    $maintainable = $companion->isMaintainable($post->ID);

    add_editor_style(get_template_directory_uri() . "/style.css");
    add_editor_style(get_stylesheet_uri());

    add_editor_style($companion->themeDataURL('/assets/css/common.css'));
    add_editor_style($companion->themeDataURL('/sections/content.css'));
//    add_editor_style($companion->themeDataURL('/assets/css/HoverFX.css'));
    add_editor_style(get_template_directory_uri() . '/assets/css/material-icons.min.css');


    if ($maintainable) {
        add_filter('tiny_mce_before_init', 'materialis_companion_maintainable_pages_tinymce_init');
    }
}

add_filter('body_class', function ($classes) {
    $companion    = \Materialis\Companion::instance();
    $maintainable = $companion->isMaintainable();

    if ($maintainable) {
        if (in_array('materialis-content-padding', $classes)) {
            $classes = array_diff($classes, array('materialis-content-padding'));
        }

        $classes[] = 'materialis-content-no-padding ';
        $classes[] = 'materialis-maintainable-in-customizer ';
    }

    return $classes;

}, PHP_INT_MAX);


function materialis_companion_maintainable_pages_tinymce_init($init)
{
    $init['verify_html'] = false;

    // convert newline characters to BR
    $init['convert_newlines_to_brs'] = true;

    // don't remove redundant BR
    $init['remove_redundant_brs'] = false;

    $init['remove_linebreaks'] = false;

    $opts                            = '*[*]';
    $init['valid_elements']          = $opts;
    $init['extended_valid_elements'] = $opts;
    $init['forced_root_block']       = false;
    $init['paste_as_text']           = true;

    return $init;
}


//function materialis_companion_remove_page_attribute_support($post)
//{
//    $companion = \Materialis\Companion::instance();
//    if ($post && $companion->isFrontPage($post->ID)) {
////        remove_meta_box('pageparentdiv', 'page', 'side');
//
//    }
//}
//
//add_action('edit_form_after_editor', 'materialis_companion_remove_page_attribute_support');


add_filter('materialis_header_presets', 'materialis_companion_header_presets_pro_info');

function materialis_companion_header_presets_pro_info($presets)
{


    if (apply_filters('materialis_show_info_pro_messages', true)) {
        $companion = \Materialis\Companion::instance();

        $proPresets = $companion->themeDataPath("/pro-only-presets.php");
        if (file_exists($proPresets)) {
            $proPresets = require_once($proPresets);
        } else {
            $proPresets = array();
        }

        $presets = array_merge($presets, $proPresets);

    }

    return $presets;
}

add_action('cloudpress\customizer\add_assets', 'materialis_load_theme_customizer_scripts', 10, 3);

function materialis_load_theme_customizer_scripts($customizer, $jsUrl, $cssUrl)
{
    $ver = $customizer->companion()->version;
    wp_enqueue_script('cp-customizer-shortcodes-theme-data', $customizer->companion()->themeDataURL("/assets/customizer/customizer-shortcodes.js"), array('customizer-base'), $ver, true);
}


function materialis_get_custom_mods()
{
    return array(
        ".header-homepage .hero-title" => array(
            'type' => 'data-theme',
            'mod'  => "header_title",
        ),

        ".header-homepage p.header-subtitle" => array(
            'type' => 'data-theme',
            'mod'  => "header_subtitle",
        ),

        ".header-homepage p.header-subtitle2" => array(
            'type' => 'data-theme',
            'mod'  => "header_subtitle2",
        ),

        "#footer-container.footer-7 .footer-description" => array(
            'type' => 'data-theme',
            'mod'  => "footer_content_box_text",
        ),
        ".footer .footer-box-1 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box1_content_text",
        ),
        ".footer .footer-box-2 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box2_content_text",
        ),
        ".footer .footer-box-3 > p"                      => array(
            'type' => 'data-theme',
            'mod'  => "footer_box3_content_text",
        ),

    );
}


add_filter('cloudpress\customizer\global_data', 'materialis_add_dynamic_mods_customizer_data');
function materialis_add_dynamic_mods_customizer_data($data)
{
    $materialis_custom_mods = materialis_get_custom_mods();
    $data['mods']           = apply_filters('materialis_dynamic_mods', $materialis_custom_mods);

    return $data;
}

add_action('cloudpress\companion\activated\materialis', function ($companion) {
    /** @var \Materialis\Companion $companion */
    $companion->__createFrontPage();
});


add_action('cloudpress\companion\deactivated\materialis', function ($companion) {
    /** @var \Materialis\Companion $companion */
    $companion->restoreFrontPage();
});


function materialis_get_sections_order($category)
{
    $companion = \Materialis\Companion::instance();
    $config    = $companion->loadConfig($companion->themeDataPath("/sections-order.php"));

    return (isset($config[$category]) ? $config[$category] : array());

}

add_filter('cloudpress\customizer\control\content_sections\category_data', function ($data, $category) {

    $order    = materialis_get_sections_order($category);
    $to_order = array();

    if ( ! count($order)) {
        return $data;
    }

    foreach ($order as $id) {
        if (isset($to_order[$id]) && is_string($to_order['content'])) {
            $to_order[] = $id;
        }
    }

    return array_replace(array_flip($to_order), $data);

}, 10, 2);


add_filter("cloudpress\customizer\page_settings", function ($setting_ids) {

    $setting_ids[] = "layout_section_settings_separator";
    $setting_ids[] = "layout_boxed_content_enabled";
    $setting_ids[] = "layout_boxed_content_background_color";
    $setting_ids[] = "layout_boxed_content_overlap_height";


    return $setting_ids;
});