<?php


if ( ! defined('ABSPATH')) {
    die('Silence is golden');
}

add_filter('materialis_integration_modules', function ($integrations) {

    $integrationBasePath = dirname(__FILE__);

    $integrations = array_merge($integrations, array(
//        "{$integrationBasePath}/demo-imports",
    ));

    return $integrations;
});
