<?php

namespace Materialis;

class Companion
{

    private static $instance = null;
    private static $functionsFiles = array();

    private $_customizer = null;

    private $cpData = array();
    private $remoteDataURL = null;

    private $theme = null;
    public $themeName = null;
    private $themeSlug = null;
    public $version = null;
    private $path = null;

    private $getCustomizerDataCache = array();


    private $__configsLoaded = array();

    public function __construct($root = null)
    {
        $theme           = wp_get_theme();
        $this->themeName = $theme->get('Name');
        $this->path      = $root;

        // check for current theme in customize.php
        if ($previewedTheme = $this->checkForThemePreviewdInCustomizer()) {
            $this->themeSlug = $previewedTheme["TextDomain"];
            $this->themeName = $previewedTheme["Name"];
        } else {
            // current theme is a child theme
            if ($this->theme->get('Template')) {
                $template          = $this->theme->get('Template');
                $templateThemeData = wp_get_theme($template);
                $this->themeSlug   = $templateThemeData->get('TextDomain');
                $this->themeName   = $templateThemeData->get('Name');
            } else {
                $this->themeSlug = $this->theme->get('TextDomain');
            }

        }

        if ( ! $this->isCurrentThemeSupported()) {
            return;
        }

        if (file_exists($this->themeDataPath("/functions.php"))) {
            require_once $this->themeDataPath("/functions.php");
        }

        if (file_exists($this->themeDataPath("/integrations/index.php"))) {
            require_once $this->themeDataPath("/integrations/index.php");
        }

        if ( ! self::$instance) {
            self::$instance = $this;
            add_action('init', array($this, 'initCompanion'));
            $this->registerActivationHooks();
        } else {
            add_filter('cloudpress\companion\cp_data', array($this, 'getInstanceData'));
        }

        $this->version = $this->getCustomizerData('version');
    }

    public function checkForThemePreviewdInCustomizer()
    {
        $theme                   = false;
        $is_customize_admin_page = (is_admin() && 'customize.php' == basename($_SERVER['PHP_SELF']));
        $keys                    = array('changeset_uuid', 'customize_changeset_uuid', 'customize_theme', 'theme', 'customize_messenger_channel', 'customize_autosaved');
        $input_vars              = array_merge(
            wp_array_slice_assoc($_GET, $keys),
            wp_array_slice_assoc($_POST, $keys)
        );

        if ($is_customize_admin_page && isset($input_vars['theme'])) {
            $theme = $input_vars['theme'];
        } else if (isset($input_vars['customize_theme'])) {
            $theme = $input_vars['customize_theme'];
        }

        $themeData  = wp_get_theme($theme);
        $textDomain = $themeData->get('TextDomain');
        $name       = $themeData->get('Name');

        if ($themeData->get('Template')) {
            $parentThemeData = wp_get_theme($themeData->get('Template'));
            $textDomain      = $parentThemeData->get('TextDomain');
            $name            = $parentThemeData->get('Name');
        }

        return array(
            'TextDomain' => $textDomain,
            'Name'       => $name,
        );
    }

    public function registerActivationHooks()
    {
        $self = $this;

        register_activation_hook($this->path, function () use ($self) {
            do_action('cloudpress\companion\activated\\' . $self->getThemeSlug(), $self);
        });

        register_deactivation_hook($this->path, function () use ($self) {
            do_action('cloudpress\companion\deactivated\\' . $self->getThemeSlug(), $self);
        });
    }

    public function setPageContent($data, $filter_context)
    {
        $status = $filter_context['status'];

        if ($status == "draft" && isset($data["materialis-pro::page_content"])) {
            $page_id = isset($_POST['customize_post_id']) ? intval($_POST['customize_post_id']) : -1;

            $encode        = false;
            $pages_content = $data["materialis-pro::page_content"];
            $pages_content = $pages_content["value"];
            if (is_string($pages_content)) {
                $pages_content = json_decode($pages_content, true);
                $encode        = true;
            }

            $page_content = $pages_content[$page_id];
            $page_content = preg_replace('/<!--@@CPPAGEID\[(.*)\]@@-->/s', '', $page_content);

            if ($page_id != -1) {

                $post = get_post($page_id);

                wp_create_post_autosave(array(
                    'post_ID'      => $page_id,
                    'post_content' => $page_content,
                    'post_title'   => $post->post_title,
                ));

                //unset($pages_content[$page_id]);

                if ($encode) {
                    $data["materialis-pro::page_content"]["value"] = json_encode($pages_content);
                } else {
                    $data["materialis-pro::page_content"]["value"] = $pages_content;
                }

            }
        }

        return $data;
    }

