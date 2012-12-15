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
 * Deal with dispatching events and such.
 *
 * @since  0.1
 */
class EventManager implements EventManagerInterface
{
    private $events = array();

    public function trigger($name)
    {
        if (!isset($this->events[$name]))
            return false;

        if (func_num_args() > 1) {
            $args = array_slice(func_get_args(), 1);
        } else {
            $args = array();
        }

        ksort($this->events[$name], SORT_NUMERIC);
        reset($this->events[$name]);

        do {
            foreach ($this->events[$name] as $func) {
                if(!is_callable($func))
                    continue;

                call_user_func_array($func, $args);
            }
        } while (next($this->events[$name]) !== false);
    }

    public function addListener($event, $callable, $prio=10)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = array();
        }

        if (!isset($this->events[$event][$prio])) {
            $this->events[$event][$prio] = array();
        }

        $this->events[$event][$prio] = $callable;
    }
}
