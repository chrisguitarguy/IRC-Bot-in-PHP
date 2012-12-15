<?php
/**
 * A simple IRC bot, written in PHP for fun.
 *
 * Copyright (c) 2012 Christopher Davis <http://christopherdavis.me>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category    IRC
 * @package     SimpleIRC
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2012 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

/**
 * The actual bot.
 *
 * @since  0.1
 */
class Bot
{
    const EOL = "\r\n";

    private $host;

    private $port;

    private $channel;

    private $debug;

    private $conf;

    private $events;

    private $sock = null;

    public function __construct(ConfigInterface $c, EventManagerInterface $e)
    {
        $this->host = $c->get('host');
        $this->port = $c->setDefault('port', '6667');
        $this->channel = $c->setDefault('channel', 'test');
        $this->debug = $c->setDefault('debug', false);

        $this->conf = $c;
        $this->events = $e;
    }

    public function addPlugin(EventManagerAwareInterface $p)
    {
        $p->addListeners($this->events);
    }

    public function run()
    {
        if (!($this->sock = $this->getConnection())) {
            die(1);
        }

        try {
            $this->events->trigger('start', $this, $this->conf);
        } catch (ShouldClose $e) {
            $this->log($e->getMessage());
            $this->close($e->getCode());
        }

        $sc = preg_quote($this->channel, '/');

        while (true) {
            $line = $this->getLine();

            if (!$line) {
                continue;
            }

            if ($this->debug) {
                $this->log($line);
            }

            try {
                $this->events->trigger('line', trim($line), $this, $this->conf);
            } catch (ShouldClose $e) {
                $this->log($e->getMessage());
                $this->close($e->getCode());
            }
        }

        try {
            $this->events->trigger('stop', $this, $this->conf);
        } catch (ShouldClose $e) {
            $this->log($e->getMessage());
            $this->close($e->getCode());
        }


        $this->close();
    }

    public function putMsg($msg)
    {
        fwrite($this->sock, $msg . self::EOL);
    }

    public function privMsg($msg, $to=null)
    {
        is_null($to) && $to = '#' . $this->channel;
        $this->putMsg("PRIVMSG {$to} :{$msg}");
    }

    public function log($msg, $stream=STDERR)
    {
        fwrite($stream, $msg . PHP_EOL);
    }

    private function getConnection()
    {
        $errno = $errstr = null;

        $s = fsockopen(
            gethostbyname($this->host),
            $this->port,
            $errno,
            $errstr
        );

        if (!$s) {
            $this->log("[ERROR Establishing Connection] {$errno} : {$errstr}");
        }

        return $s;
    }

    private function getLine()
    {
        $line = '';

        while (($c = fgetc($this->sock)) !== "\n")
            $line .= $c;

        return $line;
    }

    private function consume()
    {
        while ($line = $this->getLine()) {
            if ($this->debug) {
                $this->log($line);
            }
        }
    }

    private function close($code=0)
    {
        fclose($this->sock);
        die($code);
    }
}
