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
 * Date: 18.09.18
 * Time: 23:10
 */

namespace APIv3\Provider;


use APIv3\Model\ChartItem;
use APIv3\Utils\DateUtils;
use Medoo\Medoo;

class ChartProvider
{
    /** @var Medoo */
    private $medoo;

    /**
     * ChartProvider constructor.
     * @param $medoo Medoo
     */
    public function __construct($medoo)
    {
        $this->medoo = $medoo;
    }

    /**
     * @return string
     */
    public function getLatestDate()
    {
        $rows = $this->medoo->select(
            "chart",
            [
                "latest_date" => Medoo::raw("max(datChart)")
            ]
        );

        return $rows[0]["latest_date"];
    }

    public function get_movements($row)
    {
        /** @var $item ChartItem */
        if ($min > intval($row['position'])) $min = intval($row['position']);
        if ($max < intval($row['position'])) $max = intval($row['position']);
        return $row['position'];
    }

    public function getTrackMovements($track_id, $date, $num_days)
    {
        $date_min = DateUtils::subtract_days($date, $num_days - 1);

        $rows = $this->medoo->select(
            "chart",
            [
                "intPositionID(position)",
            ],
            [
                "intTrackID" => $track_id,
                "datChart[<=]" => $date,
                "datChart[>=]" => $date_min,
                "ORDER" => [
                    "datChart" => "ASC"
                ]
            ]
        );

        $min = 999999999999;
        $max = 0;
        $movements = [];
        foreach ($rows as $row)
        {
            if ($min > intval($row['position'])) $min = intval($row['position']);
            if ($max < intval($row['position'])) $max = intval($row['position']);
            $movements[] = $row['position'];
        }

        return [$min, $max, $movements];
    }

    /**
     * @param $date string
     * @return ChartItem[]
     */
    public function getForDate($date)
    {
        $rows = $this->medoo->select(
            "chart",
            [
                "intChartID(id)",
                "intPositionID(position)",
                "intTrackID(track_id)"
            ],
            [
                "datChart" => $date,
                "LIMIT" => 60,
                "ORDER" => [
                    "intPositionID" => "ASC"
                ]
            ]
        );

        return $this->map_rows($rows);
    }

    /**
     * @param $rows array
     * @return ChartItem[]
     */
    private function map_rows($rows)
    {
        $chartItems = [];
        foreach ($rows as $row)
        {
            $chartItem = new ChartItem();
            $chartItem->id = intval($row['id']);
            $chartItem->position = intval($row['position']);
            $chartItem->track_id = intval($row['track_id']);
            $chartItems[] = $chartItem;
        }

        return $chartItems;
    }
}