<?php
require_once('vendor/autoload.php');

use DebugBarExtender\DebugBar;
use DebugBarExtender\DataCollector\SpecificMeasureCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\RequestDataCollector;

$debugBar = new DebugBar();
$debugBar->addCollector(new SpecificMeasureCollector('my_collector'));
$debugBar->addCollector(new TimeDataCollector());
// $debugBar->addCollector(new RequestDataCollector());
for ($i = 0; $i < 100; $i++) {
    $debugBar->getCollector('my_collector')->startMeasure('my_measure');
    $debugBar->getCollector('my_collector')->stopMeasure('my_measure');
}
// reflection $debugBar sendDataInHeaders
$reflection = new \ReflectionMethod(new DebugBar, 'sendDataInHeaders');
// get param
$params = $reflection->getParameters();
// get default value
echo "<pre>";
foreach ($params as $param) {
    echo $param->getName() . ': ' . $param->getDefaultValue() . "\n";
}
var_dump($defaultValue);
$debugBar->sendDataInHeaders(null, 1, 1, 1);
$debugBarRender = $debugBar->getJavascriptRenderer();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?= $debugBarRender->renderHead() ?>
    <?= $debugBarRender->render() ?>
</body>

</html>