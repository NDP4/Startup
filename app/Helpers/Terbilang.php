<?php

namespace App\Helpers;

class Terbilang
{
    private static $digit = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    private static $teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
    private static $level = ['', 'ribu', 'juta', 'milyar', 'triliun'];

    public static function make($number)
    {
        if (!is_numeric($number)) {
            return '';
        }

        // Convert to integer, removing decimal places
        $number = intval($number);

        if ($number === 0) {
            return 'nol';
        }

        if ($number < 0) {
            return 'minus ' . self::make(abs($number));
        }

        $result = '';
        $level = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $levelText = self::$level[$level];
                // Special case for thousands
                if ($level === 1 && $chunk === 1) {
                    $result = 'seribu ' . $result;
                } else {
                    $result = self::convertChunk($chunk) . ' ' . $levelText . ' ' . $result;
                }
            }
            $number = floor($number / 1000);
            $level++;
        }

        return trim(str_replace('  ', ' ', $result));
    }

    private static function convertChunk($number)
    {
        $result = '';

        // Handle hundreds
        $hundreds = floor($number / 100);
        if ($hundreds > 0) {
            if ($hundreds === 1) {
                $result .= 'seratus ';
            } else {
                $result .= self::$digit[$hundreds] . ' ratus ';
            }
        }

        // Handle tens and ones
        $remainder = $number % 100;
        if ($remainder > 0) {
            if ($remainder < 10) {
                $result .= self::$digit[$remainder];
            } elseif ($remainder < 20) {
                $result .= self::$teens[$remainder - 10];
            } else {
                $tens = floor($remainder / 10);
                $ones = $remainder % 10;
                if ($ones === 0) {
                    $result .= self::$digit[$tens] . ' puluh';
                } else {
                    $result .= self::$digit[$tens] . ' puluh ' . self::$digit[$ones];
                }
            }
        }

        return trim($result);
    }
}
