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
 * Time: 23:51
 */

namespace APIv3\Middleware;


use Medoo\Medoo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class DBLogger
{
    /** @var Medoo */
    private $medoo;

    /**
     * DBLogger constructor.
     * @param $medoo Medoo
     */
    public function __construct($medoo)
    {
        $this->medoo = $medoo;
    }

    /**
     * @param $request ServerRequestInterface
     * @param $response ResponseInterface
     * @param $next callable
     * @return Response
     */
    public function __invoke($request, $response, $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);
        $body = json_decode($response->getBody());

        $headers = [
            "status" => ($response->getStatusCode() == 200) ? "sucess" : "failure",
            "code" => $response->getStatusCode(),
        ];

        return $response->withJson(["headers" => $headers, "data" => $body, "log" => $this->medoo->log()]);
    }
}