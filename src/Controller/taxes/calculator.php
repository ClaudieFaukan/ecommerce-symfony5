<?php

namespace App\Taxes;

class Calculator
{
    public function calculate(float $amount, float $taxRate): float
    {
        return $amount * (1 + $taxRate);
    }
}
