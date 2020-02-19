<?php

class CRM_CiviMobileAPI_DAO_PushNotificationMessages extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $tableName = 'civicrm_contact_push_notification_messages';

  /**
   * Static entity name.
   *
   * @var string
   */
  static $entityName = 'PushNotificationMessages';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log
   * table.
   *
   * @var boolean
   */
  static $log = TRUE;

  /**
   * Unique id of current row
   *
   * @var int
   */
  public $id;

  /**
   * Id of current contact
   *
   * @var int
   */
  public $contactId;

  /**
   * Send Data
   *
   * @var string
   */
  public $data;

  /**
   * Send Message
   *
   * @var string
   */
  public $message;

  /**
   * Current entity table name
   *
   * @var string
   */
  public $entityTable;

  /**
   * Current entity id
   *
   * @var int
   */
  public $entityId;

  /**
   * Date of sending message
   *
   * @var string
   */
  public $sendDate;

  /**
   * Is message read
   *
   * @var int
   */
  public $isRead;

  /**
   * Id of contact who invoke push notification
   *
   * @var int
   */
  public $invokeContactId;

  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName() {
    return self::$tableName;
  }

  /**
   * Returns entity name
   *
   * @return string
   */
  static function getEntityName() {
    return self::$entityName;
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('id'),
          'description' => 'id',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'contactId' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact id'),
          'description' => 'Contact id',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.contact_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'data' => [
          'name' => 'data',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Data'),
          'description' => 'Data',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.data',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'message' => [
          'name' => 'message',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Message'),
          'description' => 'Message',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.message',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'entityTable' => [
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Entity table'),
          'description' => 'Entity table',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.entity_table',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'entityId' => [
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Entity id'),
          'description' => 'Entity id',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.entity_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'sendDate' => [
          'name' => 'send_date',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Send date'),
          'description' => 'Send date',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.send_date',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'isRead' => [
          'name' => 'is_read',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Is read'),
          'description' => 'Is read',
          'required' => FALSE,
          'import' => TRUE,
          'where' => self::getTableName() . '.is_read',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
        'invokeContactId' => [
          'name' => 'invoke_contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Invoke Contact Id'),
          'description' => 'Invoke Contact Id',
          'required' => FALSE,
          'import' => TRUE,
          'where' => self::getTableName() . '.invoke_contact_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotificationMessages',
        ],
      ];

      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }

    return Civi::$statics[__CLASS__]['fields'];
  }

}
