import urllib.request
import json
url = 'https://tonapi.io/v2/accounts/UQBTW0bj2lR0NQ2WdlRVyuLNNO55uokEo9hauUMWOArxAKVx/jettons/history?limit=10'
try:
    req = urllib.request.Request(url, headers={'Accept': 'application/json'})
    with urllib.request.urlopen(req) as response:
        print(response.read().decode('utf-8'))
except Exception as e:
    print('Error:', e)
