// WebDev/www/node-app.js
const http = require('http');

const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('🟩 Success! Modern Node.js v24.20.0 LTS Backend Server is Alive and Functional!\n');
});

// Bind natively to your internal loopback port
server.listen(3000, '127.0.0.1', () => {
    console.log('Node.js app running silently over port 3000...');
});
