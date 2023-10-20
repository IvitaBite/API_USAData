<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;

class USADataAPI
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client();
    }

    public function getNationData(string $nation): ?YearCollection
    {
        try {
            $response = $this->http->get("https://datausa.io/api/data?drilldowns=Nation&measures=Population");
        } catch (GuzzleException $e) {
            echo "GuzzleException: " . $e->getMessage() . "\n";
            return null;
        }

        $data = json_decode($response->getBody()->__toString(), true);

        if (!is_array($data)) {
            echo "Error: JSON decoding failed. The response body could not be decoded as JSON.";
            return null;
        }

        $yearCollection = new YearCollection();

        if (isset($data["data"])) {
            foreach ($data["data"] as $entry) {
                if ($entry["Nation"] === $nation) {
                    $year = (int)$entry["Year"]; // Cast to int
                    $population = (int)$entry["Population"]; // Cast to int

                    $yearObject = new Year($year, $population);
                    $yearCollection->add($yearObject);
                }
            }
        }
        return $yearCollection;
    }

    public function estimatePopulationForToday(YearCollection $yearCollection)
    {
        $currentYear = Carbon::now()->year;
        $populationData = $yearCollection->getYears();

        usort($populationData, function ($a, $b) {
            return $a->getYear() - $b->getYear();
        });

        $population = 0;

        foreach ($populationData as $year) {
            if ($year->getYear() === $currentYear) {
                return $year->getPopulation();
            } elseif ($year->getYear() < $currentYear) {
                $previousYear = $year->getYear() - 1;
                $nextYearPopulation = $year->getpopulation();
                $previousYearPopulation = $this->getPopulationForYear($populationData, $previousYear);
                $yearsDifference = $year->getYear() - $previousYear;
                $estimatedPopulation = $previousYearPopulation + (($nextYearPopulation - $previousYearPopulation) / $yearsDifference);
                return $estimatedPopulation;
            }
        }
        return $population;
    }

    private function getPopulationForYear(array $populationData, int $year): int
    {
        foreach ($populationData as $data) {
            if ($data->getYear() === $year) {
                return $data->getPopulation();
            }
        }
        return 0;
    }
}