'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'XLSXReaderService','FactorysubtractHours','FactoryArrears',
  function ($scope, documentValidate, server, XLSXReaderService,FactorysubtractHours,FactoryArrears) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.Math = window.Math;
    //console.log($scope.Math.abs(-7.25));
    $scope.datos = [];
    $scope.datosNuevo = [];
    $scope.datosListos = [];
    $scope.datosListos2 = [];
  //  $scope.datosListos2.columns = [];
    $scope.datosRespaldo = [];
    $scope.datosGroup = [];
    $scope.validated = false;
    $scope.employees = [];
    $scope.date = [];
    $scope.employeesFile =  [];
    $scope.cabeceraInicial = true;
    $scope.cabeceraFinal = false;
    $scope.pertenece = '';
    $scope.perteneceType = '';
    $scope.employeeFile = [];
    $scope.configuracion =
     [{_id: 1, hour: '08:00', type: 'in'},
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

      server.post('getEmployees').success(function(result){

        var Codigos = _($scope.datos).pluck('Codigo').map(function (value){return {'Codigo': value } });

        angular.forEach(_.groupBy(Codigos, 'Codigo'), function (row) {
          $scope.employees = _(result).where({ 'code':  row[0].Codigo });
          //console.log(row[0].Codigo, $scope.employees[0]);
          $scope.employeesFile.push($scope.employees[0]);

        });

      });


      $scope.datos = $scope.datosNuevo;
      $scope.datosRespaldo = $scope.datosNuevo;
      $scope.cabeceraInicial = false;
      $scope.cabeceraFinal = true;

      if(!dateval){
        alert('Archivo vacio, ninguna fecha corresponde al mes seleccionado: '+ $scope.monthSearch);
      }
    }

    $scope.serachEmploye = function(){
      $scope.prueba = [];
      $scope.prueba2 = [];
      var myConf = '';
      var groupDate = '';
      var groupDate2 = '';
      $scope.DateFile = '';
      $scope.datosNuevo= '';
      $scope.datosListos='';
      $scope.pertenece = 0;
      $scope.datosListos2 =[];

      $scope.datosNuevo = _.map(
          _.where($scope.datosRespaldo, {Codigo : $scope.employeeFile.code}),
          function(person) {
            return { Codigo: person.Codigo, Fecha: person.Fecha, Hora: person.Hora};
          }
      );
      var groupDate =_.groupBy($scope.datosNuevo, 'Fecha');
      angular.forEach((groupDate), function(row){ //itero los datos de cada empleado seleccionado en el select
        $scope.datosListos = _.map(
            _.where($scope.datosNuevo, {Fecha : row[0].Fecha}),
            function(person) {
              return { Codigo: person.Codigo, Fecha: person.Fecha, Hora: person.Hora};
            }
        );
            var i = 1;
            angular.forEach(($scope.datosListos), function(datos){ //itero los datos agrupados por fecha de cada empleado seleccionado en el select
                  var valorInicial = 99;
                  angular.forEach(($scope.configuracion), function(conf){ //itero los datos de la configuracion para compararlos con la hora del marcaje
                    var resultRest = FactorysubtractHours.subtractHours(conf.hour,datos.Hora);
                    if (parseInt(valorInicial) >= $scope.Math.abs(parseInt(resultRest)) ){
                      valorInicial = resultRest;
                      $scope.pertenece = conf.hour;
                      $scope.perteneceType = conf.type;
                    }
                  });
              var color =  FactoryArrears.Arrears($scope.pertenece,datos.Hora,$scope.perteneceType);
              $scope.prueba[i] = ({hour:$scope.pertenece, type: $scope.perteneceType, register: {hora:datos.Hora,color: color}});
              $scope.pertenece = 0;
              $scope.datosListos2.push({Fecha: row[0].Fecha, Columns: [$scope.prueba[i]]});
              i++;
            });
        $scope.prueba2 =_.groupBy($scope.datosListos2, 'Fecha');

      });
      //console.log($scope.prueba2,'agrupado');
      $scope.DateFile = _.values($scope.prueba2);
      //console.log(JSON.stringify($scope.DateFile));
      //console.log($scope.DateFile);
      //estoy parada con el  <div ng-repeat="col in datosarchivo[$index].Columns" style="background: {{col.register.color}}; color: darkblue"> si le envio 0 sale el de los otros empleados si le paso el index solo de uno
    };


    $scope.save = function(){
      //no llegan los objetos del formulario
      console.log($scope.employeeFile.code, 'empleado');
      console.log($scope.descuento,'descuento');
    };
    handlePanelAction();
  }
]);
