<?php


add_action("materialis_header_background_overlay_settings", "materialis_front_page_header_overlap_options", 5, 5);

function materialis_front_page_header_overlap_options($section, $prefix, $group, $inner, $priority)
{
    if ($inner) {
        return;
    }
    $priority = 5;
    $prefix   = "header";
    $section  = "header_background_chooser";
    $group    = "";

    materialis_add_kirki_field(array(
        'type'      => 'checkbox',
        'settings'  => 'header_overlap',
        'label'     => esc_html__('Allow content to overlap header', 'materialis'),
        'default'   => true,
        'section'   => $section,
        'priority'  => $priority,
        'group'     => $group,
        'transport' => 'postMessage',
    ));

    materialis_add_kirki_field(array(
        'type'            => 'dimension',
        'settings'        => 'header_overlap_with',
        'label'           => esc_html__('Overlap with', 'materialis'),
        'default'         => '95px',
        'active_callback' => array(
            array(
                "setting"  => "header_overlap",
                "operator" => "==",
                "value"    => true,
            ),
        ),
        'output'          => array(
            array(
                "element"  => ".materialis-front-page.overlap-first-section .header-homepage",
                'property' => 'padding-bottom',
                'media_query'   => '@media (min-width: 768px)',
            ),
            array(
                "element"       => ".materialis-front-page.overlap-first-section .page-content div[data-overlap]:first-of-type > div:not([class*=\"section-separator\"]) ",
                'property'      => 'margin-top',
                'value_pattern' => "-$",
                'media_query'   => '@media (min-width: 768px)',
            ),
        ),
        'js_vars'         => array(
            array(
                "element"  => ".materialis-front-page.overlap-first-section .header-homepage",
                'property' => 'padding-bottom',
                'function' => 'style',
                'media_query'   => '@media (min-width: 768px)',
            ),
            array(
                "element"       => ".materialis-front-page.overlap-first-section .page-content div[data-overlap]:first-of-type > div:not([class*=\"section-separator\"]) ",
                'property'      => 'margin-top',
                'value_pattern' => "-$",
                'function'      => 'style',
                'media_query'   => '@media (min-width: 768px)',
            ),
        ),
        'transport'       => 'postMessage',
        'section'         => $section,
        'priority'        => $priority,
        'group'           => $group,
    ));
}

add_filter('body_class', function ($classes) {
    $overlap_mod = get_theme_mod('header_overlap', true);
    if (1 == intval($overlap_mod)) {
        $classes[] = "overlap-first-section";
    }

    return $classes;

});
