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
        // Evento: Atualizar Todos Painéis
        form.on('update-all.panel', function () {
            // Executar Atualização em Todos os Painéis
            form.find('.panel').each(function () {
                // Inicialização
                var panel = $(this);
                // Execução
                panel.trigger('update.panel');
            });
        });
        // Evento: Marcar Checkbox
        form.on('change', ':checkbox', function () {
            // Inicialização
            var element = $(this);
            // Atualizar Painel
            element.closest('.panel').trigger('update.panel');
        });
        // Evento: Limpar Formulário
        form.on('click', '.btn-reset', function () {
            // Limpar Formulário
            form.trigger('reset');
            // Atualizar Todos Painéis
            form.trigger('update-all.panel');
        });
        // Atualizar Todos Painéis
        form.trigger('update-all.panel');
    })();

});
