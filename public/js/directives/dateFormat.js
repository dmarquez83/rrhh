'use strict';

angular
    .module('app')
    .directive('dateFormat', dateParser);

function dateParser() {

    return {
        link: link,
        restrict: 'A',
        require: 'ngModel'
    };

    function link(scope, element, attrs, ngModel) {
        var moment = window.moment,
            dateFormat = attrs.dateFormat,
            alternativeFormat = dateFormat.replace('DD', 'D').replace('MM', 'M'); //alternative do accept days and months with a single digit

        //use push to make sure our parser will be the last to run
        ngModel.$formatters.push(formatter);
        ngModel.$parsers.push(parser);

        function parser(viewValue) {
            var value = ngModel.$viewValue; //value that none of the parsers touched
            if(value!='') {
                var date = moment(value, [dateFormat, alternativeFormat], true);
                ngModel.$setValidity('date', date.isValid());
                return date.isValid() ? date._d : value;
            }

            return value;
        }

        function formatter(value) {
            if(value != undefined){
                var m = moment(value);
                var valid = m.isValid();
                if (valid) return m.format(dateFormat);
                else return value;
            } else {
                return '';
            }    
        }
    }
}