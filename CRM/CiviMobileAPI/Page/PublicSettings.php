<?php

use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;
use CRM_CiviMobileAPI_Utils_Cms_Registration as CMSRegistration;

class CRM_CiviMobileAPI_Page_PublicSettings extends CRM_Core_Page {

  /**
   * CRM_CiviMobileAPI_Page_PublicSettings constructor.
   */
  public function __construct() {
    civimobileapi_secret_validation();
    parent::__construct();
  }

  public function run() {
    if (CRM_CiviMobileAPI_Authentication_AuthenticationHelper::isRequestValid()) {

      $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
      if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
        module_load_include('pages.inc', 'user');
        user_logout_current_user();
      }

      $settings = [
        'is_allow_public_info_api' => CRM_CiviMobileAPI_Utils_Extension::isAllowPublicInfoApi(),
        'is_allow_public_website_url_qrcode' => CRM_CiviMobileAPI_Utils_Extension::isAllowPublicWebisteURLQRCode(),
        'site_name' => CRM_CiviMobileAPI_Utils_Extension::getSiteName(),
        'is_wp_rest_plugin_active' => (int) (new CRM_CiviMobileAPI_Utils_RestPath())->isWpRestPluginActive(),
        'cms' => CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem(),
        'crm_version' => CRM_Utils_System::version(),
        'civicrm_enable_components' => CRM_CiviMobileAPI_Utils_CiviCRM::getEnabledComponents(),
        'ext_version' => CRM_CiviMobileAPI_Utils_VersionController::getInstance()->getCurrentFullVersion(),
        'is_civimobile_ext_has_right_folder_name' => (int) CRM_CiviMobileAPI_Utils_Extension::hasExtensionRightFolderName(),
        'is_allow_cms_registration' => CRM_CiviMobileAPI_Utils_Extension::isAllowCmsRegistration(),
        'is_showed_events_in_public_area' => CRM_CiviMobileAPI_Utils_Extension::isShowedEventsInPublicArea(),
        'is_showed_news_in_public_area' => CRM_CiviMobileAPI_Utils_Extension::isShowedNewsInPublicArea(),
        'news_rss_feed_url' => CRM_CiviMobileAPI_Utils_Extension::newsRssFeedUrl(),
        'cms_registration_requirements' => [
          'min_password_length' => CMSRegistration::minPasswordLength(),
          'max_password_length' => CMSRegistration::maxPasswordLength(),
          'min_username_length' => CMSRegistration::minUsernameLength(),
          'max_username_length' => CMSRegistration::maxUsernameLength(),
          'min_password_integers' => CMSRegistration::minPasswordIntegers(),
          'min_password_symbols' => CMSRegistration::minPasswordSymbols(),
          'min_password_upper_case' => CMSRegistration::minPasswordUpperCase(),
          'min_password_lower_case' => CMSRegistration::minPasswordLowerCase(),
          'max_first_name_length' => CMSRegistration::maxFirstNameLength(),
          'max_last_name_length' => CMSRegistration::maxLastNameLength(),
        ],
        'permissions' => CRM_CiviMobileAPI_Utils_Permission::getAnonymous(),
      ];

      JsonResponse::sendSuccessResponse($settings);
    }
  }

}
