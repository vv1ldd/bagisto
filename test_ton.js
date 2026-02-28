const address = 'UQDolrO5cIlq-RSkftro3HF3ZsvCI9qBHeiWgcgSOoLeCHB5';
// tonapi.io blocks direct scripts sometimes without headers, let's try
fetch(`https://tonapi.io/v2/accounts/${address}/events?limit=5`, {
    headers: { 'Accept': 'application/json' }
}).then(res => res.json()).then(console.dir).catch(console.error);