    public function initCompanion()
    {

        $this->checkNotifications();

        $this->checkIfCompatibleChildTheme();

        $this->_customizer = new \Materialis\Customizer\Customizer($this);
        \Materialis\Customizer\Template::load($this);
        \Materialis\Customizer\ThemeSupport::load();

        add_action('wp_ajax_create_home_page', array($this, 'createFrontPage'));

        add_action('wp_ajax_cp_open_in_customizer', array($this, 'openPageInCustomizer'));
        add_action('wp_ajax_cp_shortcode_refresh', array($this, 'shortcodeRefresh'));

        add_filter('page_row_actions', array($this, 'addEditInCustomizer'), 0, 2);

        add_action('admin_footer', array($this, 'addAdminScripts'));

        add_action('media_buttons', array($this, 'addEditInCustomizerPageButtons'));

        add_filter('is_protected_meta', array($this, 'isProtectedMeta'), 10, 3);

        add_filter("customize_changeset_save_data", array($this, 'setPageContent'), -10, 2);

        // loadKirkiCss Output Components;
        $this->setKirkiOutputFields();

        // look for google fonts
        $this->addGoogleFonts();

        do_action('cloudpress\companion\ready', $this);

        add_action('customize_register', function ($wp_customize) {
            \Materialis\KirkiControls\SectionSettingControl::load();
            $wp_customize->register_control_type("Materialis\\KirkiControls\\SectionSettingControl");

            add_filter('kirki/control_types', function ($controls) {

                if (class_exists("Materialis\\KirkiControls\\SectionSettingControl")) {
                    $controls['sectionsetting'] = "Materialis\\KirkiControls\\SectionSettingControl";
                }

                return $controls;
            });

        });

        // add post meta revisions (to revert from a page editable back to normal);
        $this->addMaintainableMetaToRevision();
    }

    public function getMaintainableKeysLabelPair($fields = array())
    {
        $fields = array_merge($fields, array(
            'is_' . $this->themeSlug . '_front_page'        => 'Is ' . $this->themeName . ' Front Page',
            'is_' . $this->themeSlug . '_maintainable_page' => 'Is ' . $this->themeName . ' FMaintainable Page',
        ));

        return $fields;
    }

    public function getMaintainableMetaKeys()
    {
        $keys = $this->getMaintainableKeysLabelPair();

        return array_keys($keys);

    }

    public function addMaintainableMetaToRevision()
    {
        $keys = $this->getMaintainableMetaKeys();
        foreach ($keys as $key) {
            add_filter("_wp_post_revision_field_{$key}", array($this, 'getMetaFieldRevision'), 10, 2);
        }

//        add_filter('_wp_post_revision_fields', array($this, 'getMaintainableKeysLabelPair'));
        add_action('save_post', array($this, 'saveMetaFieldRevision'), 10, 2);
        add_action('wp_restore_post_revision', array($this, 'restoreMetaFieldRevision'), 10, 2);

    }

    public function getMetaFieldRevision($value, $field)
    {
        global $revision;

        return get_metadata('post', $revision->ID, $field, true);

    }

    public function saveMetaFieldRevision($post_id, $post)
    {
        if ($parent_id = wp_is_post_revision($post_id)) {

            $parent = get_post($parent_id);

            $keys = $this->getMaintainableMetaKeys();
            foreach ($keys as $key) {
                $meta_value = get_post_meta($parent->ID, $key, true);
                if ($meta_value) {
                    add_metadata('post', $post_id, $key, $meta_value);
                }
            }
        }
    }

    public function restoreMetaFieldRevision($post_id, $revision_id)
    {
        if ($parent_id = wp_is_post_revision($revision_id)) {

            $keys = $this->getMaintainableMetaKeys();
            foreach ($keys as $key) {
                $meta_value = get_metadata('post', $revision_id, $key, true);

                if ($meta_value) {
                    update_post_meta($post_id, $key, $meta_value);
                } else {
                    delete_post_meta($post_id, $key);
                }
            }
        }
    }

    public function checkNotifications()
    {
        $notifications = $this->themeDataPath("/notifications.php");
        if (file_exists($notifications)) {
            $notifications = require_once $notifications;
        } else {
            $notifications = array();
        }

        \Materialis\Notify\NotificationsManager::load($notifications);
    }

