<?php
/**
 * CCHits.net is a website designed to promote Creative Commons Music,
 * the artists who produce it and anyone or anywhere that plays it.
 * These files are used to generate the site.
 *
 * PHP version 5
 *
 * @category Default
 * @package  Tests
 * @author   Yannick Mauray <yannick.mauray@gmail.com>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     https://github.com/CCHits/Website/wiki Developers Web Site
 * @link     https://github.com/CCHits/Website Version Control Service
 */
use PHPUnit\Framework\TestCase;

/**
 * This class tests the methods of TrackBroker.
 *
 * @category Default
 * @package  Tests
 * @author   Yannick Mauray <yannick.mauray@gmail.com>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     https://github.com/CCHits/Website/wiki Developers Web Site
 * @link     https://github.com/CCHits/Website Version Control Service
 */

final class TrackBrokerTest extends TestCase
{
    /**
     * Setup before the class' first test.
     * 
     * @return void
     */
    public static function setUpBeforeClass()
    {

    }

    /**
     * Setup before each test.
     * 
     * @return void
     */
    public function setUp()
    {

    }

    /**
     * Test function getTrackByPartialName
     * 
     * @return void
     */
    public function testGetTrackByPartialName()
    {
        // Given a partial name of 'song'
        $partialName = "song";
        // When I call TrackBroker::getTrackByPartialName
        $result = TrackBroker::getTrackByPartialName("song");
        // It should return an array of x elements
        $this->assertEquals(7, count($result));
    }

    /**
     * Clean up after each test.
     * 
     * @return void
     */
    public function tearDown()
    {

    }

    /**
     * Clean up after the class' last test.
     * 
     * @return void
     */
    public static function tearDownAfterClass()
    {

    }    
}