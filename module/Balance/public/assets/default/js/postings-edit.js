$(function () {
    (function () {
        // Template
        var template    = $('#entries .collection-template');
        var content     = template.data('content');
        var placeholder = template.data('placeholder');
        var count       = template.data('count');
        // Armazenador
        var container = $('#entries .collection-container');
        var counter   = container.find('.collection-element').length;
        // Callback: Atualizar Remoções
        var updateCallback = function () {
            // Verificar Quantidades
            var disabled = (container.find('.collection-element').length <= count);
            // Habilitar/Desabilitar Elementos
            container.find('.collection-element-remove').attr('disabled', disabled);
        };
        // Adicionar Entrada de Lançamento
        $('#entries-add').on('click', function () {
            // Adicionar Conteúdo
            container.append(content.replace(/__index__/g, counter++));
            // Atualizar Remoções
            updateCallback();
        });
        // Remover Entradas de Lançamento
        $('#entries').on('click', '.collection-element-remove', function () {
            // Remover Elemento
            $(this).closest('.collection-element').remove();
            // Atualizar Remoções
            updateCallback();
        });
        // Atualizar Remoções
        updateCallback();
    })();

    // Captura de Data e Hora
    (function () {
        // Capturar Elementos
        $('input.form-control-datetimepicker').datetimepicker({
            dateFormat : 'dd/mm/yy',
            timeFormat : 'HH:mm:ss'
        });
    })();
});
