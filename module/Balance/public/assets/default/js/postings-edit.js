$(function () {
    (function () {
        // Template
        var template    = $('#entries .collection-template');
        var content     = template.data('content');
        var placeholder = template.data('placeholder');
        // Armazenador
        var container = $('#entries .collection-container');
        var counter   = 0;
        // Adicionar Entrada de Lançamento
        $('#entries-add').on('click', function () {
            container.append(content.replace(/__index__/g, counter++));
        });
        // Remover Entradas de Lançamento
        $('#entries').on('click', '.collection-element-remove', function () {
            $(this).closest('.collection-element').remove();
        });
    })();
});
