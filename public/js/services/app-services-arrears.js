(function(undefined) {
    // Get angular app
    var app = angular.module("app");

    app.factory("FactoryArrears", ['$q', '$rootScope',
        function($q, $rootScope) {
            var service = function(data) {
                angular.extend(this, data);
            }

            service.Arrears = function(hourconf,hourcheck, type){

                var inicioHourconfM = parseInt(hourconf.substr(3,2));
                var inicioHourconfH = parseInt(hourconf.substr(0,2));
                var inicioHourcheckM = parseInt(hourcheck.substr(3,2));
                var inicioHourcheckH = parseInt(hourcheck.substr(0,2));

                if(type == 'in'){
                    if(inicioHourcheckH >= inicioHourconfH && inicioHourcheckM > inicioHourconfM){
                        return 'red';
                    }else{
                        return 'white';
                    }
                }else{
                    if(type == 'out'){

                        if(inicioHourcheckH < inicioHourconfH ){
                            return 'red';
                        }else{
                            return 'white';
                        }
                    }
                }
            }
            return service;
        }
    ]);
}).call(this);
