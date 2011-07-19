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


    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = (int)$port;
    }

    /**
     * Sends a payload string of Javascript code to the Zombie Node.js server.
     *
     * @param   string  $js   String of Javascript code
     *
     * @return  string
     */
    public function socketSend($js)
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

    /**
     * Wrapper around Connection::socketSend().
     * Automatically en- and decodes JSON for in- and output.
     *
     * @param   string  $js   String of Javascript code
     *
     * @return  mixed
     */
    public function socketJSON($js)
    {
        return json_decode($this->socketSend("stream.end(JSON.stringify({$js}))"));
    }

    /**
     * Setter Host
     *
     * @param   string  $host  A host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Getter Host
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Setter Port
     *
     * @param   integer  $port  A port
     */
    public function setPort($port)
    {
        $this->port = (int)$port;
    }

    /**
     * Getter Port
     *
     * @return  integer
     */
    public function getPort()
    {
        return $this->port;
    }
}

