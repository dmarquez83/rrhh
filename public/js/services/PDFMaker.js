'use strict';
angular.module('app').service('PDFMaker', [
  '$filter',
  'documentValidateWithoutAlert',
  '$q',
  'ngProgress',
  'SweetAlert',
  function ($filter, documentValidateWithoutAlert, $q, ngProgress, SweetAlert) {
    return {
      make : function (pageSize, pageOrientation, title, fileName, header, body, footer, type) {

        SweetAlert.swal({
            title: "Esta operación puede tardar algunos minutos",
            text: "Desea continuar",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            cancelButtonText: "Cancelar",
          }, function(isConfirm){

            if (isConfirm) {
                setTimeout(function(){
                ngProgress.start();
                ngProgress.height(7);

                var defered = $q.defer();
                var promise = defered.promise;

                var tableHeader = [];
                var tableBody = [];
                var tableFooter = [];

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

                if (footer) {
                  $.each(footer.children, function(key, row){
                    var newRow = [];
                    $.each(row.children, function(key, column){
                      var colspan = parseInt($(column).attr('colspan'));
                      if (colspan > 1) {
                        for (var i = 1; i <= colspan; i++) {
                          if (i === colspan) {
                            newRow.push(column.innerText);
                          } else {
                            newRow.push('');
                          }
                        };
                      } else {
                        newRow.push(column.innerText);
                      }
                    });
                    tableFooter.push(newRow);
                  });
              }


                var columnsWidth = [];
                var styleHeader = [];

                _(tableHeader).each(function(header){
                  columnsWidth.push('auto');
                  styleHeader.push({ 'text': header, 'style': 'tableHeader', fillColor: '#ebebeb'});
                });

                var styleFooter = [];
                if (tableFooter.length > 0) {
                  _(tableFooter[0]).each(function(foot){
                    styleFooter.push({ 'text': foot, 'style': 'tableFooter', fillColor: '#ebebeb', alignment: 'right'});
                  });
                }
                

                function isNumeric ( obj ) {
                    return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
                }

                function isFloat(value) {
                  if (value.toString().indexOf('.') !== -1) {
                    return true;
                  }
                  return false;
                }

                var dataValues =  _(tableBody).map(function(row){
                  var newRow = _(row).map(function(value){
                    var newValue = value.replace('.','').replace(',','.').replace('$', '');
                    if (!isNaN(newValue) && newValue !== '') {
                      var cellNumber = {text: value, alignment: 'right'};
                      if (value.toString().indexOf('.') === -1 && value.toString().indexOf(',') === -1) {
                        if (!documentValidateWithoutAlert.validateDocument(value)) {
                          return cellNumber;
                        } else {
                          return value;
                        }
                      }
                      return cellNumber;
                    }
                    return value;
                  });
                  return newRow;
                });

                var headerAndFooter = {
                  'pageSize': pageSize, 'pageOrientation' : pageOrientation, pageMargins: [ 30, 20, 30, 20 ],
                  'footer': function(currentPage, pageCount) { return { 'text' : currentPage.toString() + ' de ' + pageCount, 'style': 'footer'}; }
                };

                var layout = {
                  hLineWidth: function() {
                          return 0.5;
                  },
                  vLineWidth: function() {
                          return 0.5;
                  },
                  hLineColor: function() {
                          return '#dddbdb';
                  },
                  vLineColor: function() {
                          return '#dddbdb';
                  }
                };

                var finalBody = null;
                if (styleFooter.length > 0) {
                  finalBody =  _([styleHeader]).chain().union(dataValues).union([styleFooter]).value();
                } else {
                  finalBody =  _([styleHeader]).union(dataValues);
                }
                
                //console.log(finalBody);

                var table = {'headerRows': 1, 'body': finalBody, widths: columnsWidth};
                var styles = {
                  header: {
                    fontSize: 12,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                  },
                  tableExample : {
                    margin: [0, 0, 0, 0],
                    fontSize: 7,
                  },
                  tableHeader : {
                    fontSize: 8,
                    bold: true,
                    background: '#ebebeb',
                    margin: [5, 5, 5, 5],
                    alignment: 'center'
                  },
                  tableFooter : {
                    fontSize: 7,
                    bold: true,
                    background: '#ebebeb',
                    margin: [5, 5, 5, 5],
                    alignment: 'center'
                  },
                  footer : {
                    margin: [0, 0, 20, 0],
                    alignment: 'right',
                    fontSize: 8
                  },
                  number : {
                    alignment: 'right'
                  }
                };

                var content = {'content': [{ text: title, style: 'header'}, {'table': table, 'style': 'tableExample', 'layout': layout}], 'styles': styles};
                var docDefinition = _(headerAndFooter).extend(content);

                promise.then(function(result){
                  if(type === 'file'){
                    pdfMake.createPdf(docDefinition).download(fileName+'.pdf');
                  }
                  if(type === 'print'){
                    pdfMake.createPdf(docDefinition).print();
                  }
                  ngProgress.complete();
                  SweetAlert.swal("Generado!", 'El archivo empezó a descargarse', 'success');
                }, function(error){
                  ngProgress.complete();
                  SweetAlert.swal("Generado!", 'El archivo empezó a descargarse', 'success');
                });


                  defered.resolve();
                }, 1000);
            }

        });
      }
    };
  }
]);
