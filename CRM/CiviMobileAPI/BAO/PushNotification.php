<?php

class CRM_CiviMobileAPI_BAO_PushNotification extends CRM_CiviMobileAPI_DAO_PushNotification {

  /**
   * Adds params to Push Notification table
   *
   * @param $params
   *
   * @return \CRM_Core_DAO
   */
  public static function add(&$params) {
    $entity = new CRM_CiviMobileAPI_DAO_PushNotification();
    $entity->copyValues($params);
    return $entity->save();
  }

  /**
   * Creates new row in Push Notification table
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
   * Deletes row in Push Notification table
   *
   * @param $id
   */
  public static function del($id) {
    $entity = new CRM_CiviMobileAPI_DAO_PushNotification();
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
    $query = CRM_Utils_SQL_Select::from(CRM_CiviMobileAPI_DAO_PushNotification::getTableName());

    if ($returnValue == 'rows') {
      $query->select('
        id,
        contact_id,
        token,
        platform,
        created_date,
        modified_date,
        is_active
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
    if (!empty($params['token'])) {
      $query->where('token = @token', ['token' => $params['token']]);
    }
    if (!empty($params['platform'])) {
      $query->where('platform = @platform', ['platform' => $params['platform']]);
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

}
