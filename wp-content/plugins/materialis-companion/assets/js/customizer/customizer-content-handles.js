(function () {
    window.CP_Customizer.addModule(function (CP_Customizer) {

        var iconStyles = [
            {
                value: 'normal',
                label: window.CP_Customizer.translateCompanionString('Normal'),
                classes: 'normal-shape'
            },
            {
                value: 'round-bg',
                label: window.CP_Customizer.translateCompanionString('Rounded background'),
                classes: 'reverse round'
            },
            {
                value: 'bordered',
                label: window.CP_Customizer.translateCompanionString('Bordered'),
                classes: 'bordered round'
            }
        ];


        var iconSizes = [
            {
                value: 'small',
                label: window.CP_Customizer.translateCompanionString('Small'),
                classes: 'small'
            },
            {
                value: 'normal',
                label: window.CP_Customizer.translateCompanionString('Normal'),
                classes: 'normal-size'
            },
            {
                value: 'big',
                label: window.CP_Customizer.translateCompanionString('Large'),
                classes: 'big'
            },
            {
                value: 'large',
                label: window.CP_Customizer.translateCompanionString('Extra Large'),
                classes: 'large'
            }
        ];

        function setIconStyle(style, $icon) {
            var toRemove = iconStyles.map(function (style) {
                return style.classes;
            });

            var toAdd = "";

            iconStyles.forEach(function (iconStyle) {
                if (iconStyle.value === style) {
                    toAdd = iconStyle.classes
                }
            });

            $icon.removeClass(toRemove.join(' ')).addClass(toAdd);

        }

        function getIconStyle($icon) {
            var result = 'normal';

            iconStyles.forEach(function (style) {
                if (style.classes.trim()) {
                    var selector = CP_Customizer.utils.normalizeClassAttr(style.classes, true);
                    if ($icon.is(selector)) {
                        result = style.value;
                    }
                }
            });
            return result;
        }


        function setIconSize(size, $icon) {
            var toRemove = iconSizes.map(function (size) {
                return size.classes;
            });

            var toAdd = "";

            iconSizes.forEach(function (iconSize) {
                if (iconSize.value === size) {
                    toAdd = iconSize.classes
                }
            });

            $icon.removeClass(toRemove.join(' ')).addClass(toAdd);

        }

        function getIconSize($icon) {
            var result = 'normal';

            iconSizes.forEach(function (size) {
                if (size.classes.trim()) {
                    var selector = CP_Customizer.utils.normalizeClassAttr(size.classes, true);
                    if ($icon.is(selector)) {
                        result = size.value;
                    }
                }
            });
            return result;
        }


        // normal texts panel
        CP_Customizer.addContainerDataHandler(CP_Customizer.TEXT_ELEMENTS, function ($el) {
            var label =  window.CP_Customizer.translateCompanionString($el.attr('data-popup-label') || "");
            var result = [

                {
                    label: label || window.CP_Customizer.translateCompanionString("Text"),
                    type: "text",
                    value: $el.text().trim()
                }
            ];

            if ($el.parent().is('a') || $el.is('.mdi')) {
                return [];
            }

            return result;

        }, function ($el, value, type, field) {
            switch (type) {
                case "text":
                    var html = CP_Customizer.utils.htmlDecode($el.html()).replace(field.value, value);
                    $el.html(html);
                    break;
            }
        });

        // containers selector
        CP_Customizer.preview.addDataContainerSelector('[data-hover-fx]');

        // link panel;
        CP_Customizer.addContainerDataHandler('a', function ($el) {
            var linkIsShortcode = $el.is('[data-attr-shortcode^="href:"]');

            var hasClass = ($el.attr('class') || "").trim().length > 0;

            var result = [

                {
                    label: (hasClass ? window.CP_Customizer.translateCompanionString("Button") : window.CP_Customizer.translateCompanionString("Link"))
                    + " " + window.CP_Customizer.translateCompanionString("Text"),
                    type: "text",
                    value: $el.text().trim()
                }
            ];

            if (!linkIsShortcode) {
                result.push({
                    label: window.CP_Customizer.translateCompanionString("Link"),
                    type: "link",
                    value: {
                        link: CP_Customizer.preview.cleanURL($el.attr('href') || ""),
                        target: $el.attr('target') || "_self"
                    }
                });
            }

            return result;

        }, function ($el, value, type, field) {
            switch (type) {
                case "link":
                    $el.attr('href', value.link);
                    $el.attr('target', value.target);

                    $el.attr('data-cp-link', 1);

                    break;
                case "text":
                    if (!field.name) {
                        var html = CP_Customizer.utils.htmlDecode($el.html()).replace(field.value, value);
                        $el.html(html);
                    }
                    break;
            }
        });


        // list panel;
        CP_Customizer.addContainerDataHandler('ul', function ($el) {
            var items = $el.children('li');

            items = items.map(function (index, item) {
                return {
                    "label": window.CP_Customizer.translateCompanionString("Item") + " " + index,
                    "value": jQuery(item).html(),
                    "id": "item_" + index
                }
            })

            var result = {
                label: window.CP_Customizer.translateCompanionString("List items"),
                type: "list",
                value: items,
                getValue: function ($control) {
                    var items = [];
                    $control.children().each(function () {
                        items.push(jQuery(this).find('.item-editor').val());
                    })
                    return items;
                }
            }

            return result;

        }, function ($el, items, type) {

            var orig = $el.children().eq(0).clone();
            $el.empty();

            for (var i = 0; i < items.length; i++) {
                var $item = orig.clone();
                $item.html(items[i]);
                $el.append($item);
            }
        });

        // image link panel
        CP_Customizer.addContainerDataFilter(function ($el) {
            return !($el.is('a') && $el.children().is('img'));
        });

        CP_Customizer.addContainerDataHandler('img', function ($el) {

            var mediaType = "image",
                mediaData = false;

            if ($el.attr('data-size')) {
                mediaType = "cropable";
                var size = $el.attr('data-size').split('x');

                var section = CP_Customizer.preview.getNodeSection($el);
                var sectionExports = CP_Customizer.getSectionExports(section);

                var flexible = _.isUndefined(sectionExports.maintainCropPropotion) ? true : sectionExports.maintainCropPropotion;

                if ($el.is('[data-size-flexible=false]')) {
                    flexible = false;
                }

                mediaData = {
                    width: size[0],
                    height: size[1],
                    flexible: flexible
                };
            }


            var image = [{
                label: window.CP_Customizer.translateCompanionString("Image"),
                mediaType: mediaType,
                mediaData: mediaData,
                type: "image",
                value: ($el[0].currentSrc || $el.attr('src'))
            }];

            if ($el.parent().is('a')) {
                image.push({
                    label: window.CP_Customizer.translateCompanionString("Link"),
                    type: "link",
                    value: {
                        link: CP_Customizer.preview.cleanURL($el.parent().attr('href') || ""),
                        target: $el.parent().attr('target') || "_self"
                    }
                });
            }
            return image;

        }, function ($el, value, type) {
            switch (type) {
                case 'image':
                    $el.attr("src", value);
                    $el.removeAttr('srcset');
                    $el.removeAttr('src-orig');
                    $el.removeAttr('width');
                    $el.removeAttr('height');
                    break;
                case 'link':
                    $el.parent().attr('href', value.link);
                    $el.parent().attr('target', value.target);
                    break;
            }
        });

        // data-bg=[image]

        function getLinkFromBgImageValue(value) {
            value = value.replace(/url\((.*)\)/, "$1");
            return CP_Customizer.utils.phpTrim(value, "\"'");
        }

        CP_Customizer.addContainerDataHandler('[data-bg=image]', function ($el) {

            var mediaType = "image",
                mediaData = false;

            if ($el.attr('data-size')) {
                mediaType = "cropable";
                var size = $el.attr('data-size').split('x');
                mediaData = {
                    width: size[0],
                    height: size[1]
                };
            }


            return [{
                label: window.CP_Customizer.translateCompanionString("Background Image"),
                mediaType: mediaType,
                mediaData: mediaData,
                type: "image",
                value: getLinkFromBgImageValue($el.css('background-image'))
            }];

        }, function ($el, value, type) {
            switch (type) {
                case 'image':
                    $el.css("background-image", 'url("' + value + '")');
                    break;
            }
        });


        // material icons icon with link

        CP_Customizer.addContainerDataFilter(function ($el) {
            return !($el.children().is('i.mdi') && $el.is('a'));
        });

        var mdiIconRegexp = /mdi\-[a-z\-]+/ig;

        CP_Customizer.addContainerDataHandler('a i.mdi', function ($el) {

            var mediaType = "icon",
                mediaData = false;

            var result = [{
                label: window.CP_Customizer.translateCompanionString("Material Icon"),
                mediaType: mediaType,
                mediaData: mediaData,
                canHide: true,
                type: "linked-icon",
                value: {
                    icon: $el.attr('class').match(mdiIconRegexp).pop(),
                    link: CP_Customizer.preview.cleanURL($el.parent().attr('href') || ""),
                    target: $el.parent().attr('target') || "_self",
                    visible: CP_Customizer.preview.isNodeVisible($el.parent())
                }
            }];

            if ($el.parent().is('.read-more')) {
                result = [{
                    label: window.CP_Customizer.translateCompanionString("Text"),
                    type: "text",
                    value: $el.parent().text().trim()
                }, {
                    label: window.CP_Customizer.translateCompanionString("Link"),
                    type: "link",
                    value: {
                        link: CP_Customizer.preview.cleanURL($el.parent().attr('href') || ""),
                        target: $el.parent().attr('target') || "_self"
                    }
                }]

            }

            return result;


        }, function ($el, value, type, field) {

            if (type === "linked-icon") {
                var classValue = $el.attr('class');
                classValue = classValue.replace(/mdi\-[a-z\-]+/ig, "") + " " + value.icon;
                $el.attr('class', classValue);

                $el.parent().attr('href', value.link);
                $el.parent().attr('target', value.target);

                value.visible = _.isUndefined(value.visible) ? true : value.visible;

                if (value.visible) {
                    CP_Customizer.preview.showNode($el.parent());
                } else {
                    CP_Customizer.preview.hideNode($el.parent());
                }
            }

            if (type === "link") {
                $el.parent().attr('href', value.link);
                $el.parent().attr('target', value.target);
                $el.parent().attr('data-cp-link', 1);
            }
            if (type === "text") {
                $el.parent().html(value).append($el);
            }
        });


        CP_Customizer.addContainerDataHandler('i.mdi', function ($el) {

            var mediaType = "icon",
                mediaData = false;

            return [{
                label: window.CP_Customizer.translateCompanionString("Material Icon"),
                mediaType: mediaType,
                mediaData: mediaData,
                canHide: $el.closest('[data-type=group]').length > 0,
                type: "icon",
                styles: iconStyles,
                sizes: iconSizes,
                value: {
                    icon: $el.attr('class').match(mdiIconRegexp).pop(),
                    style: getIconStyle($el),
                    size: getIconSize($el),
                    visible: CP_Customizer.preview.isNodeVisible($el)
                }
            }];


        }, function ($el, value, type, prop) {
            if (type === "icon") {
                var classValue = $el.attr('class');
                classValue = classValue.replace(/mdi\-[a-z\-]+/ig, "") + " " + value.icon;
                $el.attr('class', classValue);

                value.visible = _.isUndefined(value.visible) ? true : value.visible;

                if (value.visible) {
                    // $el.removeAttr('data-reiki-hidden');
                    CP_Customizer.preview.showNode($el);
                } else {
                    CP_Customizer.preview.hideNode($el);
                }

                setIconStyle(value.style, $el);
                setIconSize(value.size, $el);
            }

        });


    });
})();
