/**
 * jQuery Line Progressbar
 * Author: KingRayhan<rayhan095@gmail.com>
 * Author URL: http://rayhan.info
 * Version: 1.0.0
 */

(function ($) {
    'use strict';


    $.fn.LineProgressbar = function (options) {

         options = $.extend({
             min: 0,
             max: 100,
             stop: 50,
             ShowProgressCount: true,
             duration: 2000,
             color: '#654ea3',
             backgroundColor: '#f5f5f5',
             suffix: '%',
             text: 'Category',
             radius: '0px',
             height: '10px',
             width: '100%'
        }, options);

        $.options = options;
        return this.each(function (index, el) {
            // Markup
            $(el).html('<div class="progressbar"><div class="counterText"></div><div class="percentCount"></div><div class="proggress"></div></div>');



            var progressFill = $(el).find('.proggress');
            var progressBar = $(el).find('.progressbar');
            var progressText = $(el).find('.counterText');


            progressFill.css({
                backgroundColor: options.color,
                height: options.height,
                borderRadius: options.radius
            });
            progressBar.css({
                width: options.width,
                backgroundColor: options.backgroundColor,
                borderRadius: options.radius
            });

            progressText.html(options.text);

            // Progressing
            progressFill.animate(
                {
                    width: options.stop + "%"
                },
                {
                    step: function (x) {
                        if (options.ShowProgressCount) {
                            $(el).find(".percentCount").text(Math.round(x) + options.suffix);
                        }
                    },
                    duration: options.duration
                }
            );
            ////////////////////////////////////////////////////////////////////
        });
    }
    $.fn.progressTo = function (next) {

        var options = $.options;

        return this.each(function (index, el) {

            var progressFill = $(el).find('.proggress');
            var progressBar = $(el).find('.progressbar');

            progressFill.animate(
                {
                    width: next + "%"
                },
                {
                    step: function (x) {
                        if (options.ShowProgressCount) {
                            $(el).find(".percentCount").text(Math.round(x) + options.suffix);
                        }
                    },
                    duration: options.duration
                }
            );
            ////////////////////////////////////////////////////////////////////
        });
    }

})(jQuery);
