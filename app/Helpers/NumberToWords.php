<?php
namespace App\Helpers;

class NumberToWords
{
    /**
     * Convert an amount to words.
     *
     * Supported currencies:
     *   - USD (default) : Dollar / Cent
     *   - BDT           : Taka / Poisha  (Lakh–Crore grouping)
     *
     * Backward compatible: NumberToWords::convert(12.50) still returns USD words.
     */
    public static function convert(float $amount, string $currency = 'USD'): string
    {
        return strtoupper($currency) === 'BDT'
            ? self::convertBdt($amount)
            : self::convertUsd($amount);
    }

    /**
     * USD : Dollar / Cent
     */
    public static function convertUsd(float $amount): string
    {
        [$dollars, $cents] = self::split($amount);

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

        return $amount < 0 ? 'Minus ' . $words : $words;
    }

    /**
     * BDT : Taka / Poisha
     */
    public static function convertBdt(float $amount): string
    {
        [$taka, $poisha] = self::split($amount);

        $words = self::numberToWordsBdt($taka) . ' Taka';

        if ($poisha > 0) {
            $words .= ' and ' . self::numberToWordsBdt($poisha) . ' Poisha';
        }

        $words .= ' Only';

        return $amount < 0 ? 'Minus ' . $words : $words;
    }

    /**
     * Split a float into [whole, fraction(2)] safely — handles values
     * coming from long rates such as 0.002923568 * qty.
     */
    private static function split(float $amount): array
    {
        $amount = round(abs($amount), 2);

        $whole    = (int) floor($amount);
        $fraction = (int) round(($amount - $whole) * 100);

        // Fix carry-over from rounding (e.g. 0.999 -> 1.00)
        if ($fraction === 100) {
            $whole++;
            $fraction = 0;
        }

        return [$whole, $fraction];
    }

    /**
     * International grouping: Billion / Million / Thousand
     */
    private static function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

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
        if ($number > 0) {
            if (! empty($words)) {
                $words[] = 'and';
            }
            $words[] = self::belowThousand($number);
        }

        return implode(' ', $words);
    }

    /**
     * Bangladeshi grouping: Crore / Lakh / Thousand
     */
    private static function numberToWordsBdt(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $words = [];

        if ($number >= 10000000) { // 1 Crore
            $words[]  = self::numberToWordsBdt(intdiv($number, 10000000)) . ' Crore';
            $number  %= 10000000;
        }
        if ($number >= 100000) { // 1 Lakh
            $words[]  = self::numberToWordsBdt(intdiv($number, 100000)) . ' Lakh';
            $number  %= 100000;
        }
        if ($number >= 1000) {
            $words[]  = self::numberToWordsBdt(intdiv($number, 1000)) . ' Thousand';
            $number  %= 1000;
        }
        if ($number > 0) {
            if (! empty($words)) {
                $words[] = 'and';
            }
            $words[] = self::belowThousand($number);
        }

        return implode(' ', $words);
    }

    /**
     * 1 .. 999 -> words (shared by USD & BDT)
     */
    private static function belowThousand(int $number): string
    {
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

        if ($number >= 100) {
            $words[]  = $ones[intdiv($number, 100)] . ' Hundred';
            $number  %= 100;
            if ($number > 0) {
                $words[] = 'and';
            }
        }

        if ($number > 0) {
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
