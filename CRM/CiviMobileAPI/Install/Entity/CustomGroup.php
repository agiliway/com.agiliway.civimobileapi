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
  const PUBLIC_INFO = 'civi_mobile_public_info';

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
        'name' => self::PUBLIC_INFO,
        'title' => ts('Public Info'),
        'extends' => 'Participant',
        'is_public' => 0,
      ],
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
