var BaseTester = require('./BaseTester');
// var sequential = require('promise-sequential');

class DrupalTester extends BaseTester {
  followLink(linkName) {
    return new Promise((resolve, reject) => {
      this.driver.findElement(this.by.linkText(linkName)).then(link => {
        link.click().then(() => { resolve(); });
      });
    });
  }

  validateTitle(title) {
    return this.driver.wait(this.until.titleIs(title), 1000);
  }

  validateElementContains(cssSelector, text) {
    return this.driver.findElement(this.by.css(cssSelector)).then(element => {
      return this.driver.wait(this.until.elementTextContains(element, text));
    });
  }

  fillForm(data) {
    var sequence = Promise.resolve();

    for (var name in data) {
      if (data.hasOwnProperty(name)) {
        let fieldName = name;
        let fieldData = data[fieldName];
        sequence = sequence.then(() => { return this.fillFormField(fieldName, fieldData); });
      }
    }

    return sequence;
  }

  fillFormField(name, value) {
    return new Promise((resolve, reject) => {
      this.driver.findElement(this.by.name(name)).then(field => {
        field.getAttribute("type").then(type => {
          if (type == 'tel' || type == 'text' || type == 'email' || type == 'textarea' || type == 'password') {
            field.getAttribute('class').then(classes => {
              if (classes.split(' ').includes('form-autocomplete')) {
                field.sendKeys(value).then(() => {
                  this.sleep(2).then(() => {
                    field.sendKeys(this.webdriver.Key.ARROW_DOWN).then(() => {
                      this.sleep(1).then(() => {
                        field.sendKeys(this.webdriver.Key.ENTER).then(() => {
                          resolve();
                        });
                      });
                    });
                  });
                });
              } else {
                field.sendKeys(value).then(() => { resolve(); });
              }
            })
          } else if (type == 'select-one') {
            field
              .findElement(this.by.xpath('//option[text() = "' + value + '"]'))
              .click()
              .then(() => { resolve(); });
          } else if (type == 'checkbox') {
            let isChecked = field.isSelected();
            if (isChecked != value) {
              field.click().then(() => {
                resolve();
              });
            } else {
              resolve();
            }
          } else {
            console.log(type);
            resolve();
          }
        });
      });
    });
  }

  clickButton(name) {
    return new Promise((resolve, reject) => {
      this.driver.findElement(this.by.name(name)).then(button => {
        button.click().then(() => { resolve(); });
      });
    });
  }

  navigateTo(url, title) {
    return new Promise(resolve => {
      this.driver.get(url).then(() => {
        this.driver.wait(this.until.titleIs(title), 1000).then(() => {
          resolve();
        });
      });
    });
  }

  logout() {
    return this.driver.get(this.baseUrl + '/user/logout');
  }

  login(username = 'admin', password = 'admin') {
    return this.driver.get(this.baseUrl + '/user/login').then(() => {
      return this.fillForm({
        "name": username,
        "pass": password
      });
    }).then(() => {
      return this.clickButton('op');
    }).then(() => {
      return this.driver.wait(this.until.titleContains(username), 1000);
    });
  }
}

module.exports = DrupalTester;
