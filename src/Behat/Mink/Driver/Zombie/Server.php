<?php

namespace Behat\Mink\Driver\Zombie;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The Zombie.js TCP server.
 *
 * @author      Pascal Cremer <b00gizm@gmail.com>
 */

class Server
{
    /**
     * @var     string
     */
    private $host;

    /**
     * @var     integer
     */
    private $port;

    /**
     * @var     string
     */
    private $nodeBin = null;

    /**
     * @var     string
     */
    private $serverScript = null;

    /**
     * @var     integer
     */
    private $threshold;

    /**
     * @var     resource
     */
    private $process = null;

    /**
     * Constructor
     *
     * @param   string  $host           zombie.js server host
     * @param   integer $port           zombie.js server port
     * @param   string  $nodeBin        node.js binary path
     * @param   string  $serverScript   zombie.js server script
     * @param   integer $threshold      amount of microseconds for the process to wait
     */
    public function __construct($host = '127.0.0.1', $port = 8124,
                                $nodeBin = null, $serverScript = null, $threshold = 20000000)
    {
        if (null === $nodeBin) {
            $nodeBin = 'node';
        }
        if (null === $serverScript) {
            $serverScript = $this->getServerScript();
        }

        $this->host         = $host;
        $this->port         = intval($port);
        $this->nodeBin      = $nodeBin;
        $this->serverScript = $serverScript;
        $this->threshold    = intval($threshold);
    }

    /**
     * Starts the server.
     * Spawns a process for a node server at specified port & host
     *
     * @throws  \RuntimeException
     */
    public function start()
    {
        if ($this->isRunning()) {
            throw new \RuntimeException('The server appears to be already running.');
        }

        $this->spawnZombieServer();
    }

    /**
     * Stops the server
     *
     * @throws  \RuntimeException
     */
    public function stop()
    {
        if ($this->isRunning()) {
            $this->killZombieServer();
        }
    }

    /**
     * Restarts the server
     */
    public function restart()
    {
        if ($this->isRunning()) {
            $this->stop();
        }

        $this->start();
    }

    /**
     * Checks if the server process is still alive
     */
    public function isRunning()
    {
        if (!$this->process) {
            return false;
        }

        $status = proc_get_status($this->process);

        return (1 == $status['running']);
    }

    /**
     * Spawns a new Zombie.js server process.
     *
     * This method borrows a lot of its code from Symfony's Process component
     * I first tried to use the component itself, but it does not have real
     * support for long running processes yet.
     *
     * Only supports *NIX systems for now.
     *
     * @throws  \RuntimeException
     */
    private function spawnZombieServer()
    {
        if (!function_exists('proc_open')) {
            throw new \RuntimeException(
                'Unable to spawn a new process. (proc_open is not available on your PHP installation.)'
            );
        }

        $descriptors = array(
            array('pipe', 'r'),
            array('pipe', 'w'),
            array('pipe', 'w')
        );
        $pipes = array();
        $options = array(
            'suppress_errors' => true,
            'binary_pipes'    => true,
            'bypass_shell'    => true
        );

        $serverPath   = tempnam(sys_get_temp_dir(), 'mink_zombie_server');
        $serverScript = strtr($this->serverScript, array(
            '%host%' => $this->host,
            '%port%' => $this->port
        ));
        file_put_contents($serverPath, $serverScript);

        // run server
        $this->process = proc_open(
            sprintf('exec %s %s', $this->nodeBin, $serverPath), $descriptors, $pipes, null, null, $options
        );

        if (!is_resource($this->process)) {
            throw new \RuntimeException('Unable to spawn a new process.');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        $output = '';
        $error  = '';
        $time   = $this->threshold;
        while (false === strpos($output, 'Mink::ZombieDriver started') && $time > 0) {
            usleep(1000);
            $time  -= 1000;

            $output .= fread($pipes[1], 8192);
            $error  .= fread($pipes[2], 8192);

            if ($error && '' !== trim($error)) {
                usleep(10000);
                $error .= fread($pipes[2], 8192);

                // skip "sys" module warnings
                if ('The "sys" module is now called "util". It should have a similar interface.' !== trim($error)) {
                    $this->killZombieServer();
                    throw new \RuntimeException(sprintf(
                        "Can not instantiate server (%s %s):\n%s", $this->nodeBin, $serverPath, $error
                    ));
                }
            }
        }

        // Close pipes to avoid deadlocks on proc_close
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
    }

    /**
     * Kills a running server process.
     *
     * Only supports *NIX systems for now.
     */
    private function killZombieServer()
    {
        if ($this->process) {
            $status = proc_get_status($this->process);
            posix_kill($status['pid'], 15);
            proc_close($this->process);
        }
        $this->process = null;
    }

    /**
     * Returns default zombie.js server script.
     *
     * @return  string
     */
    private function getServerScript()
    {
        return <<<'JS'
var net      = require('net');
var zombie   = require('zombie');
var browser  = null;
var pointers = [];
var buffer   = "";

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
}).listen(%port%, '%host%', function() { console.log('Mink::ZombieDriver started'); });

console.log('Zombie.js server running at %host%:%port%');
JS;
    }
}
