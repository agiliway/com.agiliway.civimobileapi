<?php

class CRM_CiviMobileAPI_Install_Entity_CustomField extends CRM_CiviMobileAPI_Install_Entity_EntityBase {

  /**
   * Entity name
   *
   * @var string
   */
  protected $entityName = 'CustomField';

  /**
   * Custom Field name
   *
   * @var string
   */
  const IS_QR_USED = 'civi_mobile_is_qr_used';
  const QR_EVENT_ID = 'civi_mobile_qr_event_id';
  const QR_CODE = 'civi_mobile_qr_code';
  const QR_IMAGE = 'civi_mobile_qr_image';
  const PUBLIC_KEY = 'public_key';

  /**
   * Params for checking Entity existence
   *
   * @var array
   */
  protected $entitySearchParamNameList = ['name', 'custom_group_id'];

  /**
   * Sets entity Param list
   */
  protected function setEntityParamList() {
    $this->entityParamList = [
      [
        'name' => self::IS_QR_USED,
        'label' => ts('Is qr code used for this Event?'),
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_USES,
        'html_type' => 'Radio',
        'data_type' => 'Boolean',
        'default_value' => 0,
        'is_view' => 1,
      ],
      [
        'name' => self::QR_EVENT_ID,
        'label' => ts('QR Event id'),
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES,
        'html_type' => 'Text',
        'data_type' => 'String',
        'default_value' => 0,
        'is_view' => 1,
      ],
      [
        'name' => self::QR_CODE,
        'label' => ts('Qr hash code'),
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES,
        'html_type' => 'Text',
        'data_type' => 'String',
        'default_value' => 0,
        'is_view' => 1,
      ],
      [
        'name' => self::QR_IMAGE,
        'label' => ts('QR image url'),
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES,
        'html_type' => 'Text',
        'data_type' => 'String',
        'default_value' => 0,
        'is_view' => 1,
      ],
      [
        'name' => self::PUBLIC_KEY,
        'label' => ts('Public key'),
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::PUBLIC_INFO,
        'html_type' => 'Text',
        'data_type' => 'String',
        'is_view' => 1,
      ],
    ];
  }

}
