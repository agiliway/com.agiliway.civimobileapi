<?php
/**
 * Gives you the ability to work with CMS accounts
 */

class CRM_CiviMobileAPI_Utils_CmsUser
{
    /**
     * Drupal 7 CMS
     */
    const CMS_DRUPAL7 = 'Drupal';

    /**
     * Drupal 6 CMS
     */
    const CMS_DRUPAL6 = 'Drupal6';

    /**
     * WordPress CMS
     */
    const CMS_WORDPRESS = 'WordPress';

    /**
     * Joomla CMS
     */
    const CMS_JOOMLA = 'Joomla';

    /**
     * Backdrop CMS
     */
    const CMS_BACKDROP = 'Backdrop';

    /**
     * Singleton pattern
     */
    private static $instance;

    /**
     * Current CMS system
     */
    private $system;

    private function __construct()
    {
        $this->system = defined('CIVICRM_UF') ? CIVICRM_UF : '';
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Validate a user using email/username and password
     *
     * @param $email
     * @param $password
     * @return bool|integer
     */
    public function validateUser($email, $password)
    {
        $uid = $this->validateAccount($email, $password);
        return $uid;
    }

    /**
     * Gets drupal user
     *
     * @param $name
     * @return bool|mixed
     */
    public function getDrupalAccount($name)
    {
        $user = user_load_by_name($name);
        if (!$user)
            $user = user_load_by_mail($name);

        return $user;
    }

    /**
     * Validate CMS
     *
     * @return bool
     */
    public function validateCMS()
    {
        return in_array($this->system, [self::CMS_DRUPAL7]);
    }

    /**
     * Validate account depends on CMS system
     *
     * @param $email
     * @param $password
     * @return bool
     */
    public function validateAccount($email, $password)
    {
        $uid = false;
        switch ($this->system) {
            case self::CMS_DRUPAL7:
                $account = $this->getDrupalAccount($email);
                require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
                if (user_check_password($password, $account)) {
                    $uid = $account->uid;
                }
                break;
        }

        return $uid;
    }

    public function getSystem()
    {
        return $this->system;
    }
}
