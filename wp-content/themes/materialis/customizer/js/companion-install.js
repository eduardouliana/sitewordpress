jQuery(document).ready(function () {

    tb_show('Materialis Companion', '#TB_inline?width=400&height=430&inlineId=materialis_homepage');
    jQuery('#TB_closeWindowButton').hide();
    jQuery('#TB_window').css({
        'z-index': '5000001',
        'height': '480px',
        'width': '780px'
    })
    jQuery('#TB_overlay').css({
        'z-index': '5000000'
    });

});
