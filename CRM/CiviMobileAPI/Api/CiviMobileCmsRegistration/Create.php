<?php

use CRM_CiviMobileAPI_Utils_Cms_Registration as CMSRegistration;

class CRM_CiviMobileAPI_Api_CiviMobileCmsRegistration_Create extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns results to api
   *
   * @return array
   * @throws api_Exception
   */
  public function getResult() {
    $transaction = new CRM_Core_Transaction();
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
      module_load_include('pages.inc', 'user');
      user_logout_current_user();
    }

    try {
      $contact = civicrm_api3('Contact', 'create', [
        'contact_type' => "Individual",
        'api.Email.create' => ['email' => $this->validParams["email"]],
        'first_name' => $this->validParams["first_name"],
        'last_name' => $this->validParams["last_name"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      $transaction->rollback();
      throw new api_Exception("CiviCRM creating Contact error: " . $e->getMessage(), 'creating_contact_error');
    }

    $this->validParams['contactID'] = $contact['id'];

    if (!CRM_Core_BAO_CMSUser::create($this->validParams, 'email')) {
      $transaction->rollback();
      throw new api_Exception('Creating CiviCRM contact error', 'creating_contact_error');
    }

    $transaction->commit();

    $message = 'User was registered.';
    $successCode = 'registration_success';

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
      $isEmailVerification = variable_get('user_email_verification', TRUE);
      $isAdministratorApproval = variable_get('user_register', TRUE) == 2;

      if ($isEmailVerification && $isAdministratorApproval) {
        $message = 'User was registered. You must to check your email to verify your account. Administrator will check your account and confirm user registration request.';
        $successCode = "registration_success_email_verification_administrator_confirmation";
      } elseif ($isEmailVerification) {
        $message = 'You must to check your email to verify your account.';
        $successCode = "registration_success_email_verification";
      } elseif ($isAdministratorApproval) {
        $message = 'Administrator will check your account and confirm user registration request.';
        $successCode = "registration_success_administrator_confirmation";
      }
    }

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA) {
      $uParams = JComponentHelper::getParams('com_users');
      switch ($uParams->get('useractivation')) {
        case 1:
          $message = 'You must to check your email to verify your account.';
          $successCode = "registration_success_email_verification";
          break;
        case 2:
          $message = 'Administrator will check your account and confirm user registration request.';
          $successCode = "registration_success_administrator_confirmation";
          break;
      }
    }

    return [
      [
        'message' => $message,
        'success_code' => $successCode,
      ]
    ];
  }

  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   * @throws api_Exception
   */
  protected function getValidParams($params) {
    $cmsUserParams = [
      'name' => $params["username"],
      'pass' => $params["password"],
      'mail' => $params["email"],
    ];
    $config = CRM_Core_Config::singleton();

    if (!$config->userSystem->isUserRegistrationPermitted()) {
      throw new api_Exception("No permissions to create user", 'no_permissions_to_create_user');
    }
    if (strlen($cmsUserParams["name"]) < CMSRegistration::minUsernameLength()) {
      throw new api_Exception("Username must have at least " . CMSRegistration::minUsernameLength() . " characters", 'username_has_not_enough_characters');
    }
    if (strlen($cmsUserParams["name"]) > CMSRegistration::maxUsernameLength()) {
      throw new api_Exception("Username must have no more " . CMSRegistration::maxUsernameLength() . " characters", 'username_has_too_much_characters');
    }
    if (strlen($cmsUserParams["pass"]) < CMSRegistration::minPasswordLength()) {
      throw new api_Exception("Password must have at least ". CMSRegistration::minPasswordLength() . " characters", 'password_has_not_enough_characters');
    }
    if (strlen($cmsUserParams["pass"]) > CMSRegistration::maxPasswordLength()) {
      throw new api_Exception("Password must have no more " . CMSRegistration::maxPasswordLength() . " characters", 'password_has_too_much_characters');
    }
    if (preg_match_all("/[a-z]/", $cmsUserParams["pass"]) < CMSRegistration::minPasswordLowerCase()) {
      throw new api_Exception("Password must have at least " . CMSRegistration::minPasswordLowerCase() . " characters in lower case", 'password_has_not_enough_characters_in_lower_case');
    }
    if (preg_match_all("/[A-Z]/", $cmsUserParams["pass"]) < CMSRegistration::minPasswordUpperCase()) {
      throw new api_Exception("Password must have at least " . CMSRegistration::minPasswordUpperCase() . " characters in upper case", 'password_has_not_enough_characters_in_upper_case');
    }
    if (preg_match_all("/[0-9]/", $cmsUserParams["pass"]) < CMSRegistration::minPasswordIntegers()) {
      throw new api_Exception("Password must have at least " . CMSRegistration::minPasswordIntegers() . " integers", 'password_has_not_enough_integers');
    }
    if (preg_match_all("/[^a-zA-Z0-9]/", $cmsUserParams["pass"]) < CMSRegistration::minPasswordSymbols()) {
      throw new api_Exception("Password must have at least " . CMSRegistration::minPasswordSymbols() . " symbols", 'password_has_not_enough_symbols');
    }
    if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]+$/ix", $cmsUserParams["mail"])) {
      throw new api_Exception("Invalid email", 'email_is_not_valid');
    }
    if (strlen($params["first_name"]) > CMSRegistration::maxFirstNameLength()) {
      throw new api_Exception("First name must have no more " . CMSRegistration::maxFirstNameLength() . " characters", 'first_name_has_too_much_characters');
    }
    if (strlen($params["last_name"]) > CMSRegistration::maxLastNameLength()) {
      throw new api_Exception("Last name must have no more " . CMSRegistration::maxLastNameLength() . " characters", 'last_name_has_too_much_characters');
    }

    $errors = [];
    $config->userSystem->checkUserNameEmailExists($cmsUserParams, $errors);
    if (!empty($errors)) {
      if (array_key_exists('cms_name', $errors)) {
        throw new api_Exception($errors['cms_name'], 'username_already_exists');
      }
      if (array_key_exists('email',$errors)) {
        throw new api_Exception($errors['email'], 'email_already_exists');
      }
      throw new api_Exception('Unknown error', 'unknown_email_error');
    }

    return [
      'cms_name' => $cmsUserParams['name'],
      'cms_pass' => $cmsUserParams['pass'],
      'email' => $params["email"],
      'first_name' => $params["first_name"],
      'last_name' => $params["last_name"],
    ];
  }

}
