'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'XLSXReaderService',
  function ($scope, documentValidate, server, XLSXReaderService) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.datos = [];
    $scope.validated = false;
    $scope.datosNuevo = [];
    $scope.cabeceraInicial = true;
    $scope.cabeceraFinal = false;
    $scope.configuracion = [{_id: 1, hour: '08:00', type: 'in'},
                            {_id: 2, hour: '13:00', type: 'out'},
                            {_id: 3, hour: '14:00', type: 'in'},
                            {_id: 4, hour: '17:00', type: 'out'}];


    var countsheets = 0;
    var quantitycol = 0;
    var col1 = '';
    var col2 = '';
    var col3 = '';
    var message='';
    var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];

    $scope.fileChanged = function(files) {

      $scope.namefile = (files[0].name);
      $scope.typefile = $scope.namefile.split('.')[1];
      if (($scope.typefile == 'xls')  || ($scope.typefile == 'xlsx') ){
        $scope.isProcessing = true;
        $scope.sheets = [];
        $scope.excelFile = files[0];
        XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
          $scope.sheets = xlsxData.sheets;
          $scope.isProcessing = false;
          countsheets = (Object.keys($scope.sheets).length);
          if(countsheets > 1){
            toastr.error('Error', 'El archivo Excel tiene mas de 1 Hoja de trabajo');
          }else{
            angular.forEach($scope.sheets, function (sheetData, sheetName) {

              angular.forEach(sheetData, function (row) {


                quantitycol = (Object.keys(row).length);
                if(quantitycol == 3){
                  col1 = (Object.keys(row)[0]);
                  col2 = (Object.keys(row)[1]);
                  col3 = (Object.keys(row)[2]);
                  if(col1=='Codigo' && col2=='Fecha' && col3=='Hora'){
                    $scope.validated = true;
                  }else{
                    message = 'El Nombre correcto de las columnas es "codigo","fecha" y "hora ';
                  }
                }else{
                  message = 'La cantidad de Columnas debe ser igual a 3, este archivo tiene: '+quantitycol+' Columnas';
                }
              });
              if ($scope.validated) {
                $scope.datos = sheetData;
              }else{
                toastr.error('Error', message);
              }
            });
          }
        });
      }else
      {
        toastr.error('Error', 'El Tipo de archivo permitido es .xls y .xlsx');
      }

    }

    $scope.enviar = function(){

      var dateval = false;

          angular.forEach($scope.datos, function (row) {
          var myarr = row.Fecha.split("/");
          var fecha = myarr[1]+'/'+myarr[0]+'/'+myarr[2];
          //console.log(row.Codigo,row.Fecha,row.Hora,fecha);
          var objDate = new Date(fecha),
              locale = "en-us",
              month = objDate.toLocaleString(locale, { month: "long" });
          if($scope.monthSearch==month){
            dateval = true;
            $scope.datosNuevo.push(row);
            //var datosNuevos = {Codigo : row.Codigo, Fecha : row.Fecha, Hora : row.Hora};
          }
        });

      $scope.datos = $scope.datosNuevo;
      $scope.cabeceraInicial = false;
      $scope.cabeceraFinal = true;

        if(!dateval){
          alert('Archivo vacio, ninguna fecha corresponde al mes seleccionado: '+$scope.monthSearch);
        }


    }

    handlePanelAction();
  }
]);
