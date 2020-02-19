<?php

use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

class CRM_CiviMobileAPI_Page_PublicApi_Middleware {

  /**
   * Sets params for showing only public Events
   * @param $params
   * @return mixed
   */
  public static function showOnlyPublicEvents($params) {
    $params['is_public'] = 1;

    return $params;
  }

  /**
   * Sets params for showing only is active objects
   *
   * @param $params
   * @return mixed
   */
  public static function showOnlyActive($params) {
    $params['is_active'] = 1;

    return $params;
  }

  /**
   * Forbidden for Joomla
   *
   * @param $params
   * @return mixed
   */
  public static function forbiddenForJoomla($params) {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA) {
      JsonResponse::sendErrorResponse("Joomla does not support that functionality.", 'joomla_cms', 'joomla_does_not_support_that_functionality');
    }

    return $params;
  }

  /**
   * Validates OptionGroup names
   *
   * @param $params
   * @return mixed
   */
  public static function validateOptionGroupName($params) {
    if (empty($params['option_group_id'])) {
      JsonResponse::sendErrorResponse(ts("'option_group_id' is required field."), 'option_group_id', 'required_field');
    }

    $availableOptionGroupId = [
      'event_type',
      'participant_role',
    ];

    if (!in_array($params['option_group_id'], $availableOptionGroupId)) {
      JsonResponse::sendErrorResponse(ts("Not allow value field for 'option_group_id' field."), 'option_group_id', 'not_allow_value');
    }

    return $params;
  }

  /**
   * Checks if is allow cms registration
   *
   * @param $params
   * @return mixed
   */
  public static function isAllowCmsRegistration($params) {
    if (!CRM_CiviMobileAPI_Utils_Extension::isAllowCmsRegistration()) {
      JsonResponse::sendErrorResponse("Not allow registration on CMS", 'email', 'not_allow_cms_registration');
    }

    return $params;
  }

  /**
   * Checks if is allow cms registration
   *
   * @param $params
   * @return mixed
   */
  public static function isAllowPublicInfoApi($params) {
    if (!CRM_CiviMobileAPI_Utils_Extension::isAllowPublicInfoApi()) {
      JsonResponse::sendErrorResponse("Not allow api with public info.", 'email', 'not_allow_public_info_api');
    }

    return $params;
  }

  /**
   * Validates OptionGroup names
   *
   * @param $params
   * @return mixed
   */
  public static function isPublicEvent($params) {
    if (empty($params['event_id'])) {
      JsonResponse::sendErrorResponse(ts("'event_id' is required field."), 'event_id', 'required_field');
    }

    $event = new CRM_Event_BAO_Event();
    $event->id = $params['event_id'];
    $event->is_public = 1;
    $eventExistence = $event->find(TRUE);
    if (empty($eventExistence)) {
      JsonResponse::sendErrorResponse(ts('Event(id = %1) does not exist or is not public.', [1 => $params['event_id']]), 'event_id', 'public_event_does_not_exist');
    }

    return $params;
  }


}
