<?php

class CRM_CiviMobileAPI_DAO_PushNotification extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $_tableName = 'civicrm_contact_push_notification';

  /**
   * Static entity name.
   *
   * @var string
   */
  static $entityName = 'PushNotification';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log
   * table.
   *
   * @var boolean
   */
  static $_log = TRUE;

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
  public $contact_id;

  /**
   * Unique user identifier
   *
   * @var string
   */
  public $token;

  /**
   * Current user platform
   *
   * @var string
   */
  public $platform;

  /**
   * Date of creation
   *
   * @var string
   */
  public $created_date;

  /**
   * Date of last update
   *
   * @var string
   */
  public $modified_date;

  /**
   * Is row active
   *
   * @var int
   */
  public $is_active;

  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName() {
    return self::$_tableName;
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
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'contact_id' => [
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
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'token' => [
          'name' => 'token',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Token'),
          'description' => 'Token',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.token',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'platform' => [
          'name' => 'platform',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Platform'),
          'description' => 'Platform',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.platform',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'created_date' => [
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Create date'),
          'description' => 'Create date',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.created_date',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'modified_date' => [
          'name' => 'modified_date',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Update date'),
          'description' => 'Update date',
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.modified_date',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
        'is_active' => [
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Is active'),
          'description' => 'Is active',
          'required' => FALSE,
          'import' => TRUE,
          'where' => self::getTableName() . '.is_active',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_PushNotification',
        ],
      ];

      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }

    return Civi::$statics[__CLASS__]['fields'];
  }
}