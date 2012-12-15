<?php
class Commander implements EventManagerAwareInterface
{
    public function addListeners(EventManagerInterface $e)
    {
        $e->addListener('line', array($this, 'handleLine'));
    }

    public function handleLine($line, Bot $bot)
    {
        list($_, $cmd, $msg) = explode(':', $line, 3);

        if (preg_match('/\.([a-z]+)(\s+|$)/u', trim($msg), $m)) {
            $meth = "do_{$m[1]}";

            if (method_exists($this, $meth)) {
                $this->$meth($bot, $msg);
            }
        }
    }

    public function do_hello($bot)
    {
        $bot->privMsg('Hello');
    }
}
