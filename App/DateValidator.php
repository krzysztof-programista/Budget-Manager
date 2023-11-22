<?php

namespace App;

class DateValidator
{
    public static function getDate()
    {
        $dateRange = [];

        $currentYear = date('Y');
        $currentMonth = date('m');
        $endDayOfCurrentMonth = static::findLastDayOfMonth($currentMonth, $currentYear);

        //current month
        $startOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01';
        $endOfCurrentMonth = $currentYear . '-' . $currentMonth . '-' . $endDayOfCurrentMonth;

        //previous month
        if ($currentMonth != '01') {
            $previousMonth = (int) $currentMonth - 1;

            if (strlen($previousMonth) == 1) {
                $previousMonth = '0' . $previousMonth;
            }

            $year = $currentYear;
        } else {
            $previousMonth = '12';
            $year = (int) $currentYear - 1;
        }

        $endDayOfPreviousMonth = static::findLastDayOfMonth($previousMonth, $year);

        $startOfPreviousMonth = $year . '-' . $previousMonth . '-01';
        $endOfPreviousMonth = $year . '-' . $previousMonth . '-' . $endDayOfPreviousMonth;

        //current year
        $startOfCurrentYear = $currentYear . '-01-01';
        $endOfCurrentYear = $currentYear . '-12-31';


        $dateRange = [
            'startOfCurrentMonth' => $startOfCurrentMonth,
            'endOfCurrentMonth' => $endOfCurrentMonth,
            'startOfPreviousMonth' => $startOfPreviousMonth,
            'endOfPreviousMonth' => $endOfPreviousMonth,
            'startOfCurrentYear' => $startOfCurrentYear,
            'endOfCurrentYear' => $endOfCurrentYear,
        ];

        return $dateRange;
    }

    public static function findLastDayOfMonth($month, $year)
    {
        switch ($month) {
            case '01':
            case '03':
            case '05':
            case '07':
            case '08':
            case '10':
            case '12':
                return '31';
            case '04':
            case '06':
            case '09':
            case '11':
                return '30';
            case '02':
                return ($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0 ? '29' : '28';
        }
    }
}