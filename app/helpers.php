<?php

use Carbon\Carbon;

if (! function_exists('app_time')) {
    /**
     * Return the given datetime in the application timezone for display.
     * Use this so saved times show correctly according to config('app.timezone') (e.g. Asia/Kolkata).
     *
     * @param  \Carbon\CarbonInterface|\DateTimeInterface|string|null  $datetime
     * @return \Carbon\Carbon|null
     */
    function app_time($datetime)
    {
        if ($datetime === null) {
            return null;
        }

        $tz = config('app.timezone', 'Asia/Kolkata');

        return Carbon::parse($datetime)->setTimezone($tz);
    }
}

if (! function_exists('app_time_format')) {
    /**
     * Format the given datetime in the application timezone.
     *
     * @param  \Carbon\CarbonInterface|\DateTimeInterface|string|null  $datetime
     * @param  string  $format
     * @return string
     */
    function app_time_format($datetime, string $format = 'd M Y, H:i'): string
    {
        $dt = app_time($datetime);

        return $dt ? $dt->format($format) : '';
    }
}
