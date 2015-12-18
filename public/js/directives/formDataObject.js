'use strict';
angular.module('app').factory('formDataObject', function () {
  return function (data) {
    console.log(data);
    var fd = new FormData();
    angular.forEach(data, function (value, key) {
      fd.append(key, value);
    });
    return fd;
  };
});