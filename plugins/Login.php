<?php
class Login implements EventManagerAwareInterface
{
    public function addListeners(EventManagerInterface $e)
    {
        $e->addListener('start', array($this, 'onLogin'));
    }

    public function onLogin(Bot $bot, ConfigInterface $config)
    {
        $user = $config->get('user');
        $password = $config->get('password');
        $server = $config->get('host');
        $hostname = $config->setDefault('localhost', 'BotServer');
        $chan = $config->get('channel');

        if (!$chan) {
            throw new ShouldClose('No channel', 1);
        }

        if (!$user) {
            throw new ShouldClose('No username', 1);
        }

        if (!$server) {
            throw new ShouldClose('No host', 1);
        }

        if ($password) {
            $bot->putMsg("PASS {$password}");
        }

        $bot->putMsg("NICK {$user}");

        $bot->putMsg("USER {$user} {$hostname} {$server} :{$user}");

        $bot->putMsg("JOIN #{$chan}");

        echo 'here';
    }
}
