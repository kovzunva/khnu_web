<?php
namespace App\Services;
use Carbon\Carbon;

use Illuminate\Http\Request;

class DateService
{
    public static function formatDateToInt($dateText, $isN_E){
        if ($dateText !== '') {
            $dateParts = explode('.', $dateText);

            if (count($dateParts) === 3) {
                $formattedDate = $dateParts[2] . $dateParts[1] . $dateParts[0];
                $numericDate = (int) $formattedDate;

                if ($isN_E) {
                    $numericDate *= -1;
                }

                return $numericDate;
            }
        }

        return null;
    }

    public static function formatDateFromInt($dateInt){
        if (!$dateInt) {
            return '';
        }

        $date = abs($dateInt);
        $year = substr($date, 0, strlen($date)-4);
        $month = substr($date, strlen($date)-4, 2);
        $day = substr($date, strlen($date)-2, 2);
        while (strlen($year)<4) $year = '0'.$year;

        return $day . '.' . $month . '.' . $year;
    }
    
    public static function formatDateToYYYYMMDD($date){
        try {
            return Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
        }
        catch (\Exception $e) {
            return null;
        }
    }
    public static function formatDateToDDMMYYYY($date){
        try {
            return Carbon::createFromFormat('Y-m-d', Carbon::parse($date)->toDateString())->format('d.m.Y');
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
