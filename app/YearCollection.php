<?php

declare(strict_types=1);

namespace App;

class YearCollection
{
    private array $years;

    public function __construct(array $years = [])
    {
        foreach ($years as $year) {
            $this->add($year);
        }
    }
    public function getYears(): array
    {
        return $this->years;
    }

    public  function add(Year $year) {
        $this->years [] =$year;
    }
}