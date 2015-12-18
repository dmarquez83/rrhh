'use strict';
angular.module('app').service('XLSXMaker',
  [ 'server', 'ngProgress', 'SweetAlert',
  function (server, ngProgress, SweetAlert) {
    return {
      make : function (title, fileName, header, body) {
        SweetAlert.swal({
          title: "Esta operación puede tardar algunos minutos",
          text: "Desea continuar",
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true,
          cancelButtonText: "Cancelar",
        },
        function(isConfirm){

            if (isConfirm) {

              ngProgress.start();
              ngProgress.height(5);
              var tableHeader = [];
              var tableBody = [];

              $.each(header.children, function(key, row){
                $.each(row.children, function(key, column){
                  tableHeader.push(column.innerText);
                });
              });

              $.each(body.children, function(key, row){
                var newRow = [];
                $.each(row.children, function(key, column){
                  newRow.push(column.innerText);
                });
                tableBody.push(newRow);
              });

              var dataForExcel = {
                'fileName': fileName,
                'title': 'Hoja 1',
                'head': tableHeader,
                'data': tableBody
              };

              server.post('excel', dataForExcel).success(function(result){
                var path = result.split('public')[1];
                var anchor = document.createElement('a');
                anchor.download = fileName + '.xlsx';
                anchor.href = path;
                anchor.click();
                ngProgress.complete();
                SweetAlert.swal("Generado!", 'El archivo empezó a descargarse', 'success');
              });

            }  

        }
      );

      }
    };
  }
]);
