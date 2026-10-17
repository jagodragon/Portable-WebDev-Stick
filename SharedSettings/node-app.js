// SharedSettings/node-app.js
const http = require('http');
const net = require('net');

const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain; charset=utf-8' });

    // Open a direct low-level socket stream to your portable Redis engine
    const redisSocket = net.connect(6379, '127.0.0.1', () => {
        // Send a raw RESP protocol command to push a tracking log into memory
        const timestamp = new Date().toISOString();
        redisSocket.write(`SET last_js_visit "${timestamp}"\r\n`);
    });

    redisSocket.on('data', (data) => {
        res.end(`🟩 Success! Node.js backend server successfully pushed timestamp to Redis!\n`);
        redisSocket.end();
    });

    redisSocket.on('error', (err) => {
        res.end(`🟨 Node.js is active, but couldn't stream to Redis: ${err.message}\n`);
    });
});

// Bind natively to all local interfaces on port 3000
server.listen(3000, '0.0.0.0', () => {
    console.log('Node.js app running natively over port 3000...');
});
