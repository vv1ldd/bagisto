const http = require('http');

const req = http.request({
  hostname: 'localhost',
  path: '/checkout',
  method: 'GET',
  headers: {
    'Host': 'meanly.test'
  }
}, (res) => {
  let data = '';
  res.on('data', (chunk) => { data += chunk; });
  res.on('end', () => {
    const lines = data.split('\n');
    console.log("Total lines:", lines.length);
    for (let i = 1910; i <= 1925; i++) {
        if (lines[i]) console.log(`${i+1}: ${lines[i]}`);
    }
  });
});
req.on('error', (e) => {
  console.error("HTTP error:", e.message);
});
req.end();
