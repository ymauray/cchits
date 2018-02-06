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
 * @link     https://github.com/CCHits/Website/wiki Developers Web Site
 * @link     https://github.com/CCHits/Website Version Control Service
 */
/**
 * This class handles all OpenID Requests
 *
 * @category Default
 * @package  UI
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     http://cchits.net Actual web service
 * @link     https://github.com/CCHits/Website/wiki Developers Web Site
 * @link     https://github.com/CCHits/Website Version Control Service
 */
class OpenID
{
    protected static $handler = null;

    protected $nickname    = false;
    protected $email       = true;
    protected $realname    = false;
    protected $language    = false;
    protected $dateofbirth = false;
    protected $gender      = false;
    protected $postcode    = false;
    protected $country     = false;
    protected $timezone    = false;

    // There are more AX attributes we can ask for, but most will not be supplied. Also, these others
    // don't correspond with the SReg attributes that can be requested.
    // For details see http://www.axschema.org/types/ and http://www.axschema.org/types/experimental/

    // If you know about another sreg or ax attribute you want to request, specify them in here, using
    // the templates below to add them in.

    protected $ax_attribute = array();
    protected $sreg_attribute = array();

    protected $consumer = null;

    /**
     * An internal function to make this a singleton
     *
     * @return object This class by itself.
     */
    private static function getHandler()
    {
        if (self::$handler == null) {
            self::$handler = new self();
        }
        return self::$handler;
    }


    /**
     * Load appropriate libraries and set certain variables
     *
     * @return void
     */
    function __construct()
    {
        // start session (needed for YADIS)
        UI::start_session();

        $arrUri = UI::getUri();
        $externalLibraryLoader = new ExternalLibraryLoader();
        $openid_ver = $externalLibraryLoader->getVersion('PHP-OPENID');
        $libOpenID = dirname(__FILE__) . '/../EXTERNALS/PHP-OPENID/' . $openid_ver;

        set_include_path(get_include_path() . PATH_SEPARATOR . $libOpenID);
        include_once "Auth/OpenID/Consumer.php";
        include_once "Auth/OpenID/FileStore.php";
        include_once "Auth/OpenID/SReg.php";
        include_once "Auth/OpenID/AX.php";

        // create file storage area for OpenID data
        $store = new Auth_OpenID_FileStore(dirname(__FILE__) . '/../OPENID_STORE');

        // create OpenID consumer
        $this->consumer = new Auth_OpenID_Consumer($store);
    }


