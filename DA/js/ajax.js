/*
 * AJAX Nette Framwork plugin for jQuery
 *
 * @copyright   Copyright (c) 2009 Jan Marek
 * @license     MIT
 * @link        http://nettephp.com/cs/extras/jquery-ajax
 * @version     0.2
 */

jQuery.extend({
    nette: {
        updateSnippet: function(id, html) {
            $("#" + id).html(html);
            if(id == "snippet--flashMessages") {
                setTimeout(function() { $("#" + id).slideUp(800, function() {
                        $(this).show();
                }).html("");  }, 2000);
            }
        },

        success: function(payload) {
            // redirect
            if (payload.redirect) {
                window.location.href = payload.redirect;
                return;
            }

            // snippets
            if (payload.snippets) {
                for (var i in payload.snippets) {
                    jQuery.nette.updateSnippet(i, payload.snippets[i]);
                }
            }
        }
    }
});

jQuery.ajaxSetup({
    success: jQuery.nette.success,
    dataType: "json"
});

var href;

$('document').ready(function() {  
        $("a.ajax").live("click", function (event) {
            event.preventDefault();
            href = $(this).attr("href");
            if($(this).data().confirm) {
                alertify.confirm($(this).data().confirm, function(e) {
                    if(e) {
                        $.get(href);
                    }
                });
            }
            else
                $.get(href);
        });
});