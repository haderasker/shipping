
$('.datatable-default').DataTable({
    responsive: true,
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": false,
    "info": true,
    "autoWidth": false,
    "scrollX": true,
});

function setSelected(e){
    // var e = $(el);
    // var colIndex = e.parents('th').index();
    var checked = $(e).text() == "select";
    // .find('td').eq(colIndex).find('input:checkbox')

    // var chk = e.closest('table').find('tbody');
        // .filter(':not(:has([colspan]))')
        // .children(':nth-child(' + colIndex + ')');

    $('input:checkbox.check-select').prop('checked', checked);

    if (checked)
    $(e).text('unselect');
    else
    $(e).text('select');
}
