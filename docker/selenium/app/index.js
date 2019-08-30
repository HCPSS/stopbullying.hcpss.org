var StopBullyingTester = require('./StopBullyingTester');

(async function () {
  let tester = new StopBullyingTester('firefox', 'http://stopbullying.hcpss.localhost');

  let minimalResult = await tester.submitBullyingReport({
    "field_description[0][value]": "a"
  });

  await tester.validateReport(minimalResult);

  await tester.validateAnonymizer();
})();
