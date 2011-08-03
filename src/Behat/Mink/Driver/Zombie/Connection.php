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
 * The connection to the node TCP server.
 *
 * @author      Pascal Cremer <b00gizm@gmail.com>
 */

class Connection
{
    /**
     * @var string
     */
    private $host = null;

    /**
     * @var integer
     */
    private $port = null;

    /**
     * Initializes connection instance.
     *
     * @param   string  $host   zombie.js server host
     * @param   integer $port   zombie.js server port
     */
    public function __construct($host = '127.0.0.1', $port = 8124)
    {
        $this->host = $host;
        $this->port = intval($port);
    }

    /**
     * Returns connection host.
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns connection port.
     *
     * @return  string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Evaluates a string of Javascript code and returns the response
     *
     * @param   string  $js  String of Javascript code
     *
     * @return  mixed   Server response
     *
     * @throws  \RuntimeException
     */
    public function evalJS($js)
    {
        return $this->socketSend($js);
    }

    /**
     * Evaluates a string of Javascript code which is converted
     * from / to JSON
     *
     * @param   string  $js  String of Javascript code
     *
     * @return  mixed   Server response
     *
     * @throws  \RuntimeException
     */
    public function evalJSON($js)
    {
        return json_decode($this->socketSend("stream.end(JSON.stringify({$js}));"));
    }

    /**
     * Sends a payload string of Javascript code to the Zombie Node.js server.
     *
     * @param   string  $js   String of Javascript code
     *
     * @return  string
     */
    private function socketSend($js)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (false === @socket_connect($socket, $this->host, $this->port)) {
            $errno = socket_last_error();
            throw new \RuntimeException(
              sprintf("Could not establish connection: %s (%s)",
              socket_strerror($errno),
              $errno)
            );
        }

        socket_write($socket, $js, strlen($js));
        socket_shutdown($socket, 1);

        $out = '';
        while($o = socket_read($socket, 2048)) {
            $out .= $o;
        }

        socket_close($socket);

        return $out;
    }
}
