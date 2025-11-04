$(function () {
  $('#xp-default-datatable').DataTable({
    order: [],
    pageLength: 10,
    lengthMenu: [[10,15,25,50,100,-1],[10,15,25,50,100,'All']],
    responsive: true,
    language: { lengthMenu: 'Show _MENU_ entries' },

    // NEW: enforce narrow columns
    columnDefs: [
      { targets: 0, width: '56px', className: 'text-center' }, // No.
      { targets: -1, width: '92px', className: 'text-center' } // Actions
    ]
  });
});
