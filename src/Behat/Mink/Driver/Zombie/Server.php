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
    private $jsPath = null;

    /**
     * @Behat\Mink\Driver\Zombie\Connection
     */
    private $conn = null;

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
     * @param   string   $jsPath     Path to server script
     * @param   integer  $threshold  Amount of microseconds for the process to wait
     */
    public function __construct($jsPath = null, $threshold = 1000000)
    {
        if (null === $jsPath) {
            $jsPath = __DIR__.'/server.js';
        }

        $this->jsPath = $jsPath;
        $this->threshold = ((int)$threshold > 0) ? (int)$threshold : 1000000;
    }

    /**
     * Descructor (Safely clean up)
     */
    public function __destruct()
    {
        $this->killProcess();
    }

    /**
     * Starts the server.
     * Spawns a process for a node server at 127.0.0.1 (localhost), port 8124
     *
     * @throws  \RuntimeException
     */
    public function start()
    {
        if (!file_exists($this->jsPath)) {
            throw new \RuntimeException(
                sprintf("The file at path '%s' could not be found", $this->jsPath)
            );
        }

        if ($this->isRunning()) {
            throw new \RuntimeException('The server appears to be already running.');
        }

        $this->spawnProcess();
        $this->conn = new Connection('127.0.0.1', '8124');
    }

    /**
     * Stops the server
     *
     * @throws  \RuntimeException
     */
    public function stop()
    {
        if (!$this->isRunning()) {
            throw new \RuntimeException('The server appears to be not running');
        }

        $this->killProcess();
        $this->conn = null;
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
     * Executes a string of Javascript code (and does not care for
     * the response)
     *
     * @param   string  $js  String of Javascript code
     *
     * @return  void
     */
    public function execJS($js)
    {
        $this->evaluate($js);
        return;
    }

    /**
     * Evaluates a string of Javascript code and returns the response
     *
     * @param   string  $js  String of Javascript code
     *
     * @return  mixed   Server response
     */
    public function evalJS($js)
    {
        return $this->evaluate($js);
    }

    /**
     * Evaluates a string of Javascript code which is converted
     * from / to JSON
     *
     * @param   string  $js  String of Javascript code
     *
     * @return  mixed   Server response
     */
    public function evalJSON($js)
    {
        return $this->evaluate($js, true);
    }

    /**
     * Getter server script path
     *
     * @return  Path to server script
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * Setter connection
     *
     * @param   Behat\Mink\Driver\Zombie\Connection  A connection object
     */
    public function setConnection(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Getter connection
     *
     * @return  Behat\Mink\Driver\Zombie\Connection  A connection object
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Setter threshold
     *
     * @param   integer  $threshold  amount of microseconds to wait
     */
    public function setThreshold($threshold)
    {
        if ((int)$threshold > 0) {
            $this->threshold = (int)$threshold;
        }
    }

    /**
     * Getter threshold
     *
     * @return  integer  Actual amount of microseconds to wait
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * The 'core' Javascript evaluate method. It is basically a wrapper around
     * Behat\Mink\Driver\Zombie\Connection::socketSend()
     *
     * @param   string    $js    String of Javascript code
     * @param   boolean   $json  Flag for conversion from/to JSON
     *
     * @return  mixed     Server response
     *
     * @throws  \RuntimeException
     */
    protected function evaluate($js, $json = false)
    {
        if (!$this->isRunning() || !$this->conn) {
            throw new \RuntimeException(
                'No active connection available (is the server running..?)'
            );
        }

        if ($json) {
            return json_decode($this->conn->socketSend("stream.end(JSON.stringify({$js}));"));
        }

        return $this->conn->socketSend($js);
    }

    /**
     * Spawns a new server process.
     *
     * This method borrows a lot of its code from Symfony's Process component
     * I first tried to use the component itself, but it does not have real
     * support for long running processes yet.
     *
     * Only supports *NIX systems for now.
     *
     * @throws  \RuntimeException
     */
    protected function spawnProcess()
    {
        if (!function_exists('proc_open')) {
            throw new \RuntimeException(
                'Unable to spawn a new process. (proc_open is not available on your PHP installation.)'
            );
        }

        $descriptors = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));
        $options = array('suppress_errors' => true, 'binary_pipes' => true, 'bypass_shell' => true);

        $this->process = proc_open(sprintf("env node %s", $this->jsPath), $descriptors, $pipes, NULL, NULL, $options);

        if (!is_resource($this->process)) {
            throw new \RuntimeException('Unable to spawn a new process.');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        $output = null;
        $time   = 0;
        while (false === strpos($output, 'Zombie.js server running') && $time < $this->threshold) {
            usleep(1000);
            $output = fread($pipes[1], 8192);
            $time  += 1000;
        }

        // If the process is not running, check STDERR for error messages
        // and throw exception
        $status = proc_get_status($this->process);
        if (0 == $status['running']) {
            $err = stream_get_contents($pipes[2]);
            $msg = 'Process is not running.';
            if (!empty($err)) {
                $msg .= sprintf(" (failed with error: %s", $err);
            }

            throw new \RuntimeException($msg);
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
    protected function killProcess()
    {
        if ($this->process) {
            $status = proc_get_status($this->process);
            posix_kill($status['pid'], 15);
            proc_close($this->process);
            $this->process = null;
        }
    }
}

