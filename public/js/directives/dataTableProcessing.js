'user strict';
angular.module('app').factory('DTLoadingTemplate', function () {
      return {
          html: '<div id="page-loader" class="fade in"><span class="spinner"></span></div>'
      }
});  