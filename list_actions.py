import json

with open('ton_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        print(action['type'])
        if action['type'] == 'SmartContractExec':
             # Often jetton transfers are part of smart contract execution details
             pass
