const webdriver = require('selenium-webdriver');

class BaseTester {
  constructor(browser, baseUrl) {
    this.driver = new webdriver.Builder()
      .forBrowser(browser)
      .usingServer('http://' + browser + ':4444/wd/hub')
      .build();

    this.by = webdriver.By;
    this.until = webdriver.until;
    this.baseUrl = baseUrl;
    this.webdriver = webdriver;
  }

  sleep(seconds) {
    return new Promise(resolve => setTimeout(resolve, seconds * 1000));
  }
}

module.exports = BaseTester;
