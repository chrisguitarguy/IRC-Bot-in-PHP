<?php
class Ponger implements EventManagerAwareInterface
{
    public function addListeners(EventManagerInterface $e)
    {
        $e->addListener('list', array($this, 'maybePong'));
    }

    public function maybePong($line, Bot $bot)
    {
        if (preg_match('/PING\s+:(.*)$/ui', $line, $matches)) {
            $box->putMsg("PONG :{$matches[1]}");
        }
    }
}
