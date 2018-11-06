=== Essential Content Types ===
Contributors: catchplugins, catchthemes, sakinshrestha, pratikshrestha, maheshmaharjan
Donate link: https://catchplugins.com/plugins/essential-content-types-pro/
Tags: custom post types, CPT, CMS, post, types, post type, taxonomy, tax, custom, content types, post types, custom content types, testimonial, portfolio, featured content, service
Requires at least: 4.5
Tested up to: 4.9.6
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Essential Content Types allows you to feature the impressive content through different content/post types on your website just the way you want it. These content/post types are missed by the themes in WordPress Theme Directory as the feature falls more towards the plugins’ territory.

== Description ==

**Essential Content Types** allows you to feature the impressive content through different content/post types on your website just the way you want it. These content/post types are missed by the themes in WordPress Theme Directory as the feature falls more towards the plugins' territory.

Content is at the forefront of any website. Changing the layout of your important content changes the way your website looks, which may not be a plus point if your current website layout is loved by your users.

Additionally, switching themes changes your website layout completely. Therefore, to keep things looking spic-and- span in your website, we bring you Essential Content.

Essential Content allows you to add up to three content/post types:
* Portfolio – Create and display your portfolio on your website
* Testimonials – Add customer testimonials to your website
* Featured Content – Display the content you want as featured content on your website to attract visitors' attention
* Services – Add your services on your website

Features of Essential Content:
* Enable/Disable any content/post type as needed
* Light-weight
* Supports all themes on WordPress

Essential Content is inspired by *Jetpack's Custom Content Types feature*.

However, not everyone wants to have a plugin that “does-it- all”. Some may want plugins to be niche focused and concentrate on smaller areas.

That is precisely what we have done with Essential Content. Essential content, true to its name, has only the essential elements. We have added the features that WordPressers use most. We have ruled out all other elements to make it non-bloated and clean. It takes up lesser space and does the job well.

If you think we have missed any essential content/post types, please let us know. We’ll review the frequency of usage and add your suggestions.

***Portfolio Shortcode***

You can use shortcodes to embed portfolio projects on posts and pages.

**Embedding Portfolio Projects**

To embed portfolio projects on posts and pages, first activate the Portfolio custom content type on your site and add some projects to your portfolio.

Next, add the `[portfolio]` shortcode to a post or page. The shortcode will display projects in different ways, depending on how you use the optional attributes to customize the portfolio layout.

**Attributes**
* display_types: display Project Types. (true/false)
* display_tags: display Project Tags. (true/false)
* display_content: display project content. (true/false)
* include_type: display specific Project Types. Defaults to all. (comma-separated list of Project Type slugs)
* include_tag: display specific Project Tags. Defaults to all. (comma-separated list of Project Tag slugs)
* columns: number of columns in shortcode. Defaults to 2. (number, 1-6)
* showposts: number of projects to display. Defaults to all. (number)
* order: display projects in ascending or descending order. Defaults to ASC for sorting in ascending order, but you can reverse the order by using DESC to display projects in descending order instead. (ASC/DESC)
* orderby: sort projects by different criteria, including author name, project title, and even rand to display in a random order. Defaults to sorting by date. (author, date, title, rand)

**Example**
`
[portfolio display_types="true" display_tags="false" include_type="grooming-tips,best-kitties" columns="2" showposts="10" orderby="title"]
`
The example will display up to ten portfolio projects in two columns, in ascending alphabetical order by project title. It will display Project Types, hide Project Tags, and only display projects that are assigned the “Grooming Tips” or “Best Kitties” Project Types.


***Featured Content Shortcode***

You can use shortcodes to embed featured content on posts and pages.

**Embedding Featured Content Projects**

To embed featured content on posts and pages, first activate the Featured Content content type on your site and add some projects to your featured content.

Next, add the `[featured_content]` shortcode to a post or page. The shortcode will display projects in different ways, depending on how you use the optional attributes to customize the featured content layout.

**Attributes**
* display_types: display Content Types. (true/false)
* display_tags: display Content Tags. (true/false)
* display_content: display project content. (true/false)
* include_type: display specific Content Types. Defaults to all. (comma-separated list of Content Type slugs)
* include_tag: display specific Content Tags. Defaults to all. (comma-separated list of Content Tag slugs)
* columns: number of columns in shortcode. Defaults to 2. (number, 1-6)
* showposts: number of projects to display. Defaults to all. (number)
* order: display projects in ascending or descending order. Defaults to ASC for sorting in ascending order, but you can reverse the order by using DESC to display projects in descending order instead. (ASC/DESC)
* orderby: sort projects by different criteria, including author name, project title, and even rand to display in a random order. Defaults to sorting by date. (author, date, title, rand)

**Example**
`
[featured_content display_types="true" display_tags="false" include_type="grooming-tips,best-kitties" columns="2" showposts="10" orderby="title"]
`
The example will display up to ten featured content in two columns, in ascending alphabetical order by project title. It will display Content Types, hide Content Tags, and only display projects that are assigned the “Grooming Tips” or “Best Kitties” Content Types.

***Testimonials Shortcode***

You can use shortcodes to embed testimonials on posts and pages.
Embedding Testimonials

To embed testimonials on posts and pages, first activate the Testimonial custom content type on your site and add some testimonials.

Next, add the `[testimonials]` shortcode to a post or page. The shortcode will display testimonials in different ways, depending on how you use the optional attributes to customize the testimonials layout.

