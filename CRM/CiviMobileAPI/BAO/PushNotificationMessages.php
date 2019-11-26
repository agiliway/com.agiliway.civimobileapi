<?php

class CRM_CiviMobileAPI_BAO_PushNotificationMessages extends CRM_CiviMobileAPI_DAO_PushNotificationMessages {

  /**
   * Life time
   */
  const LIFE_TIME_IN_DAYS = 90;

  /**
   * Adds params to Push Notification Messages table
   *
   * @param $params
   *
   * @return \CRM_Core_DAO
   */
  public static function add(&$params) {
    $entity = new CRM_CiviMobileAPI_DAO_PushNotificationMessages();
    $entity->copyValues($params);
    return $entity->save();
  }

  /**
   * Creates new row in Push Notification Messages table
   *
   * @param $params
   *
   * @return \CRM_Core_DAO
   */
  public static function &create(&$params) {
    $transaction = new self();

    if (!empty($params['id'])) {
      CRM_Utils_Hook::pre('edit', self::getEntityName(), $params['id'], $params);
    }
    else {
      CRM_Utils_Hook::pre('create', self::getEntityName(), NULL, $params);
    }

    $entityData = self::add($params);

    if (is_a($entityData, 'CRM_Core_Error')) {
      $transaction->rollback();
      return $entityData;
    }

    $transaction->commit();

    if (!empty($params['id'])) {
      CRM_Utils_Hook::post('edit', self::getEntityName(), $entityData->id, $entityData);
    }
    else {
      CRM_Utils_Hook::post('create', self::getEntityName(), $entityData->id, $entityData);
    }

    return $entityData;
  }

  /**
   * Deletes row in Push Notification Messages table
   *
   * @param $id
   */
  public static function del($id) {
    $entity = new CRM_CiviMobileAPI_DAO_PushNotificationMessages();
    $entity->id = $id;
    $params = [];
    if ($entity->find(TRUE)) {
      CRM_Utils_Hook::pre('delete', self::getEntityName(), $entity->id, $params);
      $entity->delete();
      CRM_Utils_Hook::post('delete', self::getEntityName(), $entity->id, $entity);
    }
  }

  /**
   * Builds query for receiving data
   *
   * @param string $returnValue
   *
   * @return \CRM_Utils_SQL_Select
   */
  private static function buildSelectQuery($returnValue = 'rows') {
    $query = CRM_Utils_SQL_Select::from(CRM_CiviMobileAPI_DAO_PushNotificationMessages::getTableName());

    if ($returnValue == 'rows') {
      $query->select('
        id,
        contact_id,
        message,
        entity_table,
        entity_id,
        send_date,
        is_read
      ');
    }
    else {
      if ($returnValue == 'count') {
        $query->select('COUNT(id)');
      }
    }

    return $query;
  }

  /**
   * Builds 'where' condition for query
   *
   * @param $query
   * @param array $params
   *
   * @return mixed
   */
  private static function buildWhereQuery($query, $params = []) {
    if (!empty($params['contact_id'])) {
      $query->where('contact_id = #contact_id', ['contact_id' => $params['contact_id']]);
    }

    return $query;
  }

  /**
   * Gets all data
   *
   * @param array $params
   *
   * @return array
   */
  public static function getAll($params = []) {
    $query = self::buildWhereQuery(self::buildSelectQuery(), $params);

    return CRM_Core_DAO::executeQuery($query->toSQL())->fetchAll();
  }

  /**
   * Gets notifications by params
   *
   * @param $params
   *
   * @return array|bool
   * @throws \CRM_Core_Exception
   */
  public static function getNotifications($params) {
    $query = CRM_Utils_SQL_Select::from(self::getTableName());
    $query->select('*');

    if (!empty($params['contact_id'])) {
      $query->where('contact_id = #contact_id', ['contact_id' => $params['contact_id']]);
    }

    $query->orderBy($params['sort'] . ' ' . $params['direction']);

    if (isset($params['limit'])) {
      $offset = isset($params['offset']) ? $params['offset'] : 0;
      $query->limit($params['limit'], $offset);
    }

    $results = CRM_Core_DAO::executeQuery($query->toSQL());
    $data = $results->fetchAll();

    return !empty($data) ? $data : FALSE;
  }

  /**
   * Deletes older than count of days
   *
   * @param $day
   */
  public static function deleteOlderThan($day) {
    $query = '
      DELETE FROM civicrm_contact_push_notification_messages 
      WHERE send_date < NOW() - INTERVAL %1 DAY;
    ';

    CRM_Core_DAO::singleValueQuery($query, [
      1 => [$day, 'Integer']
    ]);
  }

}
