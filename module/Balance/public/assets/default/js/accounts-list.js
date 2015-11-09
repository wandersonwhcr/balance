$(function () {
    // Movimentar Linhas
    (function () {
        // Tabela
        var table = $('.table');
        // Draggable
        table.find('tbody').sortable({
            axis: 'y',
            scroll: true,
            handle: '.table-move',
            cancel: ''
        }).disableSelection();
    })();
});
