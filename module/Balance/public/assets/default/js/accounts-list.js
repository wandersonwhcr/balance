$(function () {
    // Movimentar Linhas
    (function () {
        // Tabela
        var table = $('.table');
        // Movimentar Linhas
        table.on('click', '.table-move', function () {
            console.log(this);
        });
    })();
});
