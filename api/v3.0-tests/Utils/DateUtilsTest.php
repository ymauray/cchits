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
 * Time: 13:09
 */

namespace APIv3Tests\Utils;

use APIv3\Utils\DateUtils;

class DateUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testDay_before()
    {
        $date = "2018-09-13";
        $day_before = DateUtils::day_before($date);
        self::assertEquals("2018-09-12", $day_before);
    }

    public function testDay_before_january_first()
    {
        $date = "2018-01-01";
        $day_before = DateUtils::day_before($date);
        self::assertEquals("2017-12-31", $day_before);
    }

    public function testDay_before_march_first()
    {
        $date = "2018-03-01";
        $day_before = DateUtils::day_before($date);
        self::assertEquals("2018-02-28", $day_before);
    }

    public function testDay_before_march_first_2020()
    {
        $date = "2020-03-01";
        $day_before = DateUtils::day_before($date);
        self::assertEquals("2020-02-29", $day_before);
    }

    public function testSubtract_days()
    {
        $date = "2018-09-13";
        $past_date = DateUtils::subtract_days($date, 60);
        self::assertEquals("2018-07-15", $past_date);
    }
}
