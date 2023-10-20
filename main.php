<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use App\USADataAPI;

$apiUrl = "https://datausa.io/api/data?drilldowns=Nation&measures=Population";

$dataUSA = new USADataAPI();
$nation = "United States";

$yearCollection = $dataUSA->getNationData($nation);

foreach ($yearCollection->getYears() as $year) {
    echo "Year: " . $year->getYear() . "\n";
    echo "Population: " . $year->getPopulation() . "\n";
    echo "----------------------------------\n";
}

$estimatedPopulation = $dataUSA->estimatePopulationForToday($yearCollection);
echo "Estimated Population for Today: $estimatedPopulation\n";