var StopBullyingTester = require('./StopBullyingTester');

(async function () {
  let tester = new StopBullyingTester('firefox', 'http://stopbullying.hcpss.localhost');

  let minimalResult = await tester.submitBullyingReport({
    "field_school": "Long Reach High School",
    "field_description[0][value]": "a"
  });

  console.log(minimalResult);

  //await tester.validateReport(minimalResult);

  //await tester.validateAnonymizer();
})();
