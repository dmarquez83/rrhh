'use strict';
angular.module('app').directive('maximize', function () {
  function link(scope, el, attrs, ngModelCtrl) {
    el.preventDefault();
    var t = $(this).closest('.panel');
    var n = $(t).find('.panel-body');
    var r = 40;
    if ($(n).length !== 0) {
      var i = $(t).offset().top;
      var s = $(n).offset().top;
      r = s - i;
    }
    if ($('body').hasClass('panel-expand') && $(t).hasClass('panel-expand')) {
      $('body, .panel').removeClass('panel-expand');
      $('.panel').removeAttr('style');
      $(n).removeAttr('style');
    } else {
      $('body').addClass('panel-expand');
      $(this).closest('.panel').addClass('panel-expand');
      if ($(n).length !== 0 && r != 40) {
        var o = 40;
        $(t).find(' > *').each(function () {
          var e = $(this).attr('class');
          if (e != 'panel-heading' && el != 'panel-body') {
            o += $(this).height() + 30;
          }
        });
        if (o != 40) {
          $(n).css('top', o + 'px');
        }
      }
    }
    $(window).trigger('resize');
  }
  return {
    restrict: 'A',
    require: 'ngModel',
    link: link
  };
});