'use strict';

var paths =  {
    "e2e": "e2e",
    "tmp": ".tmp"
}


exports.config = {

  capabilities: {
    'browserName': 'chrome'
  },

  specs: [
    paths.e2e + 'roles.spec.js',
    paths.e2e + 'warehouses.spec.js',
    paths.e2e + 'customers.spec.js'
  ],

  jasmineNodeOpts: {
    showColors: true,
    defaultTimeoutInterval: 30000
  },

  onPrepare: function() {
    setTimeout(function() {
        browser.driver.executeScript(function() {
            return {
                width: window.screen.availWidth,
                height: window.screen.availHeight
            };
        }).then(function(result) {
            browser.driver.manage().window().setSize(result.width, result.height);
            browser.driver.manage().window().setPosition(0, 0);
        });
    });
  }
};