**Attributes**
* display_content: display testimonial content. (full/true/false)
* image: display the featured image. (true/false)  Defaults to true.
* columns: number of columns in shortcode. Defaults to 1. (number, 1-6)
* showposts: number of testimonials to display. Defaults to all. (number)
* order: display testimonials in ascending or descending chronological order. Defaults to ASC for sorting in ascending order, but you can reverse the order by using DESC to display testimonials in descending order instead. (ASC/DESC)
* orderby: sort testimonials by different criteria, including author name, testimonial title, and even rand to display in a random order. Defaults to sorting by date. (author, date, title, rand)

**Example**
`
[testimonials columns="2" showposts="10" orderby="title"]
`
The example will display up to ten testimonials in two columns, in ascending alphabetical order by testimonial title.

***Service Shortcode***

You can use shortcodes to embed service on posts and pages.

**Embedding Service Projects**

To embed service on posts and pages, first activate the Service content type on your site and add some projects to your Service.

Next, add the `[services]` shortcode to a post or page. The shortcode will display projects in different ways, depending on how you use the optional attributes to customize the featured content layout.

**Attributes**
* display_types: display Content Types. (true/false)
* display_tags: display Content Tags. (true/false)
* display_content: display project content. (true/false)
* include_type: display specific Content Types. Defaults to all. (comma-separated list of Content Type slugs)
* include_tag: display specific Content Tags. Defaults to all. (comma-separated list of Content Tag slugs)
* columns: number of columns in shortcode. Defaults to 2. (number, 1-6)
* showposts: number of projects to display. Defaults to all. (number)
* order: display projects in ascending or descending order. Defaults to ASC for sorting in ascending order, but you can reverse the order by using DESC to display projects in descending order instead. (ASC/DESC)
* orderby: sort projects by different criteria, including author name, project title, and even rand to display in a random order. Defaults to sorting by date. (author, date, title, rand)

**Example**
`
[services display_types="true" display_tags="false" include_type="grooming-tips,best-kitties" columns="2" showposts="10" orderby="title"]
`
The example will display up to ten featured content in two columns, in ascending alphabetical order by project title. It will display Content Types, hide Content Tags, and only display projects that are assigned the “Grooming Tips” or “Best Kitties” Content Types.

***Food Menu Shortcode***

You can use shortcodes to embed Food Menu on posts and pages.
Embedding Food Menu

**Embedding Food Menu**

To embed food menus on posts and pages, first activate the Food Menu custom content type on your site and add some food menus.

Next, add the `[food_menu]` shortcode to a post or page. The shortcode will display food menu in the selected post or page.

**Attributes**
* showposts: number of menu items to display. Defaults to all. (number)
* include_type: display specific Content Types. Defaults to all. (comma-separated list of Content Type slugs)
* include_tag: display specific Content Tags. Defaults to all. (comma-separated list of Content Tag slugs)

**Example**

`[food_menu showposts="10" include_type="pizza,burger,breakfast"]`
The example will display up to ten menu items. It will only display menu items in “Pizza”, “Burger” or “Breakfast” sections.

== Installation ==

The easy way (via Dashboard) :

* Go to Plugins > Add New
* Type in the **Essential Content Types** in Search Plugins box
* Click Install Now to install the plugin
* After Installation click activate to start using the **Essential Content Types**
* Go to **Essential Content Types** from Dashboard menu
* Use Shortcodes in your posts/pages/templates

Not so easy way (via FTP) :

* Download the **Essential Content Types**
* Unarchive **Essential Content Types** plugin
* Copy folder with `essential-content-types.zip`
* Open the ftp `\wp-content\plugins\`
* Paste the plug-ins folder in the folder
* Go to admin panel => open item "Plugins" => activate **Essential Content Types**
* Go to **Essential Content Types** from Dashboard menu
* Use Shortcodes in your posts/pages/templates

== Screenshots ==

1. Main Dashboard
2. Customizer: Portfolio Archive Options
3. Customizer: Testimonial Archive Options
4. Customizer: Featured Content Archive Options
5. Customizer: Services Archive Options

== Changelog ==

= 1.3 (Released: July 05, 2018) =
* Added: Default featured-image-size
* Bug fixed: settings_page function found
* Changed: function name changed to settings_page
* Updated: Html structure

= 1.2 (Released: May 07, 2018) =
* Updated: Moved domain from catchthemes.com to catchplugins.com
* Compatibility check up to version 4.9.5

= 1.1 =
* Removed: Unnecessary code hiding menu price in Food Menu CPT

= 1.0.9 =
* Added: CPT-Food Menu Items
* Compatibility check up to version 4.9.4
* Update: Moved all plugin customizer options to new panel Essential Content Types Plugin Options

= 1.0.8 =
* Compatibility check up to version 4.9

= 1.0.7 =
* Added: Action links in plugin page
* Added: Restrict activation if Pro version is active
* Updated: Plugin page and reivew links

= 1.0.6 =
* Added: Screenshots: Services Archive Option
* Bug Fixed: Services Archive option in Customizer
* Bug Fixed: Link to Services Archive option in Dashboard

= 1.0.5 =
* Added: Custom Post Type: Service
* Added: Position in Testimonials

= 1.0.3 & 1.0.4 =
* Shortcode instruction link added

= 1.0.2 =
* Checked: Version compatibility WordPress 4.8
* Renamed Featured Content to ECT: Featured Content

= 1.0.1 =
* Bug Fixed: Featured Content compatibility with themes with Jetpack: Featured Content Support
* Bug Fixed: Admin CSS

= 1.0.0 =
* Initial Release
