$(function () {
    // Movimentar Linhas
    (function () {
        // Tabela
        var table    = $('.table');
        var columns  = table.find('thead tr:first-child th');
        var position = null;
        // Draggable
        table.find('tbody').sortable({
            axis: 'y',
            scroll: true,
            handle: '.table-move',
            cancel: '',
            placeholder: 'ui-state-highlight',
            helper: function (event, ui) {
                // Configurar Colunas
                ui.find('td').each(function (i) {
                    // Coluna da Linha
                    var column = $(this);
                    // Configurar Largura do Helper
                    column.width(columns.eq(i).width());
                });
                // Largura de Colunas
                return ui;
            },
            start: function (event, ui) {
                // Capturar Posição do Item
                position = table.find('tbody tr').index(ui.item);
                // Altura do Placeholder
                ui.placeholder.height(ui.item.height());
            },
            stop: function (event, ui) {
                // Posição Atual
                var cPosition = table.find('tbody tr').index(ui.item);
                // Parâmetros de Execução
                var params = {
                    before: position,
                    after:  cPosition
                };
                // Processamento
                $.post($.application.basePath('/accounts/order'), params, function (envelopes) {
                    // Ordenação Efetuada com Sucesso
                }, 'json');
                // Limpar Posição Final
                position = null;
            }
        }).disableSelection();
    })();
});
