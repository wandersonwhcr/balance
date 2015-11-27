$(function () {
    // Movimentar Linhas
    (function () {
        // Tabela
        var table   = $('.table');
        var columns = table.find('thead tr:first-child th');
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
                // Altura do Placeholder
                ui.placeholder.height(ui.item.height());
            },
            stop: function (event, ui) {
                // Parâmetros
                var params = {};
                // Captura de Chaves Primárias
                params.id       = ui.item.find('.table-move').data('id');
                params.previous = ui.item.prev().find('.table-move').data('id');
                // Encontrado?
                if (params.previous == undefined) {
                    // Configurar como Inexistente
                    params.previous = 0;
                }
                // Processamento
                $.post($.application.basePath('/accounts/order'), params, function (envelopes) {
                    // Ordenação Efetuada com Sucesso
                }, 'json');
            }
        }).disableSelection();
    })();
});
