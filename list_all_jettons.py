import json

with open('dest_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        if action['type'] == 'JettonTransfer':
            jt = action['JettonTransfer']
            print(f"Amount: {jt.get('amount')}, Symbol: {jt.get('jetton', {}).get('symbol')}, Recipient: {jt.get('recipient', {}).get('address')}")
