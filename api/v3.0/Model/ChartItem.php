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
 * Time: 13:40
 */

namespace APIv3\Model;


class ChartItem
{
    /** @var int */
    public $id;

    /** @var int */
    public $track_id;

    /** @var int */
    public $position;

    /** @var int|null */
    public $position_day_before;

    /** @var int */
    public $position_min;

    /** @var int */
    public $position_max;

    /**
     * @var (int|null)[]
     */
    public $movements;
}