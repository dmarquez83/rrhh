'use strict';
angular.module('app').factory('progress', [
  'ngProgress',
  function (ngProgress) {
    var timer;
    ngProgress.color('#428bca');
    ngProgress.height('3px');
    return {
      start: function () {
        var me = this;
        me.reset();
        me.complete();
      },
      complete: function () {
        ngProgress.complete();
      },
      reset: function () {
        ngProgress.reset();
      }
    };
  }
]);