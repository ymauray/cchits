<?php
/**
 *     CCHits. Where you make the charts.
 *     Copyright (C) 2018  CCHits.net
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU Affero General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU Affero General Public License for more details.
 *
 *     You should have received a copy of the GNU Affero General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Created by IntelliJ IDEA.
 * User: yannick
 * Date: 19.09.18
 * Time: 13:02
 */

namespace APIv3\Utils;


class DateUtils
{
    public static function day_before($date)
    {
        return self::subtract_days($date, 1);
    }

    public static function subtract_days($date, $num_days)
    {
        date_default_timezone_set("UTC");
        $timestamp = strtotime($date);
        $past_timestamp = strtotime("-" . $num_days . " days", $timestamp);
        $past_date = strftime("%Y-%m-%d", $past_timestamp);

        return $past_date;
    }
}