import json
import requests

solditems = requests.get('https://www.trade-tariff.service.gov.uk/api/v2/commodities/0101210000') # (your url)
data = solditems.json()
with open('data.json', 'w') as f:
    json.dump(data, f)