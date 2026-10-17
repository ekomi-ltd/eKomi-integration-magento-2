/**
 * Ekomi — Exclude Products character counter & validation
 * Attaches a live counter and red-highlight to the Exclude Products field
 * in the Magento 2 admin system config.
 */
require(['jquery', 'mage/translate', 'domReady!'], function ($, $t) {
    'use strict';

    var MAX_LENGTH = 255;
    var FIELD_ID   = 'ekomiintegration_general_exclude_products';

    function initCounter() {
        var $input = $('#' + FIELD_ID);
        if (!$input.length) {
            return;
        }

        // Build counter element
        var $counter = $('<span>', {
            id:    FIELD_ID + '_counter',
            style: 'display:block;font-size:12px;margin-top:3px;color:#666;'
        });
        var $error = $('<span>', {
            id:    FIELD_ID + '_error',
            style: 'display:none;font-size:12px;margin-top:3px;color:#e22626;font-weight:bold;'
        });

        $input.after($error).after($counter);

        function update() {
            var len = $input.val().length;
            $counter.text(len + '/' + MAX_LENGTH);
            $error.text($t('Exclude Products must not exceed %1 characters. Current length: %2.').replace('%1', MAX_LENGTH).replace('%2', len));

            if (len > MAX_LENGTH) {
                $input.css({'border-color': '#e22626', 'box-shadow': '0 0 0 1px #e22626'});
                $counter.css('color', '#e22626');
                $error.show();
            } else {
                $input.css({'border-color': '', 'box-shadow': ''});
                $counter.css('color', '#666');
                $error.hide();
            }
        }

        $input.on('input keyup', update);
        update();

        // Block form save when over limit
        $input.closest('form').on('submit', function (e) {
            if ($input.val().length > MAX_LENGTH) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $input.focus();
                $error.show();
                return false;
            }
        });
    }

    initCounter();
});
