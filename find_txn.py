import json

with open('dest_events.json') as f:
    data = json.load(f)

for event in data.get('events', []):
    for action in event.get('actions', []):
        if action['type'] == 'JettonTransfer':
            jt = action['JettonTransfer']
            # USDT on TON has 6 decimals, so 0.000459 is 459 subunits
            if str(jt.get('amount')) == '459':
                print(json.dumps(action, indent=2))
