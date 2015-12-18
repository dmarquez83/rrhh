'use strict';
angular.module('app').directive('onReadFile', [
  '$parse',
  function ($parse) {
    return {
      restrict: 'A',
      scope: false,
      link: function (scope, element, attrs) {

        var fn = $parse(attrs.onReadFile);
        
        element.on('change', function (onChangeEvent) {
          scope.$apply(function () {
            var reader = new FileReader();
            reader.onload = function (e) {
              var data = e.target.result;
              var workbook = null;
              var jsonData = null;
              try {
                workbook = XLSX.read(data, { type: 'binary' });
                var numerSheets = _(workbook.Sheets).size();
                if (numerSheets == 1) {
                  _(workbook.Sheets).each(function (content, nameSheet) {
                    jsonData = XLSX.utils.sheet_to_json(content);
                  });
                } else {
                  toastr.error('Error', 'El archivo Excel tiene mas de 1 Hoja de trabajo');
                }
              } catch (err) {
                try {
                  workbook = XLS.read(data, { type: 'binary' });
                  var numerSheets = _(workbook.Sheets).size();
                  if (numerSheets == 1) {
                    _(workbook.Sheets).each(function (content, nameSheet) {
                      jsonData = XLS.utils.sheet_to_row_object_array(content);
                    });
                  } else {
                    toastr.error('El archivo Excel tiene mas de 1 Hoja de trabajo');
                  }
                } catch (err) {
                  toastr.error('Existe un problema con el archivo Excel' + err);
                }
              }
              fn(scope, { $fileContent: jsonData });
            };
            reader.readAsBinaryString((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
          });
        });

      }
    };
  }
]);