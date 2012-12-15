#!/usr/bin/php
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

// @todo actually handle system signals?

// simple autoloader
spl_autoload_register(function($cls) {
    if (file_exists(__DIR__ . "/src/{$cls}.php")) {
        require __DIR__ . "/src/{$cls}.php";
        return true;
    } elseif (file_exists(__DIR__ . "/plugins/{$cls}.php")) {
        require __DIR__ . "/plugins/{$cls}.php";
        return true;
    }

    return false;
});

// create the bot
$bot = new Bot(new Config(array(
    'user'      => 'bottest', // the bots username
    'host'      => 'localhost', // the host to which to connect
    'debug'     => true, // in debug mode?
    'channel'   => 'test', // the channel which the bot will join
)), new EventManager());

// Add some plugins (event listeners)
$bot->addPlugin(new Login()); // logs in with the creds from Config
$bot->addPlugin(new Ponger()); // respons to pings
$bot->addPlugin(new Commander()); // responds to comments like .hello

// run the thing.
$bot->run();
