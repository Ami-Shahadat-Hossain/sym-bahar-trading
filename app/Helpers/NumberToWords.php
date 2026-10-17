<?php
namespace App\Helpers;

class NumberToWords
{
    public static function convert(float $amount): string
    {
        $isNegative = $amount < 0;
        $amount     = abs($amount);

        // USD has 2 decimal places (cents)
        $amount  = round($amount, 2);
        $dollars = (int) floor($amount);
        $cents   = (int) round(($amount - $dollars) * 100);

        // Fix carry-over from rounding (e.g. 0.999 -> 1.00)
        if ($cents === 100) {
            $dollars++;
            $cents = 0;
        }

        $words = self::numberToWords($dollars)
            . ' '
            . ($dollars === 1 ? 'Dollar' : 'Dollars');

        if ($cents > 0) {
            $words .= ' and '
            . self::numberToWords($cents)
                . ' '
                . ($cents === 1 ? 'Cent' : 'Cents');
        }

        $words .= ' Only';

        return $isNegative ? 'Minus ' . $words : $words;
    }

    private static function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = [
            1  => 'One', 2        => 'Two', 3       => 'Three', 4    => 'Four',
            5  => 'Five', 6       => 'Six', 7       => 'Seven', 8    => 'Eight',
            9  => 'Nine', 10      => 'Ten', 11      => 'Eleven', 12  => 'Twelve',
            13 => 'Thirteen', 14  => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
            17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty',
            5 => 'Fifty', 6  => 'Sixty', 7  => 'Seventy',
            8 => 'Eighty', 9 => 'Ninety',
        ];

        $words = [];

        if ($number >= 1000000000) {
            $words[]  = self::numberToWords(intdiv($number, 1000000000)) . ' Billion';
            $number  %= 1000000000;
        }
        if ($number >= 1000000) {
            $words[]  = self::numberToWords(intdiv($number, 1000000)) . ' Million';
            $number  %= 1000000;
        }
        if ($number >= 1000) {
            $words[]  = self::numberToWords(intdiv($number, 1000)) . ' Thousand';
            $number  %= 1000;
        }
        if ($number >= 100) {
            $words[]  = $ones[intdiv($number, 100)] . ' Hundred';
            $number  %= 100;
        }
        if ($number > 0) {
            if (! empty($words)) {
                $words[] = 'and';
            }
            if ($number < 20) {
                $words[] = $ones[$number];
            } else {
                $words[] = $tens[intdiv($number, 10)]
                    . ($number % 10 ? ' ' . $ones[$number % 10] : '');
            }
        }

        return implode(' ', $words);
    }
}
