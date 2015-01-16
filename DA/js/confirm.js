$('document').ready(function() {
        $('[data-confirm]:not(.ajax)').click(function() {
            return confirm($(this).data().confirm);
        });
});