    /**
     * Request authentication from the $id
     *
     * @param string $id      The requested OpenID authentication
     * @param string $base    The path where these functions are triggered from
     * @param string $success The path to return to after authentication is completed successfully
     * @param string $fail    The path to return to after authentication is completed unsuccessfully or fails
     *
     * @return void
     */
    public static function request($id = '', $base = '', $success = '', $fail = '')
    {
        $handler = self::getHandler();
        $auth = $handler->consumer->begin($id);
        if (!$auth) {
            $_SESSION['OPENID_AUTH'] = false;
            $_SESSION['OPENID_FAILED_REASON'] = 0;
            header("Location: $fail");
        }

        $_SESSION['OPENID_SUCCESS'] = $success;
        $_SESSION['OPENID_FAILED'] = $fail;

        if (isset($handler->nickname) and $handler->nickname == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/friendly', 1, 1, 'friendly');
            $handler->sreg_attribute[] = 'nickname';
        }

        if (isset($handler->email) and $handler->email == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, 1, 'email');
            $handler->sreg_attribute[] = 'email';
        }
        if (isset($handler->realname) and $handler->realname == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson', 1, 1, 'fullname');
            // Google doesn't actually return a response to fullname, but will return the first and last name.
            // http://code.google.com/apis/accounts/docs/OpenID.html#Parameters
            // Just to be sure we don't miss anything, we'll request the lot.
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/prefix', 1, 1, 'prefix');
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, 1, 'firstname');
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/middle', 1, 1, 'middlename');
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, 1, 'lastname');
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/suffix', 1, 1, 'suffix');
            $handler->sreg_attribute[] = 'fullname';
        }
        if (isset($handler->dateofbirth) and $handler->dateofbirth == true) {
            $handler->ax_attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/birthDate', 1, 1, 'dob');
            $handler->sreg_attribute[] = 'dob';
        }
        if (isset($handler->gender) and $handler->gender == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/person/gender', 1, 1, 'gender');
            $handler->sreg_attribute[] = 'gender';
        }
        if (isset($handler->postcode) and $handler->postcode == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/postalCode/home', 1, 1, 'postcode');
            $handler->sreg_attribute[] = 'postcode';
        }
        if (isset($handler->country) and $handler->country == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/country/home', 1, 1, 'country');
            $handler->sreg_attribute[] = 'country';
        }
        if (isset($handler->language) and $handler->language == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language', 1, 1, 'language');
            $handler->sreg_attribute[] = 'language';
        }
        if (isset($handler->timezone) and $handler->timezone == true) {
            $handler->ax_attribute[]
                = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/timezone', 1, 1, 'timezone');
            $handler->sreg_attribute[] = 'timezone';
        }

        // Add AX fetch request to authentication request
        $ax = new Auth_OpenID_AX_FetchRequest;
        foreach ($handler->ax_attribute as $attr) {
            $ax->add($attr);
        }
        $auth->addExtension($ax);

        // Add SReg attributes to authentication request
        $sreg_request = Auth_OpenID_SRegRequest::build(array(), $handler->sreg_attribute);
        if ($sreg_request) {
            $auth->addExtension($sreg_request);
        }

        // redirect to OpenID provider for authentication
        $url = $auth->redirectURL($base, $base . '?return=1');
        header('Location: ' . $url);
    }


    /**
     * Act on the response from the OpenID Provider. Then redirect back to the completed authentication path.
     *
     * @param string $base The path where these functions are triggered from
     *
     * @return void
     */
    static function response($base = '')
    {
        $handler = self::getHandler();
        $response = $handler->consumer->complete($base . '?return=1');

        if ($response->status == Auth_OpenID_SUCCESS) {
            // Get registration informations
            $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
            $ax_resp = Auth_OpenID_AX_FetchResponse::fromSuccessResponse($response);

            $arr_auth = get_object_vars($response);
            $endpoint = get_object_vars($arr_auth['endpoint']);

            $openid_url = $endpoint['claimed_id'];

            $name_prefix = '';
            $name_first = '';
            $name_middle = '';
            $name_last = '';
            $name_suffix = '';
            $name_full = '';
            $name_alias = '';
            $email = '';
            $language = '';
            $dateofbirth = '';
            $gender = '';
            $postcode = '';
            $country = '';
            $timezone = '';
            if (isset($sreg_resp) and is_object($sreg_resp)) {
                $arr_sreg_resp = get_object_vars($sreg_resp);
                $arr_sreg_data = $arr_sreg_resp['data'];
                if (isset($arr_sreg_data) and is_array($arr_sreg_data) and count($arr_sreg_data) > 0) {
                    if (isset($arr_sreg_data['fullname'])) {
                        $name_full = $arr_sreg_data['fullname'];
                    }
                    if (isset($arr_sreg_data['nickname'])) {
                        $name_alias = $arr_sreg_data['nickname'];
                    }
                    if (isset($arr_sreg_data['email'])) {
                        $email = $arr_sreg_data['email'];
                    }
                    if (isset($arr_sreg_data['language'])) {
                        $language = $arr_sreg_data['language'];
                    }
                    if (isset($arr_sreg_data['dob'])) {
                        $dateofbirth = $arr_sreg_data['dob'];
                    }
                    if (isset($arr_sreg_data['gender'])) {
                        $gender = $arr_sreg_data['gender'];
                    }
                    if (isset($arr_sreg_data['postcode'])) {
                        $postcode = $arr_sreg_data['postcode'];
                    }
                    if (isset($arr_sreg_data['country'])) {
                        $country = $arr_sreg_data['country'];
                    }
                    if (isset($arr_sreg_data['timezone'])) {
                        $timezone = $arr_sreg_data['timezone'];
                    }
                }
            }
            if (isset($ax_resp) and is_object($ax_resp)) {
                $arr_ax_resp = get_object_vars($ax_resp);
                $arr_ax_data = $arr_ax_resp['data'];
                if (isset($arr_ax_data["http://axschema.org/namePerson/prefix"]) 
                    and count($arr_ax_data["http://axschema.org/namePerson/prefix"])>0
                ) {
                    $name_prefix = $arr_ax_data["http://axschema.org/namePerson/prefix"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson/first"]) 
                    and count($arr_ax_data["http://axschema.org/namePerson/first"])>0
                ) {
                    $name_first = $arr_ax_data["http://axschema.org/namePerson/first"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson/middle"]) 
                    and count($arr_ax_data["http://axschema.org/namePerson/middle"])>0
                ) {
                    $name_middle = $arr_ax_data["http://axschema.org/namePerson/middle"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson/last"])
                    and count($arr_ax_data["http://axschema.org/namePerson/last"])>0
                ) {
                    $name_last = $arr_ax_data["http://axschema.org/namePerson/last"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson/suffix"])
                    and count($arr_ax_data["http://axschema.org/namePerson/suffix"])>0
                ) {
                    $name_suffix = $arr_ax_data["http://axschema.org/namePerson/suffix"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson"])
                    and count($arr_ax_data["http://axschema.org/namePerson"])>0
                ) {
                    $name_full = $arr_ax_data["http://axschema.org/namePerson"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/namePerson/friendly"]) 
                    and count($arr_ax_data["http://axschema.org/namePerson/friendly"])>0
                ) {
                    $name_alias = $arr_ax_data["http://axschema.org/namePerson/friendly"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/contact/email"])
                    and count($arr_ax_data["http://axschema.org/contact/email"])>0
                ) {
                    $email = $arr_ax_data["http://axschema.org/contact/email"][0];
                }
                if (isset($arr_ax_data["http://axschema.org/pref/language"])
                    and count($arr_ax_data["http://axschema.org/pref/language"])>0
                ) {
                    $language = $arr_ax_data["http://axschema.org/pref/language"][0];
                }
                if (isset($arr_ax_data['http://axschema.org/birthDate'])
                    and count($arr_ax_data['http://axschema.org/birthDate'])>0
                ) {
                    $dateofbirth = $arr_ax_data['http://axschema.org/birthDate'][0];
                }
                if (isset($arr_ax_data['http://axschema.org/person/gender'])
                    and count($arr_ax_data['http://axschema.org/person/gender'])>0
                ) {
                    $gender = $arr_ax_data['http://axschema.org/person/gender'][0];
                }
                if (isset($arr_ax_data['http://axschema.org/contact/postalCode/home'])
                    and count($arr_ax_data['http://axschema.org/contact/postalCode/home'])>0
                ) {
                    $postcode = $arr_ax_data['http://axschema.org/contact/postalCode/home'][0];
                }
                if (isset($arr_ax_data['http://axschema.org/contact/country/home'])
                    and count($arr_ax_data['http://axschema.org/contact/country/home'])>0
                ) {
                    $country = $arr_ax_data['http://axschema.org/contact/country/home'][0];
                }
                if (isset($arr_ax_data['http://axschema.org/pref/timezone'])
                    and count($arr_ax_data['http://axschema.org/pref/timezone'])>0
                ) {
                    $timezone = $arr_ax_data['http://axschema.org/pref/timezone'][0];
                }
            }
            if ($name_full == '' 
                and ($name_prefix != '' 
                or $name_first != '' 
                or $name_middle != '' 
                or $name_last != '' 
                or $name_suffix != '')
            ) {
                foreach (array($name_prefix, $name_first, $name_middle, $name_last, $name_suffix) as $name_part) {
                    if ($name_full != '' and $name_part != '') {
                        $name_full .= ' ';
                    }
                    if ($name_part != '') {
                        $name_full .= $name_part;
                    }
                }
            }
            $_SESSION['OPENID_AUTH'] = array('url' => $openid_url,
                                             'fullname' => $name_full,
                                             'nickname' => $name_alias,
                                             'email' => $email,
                                             'language' => $language,
                                             'dob' => $dateofbirth,
                                             'gender' => $gender,
                                             'postcode' => $postcode,
                                             'country' => $country,
                                             'timezone' => $timezone);
        } else {
            $_SESSION['OPENID_AUTH'] = false;
            header("Location: {$_SESSION['OPENID_FAILED']}");
        }

        // redirect to restricted application page
        header("Location: {$_SESSION['OPENID_SUCCESS']}");
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_nickname($state)
    {
        $handler = self::getHandler();
        $handler->nickname = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_email($state)
    {
        $handler = self::getHandler();
        $handler->email = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_realname($state)
    {
        $handler = self::getHandler();
        $handler->realname = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_language($state)
    {
        $handler = self::getHandler();
        $handler->language = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_dateofbirth($state)
    {
        $handler = self::getHandler();
        $handler->dateofbirth = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_gender($state)
    {
        $handler = self::getHandler();
        $handler->gender = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_postcode($state)
    {
        $handler = self::getHandler();
        $handler->postcode = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_country($state)
    {
        $handler = self::getHandler();
        $handler->country = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $state Should this value be requested
     *
     * @return void
     */
    function set_timezone($state)
    {
        $handler = self::getHandler();
        $handler->timezone = $state;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $array These values should be requested
     *
     * @return void
     */
    function set_ax_attribute($array)
    {
        $handler = self::getHandler();
        $handler->ax_attribute = $array;
    }

    /**
     * The Setter for the AX/SReg value named
     *
     * @param boolean $array These values should be requested
     *
     * @return void
     */
    function set_sreg_attribute($array)
    {
        $handler = self::getHandler();
        $handler->sreg_attribute = $array;
    }
}