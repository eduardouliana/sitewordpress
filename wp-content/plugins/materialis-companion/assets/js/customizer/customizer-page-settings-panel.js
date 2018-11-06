(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {

        var panel = CP_Customizer.panels.pageContentPanel;


        $('body').on('click', '[data-name="page_content_panel"] span.section-icon.setting.page-settings', function () {
            CP_Customizer.openRightSidebar('cp-current-page-settings');
        });

        function getRegisteredControls() {
            var controls_ids = $('[data-name="page_content_panel"] .page-settings').attr('data-settings');
            var controls = [];
            controls_ids.split(',').forEach(function (id) {
                var _c = wp.customize.control(id);
                if (_c) {
                    controls.push(_c);
                }
            });

            return controls;
        }


        panel.registerArea('general_page_settings', {

            $controlsHolder: null,

            refreshControls: function () {
                var controls = getRegisteredControls(),
                    self = this;

                _.each(controls, function (c) {
                    self.$controlsHolder.append(c.container);
                });
            },

            init: function ($container) {

                this.$controlsHolder = $("<ul/>");
                $container.append($('<li/>').append(this.$controlsHolder));
                var self = this;

                self.refreshControls();

                wp.customize.bind('pane-contents-reflowed', function () {
                    self.refreshControls();
                })
            }
        });
    });
})(window, CP_Customizer, jQuery);
