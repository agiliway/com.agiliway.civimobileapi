<?php
/**
 * Gives you the ability to work with CMS accounts
 */

class CRM_CiviMobileAPI_Utils_CmsUser {

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

  private function __construct() {
    $this->system = defined('CIVICRM_UF') ? CIVICRM_UF : '';
  }

  private function __clone() {}

  /**
   * Gets instance of CRM_CiviMobileAPI_Utils_CmsUser
   *
   * @return \CRM_CiviMobileAPI_Utils_CmsUser
   */
  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
   * Gets drupal user
   *
   * @param $name
   *
   * @return bool|mixed
   */
  public function getDrupalAccount($name) {
    $user = user_load_by_name($name);
    if (!$user) {
      $user = user_load_by_mail($name);
    }

    return $user;
  }

  /**
   * Gets WordPress user
   *
   * @param $name
   *
   * @return bool|mixed
   */
  public function getWordPressAccount($name) {
    $user = get_user_by('login', $name);
    if (!$user) {
      $user = get_user_by('email', $name);
    }

    return $user;
  }

  /**
   * Gets Joomla user
   *
   * @param $name
   *
   * @return bool|mixed
   */
  public function getJoomlaAccount($name) {
    $userId = JUserHelper::getUserId($name);

    if (!isset($userId) || empty($userId)) {
      $db = JFactory::getDbo();
      $query = $db->getQuery(TRUE)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__users'))
        ->where($db->quoteName('email') . ' = ' . $db->quote($name));
      $db->setQuery($query, 0, 1);

      $userId = $db->loadResult();
    }

    $user = NULL;
    if (isset($userId) && !empty($userId)) {
      $user = JFactory::getUser($userId);
    }

    return $user;
  }

  private function validateJoomlaUser($username, $password) {
    $app = JFactory::getApplication();
    $credentials = [
      'username' => $username,
      'password' => $password,
    ];
    $result = $app->login($credentials);

    return $result;
  }

  /**
   * Validate CMS
   *
   * @return bool
   */
  public function validateCMS() {
    return in_array($this->system, [
      self::CMS_DRUPAL7,
      self::CMS_WORDPRESS,
      self::CMS_JOOMLA,
    ]);
  }

  /**
   * Validate account depends on CMS system
   *
   * @param $email
   * @param $password
   *
   * @return bool
   */
  public function validateAccount($email, $password) {
    $uid = FALSE;
    switch ($this->system) {
      case self::CMS_DRUPAL7:
        $account = $this->getDrupalAccount($email);
        require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
        if (user_check_password($password, $account)) {
          $uid = $account->uid;
        }

        if (!$uid && module_exists('ldap_authentication')) {
          module_load_include('inc', 'ldap_authentication');

          $form_state = [
            'values' => [
              'name' => $email,
              'pass' => $password,
            ]
          ];

          _ldap_authentication_user_login_authenticate_validate($form_state, []);

          $uid = !empty($form_state['uid']) ? $form_state['uid'] : FALSE;
        }
        break;
      case self::CMS_WORDPRESS:
        $account = $this->getWordPressAccount($email);
        if ($account && wp_check_password($password, $account->user_pass, $account->ID)) {
          $uid = $account->ID;
        }
        break;
      case self::CMS_JOOMLA:
        $account = $this->getJoomlaAccount($email);
        if (isset($account) && $this->validateJoomlaUser($account->username, $password)) {
          $uid = $account->id;
        }
    }

    return $uid;
  }

  /**
   * @param $identificator
   *
   * @return bool|mixed
   */
  public function searchAccount($identificator) {
    $account = FALSE;
    switch ($this->system) {
      case self::CMS_DRUPAL7:
        $account = $this->getDrupalAccount($identificator);
        break;
      case self::CMS_WORDPRESS:
        $account = $this->getWordPressAccount($identificator);
        break;
      case self::CMS_JOOMLA:
        $account = $this->getJoomlaAccount($identificator);
        break;
    }

    return $account;
  }

  /**
   * @return array|false|mixed|string
   */
  public function getSystem() {
    return $this->system;
  }

}
