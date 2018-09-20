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
 * Time: 23:03
 */

namespace APIv3\Business;


use APIv3\Model\ChartItem;
use APIv3\Provider\ChartProvider;
use APIv3\Utils\DateUtils;

class ChartBusiness
{
    /** @var ChartProvider */
    private $chartProvider;

    /**
     * ChartBusiness constructor.
     * @param $chartProvider ChartProvider
     */
    public function __construct($chartProvider)
    {
        $this->chartProvider = $chartProvider;
    }

    public function getLatestDate()
    {
        $latest_date = $this->chartProvider->getLatestDate();
        return $latest_date;
    }

    /**
     * @param $date string
     * @return ChartItem[]
     */
    public function getChartForDate($date)
    {
        /** @var ChartItem[] $chart */
        $chart = $this->chartProvider->getForDate($date);

        /** @var string $day_before */
        $day_before = DateUtils::day_before($date);

        /** @var ChartItem[] $chart_day_before */
        $chart_day_before = $this->chartProvider->getForDate($day_before);

        /** @var array $map_positions_day_before */
        $map_positions_day_before = [];
        foreach ($chart_day_before as $item)
        {
            $map_positions_day_before[$item->track_id] = $item->position;
        }

        foreach ($chart as $item)
        {
            list($item->position_min, $item->position_max, $item->movements) =
                $this->chartProvider->getTrackMovements($item->track_id, $date, 60);
            if (array_key_exists($item->track_id, $map_positions_day_before)) {
                $item->position_day_before = $map_positions_day_before[$item->track_id];
            }
            else {
                $item->position_day_before = null;
            }
        }
        return $chart;
    }
}