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
        $this->data = null;
        return $this;
    }

    public function sendDataInHeaders($useOpenHandler = null, $headerName = 'phpdebugbar', $maxHeaderLength = 4096, $maxTotalHeaderLength = 250000)
    {
        if ($useOpenHandler === null) {
            $useOpenHandler = self::$useOpenHandlerWhenSendingDataHeaders;
        }
        if ($useOpenHandler && $this->storage !== null) {
            $this->getData();
            $headerName .= '-id';
            $headers = array($headerName => $this->getCurrentRequestId());
        } else {
            $reflection = new \ReflectionMethod($this, "getDataAsHeaders");
            $params = [];
            foreach ($reflection->getParameters() as $param) {
                // get default
                $params[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
            $headers = $this->getDataAsHeaders($headerName ?: $params[0], $maxHeaderLength ?: $params[1], $maxTotalHeaderLength ?: $params[2]);
        }
        $this->getHttpDriver()->setHeaders($headers);
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
