<?php

/**
 * Class provide permission helper methods
 */
class CRM_CiviMobileAPI_Utils_Permission {

  /**
   * CiviCRM Permission. It mean User can only update Participant status to 'register' or 'attended'
   *
   * @var string
   */
  const CAN_CHECK_IN_ON_EVENT = 'can check in on event';

  /**
   * Check if user can manage Participant
   *
   * @param $eventCreatorId
   *
   * @return bool
   */
  public static function isUserCanManageParticipant($eventCreatorId) {
    $loginContactId = CRM_CiviMobileAPI_Utils_Contact::getCurrentContactId();
    if (!empty($eventCreatorId) && $eventCreatorId == $loginContactId) {
      return true;
    }

    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    return false;
  }

  /**
   * Validates if enough permission for change Participant status
   * from 'Register' to 'Attended' or vice versa
   *
   * @return bool
   */
  public static function isEnoughPermissionForChangingParticipantStatuses() {
    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    if (CRM_Core_Permission::check(CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT) || CRM_Core_Permission::check('edit all events')) {
      return true;
    }

    return false;
  }

  /**
   * Validates if enough permission for view my tickets
   *
   * @return bool
   */
  public static function isEnoughPermissionForViewMyTickets() {
    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    if (CRM_Core_Permission::check('access CiviCRM')
      && CRM_Core_Permission::check('access CiviEvent')
      && CRM_Core_Permission::check('view event info')
    ) {
      return true;
    }

    return false;
  }

  /**
   * Is enough permission for create participant with payment
   *
   * @return bool
   */
  public static function isEnoughPermissionForCreateParticipantWithPayment() {
    //TODO
    return true;
  }

  /**
   * Is enough permission for delete ContactGroup entity
   *
   * @return bool
   */
  public static function isEnoughPermissionForDeleteContactGroup() {
    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    if (CRM_Core_Permission::check('access CiviCRM')
      && CRM_Core_Permission::check('edit all contacts')
      && CRM_Core_Permission::check('view my contact')
    ) {
      return true;
    }

    return false;
  }

  /**
   * Is enough permission for get available ContactGroups in create select
   *
   */
  public static function isEnoughPermissionForGetAvailableContactGroup() {
    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    if (CRM_Core_Permission::check('access CiviCRM')
        && (CRM_Core_Permission::check('edit my contact')
          || CRM_Core_Permission::check('view all contacts')
          || CRM_Core_Permission::check('view my contact')
          || CRM_Core_Permission::check('edit all contacts')
        )
    ) {
      return true;
    }

    return false;
  }

  /**
   * Is enough permission tag structure
   */
  public static function isEnoughPermissionForGetTagStructure() {
    if (CRM_Core_Permission::check('administer CiviCRM')) {
      return true;
    }

    if (CRM_Core_Permission::check('access CiviCRM')
        && (CRM_Core_Permission::check('edit my contact')
          || CRM_Core_Permission::check('view all contacts')
          || CRM_Core_Permission::check('view my contact')
          || CRM_Core_Permission::check('edit all contacts')
        )
    ) {
      return true;
    }

    return false;
  }

}
