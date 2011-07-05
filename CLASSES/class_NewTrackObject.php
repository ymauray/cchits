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
 * This class extends the TrackObject class to create a new item in the database.
 *
 * @category Default
 * @package  Objects
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */
class NewTrackObject extends TrackObject
{
    /**
     * Establish the creation of the new item by setting the values and then calling the create function.
     *
     * @param object  $objUser            UserObject
     * @param object  $objArtist          ArtistObject
     * @param string  $strTrackName       The name of the track
     * @param string  $strTrackNameSounds How to pronounce the name of the track
     * @param string  $strTrackUrl        The location to find out more about the track
     * @param string  $enumTrackLicense   A string representing the license criteria
     * @param boolean $isNSFW             A value indicating the Work/Family Safe Status of the track
     * @param string  $fileSource         The file name in the media directory
     * @param md5sum  $md5FileHash        A hash of the media file
     *
     * @return true|false The state of the creation act
     */
    public function __construct(
        $objUser = null,
        $objArtist = null,
        $strTrackName = "",
        $strTrackNameSounds = "",
        $strTrackUrl = "",
        $enumTrackLicense = "",
        $isNSFW = false,
        $fileSource = "",
        $md5FileHash = ""
    ) {
        if ($objUser != null 
            and $objArtist != null 
            and $strTrackName != "" 
            and $strTrackNameSounds != "" 
            and $strTrackUrl != "" 
            and $enumTrackLicense != "" 
            and $fileSource != "" 
            and $md5FileHash != ""
        ) {
            $this->set_intArtistID($objArtist->get_intArtistID());
            $this->set_strTrackName($strTrackName);
            $this->set_strTrackNameSounds($strTrackNameSounds);
            $this->set_strTrackUrl($strTrackUrl);
            $this->set_enumTrackLicense($enumTrackLicense);
            $this->set_isNSFW($isNSFW);
            $this->set_fileSource($fileSource);
            $this->set_md5FileHash($md5FileHash);
            $this->set_isApproved($objUser->get_isAuthorized());
            return $this->create();
        } else {
            return false;
        }
    }
}