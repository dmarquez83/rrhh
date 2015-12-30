(function(undefined) {
    // Get angular app
    var app = angular.module("app");

    app.factory("FactorysubtractHours", ['$q', '$rootScope',
        function($q, $rootScope) {
            var service = function(data) {
                angular.extend(this, data);
            }

            service.subtractHours = function(inicio,fin){
                //var inicio = '10:00';
                //var fin = '08:05';

                var inicioMinutos = parseInt(inicio.substr(3,2));
                var inicioHoras = parseInt(inicio.substr(0,2));

                var finMinutos = parseInt(fin.substr(3,2));
                var finHoras = parseInt(fin.substr(0,2));

                var transcurridoMinutos = finMinutos - inicioMinutos;
                var transcurridoHoras = finHoras - inicioHoras;

                if (transcurridoMinutos < 0) {
                    transcurridoHoras--;
                    transcurridoMinutos = 60 + transcurridoMinutos;
                }

                var horas = transcurridoHoras.toString();
                var minutos = transcurridoMinutos.toString();

                if (horas.length < 2) {
                    horas = "0"+horas;
                }

                if (horas.length < 2) {
                    horas = "0"+horas;
                }

                var resultado = (horas+":"+minutos);

                return resultado;
            }


            return service;
        }
    ]);
}).call(this);
