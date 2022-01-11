<?php

define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));

$path   = ROOT_PATH . '/app/models/Abstracts/';
$models = glob($path . '*.php');

$template = file_get_contents(__DIR__ . '/ModelTemplate.php');
$eventsTemplate = file_get_contents(__DIR__ . '/ModelEventsTemplate.php');

$recreate = true;

foreach ($models as $model)
{
    $name            = str_replace('.php', '', str_replace($path . 'Abstract', '', $model));
    $modelFile       = $path . '../' . $name . '.php';
    $modelEventsFile = $path . '../Events/' . $name . 'Events.php';
    
    if (!file_exists($modelFile) || $recreate)
    {
        $createdModel = str_replace('ModelTemplate', $name, $template);
        file_put_contents($modelFile, $createdModel);
        
        echo "Model file created: $modelFile\n";
    }
    
    if (!file_exists($modelEventsFile) || $recreate)
    {
        $createdModel = str_replace('ModelTemplate', $name, $eventsTemplate);
        file_put_contents($modelEventsFile, $createdModel);
        
        echo "Model events file created: $modelEventsFile\n";
    }
}
