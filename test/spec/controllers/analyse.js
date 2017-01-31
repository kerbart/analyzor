'use strict';

describe('Controller: AnalyseCtrl', function () {

  // load the controller's module
  beforeEach(module('analysorApp'));

  var AnalyseCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    AnalyseCtrl = $controller('AnalyseCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(AnalyseCtrl.awesomeThings.length).toBe(3);
  });
});
