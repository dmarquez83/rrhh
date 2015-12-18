'use strict';
angular.module('app').service('openGeneralJournalEntry', [
  '$modal', 'server',
  function ($modal, server) {

    return {
      openFromNumber : function (generalJournalNumber) {
        var parameter = {parameter: 'number', 'value': generalJournalNumber};
        server.getByParameterPost('generalJournal', parameter).success(function(data){
          $modal.open({
          templateUrl: '../../views/accounting/generalJournal/details.html',
          controller: 'DetailsGeneralJournalEntryCtrl',
          windowClass: 'xlg',
            resolve: {
              selectedJournalEntry: function () {
                return data[0];
              }
            }
          });
        });

        

      }
    };
  }
]);
