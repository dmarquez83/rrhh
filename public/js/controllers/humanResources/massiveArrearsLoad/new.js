'use strict';
angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  'documentValidate',
  'server',
  'XLSXReaderService','FactorysubtractHours','FactoryArrears',
  function ($scope, documentValidate, server, XLSXReaderService,FactorysubtractHours,FactoryArrears) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.datafile = [];
    $scope.datafilset = [];
    $scope.dataemployee = '';
    $scope.databackup = [];
    $scope.initialheader = true;
    $scope.endheader = false;
    $scope.configuration =
         [{_id: 1, hour: '08:00', type: 'in'},
          {_id: 2, hour: '13:00', type: 'out'},
          {_id: 3, hour: '14:00', type: 'in'},
          {_id: 4, hour: '17:00', type: 'out'}];
    $scope.Math = window.Math;
    $scope.validated = false;
    $scope.employees = [];
    $scope.employeesFile =  [];
    $scope.employeeFile = [];
    $scope.assignedDiscounts = {};
    $scope.descuento = 0.00;
    var countsheets = 0;
    var quantitycol = 0;
    var col1 = '';
    var col2 = '';
    var col3 = '';
    var message='';

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
                $scope.datafile = sheetData;
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
      console.log($scope.datafile);
    };

    $scope.send = function(){

      var dateval = false;

      angular.forEach($scope.datafile, function (row) {
        var myarr = row.Fecha.split("/");
        var dateFile = myarr[1]+'/'+myarr[0]+'/'+myarr[2];
        var objDate = new Date(dateFile),
            locale = "en-us",
            month = objDate.toLocaleString(locale, { month: "long" });
        if($scope.monthSearch==month){
          dateval = true;
          $scope.datafilset.push(row);
        }
      });

      server.post('getEmployees').success(function(result){
        var Codigos = _($scope.datafile).pluck('Codigo').map(function (value){return {'Codigo': value } });
        angular.forEach(_.groupBy(Codigos, 'Codigo'), function (row) {
          $scope.employees = _(result).where({ 'code':  row[0].Codigo });
          $scope.employeesFile.push($scope.employees[0]);
        });
      });

      $scope.datafile = $scope.datafilset;
      $scope.databackup = $scope.datafile;
      $scope.initialheader = false;
      $scope.endheader = true;

      if(!dateval){
        alert('Archivo vacio, ninguna fecha corresponde al mes seleccionado: '+ $scope.monthSearch);
      }
    }

    $scope.serachEmploye = function(){
      $scope.datarow = [];
      $scope.datarow_ =[];
      $scope.datarow_groupby = [];
      $scope.dataemployeedate={};
      $scope.resultdata = '';

      $scope.dataemployee = _.map(
          _.where($scope.databackup, {Codigo : $scope.employeeFile.code}),
          function(person) {
            return { Codigo: person.Codigo, Fecha: person.Fecha, Hora: person.Hora};
          }
      );
      var groupDate =_.groupBy($scope.dataemployee, 'Fecha');
      angular.forEach((groupDate), function(row){ //itero los datos agrupados por fecha de cada empleado seleccionado en el select - devuelve las fechas agrupadas
        $scope.dataemployeedate = _.map(
            _.where($scope.dataemployee, {Fecha : row[0].Fecha}),
            function(person) {
              return { Codigo: person.Codigo, Fecha: person.Fecha, Hora: person.Hora};
            }
        );
        var i = 1;
        angular.forEach(($scope.dataemployeedate), function(datos){ //itero los datos filtrados por cada fecha de cada empleado seleccionado en el combo de empleados
          var valorInicial = 99;
          $scope.hourconf = 0;
          angular.forEach(($scope.configuration), function(conf){ //itero los datos de la configuracion para compararlos con la hora del marcaje
            var resultRest = FactorysubtractHours.subtractHours(conf.hour,datos.Hora);
            if (parseInt(valorInicial) >= $scope.Math.abs(parseInt(resultRest)) ){
              valorInicial = resultRest;
              $scope.hourconf = conf.hour;
              $scope.hourconfType = conf.type;
            }
          });
          var color =  FactoryArrears.Arrears($scope.hourconf,datos.Hora,$scope.hourconfType);
          $scope.datarow[i] = ({hour:$scope.hourconf, type: $scope.hourconfType, register: {hora:datos.Hora,color: color}});

          $scope.datarow_.push({Fecha: row[0].Fecha, Columns: [$scope.datarow[i]]});
          i++;
        });
        $scope.datarow_groupby =_.groupBy($scope.datarow_, 'Fecha');

      });
      //console.log($scope.datarow_groupby,'agrupado');
      $scope.resultdata = _.values($scope.datarow_groupby);
      console.log(JSON.stringify($scope.resultdata),'json');
      console.log($scope.resultdata,'normal');
      //estoy parada con el  <div ng-repeat="col in datosarchivo[$index].Columns" style="background: {{col.register.color}}; color: darkblue">
      // si le envio 0 sale el de los otros empleados si le paso el index solo de uno
    };


    $scope.save = function(){

      //falta validaciones que tengo seleccionado un empleado para grabar  y que el descuento sea numerico

      $scope.employeeFile.discounts = _($scope.employeeFile).has('discounts') ? $scope.employeeFile.discounts : [];
      $scope.assignedDiscounts = {'discount': {type:'Valor',code:'descuento00000', name:'Delay',value:parseFloat($scope.descuento)}};
      $scope.assignedDiscounts.date = moment().format();
      $scope.assignedDiscounts.frequency = 'once';
      $scope.employeeFile.discounts.push($scope.assignedDiscounts);
      var discounts = { 'discounts': angular.copy($scope.employeeFile.discounts) };
      //console.log(discounts);
      server.update('employee', discounts, $scope.employeeFile._id).success(function (data) {
        toastr[data.type](data.msg);
      });
    };
    handlePanelAction();
  }
]);