    public function setKirkiOutputFields()
    {
        global $wp_customize;

        if ( ! class_exists("\Kirki")) {
            return;
        }

        // is managed in customizer;
        if ($wp_customize) {
            return;
        }

        $settings = (array)$this->getCustomizerData("customizer:settings");

        foreach ($settings as $id => $data) {
            $controlClass = self::getTreeValueAt($data, "control:class", "");
            if (strpos($controlClass, "kirki:") === 0 && self::getTreeValueAt($data, "control:wp_data:output")) {
                $configArgs = self::getTreeValueAt($data, "wp_data", array());
                \Kirki::add_config($id, $configArgs);

                $fieldArgs             = self::getTreeValueAt($data, "control:wp_data", array());
                $fieldArgs['type']     = str_replace("kirki:", "", $controlClass);
                $fieldArgs['settings'] = $id;
                $fieldArgs['section']  = self::getTreeValueAt($data, "section");

                if ( ! isset($fieldArgs['default'])) {
                    $fieldArgs['default'] = self::getTreeValueAt($data, "wp_data:default", array());
                }

                \Kirki::add_field($id, $fieldArgs);
            }
        }

    }

    public function loadJSON($path)
    {

        if ( ! file_exists($path)) {
            return array();
        }

        $content = file_get_contents($path);

        return json_decode($content, true);
    }

    public function loadConfig($path, $default = array())
    {

        if (isset($this->__configsLoaded[$path])) {
            return $this->__configsLoaded[$path];
        }

        if ( ! file_exists($path)) {
            return $default;
        }

        $content = require $path;

        $this->__configsLoaded[$path] = $content;

        return $content;
    }

    public function requireCPData($filter = true)
    {
        $cpData = get_theme_mod('theme_options', null);
        if ( ! $cpData) {
            $site = site_url();
            $site = preg_replace("/http(s)?:\/\//", "", $site);
            $key  = get_theme_mod('theme_pro_key', 'none');

            $cpData = $this->loadJSON($this->themeDataPath("/data.json"));

            if ( ! $cpData) {
                if ($this->remoteDataURL) {
                    require_once ABSPATH . WPINC . "/pluggable.php";

                    $url = $this->remoteDataURL . "/" . $this->themeSlug;

                    // allow remote url
                    add_filter('http_request_args', function ($r, $_url) use ($url) {
                        if ($url === $_url) {
                            $r['reject_unsafe_urls'] = false;
                        }

                        return $r;
                    }, 10, 2);

                    $data = wp_safe_remote_get(
                        $url,
                        array(
                            'method'      => 'GET',
                            'timeout'     => 45,
                            'redirection' => 5,
                            'blocking'    => true,
                            'httpversion' => '1.0',
                            'body'        => array(
                                'site' => $site,
                                'key'  => $key,
                            ),
                        )
                    );
                    if ($data instanceof \WP_Error) {
                        //TODO: Add a nicer message here
                        ob_get_clean();
                        wp_die('There was an issue connecting to the theme server. Please contact the theme support!');
                    } else {
                        //TODO: Load remote data
                        // $cpData = {};
                        // set_theme_mod('theme_options',$this->cpData);
                    }
                }
            }
        }

        if ($filter) {
            $cpData = apply_filters("cloudpress\companion\cp_data", $cpData, $this);
        }

        $this->cpData = $cpData;

        return $cpData;
    }

    public function getInstanceData()
    {
        return $this->requireCPData(false);
    }

    public function getCustomizerData($key = null, $filter = true)
    {
        if (isset($this->getCustomizerDataCache[$key])) {
            return $this->getCustomizerDataCache[$key];
        }

        if ( ! is_array($this->cpData)) {
            return array();
        }

        $this->requireCPData($filter);

        if ($key === null) {
            return $this->cpData;
        }

        $result = self::getTreeValueAt($this->cpData, $key);

        $this->getCustomizerDataCache[$key] = $result;

        return $result;
    }

    public function isCurrentThemeSupported()
    {
        $supportedThemes = (array)$this->getCustomizerData('themes', false);
        $currentTheme    = $this->themeSlug;

        $supported = (in_array($currentTheme, $supportedThemes) || in_array('*', $supportedThemes));

        return $supported;
    }

    public function checkIfCompatibleChildTheme()
    {
        $theme    = wp_get_theme();
        $response = false;
        if ($theme && $theme->get('Template')) {
            $template = $theme->get('Template');

            if (in_array($template, $this->getCustomizerData('themes'))) {
                add_filter('cloudpress\customizer\supports', "__return_true");
                $response = true;
            }

        }

        return $response;
    }

