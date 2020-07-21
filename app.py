import requests
from pprint import pprint

# These are our parameters that will change.
# bullying_site_url = 'http://stopbullying.hcpss.localhost'
# client_id         = '132441cf-9fdd-4e34-844d-3084ba3f0b7f'
# client_secret     = 'mmwoodc'
bullying_site_url = 'http://10.216.209.84:8091'
client_id         = '66657c2e-41b3-473f-80bf-a7a96196302c'
client_secret     = 'I4YWU2Nzg0Zjk4MmQ3NTMwZCIsImlhdC'

# The dev site is in dev mode so the site has hundreds of records. This is not
# realistice in production, so lets limit the number of documents we work with.
num_documents = 5

# Authenticate to the bullying site using an oath2 client credentials grant.
def getAccessToken(base_url, client_id, client_secret):
    data = {
        'grant_type': 'client_credentials',
        'client_id': client_id,
        'client_secret': client_secret,
        'scope': 'api'
    }

    response = requests.post(base_url + '/oauth/token', data = data).json()

    #print(response)

    return response['access_token']

# Helper function to find the relation in the list of relations with the give
# uuid.
def getRelation(relations, field):
    if field['data']:
        if isinstance(field['data'], list):
            related = []
            for data in field['data']:
                related.append(relations[data['id']])
            return related
        else:
            return relations[field['data']['id']]

# Get reports.
def getReports(token, num_documents):
    # These attributes are not native parts of the report, but are instead
    # related objects. We have to explicitly say we want them. Alternatively,
    # we could make a seperate http request to get these objects.
    includes = [
        'field_reporter',
        'field_reporter.field_role',
        'field_bullying_type',
        'field_locations',
        'field_offenders',
        'field_offenders.field_school',
        'field_targets',
        'field_targets.field_school',
        'field_witnesses'
    ]

    params = {
        'include': ','.join(includes),
        'page[limit]': num_documents,
    }

    headers = { "Authorization": "Bearer " + token }
    response = requests.get(bullying_site_url + "/jsonapi/report/bullying_harassment", params=params, headers=headers)

    print(response)

    return response.json()

token = getAccessToken(bullying_site_url, client_id, client_secret)
response = getReports(token, num_documents)

print(response)

report = response['data']

# We are going to be looking up the relationships by id, so let's make a
# dictionary out of them.
relations = {}
for relation in response['included']:
    relations[relation['id']] = relation

for report in response['data']:
    # Do what you want with the report data here.
    # pprint(report)

    # Much of the information contained in a report is inside related entities.
    # The information we need to lookup the relationships are contained in
    # report['relationships']. So we need to look them up by id.
    reporter = getRelation(relations, report['relationships']['field_reporter'])
    if reporter:
        # The reporter's school and role are themselves relationships.
        #reporter_school = getRelation(relations, reporter['relationships']['field_school'])
        reporter_role = getRelation(relations, reporter['relationships']['field_role'])

    # And so are all these.
    bullying_types = getRelation(relations, report['relationships']['field_bullying_type'])
    locations      = getRelation(relations, report['relationships']['field_locations'])
    offenders      = getRelation(relations, report['relationships']['field_offenders'])
    targets        = getRelation(relations, report['relationships']['field_targets'])
    witnesses      = getRelation(relations, report['relationships']['field_witnesses'])

    # Delete the report.
    #requests.delete(bullying_site_url + "/jsonapi/report/bullying_harassment/" + report['id'], headers={ "Authorization": "Bearer " + token })
