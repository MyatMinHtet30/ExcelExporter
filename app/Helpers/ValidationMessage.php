<?php

namespace App\Helpers;

class ValidationMessage
{
    public static string $required   = 'is required.';
    public static string $string     = 'must be text without numbers or special characters.';
    public static string $date       = 'Please enter a valid date for :attribute.';
    public static string $maxLength  = 'may not exceed :max characters.';
    public static string $numeric    = 'must be a number.';
    public static string $minValue   = 'must be at least :min.';
    public static string $array      = 'must be a list.';


}