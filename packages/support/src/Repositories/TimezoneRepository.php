<?php

namespace Laravolt\Support\Repositories;

use DateTime;
use DateTimeZone;

class TimezoneRepository implements \Laravolt\Support\Contracts\TimezoneRepository
{
    public function all()
    {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $offsets = [];
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($offsets);

        $lists = [];
        foreach ($offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $lists[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $lists;
    }
}
