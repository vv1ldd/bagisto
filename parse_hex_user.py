import json

with open('hex_user_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        if action['type'] == 'JettonTransfer':
            jt = action['JettonTransfer']
            print("JettonTransfer Found!")
            print("  Sender:", jt.get('sender', {}).get('address'))
            print("  Recipient:", jt.get('recipient', {}).get('address'))
            print("  Amount:", jt.get('amount'))
            print("  Symbol:", jt.get('jetton', {}).get('symbol'))
            print("  Event ID:", event.get('event_id'))
