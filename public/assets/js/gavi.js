/* =========================================================
   GAVI Dashboard - Custom JS
   ========================================================= */

$(function () {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function () {
        $('.alert.alert-success, .alert.alert-info').fadeOut('slow');
    }, 5000);

    // Confirm delete/action dialogs
    $(document).on('click', '[data-confirm]', function (e) {
        if (!confirm($(this).data('confirm'))) {
            e.preventDefault();
        }
    });

    // Auto-submit form on select change (for filters)
    $(document).on('change', '.auto-submit', function () {
        $(this).closest('form').submit();
    });
});
