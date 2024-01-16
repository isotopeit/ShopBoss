<?php

use Isotope\ShopBoss\Models\Setting;

if (!function_exists('settings')) {
    function settings() {
        $settings = Setting::first();

        return $settings;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($value, $format = true) {
        if (!$format) {
            return $value;
        }
        // $settings           = Setting::first();
        // $position           = $settings->default_currency_position ?? '1';
        // $symbol             = $settings->currency->symbol ?? "$" ;
        // $decimal_separator  = $settings->currency->decimal_separator ?? '2';
        // $thousand_separator = $settings->currency->thousand_separator ?? '3';

        // if ($position == 'prefix') {
        //     $formatted_value = $symbol . number_format((float) $value, 2, $decimal_separator, $thousand_separator);
        // } else {
        //     $formatted_value = number_format((float) $value, 2, $decimal_separator, $thousand_separator) . $symbol;
        // }
        return number_format($value, 2). settings()->currency;
    }
}

if (!function_exists('make_reference_id')) {
    function make_reference_id($prefix, $number) {
        $padded_text = $prefix . '-' . str_pad($number, 5, 0, STR_PAD_LEFT);

        return $padded_text;
    }
}

if (!function_exists('array_merge_numeric_values')) {
    function array_merge_numeric_values() {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}
