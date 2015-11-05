$(function () {
    (function () {
        var element     = $('#entries .collection-element');
        var template    = element.data('template');
        var placeholder = element.data('placeholder');
        var container   = $('#entries .collection-container');
        // Adicionar Entrada de Lançamento
        $('#entries-add').on('click', function () {
            container.append(template);
        });
    })();
});
