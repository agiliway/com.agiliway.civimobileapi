<?php

class CRM_CiviMobileAPI_Install_Entity_CustomGroup extends CRM_CiviMobileAPI_Install_Entity_EntityBase {

  /**
   * Custom Group name
   *
   * @var string
   */
  const QR_USES = 'civi_mobile_qr_uses';
  const QR_CODES = 'civi_mobile_qr_codes';
  const CONTACT_SETTINGS = 'contact_settings';

  /**
   * Entity name
   *
   * @var string
   */
  protected $entityName = 'CustomGroup';

  /**
   * Params for checking Entity existence
   *
   * @var array
   */
  protected $entitySearchParamNameList = ['name'];

  /**
   * Sets entity Param list
   */
  protected function setEntityParamList() {
    $this->entityParamList = [
      [
        'name' => self::QR_USES,
        'title' => ts('Qr options'),
        'extends' => 'Event',
        'is_public' => 0,
      ],
      [
        'name' => self::QR_CODES,
        'title' => ts('Qr codes'),
        'extends' => 'Participant',
        'is_public' => 0,
      ],
      [
        'name' => self::CONTACT_SETTINGS,
        'title' => ts('Contact Settings'),
        'extends' => 'Contact',
        'is_public' => 1,
        'table_name' => 'civicrm_contact_settings',
        'style' => 'Inline',
        'weight' => 1,
        'is_active' => 1,
        'collapse_display' => 1,
        'collapse_adv_display' => 1,
        'is_reserved' => 0,
        'is_multiple' => 0
      ]
    ];
  }

  /**
   * Disables by id
   *
   * @param $entityId
   */
  protected function disable($entityId) {
    CRM_Core_BAO_CustomGroup::setIsActive((int) $entityId, 0);
  }

  /**
   * Enables by id
   *
   * @param $entityId
   */
  protected function enable($entityId) {
    CRM_Core_BAO_CustomGroup::setIsActive((int) $entityId, 1);
  }

}
