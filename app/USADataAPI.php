<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;

class USADataAPI
{
    private string $apiUrl;
    private Client $http;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
        $this->http = new Client();
    }

    public function getNationData(string $apiUrl): ?YearCollection
    {
        try {
            $response = $this->http->get($apiUrl);
        } catch (GuzzleException $e) {
            echo "GuzzleException: " . $e->getMessage() . "\n";
            return null;
        }

        $data = json_decode($response->getBody()->__toStrimg(), true);

        if ($data === false) {
            echo "Error: JSON decoding failed. The response body could not be decoded as JSON.";
            return null;
        }

        if ($data === null) {
            echo "Error: JSON decoding returned null. The response body may not be valid JSON.";
            return null;
        }

        $yearCollection= new YearCollection();

        foreach ($data as $yearData) {
            $year = new Year(
                $yearData["Year"],
                $yearData["Population"],
            );
            $yearCollection->add($year);
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

    private function getPopulationForYear(array $populationData, int $year)
    {
        foreach ($populationData as $data) {
            if ($data->getYear() === $year) {
                return $data->getPopulation();
            }
        }
        return 0;
    }
}