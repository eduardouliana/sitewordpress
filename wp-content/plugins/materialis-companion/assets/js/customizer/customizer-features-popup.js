(function (root, CP_Customizer, $) {
    CP_Customizer.addModule(function (CP_Customizer) {
        var popup = CP_Customizer.options('featuresPopup', null);
        var containerID = "cp_feauture_popups_" + Math.round(Math.random() * 10000);
        var $container = $("<div/>", {
            id: containerID,
            style: "display:none"
        });

        if (popup) {
            $container.append(popup.content);
            CP_Customizer.one(CP_Customizer.events.PREVIEW_LOADED, function () {
                $('body').append($container);
                var $tbWindow = CP_Customizer.popUp(popup.title || 'New Feature', containerID, popup.data || {
                    class: "ocdie-tbWindow"
                });
                $tbWindow.find('[id=TB_closeWindowButton]').off('click.feature_popup').on('click.feature_popup', function (event) {
                    jQuery.post(
                        ajaxurl,
                        {
                            value: '1',
                            action: "companion_disable_popup",
                            option: 'feature_popup_' + popup.id + '_disabled',
                            companion_disable_popup_wpnonce: popup.nonce
                        }
                    );
                });
            });
        }

    });

})(window, CP_Customizer, jQuery);