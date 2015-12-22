$(function () {

    // Módulos Instalados
    (function () {
        // Inicialização
        var form = $('#form-modules');
        // Evento: Atualizar Painel
        form.on('update.panel', '.panel', function () {
            // Inicialização
            var panel = $(this);
            // Instalado?
            var installed = panel.find('.panel-heading :checkbox').is(':checked');
            // Painel Padrão?
            panel[installed ? 'removeClass' : 'addClass']('panel-default');
            // Panel Primário?
            panel[installed ? 'addClass' : 'removeClass']('panel-success');
        });
        // Evento: Marcar Checkbox
        form.on('click', ':checkbox', function () {
            // Inicialização
            var element = $(this);
            // Atualizar Painel
            element.closest('.panel').trigger('update.panel');
        });
        // Executar Atualização em Todos os Painéis
        form.find('.panel').each(function () {
            // Inicialização
            var panel = $(this);
            // Execução
            panel.trigger('update.panel');
        });
    })();

});
