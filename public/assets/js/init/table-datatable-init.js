$(function () {
  $('#xp-default-datatable').DataTable({
    order: [],                 // keep Laravel order
    pageLength: 10,            // default selected value
    lengthMenu: [[10,15,25,50,100,-1],[10,15,25,50,100,'All']],
    responsive: true,
    language: {
      lengthMenu: 'Show _MENU_ entries' // optional: force this text
    }
  });
});
