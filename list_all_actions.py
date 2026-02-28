import json

with open('dest_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    print(f"Event ID: {event['event_id']}")
    for action in event.get('actions', []):
        print(f"  Action Type: {action['type']}")
