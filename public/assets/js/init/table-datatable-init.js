"use strict";
$(document).ready(function(){

    /* -----  Table - Datatable  ----- */
    $('#datatable').DataTable();

    $('#xp-default-datatable').DataTable( {
        "order": [[ 3, "desc" ]]
    } );
    
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    table.buttons().container()
    .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

});

(function(){
        var lang = document.documentElement.lang || '{{ app()->getLocale() }}' || 'en';
        var urls = {
          th: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
          en: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json'
        };
        if (window.jQuery && jQuery.fn && jQuery.fn.dataTable) {
          jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            language: { url: urls[lang] || urls.en }
          });
        }
      })();