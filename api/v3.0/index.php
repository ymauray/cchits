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

namespace APIv3;

use APIv3\API\ChartApi;
use APIv3\Business\ChartBusiness;
use APIv3\Middleware\DBLogger;
use APIv3\Middleware\Headers;
use APIv3\Provider\ChartProvider;
use Interop\Container\Exception\ContainerException;
use Medoo\Medoo;
use Slim\App;
use Slim\Container;

require '../vendor/autoload.php';

$container = new Container();

/**
 * @param $container Container
 * @return Medoo
 */
$container["Medoo"] = function ($container) {
    $medoo = new Medoo([
        'database_type' => 'mysql',
        'database_name' => 'cchits',
        'server' => 'localhost',
        'username' => 'cchits',
        'password' => 'cchits',

        // [optional]
        'charset' => 'utf8',
        'logging' => true,
    ]);
    return $medoo;
};

/**
 * @param $container Container
 * @return ChartProvider
 */
$container[ChartProvider::class] = function ($container) {
    $medoo = $container->get("Medoo");
    $charProvider = new ChartProvider($medoo);
    return $charProvider;
};

/**
 * @param $container Container
 * @return ChartBusiness
 */
$container[ChartBusiness::class] = function ($container) {
    $chartProvider = $container->get(ChartProvider::class);
    $chartBusiness = new ChartBusiness($chartProvider);
    return $chartBusiness;
};

/**
 * @param $container Container
 * @return ChartApi
 */
$container[ChartApi::class] = function ($container) {
    $chartBusiness = $container->get(ChartBusiness::class);
    $chartApi = new ChartApi($chartBusiness);
    return $chartApi;
};

$app = new App($container);

try {
    $medoo = $container->get("Medoo");
    $middleware = new DBLogger($medoo);
    $app->add($middleware);
} catch (ContainerException $e) {
}

$app->get("/chart", ChartApi::class . ":getLatest");

try {
    $app->run();
} catch (\Slim\Exception\MethodNotAllowedException $e) {
} catch (\Slim\Exception\NotFoundException $e) {
} catch (\Exception $e) {
}
