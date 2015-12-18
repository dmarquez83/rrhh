'use strict';
angular.module('app').service('dataTablePDFMaker', [
  '$filter',
  'documentValidateWithoutAlert',
  function ($filter, documentValidateWithoutAlert) {
    return {
      make : function (pageSize, pageOrientation, title, fileName, tableHeader, data, type, columnNames) {

        data = angular.copy(data);
        columnNames = angular.copy(columnNames);

        Object.byString = function(o, s) {
          s = s.replace(/\[(\w+)\]/g, '.$1');
          s = s.replace(/^\./, '');
          var a = s.split('.');
          for (var i = 0, n = a.length; i < n; ++i) {
            var k = a[i];
            if (o != null && o != undefined) {
              if (k in o) {
                  o = o[k];
              } else {
                  return "";
              }
            } else {
              return "";
            }
          }
          return o;
        }

        var columnsWidth = [];
        var selectedColumnsTittle = [];
        var objectColumnNames = columnNames ? columnNames: {};
        var selectedColumns = columnNames ? _(columnNames).allKeys(): [];

        if(selectedColumns.length == 0){
          selectedColumns = _(tableHeader).map(function (column) {
            if (column.bVisible === true) {
              selectedColumnsTittle.push({ 'text': column.sTitle, 'style': 'tableHeader', fillColor: '#ebebeb'});

              if(column.mData === 'creationDate' || column.mData.indexOf('date') !== -1 || column.mData.indexOf('Date') !== -1) {
                columnsWidth.push(40);
              } else {
                columnsWidth.push('auto');
              }

              if(columnNames == null || columnNames == undefined){
                objectColumnNames[column.mData] = '';
              }

              return column.mData;
            }
          });
        } else {
          _(tableHeader).each(function (column) {
            if (column.bVisible === true) {
              selectedColumnsTittle.push({ 'text': column.sTitle, 'style': 'tableHeader', fillColor: '#ebebeb'});
              if (column.mData === 'names' || column.mData === 'surnames' || column.mData === 'description' || column.mData === 'name'
                || column.mData === 'customer' || column.mData === 'supplier' || column.mData === 'employee') {
                columnsWidth.push('*');
              } else {
                columnsWidth.push('auto');
              }
            }
          });
        }

        function isNumeric ( obj ) {
            return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
        }

        selectedColumns = _(selectedColumns).compact();

        function getPersonName (rowData){
          var personName = '';
          if (_(rowData).has('customer')){
            var customer = '';
            var businessName = _(rowData.customer).has('businessName') ? rowData.customer.businessName : '';
            var comercialName = _(rowData.customer).has('comercialName') ? rowData.customer.comercialName : '';
            var personName = _(rowData.customer).has('names') ? rowData.customer.names : '';
            var personSurname = _(rowData.customer).has('surnames') ? rowData.customer.surnames : '';
            var personCompleteName = personName.trim() + ' ' + personSurname.trim();

            customer = (personCompleteName !== ' ' ? personCompleteName: customer);
            customer = (businessName !== '' ? businessName: customer);
            customer = (comercialName !== '' ? comercialName: customer);
            personName = customer;
          }

          if (_(rowData).has('supplier')){
            var supplier = '';
            var businessName = _(rowData.supplier ).has('businessName') ? rowData.supplier.businessName : '';
            var comercialName = _(rowData.supplier ).has('comercialName') ? rowData.supplier.comercialName : '';
            var personName = _(rowData.supplier ).has('names') ? rowData.supplier.names : '';
            var personSurname = _(rowData.supplier ).has('surnames') ? rowData.supplier.surnames : '';
            var personCompleteName = personName.trim() + ' ' + personSurname.trim();

            supplier = (personCompleteName !== ' ' ? personCompleteName: supplier);
            supplier = (businessName !== '' ? businessName: supplier);
            supplier = (comercialName !== '' ? comercialName: supplier);

            personName = supplier;
          }
          return personName;
        }

        data = _(data).map(function (rowData) {
          _(objectColumnNames).mapObject(function(value, key){
            var newProperty = {};
            var valueProperty = '';
            if (key === 'customer' || key === 'supplier') {
              valueProperty = getPersonName(rowData);
            } else {
              valueProperty = Object.byString(rowData, key);
            }
            newProperty[key] =  valueProperty === undefined ? '' : valueProperty.toString();
            _(rowData).extend(newProperty);

          });
          var newObjectColumnNames = angular.copy(objectColumnNames);
          var  newRowData = _(rowData).pick(selectedColumns);
          _(newObjectColumnNames).extendOwn(newRowData);

          return newObjectColumnNames;
        });

        function isFloat(value) {
          if (value.toString().indexOf('.') !== -1) {
            return true;
          }
          return false;
        }


        var dataValues = _(data).map(function(rowData){
          var rowValues = _(rowData).values();
          rowValues = _(rowValues).map(function(value){
            if(isNumeric(value)){
              if(isFloat(value)) {
                var formatNumber = $filter('number')(value, '2');
                var cellNumber = {text: formatNumber, alignment: 'right'};
                return cellNumber;
              } else if(!documentValidateWithoutAlert.validateDocument(value)){
                var formatNumber = $filter('number')(value, '2');
                var cellNumber = {text: formatNumber, alignment: 'right'};
                return cellNumber;
              }
              return value;
            }
            if (Date.parse(value)) {
              return $filter('date')(value, 'yyyy-MM-dd');
            }
            return value;
          });
          return rowValues;
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

        var table = {'headerRows': 1, 'body': _([selectedColumnsTittle]).union(dataValues), widths: columnsWidth};
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

        if(type === 'file'){
          pdfMake.createPdf(docDefinition).download(fileName+'.pdf');
        }
        if(type === 'print'){
          pdfMake.createPdf(docDefinition).print();
        }
        return docDefinition;
      }
    };
  }
]);
