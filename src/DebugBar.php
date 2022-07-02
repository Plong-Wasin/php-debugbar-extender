<?php

namespace DebugBarExtender;

use DebugBar\DebugBar as BaseDebugBar;
use DebugBar\DebugBarException;

class DebugBar extends BaseDebugBar
{
    /**
     * remove a collector by name
     * @param string $name
     * @return $this
     */
    public function removeCollector($name)
    {
        if (!isset($this->collectors[$name])) {
            throw new DebugBarException("Collector '$name' does not exist");
        }
        unset($this->collectors[$name]);
        return $this;
    }
    /**
     * remove all collectors
     * @return $this
     */
    public function removeCollectors()
    {
        $this->collectors = array();
        return $this;
    }
    /**
     * sort collectors by callback
     * @param callable $callback
     * @return $this
     */
    public function sortCollectors($callback)
    {
        if (!is_callable($callback)) {
            throw new DebugBarException('Callback is not callable');
        }
        uasort($this->collectors, $callback);
        return $this;
    }
    public function offsetUnset($key)
    {
        if (!isset($this->collectors[$key])) {
            throw new DebugBarException("Collector '$key' does not exist");
        }
        unset($this->collectors[$key]);
        return $this;
    }
}
