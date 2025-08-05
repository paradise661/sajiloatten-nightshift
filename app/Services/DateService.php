<?php

namespace App\Services;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Carbon\Carbon;
use DateTime;

class DateService
{
    public static function BSToAD($bs_date)
    {
        if ($bs_date) {
            return LaravelNepaliDate::from($bs_date)->toEnglishDate();
        }
    }

    public static function ADToBS($ad_date)
    {
        if ($ad_date) {
            return LaravelNepaliDate::from($ad_date)->toNepaliDate();
        }
    }

    public static function ADToBSFullMonth($ad_date)
    {
        if ($ad_date) {
            return LaravelNepaliDate::from($ad_date)->toNepaliDate(format: 'F d');
        }
    }

    public static function getWeekends($day_names)
    {
        $start = new DateTime(date('Y-m-01'));
        $end = new DateTime(date('Y-m-d'));

        // Map day names to numbers
        $day_map = [
            "Sunday" => 0,
            "Monday" => 1,
            "Tuesday" => 2,
            "Wednesday" => 3,
            "Thursday" => 4,
            "Friday" => 5,
            "Saturday" => 6
        ];

        $target_days = array_map(fn($day) => $day_map[$day], $day_names);

        $matching_dates = [];

        while ($start <= $end) {
            if (in_array((int)$start->format('w'), $target_days)) {
                $matching_dates[] = $start->format('Y-m-d');
            }
            $start->modify('+1 day');
        }

        return $matching_dates;
    }

    public static function getAdDates($year, $month)
    {
        $start_date_bs = $year . '-' . $month . '-01';
        $start_date_ad = DateService::BSToAD($start_date_bs);

        // to get last date from nepali month to AD
        $givenDate = Carbon::create($year, $month, 1);
        $nextMonthDate = $givenDate->addMonth()->format('Y-m-d');
        $nextMonthStartDate = DateService::BSToAD($nextMonthDate);
        $end_date_ad = date('Y-m-d', strtotime($nextMonthStartDate . ' -1 day'));

        return [
            'start_date' => $start_date_ad,
            'end_date' => $end_date_ad
        ];
    }
}
