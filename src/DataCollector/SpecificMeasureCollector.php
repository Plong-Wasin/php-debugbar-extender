<?php

namespace DebugBarExtender\DataCollector;

use \DebugBar\DataCollector\TimeDataCollector;
use \DebugBar\DebugBar;

class SpecificMeasureCollector extends TimeDataCollector
{
    protected $name;
    public function __construct($name = 'specific_measure', DebugBar $debugBar = null)
    {
        parent::__construct();
        $this->debugBar = $debugBar;
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function addMeasure($label, $start, $end, $params = array(), $collectorName = null)
    {
        $relative_start = 0;
        if ($this->measures) {
            $relative_start = $this->measures[count($this->measures) - 1]['relative_start'] + $this->measures[count($this->measures) - 1]['duration'];
        }
        if ($this->debugBar) {
            foreach ($this->debugBar->getCollectors() as $collector) {
                if ($collector instanceof TimeDataCollector && !$collector instanceof SpecificMeasureCollector) {
                    $collector->addMeasure($label, $start, $end, $params, $collectorName);
                }
            }
        }
        $this->measures[] = array(
            'label' => $label,
            'start' => $start,
            'relative_start' => $relative_start,
            'end' => $end,
            'relative_end' => $end - $this->requestEndTime,
            'duration' => $end - $start,
            'duration_str' => $this->getDataFormatter()->formatDuration($end - $start),
            'params' => $params,
            'collector' => $collectorName
        );
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
        return array(
            'count' => count($this->measures),
            'start' => $this->requestStartTime,
            'end' => $this->requestEndTime,
            'duration' => $duration,
            'duration_str' => $this->getDataFormatter()->formatDuration($duration),
            'measures' => array_values($this->measures)
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
