'use strict';
angular.module('app').service('server', [
  '$http',
  function ($http) {
    return {
      save: function (resource, data) {
        return $http({
          method: 'POST',
          url: '/' + resource,
          data: data
        });
      },
      update: function (resource, data, id) {
        return $http({
          method: 'PUT',
          url: '/' + resource + '/' + id,
          data: data
        });
      },
      delete: function (resource, id) {
        return $http({
          method: 'DELETE',
          url: '/' + resource + '/' + id
        });
      },
      getAll: function (resource) {
        return $http({
          method: 'GET',
          url: '/' + resource
        });
      },
      getByParameter: function (resource, parameter, data) {
        return $http({
          method: 'GET',
          url: '/' + resource + '/' + data + '?parameter=' + parameter
        });
      },
      getColumnsByParameters: function (resource, parameters, columns) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/getColumnsByParameters',
          data: {
            parameters: parameters,
            columns: columns
          }
        });
      },
      getByParameterPost: function (resource, parameters) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/getByParameterPost',
          data: parameters
        });
      },
      getAllByParameterPostForConsolidation: function (resource, parameters) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/getAllByParameterPostForConsolidation',
          data: parameters
        });
      },
      forSelectize: function (resource, parameters) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/forSelectize',
          data: parameters
        });
      },
      getByParameters: function (resource, parameters) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/searchByParameters',
          data: parameters
        });
      },
      getSpecificData: function (resource, attributes) {
        return $http({
          method: 'POST',
          url: '/' + resource + '/specificData',
          data: attributes
        });
      },
      post: function (resource, data) {
        return $http({
          method: 'POST',
          url: '/' + resource,
          data: data
        });
      },
      get: function (resource, parameter) {
        return $http({
          method: 'GET',
          url: '/' + resource + '/?parameter=' + parameter
        });
      }
    };
  }
]);
