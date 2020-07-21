var DrupalTester = require('./DrupalTester');
const assert = require('assert');

class StopBullyingTester extends DrupalTester {

  validateReport(trackingNumber) {
    return this.navigateToReport(trackingNumber).then(() => {
      return this.validateTitle(trackingNumber + ' | Stop Bullying');
    }).then(() => {
      return this.logout();
    });
  }

  navigateToReport(trackingNumber) {
    return this.login().then(() => {
      return this.navigateTo(this.baseUrl + '/admin/content/bullying_harassment', 'Report Overview | Stop Bullying');
    }).then(() => {
      return this.driver.findElement(this.by.xpath('//a[text() = "' + trackingNumber + '"]'));
    }).then(anchor => {
      return anchor.click();
    });
  }

  validateAnonymizer() {
    return this.submitBullyingReport({
      "field_reporter[0][subform][field_name][0][value]": "Matt Barger",
      "field_reporter[0][subform][field_phone][0][value]": "410-555-1234",
      "field_reporter[0][subform][field_email][0][value]": "brendan@example.com",
      "field_reporter[0][subform][field_school][0][target_id]": "Atholton Elementary",
      "field_reporter[0][subform][field_role]": "Bystander",
      "field_reporter[0][subform][anonymous]": true,
      "field_description[0][value]": "a"
    })
    .then(trackingNumber => {
      return this.validateReport(trackingNumber).then(() => {
        return this.navigateToReport(trackingNumber).then(() => {
            return this.driver
              .findElement(this.by.css('.field--name-field-reporter .paragraph'))
              .getText()
              .then(reporter => {
                assert.equal(reporter, 'ddd');
              });
          });
        });
    });
  }

  submitBullyingReport(values) {
    return this.navigateTo(this.baseUrl, 'Bullying Prevention | Stop Bullying | Howard County Public Schools').then(() => {
      return this.followLink('Report Bullying, Cyberbullying, Harassment, or Intimidation');
    })
    .then(() => { return this.fillForm(values); })
    .then(() => {
      // Try not to trigger robot detection.
      return this.sleep(10);
    })
    .then(() => { return this.clickButton('op'); })
    .then(() => {
      return this.validateElementContains('.messages-status', 'Created Report');
    })
    .then(() => {
      return this.driver.findElement(this.by.css('.messages-status')).getText();
    })
    .then(text => {
      return text.match(/Created Report (.+)\./)[1];
    });
  }
}

module.exports = StopBullyingTester;
