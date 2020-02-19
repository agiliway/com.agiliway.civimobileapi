<?php

class CRM_CiviMobileAPI_DAO_CivimobileEventPaymentInfo extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $_tableName = 'civicrm_civimobile_event_payment_info';

  /**
   * Static entity name.
   *
   * @var string
   */
  static $entityName = 'CivimobileEventPaymentInfo';

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
   * Id of Event
   *
   * @var int
   */
  public $event_id;

  /**
   * Unique user payment identifier
   *
   * @var string
   */
  public $cmb_hash;

  /**
   * Current event price set
   *
   * @var string
   */
  public $price_set;

  /**
   * Current event participant
   *
   * @var string
   */
  public $contact_id;

  /**
   * Anonymous participant first name
   *
   * @var string
   */
  public $first_name;

  /**
   * Anonymous participant last name
   *
   * @var string
   */
  public $last_name;

  /**
   * Anonymous participant email
   *
   * @var string
   */
  public $email;

  /**
   * Anonymous participant public_key
   *
   * @var string
   */
  public $public_key;

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
          'title' => ts('Id'),
          'description' => ts('Id'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'event_id' => [
          'name' => 'event_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Event Id'),
          'description' => ts('Event Id'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.event_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'cmb_hash' => [
          'name' => 'cmb_hash',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('CMB Hash'),
          'description' => ts('CMB Hash'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.hash',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'price_set' => [
          'name' => 'price_set',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Price Set'),
          'description' => ts('Price Set'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.price_set',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact Id'),
          'description' => ts('Contact Id'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.contact_id',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'first_name' => [
          'name' => 'first_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('First Name'),
          'description' => ts('First Name'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.first_name',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'last_name' => [
          'name' => 'last_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Last Name'),
          'description' => ts('Last Name'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.last_name',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'email' => [
          'name' => 'email',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Email'),
          'description' => ts('Email'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.email',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
        'public_key' => [
          'name' => 'public_key',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Public Key'),
          'description' => ts('Public Key'),
          'required' => TRUE,
          'import' => TRUE,
          'where' => self::getTableName() . '.public_key',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => TRUE,
          'table_name' => self::getTableName(),
          'entity' => self::getEntityName(),
          'bao' => 'CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo',
        ],
      ];

      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }

    return Civi::$statics[__CLASS__]['fields'];
  }

}
