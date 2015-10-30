$(function () {

    // Confirmação em Âncoras
    (function () {
        // Confirmar Execução
        $(document).on('click', 'a[data-confirm]', function (event) {
            if (!window.confirm($(this).data('confirm'))) {
                event.preventDefault();
            }
        });
    })();

});
