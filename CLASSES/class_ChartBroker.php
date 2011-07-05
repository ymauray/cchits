<?php
/**
 * CCHits.net is a website designed to promote Creative Commons Music,
 * the artists who produce it and anyone or anywhere that plays it.
 * These files are used to generate the site.
 *
 * PHP version 5
 *
 * @category Default
 * @package  CCHitsClass
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */
/**
 * This class knows all the ways to get collections of tracks
 *
 * @category Default
 * @package  Brokers
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */

class ChartBroker
{
    /**
     * A function to retrieve all the tracks associated to a day's chart.
     *
     * @param date    $strChartDate The date of the chart in Y-m-d format
     * @param integer $intPage      The start "page" number
     * @param integer $intSize      The size of each page
     *
     * @return array|false An array of the Tracks, or false if the operation fails.
     */
    function getChartByDate(
        $strChartDate = '',
        $intPage = 0,
        $intSize = 25
    ) {
        $return = array();
        $db = CF::getFactory()->getConnection();
        try {
            if ($strChartDate == '') {
                $sql = "SELECT max(datChart) as max_datChart FROM chart LIMIT 0, 1";
                $query = $db->prepare($sql);
                $query->execute();
                $strChartDate = $query->fetchColumn();
            }
            $sql = "SELECT intPositionID, intTrackID FROM chart WHERE datChart = ?";
            $pagestart = ($intPage * $intSize);
            $query = $db->prepare($sql . " ORDER BY intPositionID ASC LIMIT " . $pagestart . ", $intSize");
            $query->execute(array($strChartDate));
            $tracks = $query->fetchAll(PDO::FETCH_ASSOC);
            if ($tracks != false and count($tracks)>0) {
                foreach ($tracks as $track) {
                    $temp = TrackBroker::getTrackByID($track['intTrackID']);
                    if ($temp != false) {
                        $return[$track['intPositionID']] = $temp;
                    }
                }
            }
            return $return;
        } catch(Exception $e) {
            error_log("SQL error: " . $e);
            return false;
        }
    }
}