    public function isMaintainable($post_id = false)
    {

        if ( ! $post_id) {
            global $post;
            $post_id = ($post && property_exists($post, "ID")) ? $post->ID : false;
        }

        if ( ! $post_id) {
            return false;
        }

        $result = (
            ('1' === get_post_meta($post_id, 'is_' . $this->themeSlug . '_front_page', true))
            || ('1' === get_post_meta($post_id, 'is_' . $this->themeSlug . '_maintainable_page', true))
        );

        $result = $result || $this->applyOnPrimaryLanguage($post_id, array($this, 'isMaintainable'));

        return $result;
    }

    public function isProtectedMeta($protected, $meta_key, $meta_type)
    {
        $is_protected = array(
            'is_' . $this->themeSlug . '_front_page',
            'is_' . $this->themeSlug . '_maintainable_page',
        );
        if (in_array($meta_key, $is_protected)) {
            return true;
        }

        return $protected;
    }

    public function isMultipage()
    {
        return $this->getCustomizerData('theme_type') === "multipage";
    }

    public function getCurrentPageId()
    {
        global $post;
        $post_id = ($post && property_exists($post, "ID")) ? $post->ID : false;

        if ( ! $post_id) {
            return false;
        }

        $editablePostTypes = apply_filters('cloudpress\companion\editable_post_types', array("page"));

        if ( ! in_array($post->post_type, $editablePostTypes)) {
            return false;
        }

        return $post_id;
    }

    private function applyOnPrimaryLanguage($post_id, $callback)
    {
        $result = false;
        global $post;

        if (function_exists('pll_get_post') && function_exists('pll_default_language')) {
            $slug      = pll_default_language('slug');
            $defaultID = pll_get_post($post_id, $slug);
            $sourceID  = isset($_REQUEST['from_post']) ? $_REQUEST['from_post'] : null;
            $defaultID = $defaultID ? $defaultID : $sourceID;

            if ($defaultID && ($defaultID !== $post_id)) {
                $result = call_user_func($callback, $defaultID);
            }
        }

        global $sitepress;
        if ($sitepress) {
            $defaultLanguage = $sitepress->get_default_language();
            global $wpdb;

            $sourceTRID = isset($_REQUEST['trid']) ? $_REQUEST['trid'] : null;
            $trid       = $sitepress->get_element_trid($post_id);
            $trid       = $trid ? $trid : $sourceTRID;
            $defaultID  = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s",
                    $trid,
                    $defaultLanguage));

