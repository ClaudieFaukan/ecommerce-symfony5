<?php

namespace App\Taxes;

class Calculator
{
    public function detect(float $number): bool
    {
        if ($number >= 100) {
            return true;
        }
        return false;
    }

    public function calculate(float $amount, float $taxRate): float
    {
        return $amount * (1 + $taxRate);
    }
}
