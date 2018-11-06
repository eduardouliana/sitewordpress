(function (root, CP_Customizer, $) {

    var countUpSelector = '[data-countup="true"]';

    var countupControls = {
        min: {
            control: {
                label: 'Start counter from',
                type: 'text',
                attr: 'data-min',
                default: 0
            }
        },

        max: {
            control: {
                label: 'End counter to',
                type: 'text',
                attr: 'data-max',
                default: 100
            }

        },

        stop: {
            control: {
                label: 'Stop circle at value',
                type: 'text',
                attr: 'data-stop',
                active: function ($item) {
                    return $item.closest('.circle-counter').length > 0;
                },
                default: 50
            }

        },

        prefix: {
            control: {
                label: 'Prefix ( text in front of the number )',
                type: 'text',
                attr: 'data-prefix',
                default: ""
            }

        },

        suffix: {
            control: {
                label: 'Suffix ( text after the number )',
                type: 'text',
                attr: 'data-suffix',
                default: "%"
            }

        },

        duration: {
            control: {
                label: 'Counter duration ( in milliseconds )',
                type: 'text',
                attr: 'data-duration',
                default: 2000
            }

        }


    };

    CP_Customizer.hooks.addFilter('filter_custom_popup_controls', function (controls) {
        var extendedControls = _.extend(_.clone(controls),
            {
                countup: countupControls
            }
        );
        return extendedControls;
    });

    CP_Customizer.preview.registerContainerDataHandler(countUpSelector, function ($item) {
        CP_Customizer.openCustomPopupEditor($item, 'countup', function (values, $item) {
            console.log(values, $item);
            CP_Customizer.preview.jQuery($item[0]).data().restartCountUp();
        });
    });

    CP_Customizer.hooks.addAction('clean_nodes', function ($nodes) {
        $nodes.find(countUpSelector).each(function () {
            this.innerHTML = "";
            this.removeAttribute('data-max-computed');
        });

        $nodes.find('.circle-counter svg.circle-bar').removeAttr('style');
    });


})(window, CP_Customizer, jQuery);


(function (root, CP_Customizer, $) {

    var countBarSelector = '[data-countbar="true"]';

    var countbarControls = {
        min: {
            control: {
                label: 'Start counter from',
                type: 'text',
                attr: 'data-min',
                default: 0
            }
        },

        max: {
            control: {
                label: 'End counter to',
                type: 'text',
                attr: 'data-max',
                default: 100
            }

        },

        stop: {
            control: {
                label: 'Stop counter at value',
                type: 'text',
                attr: 'data-stop',
                default: 50
            }

        },

        suffix: {
            control: {
                label: 'Suffix ( text after the number )',
                type: 'text',
                attr: 'data-suffix',
                default: "%"
            }

        },

        text: {
            control: {
                label: 'Text',
                type: 'text',
                attr: 'data-text',
                default: "Category"
            }

        },

        color: {
            control: {
                label: 'Color',
                type: 'colorselect',
                attr: 'data-bgcolor',
                default: "#2987E2"
            }

        }

    };

    CP_Customizer.hooks.addFilter('filter_custom_popup_controls', function (controls) {
        var extendedControls = _.extend(_.clone(controls),
            {
                countBar: countbarControls
            }
        );
        return extendedControls;
    });

    CP_Customizer.preview.registerContainerDataHandler(countBarSelector, function ($item) {
        CP_Customizer.openCustomPopupEditor($item, 'countBar', function (values, $item) {
            CP_Customizer.preview.jQuery($item[0]).data().restartCountBar($item);
        });
    });

    CP_Customizer.hooks.addAction('clean_nodes', function ($nodes) {
        $nodes.find(countBarSelector).each(function () {
            this.innerHTML = "";
        });
    });


})(window, CP_Customizer, jQuery);


(function (root, CP_Customizer, $) {
    CP_Customizer.registerShortcodePopupControls(
        "materialis_contact_form",
        {
            "shortcode": {
                control: {
                    label: "3rd party form shortcode",
                    type: "text",
                    setParse: function (value) {
                        return value.replace(/^\[+/, '').replace(/\]+$/, '');
                    },

                    getParse: function (value) {

                        var val = value.replace(/^\[+/, '').replace(/\]+$/, '');

                        if (!val) return "";
                        return "[" + CP_Customizer.utils.htmlDecode(val) + "]";
                    }
                }
            }
        }
    );

    CP_Customizer.hooks.addAction('shortcode_edit_materialis_contact_form', CP_Customizer.editEscapedShortcodeAtts);

})(window, CP_Customizer, jQuery);

(function (root, CP_Customizer, $) {
    CP_Customizer.hooks.addFilter('can_edit_icon', function (value, $node) {
        if ($node.closest('.header-homepage-arrow-c').length) {
            return false;
        }

        return value;
    });
})(window, CP_Customizer, jQuery);