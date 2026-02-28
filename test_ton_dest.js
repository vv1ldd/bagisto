const dest = 'UQBTW0bj2lR0NQ2WdlRVyuLNNO55uokEo9hauUMWOArxAKVx';
fetch(`https://tonapi.io/v2/accounts/${dest}/jettons/history?limit=10`, {
    headers: { 'Accept': 'application/json' }
}).then(async res => {
    console.log("Status:", res.status);
    const text = await res.text();
    console.log("Raw Response:");
    console.log(text);
}).catch(console.error);
