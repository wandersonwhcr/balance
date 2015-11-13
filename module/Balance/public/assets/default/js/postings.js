$(function () {

    // Captura de Data e Hora
    (function () {
        // Capturar Elementos
        $('input.form-control-datetimepicker').datetimepicker({
            dateFormat : 'dd/mm/yy',
            timeFormat : 'HH:mm:ss'
        });
    })();

});
