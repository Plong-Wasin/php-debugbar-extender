<?php

namespace DebugBarExtender\DataCollector;

use \DebugBar\DataCollector\TimeDataCollector;

class SpecificMeasureCollector extends TimeDataCollector
{
    protected $name;
    protected $triggers = array();
    public function __construct($name = 'specific_measure')
    {
        parent::__construct();
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }

    public function triggerAddMeasure(callable $callback)
    {
        $this->triggers['addMeasure'] = $callback;
    }

    public function getTriggers()
    {
        return $this->triggers;
    }

    public function getTrigger($name)
    {
        return isset($this->triggers[$name]) ? $this->triggers[$name] : null;
    }

    public function removeTrigger($name)
    {
        unset($this->triggers[$name]);
        return $this;
    }

    public function addMeasure($label, $start, $end, $params = array(), $collector = null)
    {
        if (isset($this->triggers['addMeasure'])) {
            $this->triggers['addMeasure']($label, $start, $end, $params, $collector);
        }
        $this->measures[] = array(
            'label' => $label,
            'start' => $start,
            'relative_start' => $start - $this->requestStartTime,
            'end' => $end,
            'relative_end' => $end - $this->requestEndTime,
            'duration' => $end - $start,
            'duration_str' => $this->getDataFormatter()->formatDuration($end - $start),
            'params' => $params,
            'collector' => $collector
        );
    }

    public function removeMeasure($index)
    {
        unset($this->measures[$index]);
        return $this;
    }

    public function collect()
    {
        $this->requestEndTime = microtime(true);
        foreach (array_keys($this->startedMeasures) as $name) {
            $this->stopMeasure($name);
        }

        usort($this->measures, function ($a, $b) {
            if ($a['start'] == $b['start']) {
                return 0;
            }
            return $a['start'] < $b['start'] ? -1 : 1;
        });
        $duration = array_sum(array_column($this->measures, 'duration'));
        $measures = array_values($this->measures);
        $relativeStart = 0;
        for ($i = 0; $i < count($measures); $i++) {
            if ($i == 0) {
                $measures[$i]['relative_start'] = $relativeStart;
            } else {
                $measures[$i]['relative_start'] = $measures[$i - 1]['relative_start'] + $measures[$i - 1]['duration'];
            }
        }
        return array(
            'count' => count($this->measures),
            'start' => $this->requestStartTime,
            'end' => $this->requestEndTime,
            'duration' => $duration,
            'duration_str' => $this->getDataFormatter()->formatDuration($duration),
            'measures' => $measures
        );
    }

    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            "$name" => array(
                "icon" => "tasks",
                "widget" => "PhpDebugBar.Widgets.TimelineWidget",
                "map" => "$name",
                "default" => "{}"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}
