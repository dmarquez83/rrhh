(function(undefined) {
    // Get angular app
    var app = angular.module("app");

    app.factory("FactoryArrears", ['$q', '$rootScope',
        function($q, $rootScope) {
            var service = function(data) {
                angular.extend(this, data);
            }

            service.Arrears = function(hourconf,hourcheck){

                var inicioMinutos = parseInt(hourconf.substr(3,2));
                var inicioHoras = parseInt(hourconf.substr(0,2));

                var finMinutos = parseInt(hourcheck.substr(3,2));
                var finHoras = parseInt(hourcheck.substr(0,2));

                var rangoInicio = inicioHoras + 2;
                var rangoFinal = inicioHoras - 2;

                if(finHoras >= rangoFinal && finHoras<=rangoInicio ){
                    return true;
                }else{
                    return false;
                }
            }


            return service;
        }
    ]);
}).call(this);
