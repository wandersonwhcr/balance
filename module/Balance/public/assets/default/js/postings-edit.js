$(function () {
    (function () {
        // Template
        var template    = $('#entries .collection-template');
        var content     = template.data('content');
        var placeholder = template.data('placeholder');
        // Armazenador
        var container = $('#entries .collection-container');
        // Adicionar Entrada de Lançamento
        $('#entries-add').on('click', function () {
            container.append(content);
        });
        // Remover Entradas de Lançamento
        $('#entries').on('click', '.collection-element-remove', function () {
            $(this).closest('.collection-element').remove();
        });
    })();
});
