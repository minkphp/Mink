var net = require('net');
var sys = require('sys');
var zombie = require('zombie');
var browser = null;
var pointers = [];
var buffer = "";

net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;

  stream.on('data', function (data) {
    buffer += data;
  });

  stream.on('end', function () {
    if (browser == null) {
      browser = new zombie.Browser();

      // Clean up old pointers
      pointers = [];
    }

    eval(buffer);
    buffer = "";
  });
}).listen(8124, '127.0.0.1');

console.log('Zombie.js server running at 127.0.0.1:8124');

