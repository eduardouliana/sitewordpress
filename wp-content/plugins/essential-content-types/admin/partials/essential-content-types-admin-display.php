<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       catchplugins.com
 * @since      1.0.0
 *
 * @package    Essential_Content_types
 * @subpackage Essential_Content_types/admin/partials
 */

?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Essential Content Types', 'essential-content-types' ); ?></h1>
    <div id="plugin-description">
        <p><?php esc_html_e( 'Essential Content Types allows you to feature the impressive content through different content/post types on your website just the way you want it. These content/post types are missed by the themes in WordPress Theme Directory as the feature falls more towards the plugins’ territory.', 'essential-content-types' ); ?></p>
    </div>
    <div class="catchp-content-wrapper">
        <div class="catchp_widget_settings">
            <form id="essential-content-types-main" method="post" action="options.php">

                <h2 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" id="dashboard-tab" href="#dashboard"><?php esc_html_e( 'Dashboard', 'essential-content-types' ); ?></a>
                    <a class="nav-tab" id="features-tab" href="#features"><?php esc_html_e( 'Features', 'essential-content-types' ); ?></a>
                    <a class="nav-tab" id="premium-extensions-tab" href="#premium-extensions"><?php esc_html_e( 'Compare Table', 'essential-content-types' ); ?></a>
                </h2>

                <div id="dashboard" class="wpcatchtab nosave active">

                    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/dashboard-display.php'; ?>

                    <div id="go-premium" class="content-wrapper col-2">

                        <div class="header">
                            <h2><?php esc_html_e( 'Go Premium!', 'essential-content-types' ); ?></h2>
                        </div> <!-- .Header -->

                        <div class="content">
                            <button type="button" class="button dismiss">
                                <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this item.', 'essential-content-types' ); ?></span>
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                            <ul class="catchp-lists">
                                <li><strong><?php esc_html_e( 'Featured Content Widget', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Portfolio Widget', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Testomonials Widget', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Services Widget', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Ability to add shortcodes via button', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Flexible Image Size', 'essential-content-types' ); ?></strong></li>
                                <li><strong><?php esc_html_e( 'Testimonial Slider Option', 'essential-content-types' ); ?></strong></li>
                            </ul>
                            <a href="https://catchplugins.com/plugins/essential-content-types-pro/" target="_blank"><?php esc_html_e( 'Find out why you should upgrade to Essential Content Types Premium »', 'essential-content-types' ); ?></a>
                        </div> <!-- .Content -->
                    </div> <!-- #go-premium -->

                </div><!-- .dashboard -->

                <div id="features" class="wpcatchtab save">
                    <div class="content-wrapper col-3">
                        <div class="header">
                            <h3><?php esc_html_e( 'Features', 'essential-content-types' ); ?></h3>

                        </div><!-- .header -->
                        <div class="content">
                            <ul class="catchp-lists">
                                <li>
                                    <strong><?php esc_html_e( 'Portfolio', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'Portfolio enables you to showcase your professional accomplishments to the world at large. Display your most impressive Portfolios in the way you like. You also have the option to choose your Portfolio layout up to 6 columns. A marvelous way to easily display your experience and expertise.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Testimonials', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'People are always after authenticity. They are always looking for ways to know what your goods or services are really like. Customer testimonials really help in building trust. You can customize the testimonials as you want and also select your testimonial layout type up to 6 columns.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Featured Content', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'Featured Content allows you to showcase your recent and popular posts on your website. Your Featured Content can be displayed up to 1 to 6 columns. Enable the Featured Content option and display your most impressive posts.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Services', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'Every website owner wants people to know about eh services they provide. You can place the services you provide right on the spotlight. Choose to display the services in 1 to 6 columns. Display your services and let the world know what you can provide them with.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Supports all themes on WordPress', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'You don’t have to worry if you have a slightly different or complicated theme installed on your website. It supports all the themes on WordPress and makes your website more striking and playful.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Lightweight', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'It is extremely lightweight. You do not need to worry about it affecting the space and speed of your website.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Order', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'You have the freedom to choose how your content would be displayed—whether in ascending or descending alphabetical order by author name, title, date, or in random order.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Enable/Disable any content/post type as needed', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'With this option, you can choose whether you want to display your content/post type or not. You can enable or disable any content or post type as per your need.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Super Simple to Set Up', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'It is super easy to set up. Even the beginners can set it up easily and also, you do not need to have any coding knowledge. Just install, activate, customize it your way and enjoy the plugin.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Responsive Design', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'One of the key features of our plugins is that your website will magically respond and adapt to different screen sizes delivering an optimized design for iPhones, iPads, and other mobile devices. No longer will you need to zoom and scroll around when browsing on your mobile phone.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Incredible Support', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'We have a great line of support team and support documentation. You do not need to worry about how to use the plugins we provide, just refer to our Tech Support Forum. Further, if you need to do advanced customization to your website, you can always hire our theme customizer!', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Widgets', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'Multitude of widget options provide you with the option to choose the widgets that you want to display. You can have full control over each widget’s visibility and appearance. You can assign different contents on your sidebars, footer and any sidebar widgets.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Shortcodes', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'With Shortcodes, you have the option to use the powerful shortcode options to style multiple contents in completely different ways.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Number of Posts', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'You have the option to choose the number of posts you want to display on your website. Pick the number of posts that suits the best on your website.', 'essential-content-types-pro' ); ?></p>
                                </li>

                                <li>
                                    <strong><?php esc_html_e( 'Column Option', 'essential-content-types-pro' ); ?></strong>
                                    <p><?php esc_html_e( 'Column Option allows you to choose from multiple column options. Several options are available for all column types in general to edit the default behavior.', 'essential-content-types-pro' ); ?></p>
                                </li>
                            </ul>
                        <a href="https://catchplugins.com/plugins/essential-content-types-pro/" target="_blank"><?php esc_html_e( 'Upgrade to Essential Content Types Premium »', 'essential-content-types' ); ?></a>
                        </div><!-- .content -->
                    </div><!-- content-wrapper -->
                </div> <!-- Featured -->

                <div id="premium-extensions" class="wpcatchtab  save">

                    <div class="about-text">
                        <h2><?php esc_html_e( 'Get Essential Content Types Pro -', 'essential-content-types' ); ?> <a href="https://catchplugins.com/plugins/essential-content-types-pro/" target="_blank"><?php esc_html_e( 'Get It Here!', 'essential-content-types' ); ?></a></h2>
                        <p><?php esc_html_e( 'You are currently using the free version of Essential Content Types.', 'essential-content-types' ); ?><br />
<a href="https://catchplugins.com/plugins/" target="_blank"><?php esc_html_e( 'If you have purchased from catchplugins.com, then follow this link to the installation instructions (particularly step 1).', 'essential-content-types' ); ?></a></p>
                    </div>

                    <div class="content-wrapper">
                        <div class="header">
                            <h3><?php esc_html_e( 'Compare Table', 'essential-content-types' ); ?></h3>

                        </div><!-- .header -->
                        <div class="content">

                            <table class="widefat fixed striped posts">
                                <thead>
                                    <tr>
                                        <th id="title" class="manage-column column-title column-primary"><?php esc_html_e( 'Features', 'essential-content-types' ); ?></th>
                                        <th id="free" class="manage-column column-free"><?php esc_html_e( 'Free', 'essential-content-types' ); ?></th>
                                        <th id="pro" class="manage-column column-pro"><?php esc_html_e( 'Pro', 'essential-content-types' ); ?></th>
                                    </tr>
                                </thead>

                                <tbody id="the-list" class="ui-sortable">
                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Responsive Design', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Super Easy Setup', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Lightweight', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Number of Posts', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Order', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Shortcode', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Custom Post Types', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Column Option', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Fixed Image Size', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Portfolio', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Featured Content', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Services', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Testimonial', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-green">&#10003;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Food Menu', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Flexible Image Size', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Testimonial Slider Option', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Menu Title Font Size', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Portfolio Widget', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Featured Image Widget', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Testimonials  Widget', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Services  Widget', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Add Shortcode Button', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                    <tr class="iedit author-self level-0 type-post status-publish format-standard hentry">
                                        <td>
                                            <strong><?php esc_html_e( 'Ads-free Dashboard', 'essential-content-types' ); ?></strong>
                                        </td>
                                        <td class="column column-free"><div class="table-icons icon-red">&#215;</div></td>
                                        <td class="column column-pro"><div class="table-icons icon-green">&#10003;</div></td>
                                    </tr>

                                </tbody>

                            </table>

                        </div><!-- .content -->
                    </div><!-- content-wrapper -->
                </div>

            </form><!-- #essential-content-types-main -->

        </div><!-- .catchp_widget_settings -->


        <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/sidebar.php'; ?>
    </div> <!-- .catchp-content-wrapper -->

    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . '/partials/footer.php'; ?>
</div><!-- .wrap -->
