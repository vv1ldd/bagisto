import json

with open('dest_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        if action['type'] == 'JettonTransfer':
            jt = action['JettonTransfer']
            if str(jt.get('amount')) == '459':
                 print(json.dumps(jt, indent=2))
