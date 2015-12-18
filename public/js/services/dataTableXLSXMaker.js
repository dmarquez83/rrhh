'use strict';
angular.module('app').service('dataTableXLSXMaker', [ 'server', '$filter', 'documentValidateWithoutAlert',
  function (server, $filter, documentValidateWithoutAlert) {
    return {
      make : function (title, filename, tableHeader, realData, realColumnNames) {

        var data = angular.copy(realData);
        var columnNames = angular.copy(realColumnNames);

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
        };

        var columnDefs = [];
        var objectColumnNames = columnNames ? columnNames : {};
        var objectColumnNamesArray = [];
        var selectedColumns = columnNames ? _(columnNames).allKeys() : [];
        if (columnNames == undefined || columnNames == null) {
          selectedColumns = _(tableHeader).map(function (column) {
            if (column.bVisible === true) {
              columnDefs.push({'field': column.mData, 'displayName': column.sTitle});
              objectColumnNames[column.mData] = '';
              objectColumnNamesArray.push(column.sTitle);

              return column.mData;
            }
          });
        } else {
          _(tableHeader).map(function (column) {
            if (column.bVisible === true) {
              columnDefs.push({'field': column.mData, 'displayName': column.sTitle});
              objectColumnNamesArray.push(column.sTitle);
            }
          });
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
              console.log(key);
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

        function isNumeric ( obj ) {
            return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
        }

        function isFloat(value) {
          if (value.toString().indexOf('.') !== -1) {
            return true;
          }
          return false;
        }


        var finalData = _(data).map(function(rowData){
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

        var dataForExcel = {
          'fileName': filename,
          'title': title,
          'head': objectColumnNamesArray,
          'data': finalData
        };

        server.post('excel', dataForExcel).success(function(result){
          var path = result.split('public')[1];
          var anchor = document.createElement("a");
          anchor.download = filename + ".xlsx";
          anchor.href = path;
          anchor.click();
        });
      }
    };
  }
]);
