'use strict';
angular.module('app').service('CSVMaker',
  [ 'server', 'ngProgress', 'SweetAlert',
    function (server, ngProgress, SweetAlert) {
      return {
        make : function (fileName, header, body) {
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

                var csvContent = "data:text/csv;charset=utf-8,\ufeff";
                csvContent += tableHeader.join(";") + "\n";
                tableBody.forEach(function(row, index){
                  var rowString = row.join(";");
                  csvContent += index < tableBody.length ? rowString+ "\n" : rowString;
                });

                var encodedUri = encodeURI(csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", fileName+".csv");

                link.click();
                ngProgress.complete();
                SweetAlert.swal("Generado!", 'El archivo empezó a descargarse', 'success');
              }

            });

        }
      };
    }
  ]);
