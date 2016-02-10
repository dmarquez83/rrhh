'use strict';

angular.module('app').directive("fileread", [function () {
    return {
        scope: {
            fileread: "="
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                scope.$apply(function () {
                    scope.fileread = changeEvent.target.files[0];
                    // or all selected files:
                    // scope.fileread = changeEvent.target.files;
                });
            });
        }
    }
}]);

angular.module('app').controller('MassiveArrearsLoadCtrl', [
  '$scope',
  '$state',
  'documentValidate',
  'server',
  'XLSXReaderService','FactorysubtractHours','FactoryArrears','FactoryaddingHours',
  function ($scope, $state, documentValidate, server, XLSXReaderService,FactorysubtractHours,FactoryArrears,FactoryaddingHours, fileread) {

    $scope.showPreview = false;
    $scope.showJSONPreview = true;
    $scope.datafile = [];
    $scope.datafilset = [];
    $scope.dataemployee = '';
    $scope.databackup = [];
    $scope.initialheader = true;
    $scope.endheader = false;
   /* $scope.configuration =
         [{_id: 1, hour: '08:00', type: 'in'},
          {_id: 2, hour: '13:00', type: 'out'},
          {_id: 3, hour: '14:00', type: 'in'},
          {_id: 4, hour: '17:00', type: 'out'}];*/
    $scope.Math = window.Math;
    $scope.validated = false;
    $scope.employees = [];
    $scope.employeesFile =  [];
    $scope.employeeFile = [];
    $scope.employeesDiscounts = [];
    $scope.assignedDiscounts = {};
    $scope.descuento = 0.00;
    $scope.archivo = null;
    $scope.procesar = true;
    var countsheets = 0;
    var quantitycol = 0;
    var col1 = '';
    var col2 = '';
    var col3 = '';
    var message='';

    $scope.updateEmployeeDiscount = function() {
      $scope.employeesDiscounts[$scope.employeeFile.code] = $scope.descuento;
    }

    server.post('getBells').success(function(result){
      $scope.configuration = _.sortBy(result, 'countBell');
    });

    $scope.typeBells=function(type){

        if(type=='in') return 'Entrada';
        else return 'Salida';

    };

      $(document).on('change', '.btn-file :file', function () {
          var input = $(this), numFiles = input.get(0).files ? input.get(0).files.length : 1, label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [
              numFiles,
              label
          ]);
      });
      $(document).ready(function () {
          $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
              var input = $(this).parents('.input-group').find(':text'), log = numFiles > 1 ? numFiles + ' files selected' : label;
              if (input.length) {
                  input.val(log);
              } else {
                  if (log)
                      alert(log);
              }
          });
      });


    $scope.fileChanged = function() {
      $scope.namefile = ($scope.archivo.name);
      $scope.typefile = $scope.namefile.split('.')[1];
      if (($scope.typefile == 'xls')  || ($scope.typefile == 'xlsx') ){
        $scope.isProcessing = true;
        $scope.sheets = [];
        $scope.excelFile = $scope.archivo;
        XLSXReaderService.readFile($scope.excelFile, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
          $scope.sheets = xlsxData.sheets;
          $scope.isProcessing = false;
          countsheets = (Object.keys($scope.sheets).length);
          if(countsheets > 1){
            toastr.error('Error', 'El archivo Excel tiene mas de 1 Hoja de trabajo');
            $scope.procesar = true;
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
                    message = 'El Nombre correcto de las columnas es "codigo","fecha" y "hora" ';
                  }
                }else{
                  message = 'La cantidad de Columnas debe ser igual a 3, este archivo tiene: '+quantitycol+' Columnas';
                }
              });
              if ($scope.validated) {
                $scope.datafile = sheetData;
                $scope.procesar = false;
              }else{
                toastr.error('Error', message);
                $scope.procesar = true;
              }
            });
          }
        });
      }else
      {
        toastr.error('Error', 'El Tipo de archivo permitido es .xls o .xlsx');
        $scope.procesar = true;
      }
     // console.log($scope.datafile);
    };

    $scope.send = function(){

        if($scope.monthSearch){
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
                    $scope.employees = _(result).where({'code': row[0].Codigo});
                    $scope.employeesFile.push($scope.employees[0]);
                    $scope.employeesDiscounts[$scope.employees[0].code] = 0;
                });
            });

            // $scope.datafile = $scope.datafilset;
            $scope.databackup = $scope.datafile;
            $scope.initialheader = false;
            $scope.endheader = true;

            if(!dateval){
                toastr.error('Error', 'Archivo vacio, ninguna fecha corresponde al mes seleccionado: '+ $scope.monthSearch);
                $scope.endheader = false;
            }

            if($scope.configuration.length==0){
                toastr.error('error', 'No hay una configuracion de timbre establecida');
                $scope.endheader = false;
            }
        }else{
            toastr.error('error','Seleccione un Mes a Evaluar');
        }

        if($scope.datafile.length==0){
            toastr.error('error','Seleccione un archivo');
        }
    }

    /*  $scope.reloadPage = function() {
          $state.reload();
      }*/

    $scope.serachEmploye = function(){
      $scope.datarow = [];
      $scope.datarow_ =[];
      $scope.datarow_groupby = [];
      $scope.dataemployeedate={};
      $scope.resultdata = '';
      $scope.data =[];

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
            var resultRest = FactorysubtractHours.subtractHours(conf.hourBell,datos.Hora);
            if (parseInt(valorInicial) >= $scope.Math.abs(parseInt(resultRest)) ){
              valorInicial = resultRest;
              $scope.hourconf = conf.hourBell;
              $scope.hourconfType = conf.typeBell;
            }
          });
          var color =  FactoryArrears.Arrears($scope.hourconf,datos.Hora,$scope.hourconfType);
          $scope.datarow[i] = ({hour:$scope.hourconf, type: $scope.hourconfType, register: {hora:datos.Hora,color: color}});

          $scope.datarow_.push({Fecha: row[0].Fecha, Columns: [$scope.datarow[i]]});
          i++;
        });
        $scope.datarow_groupby =_.groupBy($scope.datarow_, 'Fecha');

      });

      $scope.resultdata = _.values($scope.datarow_groupby);
      $scope.total={};

      angular.forEach(($scope.resultdata),function(data){
        $scope.columnas = [];
        angular.forEach(data,function(dataColumns){
          var i=0;
          angular.forEach(dataColumns.Columns,function(dataRegister){
            angular.forEach(($scope.configuration),function(conf){
              if(dataRegister.hour == conf.hourBell ){
                $scope.columnas[i]={Hora: dataRegister.register.hora, Color:dataRegister.register.color, Type: dataRegister.type, HoraConf: dataRegister.hour};
              }else{
                if(!$scope.columnas[i]){
                  $scope.columnas[i]='';
                }
              }
              i++;
            });

          });
        });
        $scope.data.push({Fecha:data[0].Fecha, Columnas:$scope.columnas});
      });

      var i = 0;
      $scope.total = [];
      angular.forEach(($scope.configuration), function(conf) {
          var acumulador ='00:00';
          angular.forEach($scope.data, function (data) {
              angular.forEach(data.Columnas,function(col){
                  if(col.Color == 'red'  && col.HoraConf==conf.hourBell){
                      if(col.Type=='in'){
                          var resultado = FactorysubtractHours.subtractHours(conf.hourBell,col.Hora);
                      }else{
                          var resultado = FactorysubtractHours.subtractHours(col.Hora,conf.hourBell);
                      }

                      var resulacumulador = FactoryaddingHours.addingHours(acumulador,resultado);
                      acumulador = resulacumulador;
                  }
              });
          });
          if(acumulador.substr(0,2)=='00'){
              var tiempo = 'minutos';
          }else {
              var tiempo = 'horas';
          }
          $scope.total.push({Hora:conf.hourBell, Total: acumulador, Tiempo: tiempo});
          i++;
      });

      $scope.descuento = $scope.employeesDiscounts[$scope.employeeFile.code];

    };

     var validarDescuento = function(){
         if (parseFloat($scope.descuento) <= 0){
             toastr.warning('Debe Ingresar un Descuento');
             return false;
         }
         return true;
     };

      $scope.clean = function(){
        $scope.monthSearch = '';
        $scope.archivo = '';
        $scope.employeeFile=[];
        $scope.employeesFile =[];
        $scope.employeesDiscounts =[];
        $scope.descuento='';
        $scope.data=[];
        $scope.datafile=[];
        $scope.initialheader = true;
        $scope.endheader = false;
        $scope.namefile='';
        $scope.total=[];
        $scope.procesar = true;
      };


    $scope.save = function(){

        /*que si ta tiene el descuento no lo vuelva asignar preguntar al cliente*/

        if(true){

            angular.forEach(($scope.employeesFile), function(employee) {
              if($scope.employeesDiscounts[employee.code] > 0){
                employee.discounts = _(employee).has('discounts') ? employee.discounts : [];
                var assignedDiscounts = {'discount': {type:'Valor',code:'descuento00000', name:'Delay',value:parseFloat($scope.employeesDiscounts[employee.code])}};
                assignedDiscounts.date = moment().format();
                assignedDiscounts.frequency = 'once';
                employee.discounts.push(assignedDiscounts);
                var discounts = { 'discounts': angular.copy(employee.discounts) };
                //console.log(discounts);
                server.update('employee', discounts, employee._id).success(function (data) {
                });
              }
            });
            toastr.success("Se guardaron los descuentos asignados");
            $scope.clean();
        }else{
            toastr.warning("Debe Ingresar un descuento");
        }



    };
    handlePanelAction();
  }
]);
