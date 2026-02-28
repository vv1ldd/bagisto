import json

with open('ton_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        if action['type'] == 'JettonTransfer':
            print(json.dumps(action, indent=2))
