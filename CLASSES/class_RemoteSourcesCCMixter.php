<?php
/**
 * CCHits.net is a website designed to promote Creative Commons Music,
 * the artists who produce it and anyone or anywhere that plays it.
 * These files are used to generate the site.
 *
 * PHP version 5
 *
 * @category Default
 * @package  MusicSources
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */
/**
 * This class pulls appropriate data from CCMixter.org
 *
 * @category Default
 * @package  MusicSources
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */
class RemoteSourcesCCMixter extends RemoteSources
{
    /**
    * Get all the source data we can pull from the source.
    *
    * @param string $src Source URL for the retriever
    *
    * @return const A value explaining the outcome of the fetch request
    */
    function __construct($src)
    {
        if (preg_match('/(^\d+)|http:\/\/ccmixter.org\/files\/[^\/]+/(\d+)/', $src, $match) == 0) {
            throw new RemoteSource_InvalidSource();
        }
        if ($match[1] == "" and isset($match[2]) and $match[2] != "") {
            $match[1] = $match[2];
        }
        $return = array();
        $url_base = 'http://ccmixter.org/api/query?f=json&ids=';
        $file_contents = file_get_contents($url_base . $match[1]);
        if ($file_contents == FALSE) {
            throw new RemoteSource_InvalidSource();
        }
        $json_contents = json_decode($file_contents);
        if ($json_contents == FALSE) {
            throw new RemoteSource_InvalidSource();
        }
        preg_match("/licenses\/(.*)\/\d/", $json_contents[0]->license_url, $matches);
        if (!isset($matches[1])) {
            throw new RemoteSource_InvalidLicense();
        }
        $this->strTrackName = $json_contents[0]->upload_name;
        $this->strArtistName = $json_contents[0]->user_real_name;
        $this->strTrackUrl = $json_contents[0]->file_page_url;
        $this->strArtistUrl = $json_contents[0]->artist_page_url;
        $this->enumTrackLicense = $matches[1];
        if ($json_contents[0]->upload_extra->nsfw == false) {
            $this->isNSFW = 0;
        } else {
            $this->isNSFW = 1;
        }
        $this->fileUrl = $json_contents[0]->files[0]->download_url;
        try {
            if ($this->is_valid_cchits_submission()) {
                $this->create();
                return true;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}

