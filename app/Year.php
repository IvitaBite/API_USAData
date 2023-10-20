<?php

declare(strict_types=1);

namespace App;

class Year
{
    private int $year;
    private int $population;

    public function __construct(int $year, int $population)
    {
        $this->year = $year;
        $this->population = $population;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }
}
