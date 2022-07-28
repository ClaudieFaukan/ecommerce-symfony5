<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/* It takes a value in cents, divides it by 100, formats it with a decimal separator and a thousand
separator, and then adds a currency symbol */

class AmountExtension extends AbstractExtension
{
    /**
     * `getFilters()` is a function that returns an array of TwigFilter objects.
     * 
     * The TwigFilter object takes two arguments:
     * 
     * 1. The name of the filter
     * 2. An array containing the class and the function to call when the filter is used.
     * 
     * In this case, the filter is called `amount` and the function to call is `amount()` in the
     * `AppBundle\Twig\AppExtension` class.
     * 
     * @return An array of TwigFilter objects.
     */
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }

    /**
     * It takes a value in cents, divides it by 100, formats it with a decimal separator and a thousand
     * separator, and then adds a currency symbol
     * 
     * @param value The value to format.
     * @param string symbol The currency symbol to use.
     * @param string decsep The decimal separator.
     * @param string thousandsep The character to use for thousands.
     * 
     * @return a string.
     */
    public function amount($value, string $symbol = '€', string $decsep = ',', string $thousandsep = ' ')
    {
        // 19229 => 192,29 €
        $finalValue = $value / 100;
        // 192.29
        $finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
        // 192,29

        return $finalValue . ' ' . $symbol;
    }
}
