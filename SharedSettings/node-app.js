// SharedSettings/node-app.js
const http = require('http');
const net = require('net');

// Ports come from the launcher's environment (the port decider's values);
// the literals are the defaults when the app is run some other way.
const REDIS_PORT = parseInt(process.env.WEBDEV_PORT_REDIS || '6379', 10);
const NODE_PORT = parseInt(process.env.WEBDEV_PORT_NODE || '3000', 10);

const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain; charset=utf-8' });

    // Open a direct low-level socket stream to your portable Redis engine
    const redisSocket = net.connect(REDIS_PORT, '127.0.0.1', () => {
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

// Bind to localhost on the resolved Node port
server.listen(NODE_PORT, '127.0.0.1', () => {
    console.log(`Node.js app running natively over port ${NODE_PORT}...`);
});