            if ($defaultID && ($defaultID !== $post_id)) {
                $result = call_user_func($callback, $defaultID);
            }
        }

        return $result;
    }

    public function isFrontPage($post_id = false)
    {

        if ( ! $post_id) {
            global $post;
            $post_id = ($post && property_exists($post, "ID")) ? $post->ID : false;
        }

        if ( ! $post_id) {
            return false;
        }

        $isFrontPage = '1' === get_post_meta($post_id, 'is_' . $this->themeSlug . '_front_page', true);

        $isWPFrontPage = is_front_page() && ! is_home();

        if ($isWPFrontPage && ! $isFrontPage && $this->isMaintainable($post_id)) {
            update_post_meta($post_id, 'is_' . $this->themeSlug . '_front_page', '1');
            delete_post_meta($post_id, 'is_' . $this->themeSlug . '_maintainable_page');
            $isFrontPage = true;
        }

        $isFrontPage = $isFrontPage || $this->applyOnPrimaryLanguage($post_id, array($this, 'isFrontPage'));

        return $isFrontPage;
    }

    public function getFrontPage()
    {
        $query = new \WP_Query(
            array(
                "post_status" => "publish",
                "post_type"   => 'page',
                "meta_key"    => 'is_' . $this->themeSlug . '_front_page',
            )
        );
        if (count($query->posts)) {
            return $query->posts[0];
        }

        return null;
    }

    public function loadMaintainablePageAssets($post, $template)
    {
        do_action('cloudpress\template\load_assets', $this, $post, $template);
    }

    public function rootPath()
    {
        return dirname($this->path);
    }

    public function rootURL()
    {
        $templateDir = wp_normalize_path(get_stylesheet_directory());
        $pluginDir   = wp_normalize_path(plugin_dir_path($this->path));
        $path        = wp_normalize_path($this->path);
        $url         = site_url();
        if (strpos($path, $templateDir) === 0) {
            $path = dirname($path);
            $abs  = wp_normalize_path(ABSPATH);
            $path = str_replace($abs, '/', $path);
            $url  = get_stylesheet_directory_uri() . $path;
        } else {
            $url = plugin_dir_url($this->path);
        }

        return untrailingslashit($url);
    }

    public function themeDataPath($rel = "")
    {
        return $this->rootPath() . "/theme-data/" . $this->themeSlug . $rel;
    }

    public function themeDataURL($rel = "")
    {
        return $this->rootURL() . "/theme-data/" . $this->themeSlug . $rel;
    }

    public function assetsRootURL()
    {
        return $this->rootURL() . "/assets";
    }

    public function assetsRootPath()
    {
        return $this->rootPath() . "/assets";
    }

    public function customizer()
    {
        return $this->_customizer;
    }

    public function getThemeSlug($as_fn_prefix = false)
    {
        $slug = $this->themeSlug;

        if ($as_fn_prefix) {
            $slug = str_replace("-", "_", $slug);
        }

        return $slug;
    }

    public function __createFrontPage()
    {
        $page = $this->getFrontPage();

        update_option($this->themeSlug . '_companion_old_show_on_front', get_option('show_on_front'));
        update_option($this->themeSlug . '_companion_old_page_on_front', get_option('page_on_front'));

        if ( ! $page) {
            $content = apply_filters('cloudpress\companion\front_page_content', "", $this);

            $post_id = wp_insert_post(
                array(
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                    'post_name'      => $this->themeName,
                    'post_title'     => 'Front Page',
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'page_template'  => apply_filters('cloudpress\companion\front_page_template', "page-templates/homepage.php"),
                    'post_content'   => $content,
                )
            );

            set_theme_mod($this->themeSlug . '_page_content', $content);
            update_option('show_on_front', 'page');
            update_option('page_on_front', $post_id);
            update_post_meta($post_id, 'is_' . $this->themeSlug . '_front_page', "1");

            if (null == get_page_by_title('Blog')) {
                $post_id = wp_insert_post(
                    array(
                        'comment_status' => 'closed',
                        'ping_status'    => 'closed',
                        'post_name'      => 'blog',
                        'post_title'     => 'Blog',
                        'post_status'    => 'publish',
                        'post_type'      => 'page',
                    )
                );
            }

            $blog = get_page_by_title('Blog');
            update_option('page_for_posts', $blog->ID);
        } else {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $page->ID);
            update_post_meta($page->ID, 'is_' . $this->themeSlug . '_front_page', "1");
        }
    }

    public function createFrontPage()
    {
        $nonce = @$_POST['create_home_page_nounce'];
        if ( ! wp_verify_nonce($nonce, 'create_home_page_nounce')) {
            die();
        }

        $this->__createFrontPage();
    }

    public function restoreFrontPage()
    {
        if ($this->getFrontPage()) {
            update_option('show_on_front', get_option($this->themeSlug . '_companion_old_show_on_front'));
            update_option('page_on_front', get_option($this->themeSlug . '_companion_old_page_on_front'));
        }
    }

    public function isWCPage($post)
    {
        if (function_exists('wc_get_page_id')) {
            $shopId      = wc_get_page_id('shop');
            $cartId      = wc_get_page_id('cart');
            $checkoutId  = wc_get_page_id('checkout');
            $myaccountId = wc_get_page_id('myaccount');

            switch ($post->ID) {
                case $shopId:
                case $cartId:
                case $checkoutId:
                case $myaccountId:
                    return true;
                    break;
                default:
                    return false;

            }

        } else {
            return false;
        }
    }

    public function canEditInCustomizer($post = null)
    {
        $canEdit = false;

        if ( ! $post) {
            global $post;
        }

        if (is_numeric($post)) {
            $post = get_post($post);
        }

        $editablePostTypes = apply_filters('cloudpress\companion\editable_post_types', array("page"));

        if ( ! $post || ! in_array($post->post_type, $editablePostTypes)) {
            return false;
        } else {
            if ($this->isWCPage($post)) {
                return false;
            } else {
                if ($this->isMaintainable($post->ID) || $this->isFrontPage($post->ID)) {
                    $canEdit = true;
                }
            }
        }

        return apply_filters('cloudpresss\companion\can_edit_in_customizer', $canEdit, $post);

    }

    public function addEditInCustomizer($actions, $post)
    {
        if ($this->canEditInCustomizer($post)) {

            $actions = array_merge(
                array(
                    "cp_page_builder" => '<a href="javascript:void();" onclick="cp_open_page_in_customizer(' . $post->ID . ')" >Edit in Customizer</a>',
                ),
                $actions
            );
        }

        return $actions;
    }

    public function addEditInCustomizerPageButtons()
    {
        global $post;

        if ($this->canEditInCustomizer($post)) {
            echo '<a href="javascript:void();"  onclick="cp_open_page_in_customizer(' . $post->ID . ')"  class="button button-primary">' . __('Edit In Customizer', 'cloudpress-companion') . '</a>';
        }
    }

    public function addAdminScripts()
    {

        ?>

        <style type="text/css">
            a:not(.button)[onclick*="cp_open_page_"] {
                background-color: #0073aa;
                color: #ffffff;
                padding: 0.2em 0.8em;
                line-height: 150%;
                border-radius: 4px;
                display: inline-block;
                text-transform: uppercase;
                font-size: 0.82em;
            }

            a:not(.button)[onclick*="cp_open_page_"]:hover {
                background-color: #0081bd;
            }
        </style>
        <?php

        global $post;

        if ( ! $post) {
            return;
        }

        if ($this->isMultipage()) {

            $title_placeholder = apply_filters('enter_title_here', __('Enter title here', 'materialis'), $post);

            ?>
            <style>
                input[name=new-page-name-val] {
                    padding: 3px 8px;
                    font-size: 1.7em;
                    line-height: 100%;
                    height: 1.7em;
                    width: 100%;
                    outline: none;
                    margin: 0 0 3px;
                    background-color: #fff;
                    border-style: solid;
                    border-color: #c3c3c3;
                    border-width: 1px;
                    margin-bottom: 10px;
                    margin-top: 10px;
                }

                input[name=new-page-name-val].error {
                    border-color: #f39e9e;
                    border-style: solid;
                    color: #f39e9e;
                }

                h1.cp-open-in-custmizer {
                    font-size: 23px;
                    font-weight: 400;
                    margin: 0;
                    padding: 9px 0 4px 0;
                    line-height: 29px;
                }

            </style>
            <div style="display: none;" id="open_page_in_customizer_set_name">
                <h1 class="cp-open-in-custmizer"><?php _e('Set a name for the new page', 'cloudpress-companion'); ?></h1>
                <input placeholder="<?php echo $title_placeholder ?>" class="" name="new-page-name-val"/>
                <button class="button button-primary" name="new-page-name-save"> <?php _e('Set Page Name', 'cloudpress-companion'); ?></button>
            </div>
            <script>
                function cp_open_page_in_customizer(page) {

                    var isAutodraft = jQuery('[name="original_post_status"]').length ? jQuery('[name="original_post_status"]').val() === "auto-draft" : false;

                    function doAjaxCall(pageName) {
                        var data = {
                            action: 'cp_open_in_customizer',
                            page: page
                        };

                        if (pageName) {
                            data['page_name'] = pageName;
                        }

                        jQuery.post(ajaxurl, data).done(function (response) {

                            window.location = response.trim();
                        });
                    }

                    if (isAutodraft) {

                        alert("<?php echo __('Page needs to be published before editing it in customizer', 'cloudpress-companion'); ?>");
                        return;

                        var title = jQuery('[name="post_title"]').val();
                        tb_show('Set Page Name', '#TB_inline?inlineId=open_page_in_customizer_set_name&height=150', false);
                        var TB_Window = jQuery('#TB_window').height('auto');

                        var titleInput = TB_Window.find('[name="new-page-name-val"]');

                        titleInput.val(title).on('keypress', function () {
                            jQuery(this).removeClass('error');
                        });

                        TB_Window.find('[name="new-page-name-save"]').off('click').on('click', function () {
                            var newTitle = titleInput.val().trim();
                            if (newTitle.length == 0) {
                                titleInput.addClass('error');
                                return;
                            } else {
                                doAjaxCall(newTitle);
                            }
                        });

                    } else {
                        doAjaxCall();
                    }

                }
            </script>
            <?php

        }
    }

    public function wrapPostContentInSection($post_id)
    {
        $post    = get_post($post_id);
        $content = $post->post_content;

        if (trim($content)) {

            $content = wpautop($content);

            $content = "<div data-id='initial-content-section' data-export-id='initial-content-section' data-label='Initial Content' id='initial-content-section' class='content-section content-section-spacing'>\n" .
                       "   <div class='gridContainer'>\n" .
                       "     <div class='row'>\n\n" .
                       "        <div class='col-xs-12 col-sm-12'>" .
                       "                {$content}\n" .
                       "          </div>\n" .
                       "      </div>\n" .
                       "  </div>\n" .
                       "</div>\n";

            if ( ! has_action('pre_post_update', 'wp_save_post_revision')) {
                add_action('pre_post_update', 'wp_save_post_revision');
            }

            wp_update_post(array(
                'ID'           => $post_id,
                'post_content' => $content,
            ));
        }
    }

    public function openPageInCustomizer()
    {
        $post_id = intval($_REQUEST['page']);
        $toMark  = isset($_REQUEST['mark_as_editable']);

        $post = get_post($post_id);

        if ($post) {

            if ($post->post_status === "auto-draft" || $post->post_status === "draft") {

                /*
                wp_publish_post($post_id);

                $title    = isset($_REQUEST['page_name']) ? wp_kses_post($_REQUEST['page_name']) : __('Untitled Page - ' . date("Y-m-d H:i:s"), 'cloudpress-companion');
                $new_slug = sanitize_title($title);
                wp_update_post(array(
                    'ID'         => $post_id,
                    'post_title' => $title,
                    'post_name'  => $new_slug // do your thing here
            ));*/
            }

            $isMarked = get_post_meta($post_id, 'is_' . $this->themeSlug . '_maintainable_page', true);

            if ( ! intval($isMarked) && $toMark) {
                update_post_meta($post_id, 'is_' . $this->themeSlug . '_maintainable_page', "1");
                $this->wrapPostContentInSection($post_id);
                $template = get_post_meta($post_id, '_wp_page_template', true);
                if ( ! $template || $template === "default") {
                    update_post_meta($post_id, '_wp_page_template', apply_filters('materialis_maintainable_default_template', "full-width-page.php"));
                }

                do_action('cloudpress\ajax\after_maintainable_mark', $post_id);
            }

        }

        $url = $this->get_page_link($post_id);

        ?>
        <?php echo admin_url('customize.php') ?>?url=<?php echo urlencode($url) ?>
        <?php

        exit;
    }

    public function get_page_link($post_id)
    {
        global $sitepress;
        $url = false;
        if ($sitepress) {
            $url           = get_page_link($post_id);
            $args          = array('element_id' => $post_id, 'element_type' => 'page');
            $language_code = apply_filters('wpml_element_language_code', null, $args);
            $url           = apply_filters('wpml_permalink', $url, $language_code);
        }

        if ( ! $url) {
            $url = get_permalink($post_id);
        }

        return $url;
    }

    public function shortcodeRefresh()
    {
        if ( ! is_user_logged_in() || ! current_user_can('edit_theme_options')) {
            die();
        }

        add_filter('materialis_is_shortcode_refresh', '__return_true');

        $shortcode = isset($_REQUEST['shortcode']) ? $_REQUEST['shortcode'] : false;
        $context   = isset($_REQUEST['context']) ? $_REQUEST['context'] : array();

        if ( ! $shortcode) {
            die();
        }

        $shortcode = base64_decode($shortcode);

        $query   = isset($context['query']) ? $context['query'] : array();
        $content = "";
//        $wp_query = new \WP_Query($query);
        if (count($query)) {
            query_posts($query);
            while (have_posts()) {
                the_post();
                $content .= do_shortcode($shortcode);
            }

            wp_reset_query();
        } else {
            $content .= do_shortcode($shortcode);
        }

        die($content);

    }

    public function addGoogleFonts()
    {
        $self = $this;

        /**
         * Add preconnect for Google Fonts.
         */
        add_filter('wp_resource_hints', function ($urls, $relation_type) use ($self) {
            if (wp_style_is($self->getThemeSlug() . '-fonts', 'queue') && 'preconnect' === $relation_type) {
                $urls[] = array(
                    'href' => 'https://fonts.gstatic.com',
                    'crossorigin',
                );
            }

            return $urls;
        }, 10, 2);


    }


    // SINGLETON

    public static function instance()
    {
        return self::$instance;
    }

    public static function letToNum($size)
    {
        $l   = substr($size, -1);
        $ret = substr($size, 0, -1);
        switch (strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }

        return $ret;
    }

    public static function load($pluginFile)
    {
        $currentMemoryLimit = @ini_get('memory_limit');
        $desiredMemory      = '256M';

        if ($currentMemoryLimit) {
            if (self::letToNum($currentMemoryLimit) && self::letToNum($desiredMemory)) {
                @ini_set('memory_limit', $desiredMemory);
            }
        }

        new \Materialis\Companion($pluginFile);
    }

    public static function getTreeValueAt($tree, $path, $default = null)
    {
        $result   = $tree;
        $keyParts = explode(":", $path);
        if (is_array($result)) {
            foreach ($keyParts as $part) {
                if ($result && isset($result[$part])) {
                    $result = $result[$part];
                } else {
                    return $default;
                }
            }
        }

        return $result;
    }

    public static function prefixedMod($mod, $prefix = null)
    {
        $prefix = $prefix ? $prefix : self::instance()->getThemeSlug();
        $prefix = str_replace("-", "_", $prefix);

        return $prefix . "_" . $mod;
    }

    public static function getThemeMod($mod, $default = false)
    {
        global $wp_customize;

        if ($wp_customize) {
            $settings = $wp_customize->unsanitized_post_values();

            $key = "CP_AUTO_SETTING[" . $mod . "]";
            if (isset($settings[$key])) {
                return $settings[$key];
            } else {
                $exists = apply_filters('cloudpress\customizer\temp_mod_exists', false, $mod);
                if ($exists) {
                    return apply_filters('cloudpress\customizer\temp_mod_content', false, $mod);
                }
            }
        }

        if ($default === false) {
            $default                = self::instance()->getCustomizerData("customizer:settings:{$mod}:wp_data:default");
            $alternativeTextDomains = (array)self::instance()->getCustomizerData('alternativeTextDomains:' . self::instance()->getThemeSlug());

            if ( ! $default) {
                foreach ($alternativeTextDomains as $atd) {
                    $mod     = self::prefixedMod($mod, $atd);
                    $default = self::instance()->getCustomizerData("customizer:settings:{$mod}:wp_data:default");
                    if ($default !== null) {
                        break;
                    }
                }
            }
        }

        $result = $default;
        $temp   = get_theme_mod(self::prefixedMod($mod), "CP_UNDEFINED_THEME_MOD");
        if ($temp !== "CP_UNDEFINED_THEME_MOD") {
            $result = $temp;
        } else {
            $result                 = "CP_UNDEFINED_THEME_MOD";
            $alternativeTextDomains = (array)self::instance()->getCustomizerData('alternativeTextDomains:' . self::instance()->getThemeSlug());
            foreach ($alternativeTextDomains as $atd) {
                $temp = get_theme_mod(self::prefixedMod($mod, $atd), "CP_UNDEFINED_THEME_MOD");
                if ($temp !== "CP_UNDEFINED_THEME_MOD") {
                    $result = $temp;
                    break;
                }
            }

            if ($result === "CP_UNDEFINED_THEME_MOD") {
                $result = get_theme_mod($mod, $default);
            }
        }

        return $result;
    }

    public static function echoMod($mod, $default = false)
    {
        echo self::getThemeMod($mod, $default);
    }

    public static function echoURLMod($mod, $default = false)
    {
        $value = self::getThemeMod($mod, $default);
        $value = str_replace('[tag_companion_uri]', self::instance()->themeDataURL(), $value);
        echo esc_url($value);
    }

    public static function filterDefault($data)
    {
        if (is_array($data)) {
            $data = self::filterArrayDefaults($data);
        } else {
            $data = str_replace('[tag_companion_uri]', \Materialis\Companion::instance()->themeDataURL(), $data);
            $data = str_replace('[tag_theme_uri]', get_template_directory_uri(), $data);

            $data = str_replace('[tag_companion_dir]', \Materialis\Companion::instance()->themeDataPath(), $data);
            $data = str_replace('[tag_theme_dir]', get_template_directory(), $data);
            $data = str_replace('[tag_style_uri]', get_stylesheet_directory_uri(), $data);
        }

        return $data;
    }

    public static function filterArrayDefaults($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = \Materialis\Companion::filterDefault($value);
        }

        return $data;
    }

    public static function dataURL($path = '')
    {
        return self::instance()->themeDataURL($path);
    }

    public static function translateArgs($data)
    {
        if (isset($data['title'])) {
            $data['title'] = __($data['title'], 'cloudpress-companion');
        }

        if (isset($data['label'])) {
            $data['label'] = __($data['label'], 'cloudpress-companion');
        }

        if (isset($data['choices'])) {
            foreach ($data['choices'] as $key => $value) {
                if (strpos($value, "#") === false && is_string($key)) {
                    $data['choices'][$key] = __($value, 'cloudpress-companion');
                }
            }
        }

        return $data;
    }

    public static function loadJSONFile($path)
    {
        Companion::instance()->loadJSON($path);
    }
}
