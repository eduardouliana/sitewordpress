<?php

namespace Materialis\Customizer;

class Translations
{
    private static $translationMap = null;

    private static function getStringsArray()
    {
        return apply_filters('cloudpress\customizer\translation_strings',
            array(
                array(
                    "original"   => "3rd party form shortcode",
                    "translated" => __("3rd party form shortcode", "materialis-companion"),
                ),
                array(
                    "original"   => "3rd party shortcode (optional)",
                    "translated" => __("3rd party shortcode (optional)", "materialis-companion"),
                ),
                array(
                    "original"   => "Add Item",
                    "translated" => __("Add Item", "materialis-companion"),
                ),
                array(
                    "original"   => "Add WebFont",
                    "translated" => __("Add WebFont", "materialis-companion"),
                ),
                array(
                    "original"   => "Add element",
                    "translated" => __("Add element", "materialis-companion"),
                ),
                array(
                    "original"   => "Add item",
                    "translated" => __("Add item", "materialis-companion"),
                ),
                array(
                    "original"   => "Add web font",
                    "translated" => __("Add web font", "materialis-companion"),
                ),
                array(
                    "original"   => "Address",
                    "translated" => __("Address", "materialis-companion"),
                ),
                array(
                    "original"   => "Api key",
                    "translated" => __("Api key", "materialis-companion"),
                ),
                array(
                    "original"   => "Background Color",
                    "translated" => __("Background Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Background Image",
                    "translated" => __("Background Image", "materialis-companion"),
                ),
                array(
                    "original"   => "Background Overlay",
                    "translated" => __("Background Overlay", "materialis-companion"),
                ),
                array(
                    "original"   => "Background Type",
                    "translated" => __("Background Type", "materialis-companion"),
                ),
                array(
                    "original"   => "Background can be changed in PRO",
                    "translated" => __("Background can be changed in PRO", "materialis-companion"),
                ),
                array(
                    "original"   => "Blog Section Options",
                    "translated" => __("Blog Section Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Border Color",
                    "translated" => __("Border Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Bordered",
                    "translated" => __("Bordered", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Color",
                    "translated" => __("Button Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Icon",
                    "translated" => __("Button Icon", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Preset",
                    "translated" => __("Button Preset", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Shadow",
                    "translated" => __("Button Shadow", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Size",
                    "translated" => __("Button Size", "materialis-companion"),
                ),
                array(
                    "original"   => "Button Text Color",
                    "translated" => __("Button Text Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Button",
                    "translated" => __("Button", "materialis-companion"),
                ),
                array(
                    "original"   => "Cancel",
                    "translated" => __("Cancel", "materialis-companion"),
                ),
                array(
                    "original"   => "Card Border Color",
                    "translated" => __("Card Border Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Categories",
                    "translated" => __("Categories", "materialis-companion"),
                ),
                array(
                    "original"   => "Center",
                    "translated" => __("Center", "materialis-companion"),
                ),
                array(
                    "original"   => "Change Material Icon",
                    "translated" => __("Change Material Icon", "materialis-companion"),
                ),
                array(
                    "original"   => "Change Page Background Image",
                    "translated" => __("Change Page Background Image", "materialis-companion"),
                ),
                array(
                    "original"   => "Change background Image",
                    "translated" => __("Change background Image", "materialis-companion"),
                ),
                array(
                    "original"   => "Change background",
                    "translated" => __("Change background", "materialis-companion"),
                ),
                array(
                    "original"   => "Change",
                    "translated" => __("Change", "materialis-companion"),
                ),
                array(
                    "original"   => "Check all PRO features",
                    "translated" => __("Check all PRO features", "materialis-companion"),
                ),
                array(
                    "original"   => "Choose Gradient",
                    "translated" => __("Choose Gradient", "materialis-companion"),
                ),
                array(
                    "original"   => "Choose Icon",
                    "translated" => __("Choose Icon", "materialis-companion"),
                ),
                array(
                    "original"   => "Choose Images",
                    "translated" => __("Choose Images", "materialis-companion"),
                ),
                array(
                    "original"   => "Close Panel",
                    "translated" => __("Close Panel", "materialis-companion"),
                ),
                array(
                    "original"   => "Color item",
                    "translated" => __("Color item", "materialis-companion"),
                ),
                array(
                    "original"   => "Color",
                    "translated" => __("Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Column",
                    "translated" => __("Column", "materialis-companion"),
                ),
                array(
                    "original"   => "Columns per row",
                    "translated" => __("Columns per row", "materialis-companion"),
                ),
                array(
                    "original"   => "Columns",
                    "translated" => __("Columns", "materialis-companion"),
                ),
                array(
                    "original"   => "Contact Form 7 Options",
                    "translated" => __("Contact Form 7 Options", "materialis-companion"),
                ),

                array(
                    "original"   => "Content Align",
                    "translated" => __("Content Align", "materialis-companion"),
                ),
                array(
                    "original"   => "Content Column Bg. Color",
                    "translated" => __("Content Column Bg. Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Content Column Color",
                    "translated" => __("Content Column Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Content Column Options",
                    "translated" => __("Content Column Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Content align",
                    "translated" => __("Content align", "materialis-companion"),
                ),
                array(
                    "original"   => "Counter duration ( in milliseconds )",
                    "translated" => __("Counter duration ( in milliseconds )", "materialis-companion"),
                ),
                array(
                    "original"   => "Custom shortcode",
                    "translated" => __("Custom shortcode", "materialis-companion"),
                ),
                array(
                    "original"   => "Dark text",
                    "translated" => __("Dark text", "materialis-companion"),
                ),
                array(
                    "original"   => "Date",
                    "translated" => __("Date", "materialis-companion"),
                ),
                array(
                    "original"   => "Default",
                    "translated" => __("Default", "materialis-companion"),
                ),
                array(
                    "original"   => "Delete element",
                    "translated" => __("Delete element", "materialis-companion"),
                ),
                array(
                    "original"   => "Delete item",
                    "translated" => __("Delete item", "materialis-companion"),
                ),
                array(
                    "original"   => /** @lang text */
                        "Delete section from page",
                    "translated" => __(/** @lang text */
                        "Delete section from page", "materialis-companion"),
                ),
                array(
                    "original"   => "Display section title area",
                    "translated" => __("Display section title area", "materialis-companion"),
                ),
                array(
                    "original"   => "Display",
                    "translated" => __("Display", "materialis-companion"),
                ),
                array(
                    "original"   => "Edit section settings",
                    "translated" => __("Edit section settings", "materialis-companion"),
                ),
                array(
                    "original"   => "Edit",
                    "translated" => __("Edit", "materialis-companion"),
                ),
                array(
                    "original"   => "End counter to",
                    "translated" => __("End counter to", "materialis-companion"),
                ),
                array(
                    "original"   => "Extra Large",
                    "translated" => __("Extra Large", "materialis-companion"),
                ),
                array(
                    "original"   => "Material Icon",
                    "translated" => __("Material Icon", "materialis-companion"),
                ),
                array(
                    "original"   => "Font Weight",
                    "translated" => __("Font Weight", "materialis-companion"),
                ),
                array(
                    "original"   => "Frame Settings",
                    "translated" => __("Frame Settings", "materialis-companion"),
                ),
                array(
                    "original"   => "Gallery Settings",
                    "translated" => __("Gallery Settings", "materialis-companion"),
                ),
                array(
                    "original"   => "Get your api key here",
                    "translated" => __("Get your api key here", "materialis-companion"),
                ),
                array(
                    "original"   => "Gradient",
                    "translated" => __("Gradient", "materialis-companion"),
                ),
                array(
                    "original"   => "Heading",
                    "translated" => __("Heading", "materialis-companion"),
                ),
                array(
                    "original"   => "Height",
                    "translated" => __("Height", "materialis-companion"),
                ),
                array(
                    "original"   => "Hide on mobile",
                    "translated" => __("Hide on mobile", "materialis-companion"),
                ),
                array(
                    "original"   => "Highlight item",
                    "translated" => __("Highlight item", "materialis-companion"),
                ),
                array(
                    "original"   => "Icon Color",
                    "translated" => __("Icon Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Icon Size",
                    "translated" => __("Icon Size", "materialis-companion"),
                ),
                array(
                    "original"   => "Icon Style",
                    "translated" => __("Icon Style", "materialis-companion"),
                ),
                array(
                    "original"   => "Image",
                    "translated" => __("Image", "materialis-companion"),
                ),
                array(
                    "original"   => "Item content align",
                    "translated" => __("Item content align", "materialis-companion"),
                ),
                array(
                    "original"   => "Item",
                    "translated" => __("Item", "materialis-companion"),
                ),
                array(
                    "original"   => "Items Options",
                    "translated" => __("Items Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Items align",
                    "translated" => __("Items align", "materialis-companion"),
                ),
                array(
                    "original"   => "Items position",
                    "translated" => __("Items position", "materialis-companion"),
                ),
                array(
                    "original"   => "Large",
                    "translated" => __("Large", "materialis-companion"),
                ),
                array(
                    "original"   => "Lat (optional)",
                    "translated" => __("Lat (optional)", "materialis-companion"),
                ),
                array(
                    "original"   => "Latest News Settings",
                    "translated" => __("Latest News Settings", "materialis-companion"),
                ),
                array(
                    "original"   => "Left",
                    "translated" => __("Left", "materialis-companion"),
                ),
                array(
                    "original"   => "Link",
                    "translated" => __("Link", "materialis-companion"),
                ),
                array(
                    "original"   => "List item",
                    "translated" => __("List item", "materialis-companion"),
                ),
                array(
                    "original"   => "List items",
                    "translated" => __("List items", "materialis-companion"),
                ),
                array(
                    "original"   => "List",
                    "translated" => __("List", "materialis-companion"),
                ),
                array(
                    "original"   => "Lng (optional)",
                    "translated" => __("Lng (optional)", "materialis-companion"),
                ),
                array(
                    "original"   => "Main Menu",
                    "translated" => __("Main Menu", "materialis-companion"),
                ),
                array(
                    "original"   => "Make Centered",
                    "translated" => __("Make Centered", "materialis-companion"),
                ),
                array(
                    "original"   => "Make full width",
                    "translated" => __("Make full width", "materialis-companion"),
                ),
                array(
                    "original"   => "Manage Content",
                    "translated" => __("Manage Content", "materialis-companion"),
                ),
                array(
                    "original"   => "Manage Options",
                    "translated" => __("Manage Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Manage Widgets Areas",
                    "translated" => __("Manage Widgets Areas", "materialis-companion"),
                ),
                array(
                    "original"   => "Manage web fonts",
                    "translated" => __("Manage web fonts", "materialis-companion"),
                ),
                array(
                    "original"   => "More section design options available in PRO",
                    "translated" => __("More section design options available in PRO", "materialis-companion"),
                ),
                array(
                    "original"   => "Move element",
                    "translated" => __("Move element", "materialis-companion"),
                ),
                array(
                    "original"   => "No Widgets Area Selected",
                    "translated" => __("No Widgets Area Selected", "materialis-companion"),
                ),
                array(
                    "original"   => "Normal",
                    "translated" => __("Normal", "materialis-companion"),
                ),
                array(
                    "original"   => "Number of posts to display",
                    "translated" => __("Number of posts to display", "materialis-companion"),
                ),
                array(
                    "original"   => "Number of products to display",
                    "translated" => __("Number of products to display", "materialis-companion"),
                ),
                array(
                    "original"   => "OK",
                    "translated" => __("OK", "materialis-companion"),
                ),
                array(
                    "original"   => "Offset X",
                    "translated" => __("Offset X", "materialis-companion"),
                ),
                array(
                    "original"   => "Offset Y",
                    "translated" => __("Offset Y", "materialis-companion"),
                ),
                array(
                    "original"   => "Open images in Lightbox",
                    "translated" => __("Open images in Lightbox", "materialis-companion"),
                ),
                array(
                    "original"   => "Order By",
                    "translated" => __("Order By", "materialis-companion"),
                ),
                array(
                    "original"   => "Order",
                    "translated" => __("Order", "materialis-companion"),
                ),
                array(
                    "original"   => "Overlay color",
                    "translated" => __("Overlay color", "materialis-companion"),
                ),
                array(
                    "original"   => "Please upgrade to the PRO version to use this item and many others.",
                    "translated" => __("Please upgrade to the PRO version to use this item and many others.", "materialis-companion"),
                ),
                array(
                    "original"   => "Popularity",
                    "translated" => __("Popularity", "materialis-companion"),
                ),
                array(
                    "original"   => "Prefix ( text in front of the number )",
                    "translated" => __("Prefix ( text in front of the number )", "materialis-companion"),
                ),
                array(
                    "original"   => "Price",
                    "translated" => __("Price", "materialis-companion"),
                ),
                array(
                    "original"   => "Random",
                    "translated" => __("Random", "materialis-companion"),
                ),
                array(
                    "original"   => "Rating",
                    "translated" => __("Rating", "materialis-companion"),
                ),
                array(
                    "original"   => "Remove Item",
                    "translated" => __("Remove Item", "materialis-companion"),
                ),
                array(
                    "original"   => "Remove",
                    "translated" => __("Remove", "materialis-companion"),
                ),
                array(
                    "original"   => "Reorder Items",
                    "translated" => __("Reorder Items", "materialis-companion"),
                ),
                array(
                    "original"   => "Right",
                    "translated" => __("Right", "materialis-companion"),
                ),
                array(
                    "original"   => "Rounded background",
                    "translated" => __("Rounded background", "materialis-companion"),
                ),
                array(
                    "original"   => "Search for specific products to display",
                    "translated" => __("Search for specific products to display", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Background",
                    "translated" => __("Section Background", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Dimensions",
                    "translated" => __("Section Dimensions", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Options",
                    "translated" => __("Section Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Separators",
                    "translated" => __("Section Separators", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Settings",
                    "translated" => __("Section Settings", "materialis-companion"),
                ),
                array(
                    "original"   => "Section Spacing",
                    "translated" => __("Section Spacing", "materialis-companion"),
                ),
                array(
                    "original"   => "Section",
                    "translated" => __("Section", "materialis-companion"),
                ),
                array(
                    "original"   => "Select Image",
                    "translated" => __("Select Image", "materialis-companion"),
                ),
                array(
                    "original"   => "Select Products to display",
                    "translated" => __("Select Products to display", "materialis-companion"),
                ),
                array(
                    "original"   => "Select a Widgets Area",
                    "translated" => __("Select a Widgets Area", "materialis-companion"),
                ),
                array(
                    "original"   => "Select",
                    "translated" => __("Select", "materialis-companion"),
                ),
                array(
                    "original"   => "Set the shortcode",
                    "translated" => __("Set the shortcode", "materialis-companion"),
                ),
                array(
                    "original"   => "Shortcode",
                    "translated" => __("Shortcode", "materialis-companion"),
                ),
                array(
                    "original"   => "Show form controls on one column",
                    "translated" => __("Show form controls on one column", "materialis-companion"),
                ),
                array(
                    "original"   => "Small",
                    "translated" => __("Small", "materialis-companion"),
                ),
                array(
                    "original"   => "Start counter from",
                    "translated" => __("Start counter from", "materialis-companion"),
                ),
                array(
                    "original"   => "Stop circle at value",
                    "translated" => __("Stop circle at value", "materialis-companion"),
                ),
                array(
                    "original"   => "Suffix ( text after the number )",
                    "translated" => __("Suffix ( text after the number )", "materialis-companion"),
                ),
                array(
                    "original"   => "Swap columns position on mobile",
                    "translated" => __("Swap columns position on mobile", "materialis-companion"),
                ),
                array(
                    "original"   => "Tags",
                    "translated" => __("Tags", "materialis-companion"),
                ),
                array(
                    "original"   => "Text Color",
                    "translated" => __("Text Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Text Options",
                    "translated" => __("Text Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Text",
                    "translated" => __("Text", "materialis-companion"),
                ),
                array(
                    "original"   => "The quick brown fox jumps over the lazy dog",
                    "translated" => __("The quick brown fox jumps over the lazy dog", "materialis-companion"),
                ),
                array(
                    "original"   => "This item is available only in the PRO version",
                    "translated" => __("This item is available only in the PRO version", "materialis-companion"),
                ),
                array(
                    "original"   => "This item requires PRO theme",
                    "translated" => __("This item requires PRO theme", "materialis-companion"),
                ),
                array(
                    "original"   => "This section has a custom background color",
                    "translated" => __("This section has a custom background color", "materialis-companion"),
                ),
                array(
                    "original"   => "Toggle visibility in primary menu",
                    "translated" => __("Toggle visibility in primary menu", "materialis-companion"),
                ),
                array(
                    "original"   => "Transparent",
                    "translated" => __("Transparent", "materialis-companion"),
                ),
                array(
                    "original"   => "Type",
                    "translated" => __("Type", "materialis-companion"),
                ),
                array(
                    "original"   => "Upgrade to PRO",
                    "translated" => __("Upgrade to PRO", "materialis-companion"),
                ),
                array(
                    "original"   => "Use 3rd party shortcode",
                    "translated" => __("Use 3rd party shortcode", "materialis-companion"),
                ),
                array(
                    "original"   => "Use Masonry to display the gallery",
                    "translated" => __("Use Masonry to display the gallery", "materialis-companion"),
                ),
                array(
                    "original"   => "Use Transparent Color",
                    "translated" => __("Use Transparent Color", "materialis-companion"),
                ),
                array(
                    "original"   => "Use custom selection",
                    "translated" => __("Use custom selection", "materialis-companion"),
                ),
                array(
                    "original"   => "Use this field for 3rd party maps plugins. The fields above will be ignored in this case.",
                    "translated" => __("Use this field for 3rd party maps plugins. The fields above will be ignored in this case.", "materialis-companion"),
                ),
                array(
                    "original"   => "Video Popup Button",
                    "translated" => __("Video Popup Button", "materialis-companion"),
                ),
                array(
                    "original"   => "Visible",
                    "translated" => __("Visible", "materialis-companion"),
                ),
                array(
                    "original"   => "What Our Clients Say",
                    "translated" => __("What Our Clients Say", "materialis-companion"),
                ),
                array(
                    "original"   => "White text",
                    "translated" => __("White text", "materialis-companion"),
                ),
                array(
                    "original"   => "Widgets Area",
                    "translated" => __("Widgets Area", "materialis-companion"),
                ),
                array(
                    "original"   => "Width",
                    "translated" => __("Width", "materialis-companion"),
                ),
                array(
                    "original"   => "WooCommerce Section Options",
                    "translated" => __("WooCommerce Section Options", "materialis-companion"),
                ),
                array(
                    "original"   => "Zoom",
                    "translated" => __("Zoom", "materialis-companion"),
                ),
                array(
                    "original"   => "add theme color",
                    "translated" => __("add theme color", "materialis-companion"),
                ),
                array(
                    "original"   => "background",
                    "translated" => __("background", "materialis-companion"),
                ),
                array(
                    "original"   => "big",
                    "translated" => __("big", "materialis-companion"),
                ),
                array(
                    "original"   => "blue",
                    "translated" => __("blue", "materialis-companion"),
                ),
                array(
                    "original"   => "border",
                    "translated" => __("border", "materialis-companion"),
                ),
                array(
                    "original"   => "bottom",
                    "translated" => __("bottom", "materialis-companion"),
                ),
                array(
                    "original"   => "button",
                    "translated" => __("button", "materialis-companion"),
                ),
                array(
                    "original"   => "column",
                    "translated" => __("column", "materialis-companion"),
                ),
                array(
                    "original"   => "columns",
                    "translated" => __("columns", "materialis-companion"),
                ),
                array(
                    "original"   => "dark text",
                    "translated" => __("dark text", "materialis-companion"),
                ),
                array(
                    "original"   => "date",
                    "translated" => __("date", "materialis-companion"),
                ),
                array(
                    "original"   => "default",
                    "translated" => __("default", "materialis-companion"),
                ),
                array(
                    "original"   => "edit theme colors",
                    "translated" => __("edit theme colors", "materialis-companion"),
                ),
                array(
                    "original"   => "green",
                    "translated" => __("green", "materialis-companion"),
                ),
                array(
                    "original"   => "heading",
                    "translated" => __("heading", "materialis-companion"),
                ),
                array(
                    "original"   => "image",
                    "translated" => __("image", "materialis-companion"),
                ),
                array(
                    "original"   => "lead",
                    "translated" => __("lead", "materialis-companion"),
                ),
                array(
                    "original"   => "link",
                    "translated" => __("link", "materialis-companion"),
                ),
                array(
                    "original"   => "new button",
                    "translated" => __("new button", "materialis-companion"),
                ),
                array(
                    "original"   => "new link",
                    "translated" => __("new link", "materialis-companion"),
                ),
                array(
                    "original"   => "no shadow",
                    "translated" => __("no shadow", "materialis-companion"),
                ),
                array(
                    "original"   => "normal",
                    "translated" => __("normal", "materialis-companion"),
                ),
                array(
                    "original"   => "orange",
                    "translated" => __("orange", "materialis-companion"),
                ),
                array(
                    "original"   => "outline",
                    "translated" => __("outline", "materialis-companion"),
                ),
                array(
                    "original"   => "paragraph",
                    "translated" => __("paragraph", "materialis-companion"),
                ),
                array(
                    "original"   => "popularity",
                    "translated" => __("popularity", "materialis-companion"),
                ),
                array(
                    "original"   => "price",
                    "translated" => __("price", "materialis-companion"),
                ),
                array(
                    "original"   => "purple",
                    "translated" => __("purple", "materialis-companion"),
                ),
                array(
                    "original"   => "random",
                    "translated" => __("random", "materialis-companion"),
                ),
                array(
                    "original"   => "rating",
                    "translated" => __("rating", "materialis-companion"),
                ),
                array(
                    "original"   => "round outline",
                    "translated" => __("round outline", "materialis-companion"),
                ),
                array(
                    "original"   => "round",
                    "translated" => __("round", "materialis-companion"),
                ),
                array(
                    "original"   => "select image",
                    "translated" => __("select image", "materialis-companion"),
                ),
                array(
                    "original"   => "separator Height",
                    "translated" => __("separator Height", "materialis-companion"),
                ),
                array(
                    "original"   => "separator colo",
                    "translated" => __("separator colo", "materialis-companion"),
                ),
                array(
                    "original"   => "separator color",
                    "translated" => __("separator color", "materialis-companion"),
                ),
                array(
                    "original"   => "separator type",
                    "translated" => __("separator type", "materialis-companion"),
                ),
                array(
                    "original"   => "separator",
                    "translated" => __("separator", "materialis-companion"),
                ),
                array(
                    "original"   => "show shadow",
                    "translated" => __("show shadow", "materialis-companion"),
                ),
                array(
                    "original"   => "small",
                    "translated" => __("small", "materialis-companion"),
                ),
                array(
                    "original"   => "square outline",
                    "translated" => __("square outline", "materialis-companion"),
                ),
                array(
                    "original"   => "square round outline",
                    "translated" => __("square round outline", "materialis-companion"),
                ),
                array(
                    "original"   => "square round",
                    "translated" => __("square round", "materialis-companion"),
                ),
                array(
                    "original"   => "square",
                    "translated" => __("square", "materialis-companion"),
                ),
                array(
                    "original"   => "top",
                    "translated" => __("top", "materialis-companion"),
                ),
                array(
                    "original"   => "transparent ( link button )",
                    "translated" => __("transparent ( link button )", "materialis-companion"),
                ),
                array(
                    "original"   => "transparent",
                    "translated" => __("transparent", "materialis-companion"),
                ),
                array(
                    "original"   => "use existing color",
                    "translated" => __("use existing color", "materialis-companion"),
                ),
                array(
                    "original"   => "white text",
                    "translated" => __("white text", "materialis-companion"),
                ),
                array(
                    "original"   => "yellow",
                    "translated" => __("yellow", "materialis-companion"),
                ),
            )
        );
    }


    static public function getTranslations()
    {

        if ( ! static::$translationMap) {
            foreach (static::getStringsArray() as $match) {
                static::$translationMap[$match['original']] = $match['translated'];
            }
        }

        return static::$translationMap;
    }
}
