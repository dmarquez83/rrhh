'use strict';
angular.module('app').controller('InitialWizardCtrl', [
  '$scope',
  '$modalInstance',
  'documentValidate',
  '$http',
  '$timeout',
  'server',
  function ($scope, $modalInstance, documentValidate, $http, $timeout, server) {
    $scope.loading = 'hide';
    $scope.cheking = false;
    $scope.signatureIsValid = false;
    $scope.companyInfo = {
      specialContributor: false,
      accountingForced: false,
      identification: '',
      businessName: '',
      address: '',
      email: '',
      telephone: '',
      companyCode: '',
      emisionPoint: ''
    };
    $scope.companyInfo.specialContributor = false;
    $scope.companyInfo.accountingForced = false;
    $scope.secuencialDocuments = {
      invoice: {number: 1, secuencial: '000000001'},
      creditNote: {number: 1, secuencial: '000000001'},
      debitNote: {number: 1, secuencial: '000000001'},
      retention: {number: 1, secuencial: '000000001'},
      remisionGuide: {number: 1, secuencial: '000000001'},
    };

    $timeout(function () {
      $('#wizard').bwizard({
        nextBtnText: 'Siguiente',
        backBtnText: 'Anterior',
        clickableSteps: false,
        delay: 100,
        loop: true,
        validating: validateStepsOfWizard
      });
      $('#wizard > ul > li.previous').hide();
      $('#wizard > ul > li.next > a').text('Siguiente');
    }, 100);

    function validateStepsOfWizard(element, step) {
      if (step.index === 0 & step.nextIndex === 1){
        return validateFirstStep();
      }

      if (step.index === 1 & step.nextIndex === 2){
        $('#wizard > ul > li.next > a').text('Finalizar');
        return validateSecondStep();
      }

      if (step.index === 2 & step.nextIndex === 0){
        $modalInstance.close();
        return true;
      }
    };

    function validateFirstStep(){
      var result = true;
      _($scope.companyInfo).each(function(value, key){
        if(value === ''){
          result = false;
        }
      });
      if (!result) {
        toastr.warning('Complete los todos los campos');
      }
      return result;
    };

    function validateSecondStep(){
        if (!$scope.signatureIsValid) {
          var signature = {
            signature: $scope.companyInfo.electronicSignature,
            password: $scope.companyInfo.signaturePassword,
            companyName: $scope.companyInfo.businessName,
            companyIdentification: $scope.companyInfo.identification
          };
          $scope.cheking = true;
          $http.post('companyInfo/validateDigitalSignature', signature)
            .success(function(data){
              $scope.cheking = false;
              $scope.signatureIsValid = false;
              toastr[data.type](data.msg);
              if (data.type === 'success') {
                $scope.signatureIsValid = true;
                $("#wizard").bwizard("next");
              }
            });
        return false;
      } else {
        save();
        return true;
      }
    };

    function save(){
      $scope.companyInfo.documentSecuencial = angular.copy($scope.secuencialDocuments);
      server.save('companyInfo', $scope.companyInfo)
        .success(function(data){});
    };

    $scope.validateIdentification = function () {
      if ($scope.companyInfo.identification != '' && $scope.companyInfo.identification != undefined) {
        var isValidate = documentValidate.validateDocument($scope.companyInfo.identification);
        if (!isValidate) {
          $scope.companyInfo.identification = '';
          $('#identification').focus();
        }
      } else {
        $scope.companyInfo.identification = null;
      }
    };

    $scope.completeSecuencial = function(document){
      var number = $scope.secuencialDocuments[document].number;
      if (number!== '' && number !== null) {
        $scope.secuencialDocuments[document].secuencial = str_pad(number, 9, "0", 'STR_PAD_LEFT');
      }
    };

    $scope.close = function () {
      $modalInstance.close();
    };

    function str_pad(input, pad_length, pad_string, pad_type) {
      var half = '',
        pad_to_go;

      var str_pad_repeater = function(s, len) {
        var collect = '',
          i;

        while (collect.length < len) {
          collect += s;
        }
        collect = collect.substr(0, len);

        return collect;
      };

      input += '';
      pad_string = pad_string !== undefined ? pad_string : ' ';

      if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
        pad_type = 'STR_PAD_RIGHT';
      }
      if ((pad_to_go = pad_length - input.length) > 0) {
        if (pad_type === 'STR_PAD_LEFT') {
          input = str_pad_repeater(pad_string, pad_to_go) + input;
        } else if (pad_type === 'STR_PAD_RIGHT') {
          input = input + str_pad_repeater(pad_string, pad_to_go);
        } else if (pad_type === 'STR_PAD_BOTH') {
          half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
          input = half + input + half;
          input = input.substr(0, pad_length);
        }
      }

      return input;
    }

  }
]);
