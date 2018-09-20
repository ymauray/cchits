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
 * Time: 23:02
 */

namespace APIv3\API;


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use APIv3\Business\ChartBusiness;

class ChartApi
{
    /** @var ChartBusiness */
    private $charBusiness;

    /**
     * ChartApi constructor.
     * @param $chartBusiness ChartBusiness
     */
    public function __construct($chartBusiness)
    {
        $this->charBusiness = $chartBusiness;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return static
     */
    public function getLatest(Request $request, Response $response, array $args)
    {
        $latest_date = $this->charBusiness->getLatestDate();
        $data = $this->charBusiness->getChartForDate($latest_date);
        /** @var $response \Slim\Http\Response */
        return $response->withJson(["date" => $latest_date, "chart" => $data]);
    }
}