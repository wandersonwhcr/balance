$(function () {

    // Captura de Data e Hora
    (function () {
        // Capturar Elementos
        $('input.form-control-datetimepicker').datetimepicker({
            dateFormat : 'dd/mm/y',
            timeFormat : 'HH:mm:ss'
        });
    })();

});
