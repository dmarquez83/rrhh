'use strict';
angular.module('app').service('optionsDataTable', [
  '$window',
  'server',
  'DTColumnBuilder',
  'PDFMaker',
  'XLSXMaker',
  'CSVMaker',
  '$http',
  '$filter',
  '$timeout',
  function ($window, server, DTColumnBuilder, PDFMaker, XLSXMaker, CSVMaker, $http, $filter, $timeout) {
    return {
      'scrollY90' : $(window).height() * 0.9 * 0.9,
      'scrollY80' : $(window).height() * 0.9 * 0.8,
      'scrollY70' : $(window).height() * 0.9 * 0.7,
      'scrollY65' : $(window).height() * 0.9 * 0.65,
      'scrollY60' : $(window).height() * 0.5 * 0.6,
      'scrollY50' : $(window).height() * 0.5 * 0.5,
      'scrollY40' : $(window).height() * 0.5 * 0.4,
      'showOptions' : [[25, 50, 100, 500, -1], [25, 50, 100, 500, "Todo"]],
      'dom': 'l<"pull-right"B>rftip',
      'simpleDom': 'rtip',
      'buttons' : function(fileName, title, pageOrientation, pageSize) {
        return [
            {
              extend: 'collection',
              className: 'btn-white btn-sm',
              text: 'Columnas',
              buttons: ['columnsVisibility'],
              visibility: true
            },
            { text: 'Imprimir', className: 'btn-white btn-sm', extend: 'print', action: function(element, dataTable){
              var header = dataTable.header()[0];
              var body = dataTable.body()[0];
              var footer = dataTable.footer()[0];
              PDFMaker.make(pageSize, pageOrientation, title, fileName, header, body, footer, 'print');
            }},
            { text: 'CSV', className: 'btn-white btn-sm', action: function(element, dataTable){
              var header = dataTable.header()[0];
              var body = dataTable.body()[0];
              CSVMaker.make(fileName, header, body);
            }},
            { text: 'Excel', className: 'btn-white btn-sm', action: function(element, dataTable) {
              var header = dataTable.header()[0];
              var body = dataTable.body()[0];
              XLSXMaker.make(title, fileName, header, body);
            }},
            { text: 'PDF', className: 'btn-white btn-sm', action: function(element, dataTable){
              var header = dataTable.header()[0];
              var body = dataTable.body()[0];
              var footer = dataTable.footer()[0];
              PDFMaker.make(pageSize, pageOrientation, title, fileName, header, body, footer, 'file');
            }},

        ];
      },
      'urlTableTools' : '/../../../bower_components/datatables-tabletools/swf/copy_csv_xls_pdf.swf',
      'generateTableColumns' : function(columns){
        return _(columns).map(function(column){
          return DTColumnBuilder.newColumn(column.field).withTitle(column.value).withOption('defaultContent', '');
        });
      },
      'createTableColumns' : function(columns){
        return _(columns).map(function(column){
          var filters = {
            'date': function (data) {
              if (data) {
                var date = moment(data);
                return date.format('YYYY-MM-DD');
              }
              return '';
            },
            'businessPartner': function (data) {
              var businessPartner = '';
              var businessName = _(data).has('businessName') ? data.businessName : '';
              var comercialName = _(data).has('comercialName') ? data.comercialName : '';
              var personName = _(data).has('names') ? data.names : '';
              var personSurname = _(data).has('surnames') ? data.surnames : '';
              var personCompleteName = personName.trim() + ' ' + personSurname.trim();

              businessPartner = (personCompleteName !== ' ' ? personCompleteName: businessPartner);
              businessPartner = (businessName !== '' ? businessName: businessPartner);
              businessPartner = (comercialName !== '' ? comercialName: businessPartner);

              return businessPartner;
            },
            'currency' : function(data) {
              return $filter('currency')(data);
            },
            'number' : function(data) {
              return $filter('number')(data, 2);
            }
          };

          var render = (_(column).has('filter') ? filters[column.filter] : ( _(column).has('render') ? column.render : null ));

          return DTColumnBuilder.newColumn(column.field)
            .withTitle(column.title)
            .withOption('defaultContent', column.defaultContent || ' ')
            .withClass(column.class || null)
            .renderWith(render);
        });
      },
      'rowCallback' : function(functionPass){
        var final = function (nRow, aData) {
          $('td', nRow).unbind('click');
          $('td', nRow).on('dblclick', function () {
              functionPass(aData);
          });
          return nRow;
        };
        return final;
      },
      'fromSource': function(route, extraData){
        var source = function(params, callback){
          params = _(params).extend(extraData);
          $http.post(route, params).success(function(data) {
              callback(data);
          });
        };
        return source;
      },
      'footerCallback': function(columnsArray) {
        var footer =   function (row, data, start, end, display) {
          var api = this.api(), data;
          var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
          };
          _(columnsArray).each(function(indexColumn){
            var total = api.column(indexColumn, { page: 'current' }).data().reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
            $(api.column(indexColumn).footer()).html($filter('currency')(total));
          })
        };
        return footer;
      },
      'loadState': function(title){
        return function(oSettings, oData){
          return JSON.parse(localStorage.getItem(title.trim().toLowerCase() + 'DataTables'));
        }
      },
      'saveState': function(title){
        return function(oSettings, oData){
          localStorage.setItem(title.trim().toLowerCase() + 'DataTables', JSON.stringify(oData));
        }
      },
      'buttonPrint': function(printFunctionName){
        return function(documentName){
          var printButton = '<button type="button" class="btn btn-default btn-sm btn-table"' +
            ' ng-click="'+printFunctionName+'(\''+ documentName.number +'\')" >' +
            '<span class="fa fa-print" aria-hidden="true"></span>' +
            '</button>';
          return printButton;
        }
      },
      'printFunction': function(url, documentName, documentNumber, $scope){
        $http.post(url, {'number': documentNumber}).success(function(data){
          $('<iframe src="'+ data.url.replace('public/', '') +'" id="pv'+documentName+documentNumber+'" type="application/pdf" width="0px" height="0px"></iframe>').appendTo('body');
          var getMyFrame = document.getElementById('pv'+documentName+documentNumber);
          getMyFrame.focus();
          getMyFrame.contentWindow.print();
          $timeout(function(){$('#pv'+documentName+documentNumber).remove();}, 2500);
          $scope.loadingSummary = 'hidden';
        });
      }
    };
  }
]);
