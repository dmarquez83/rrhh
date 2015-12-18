'use strict';
angular.module('app').factory('customerValidator', function () {
  return {
    indentification: function (identification, existingCustomers) {
      var exist = _(existingCustomers).findWhere({ identification: identification });
      if (exist) {
        toastr.warning('Este n&uacute;mero de Cedula/Ruc ya ha sido Ingresado');
        return true;
      }
      return false;
    },
    email: function (emails, existingCustomers) {
      _(emails).each(function (email) {
        var exist = _(existingCustomers).findWhere({ email: email.toLowerCase() });
        if (exist) {
          toastr.warning('Este correo electronico ya a sido Ingresado');
          return true;
        }
      });
      return false;
    }
  };
});