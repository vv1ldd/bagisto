import json

with open('blockchain/data/ton_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        print(action['type'])
