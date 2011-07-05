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
 * This class knows how to do everything with User Objects
 *
 * @category Default
 * @package  Brokers
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     http://code.cchits.net Developers Web Site
 * @link     http://gitorious.net/cchits-net Version Control Service
 */
class UserBroker
{
    /**
     * This function gets any details about the acting User
     *
     * @return object|false User object or false if the authentication failed
     */
    function getUser()
    {
        if (session_id()==='') {
            $lifetime=604800; // 7 Days
            session_start();
            setcookie(session_name(),session_id(),time()+$lifetime);
        }
        if ($_SESSION['cookie'] != '') {
            $field = "strCookieID";
            $param = $_SESSION['cookie'];
        } elseif ($_SESSION['openid'] != '') {
            $field = "strOpenID";
            $param = $_SESSION['openid'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth_params = explode(":" , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            $username = $auth_params[0];
            unset($auth_params[0]);
            $password = sha1(implode('',$auth_params));
            $field = "sha1Password";
            $param = "{$username}:{$password}";
        } elseif (isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = sha1($_SERVER['PHP_AUTH_PW']);
            $field = "sha1Password";
            $param = "{$username}:{$password}";
        } else {
            return new NewUserObject();
        }

        if (isset($field) and isset($param)) {
            try {
                $db = CF::getFactory()->getConnection();
                $sql = "SELECT * FROM users WHERE $field = ? LIMIT 1";
                $query = $db->prepare($sql);
                $query->execute(array($param));
                $result = $query->fetchObject('UserObject');
                if ($result == false) {
                    return new NewUserObject($param);
                } else {
                    $result->set_datLastSeen(date("Y-m-d H:i:s"));
                    $result->write();
                    return $result;
                }
            } catch(Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Get the User object for the intUserID
     *
     * @param integer $intUserID UserID to search for
     *
     * @return object UserObject for intUserID
     */
    function getUserByID($intUserID = 0)
    {
        if (0 + $intUserID > 0) {
            try {
                $db = CF::getFactory()->getConnection();
                $sql = "SELECT * FROM users WHERE intUserID = ? LIMIT 1";
                $query = $db->prepare($sql);
                $query->execute(array($intUserID));
                $result = $query->fetchObject('UserObject');
                return $result;
            } catch(Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}