<?php

class CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo extends CRM_CiviMobileAPI_DAO_CivimobileEventPaymentInfo {

  /**
   * Add payment event info
   * @param $params
   * @return CRM_Core_DAO
   */
  public static function add(&$params) {
    $entity = new CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo();
    $entity->copyValues($params);
    return $entity->save();
  }

  /**
   * Create payment event info
   * @param $params
   * @return CRM_Core_DAO
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
   * Delete payment event info
   * @param $id
   */
  public static function del($id) {
    $entity = new CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo();
    $entity->id = $id;
    $params = [];
    if ($entity->find(TRUE)) {
      CRM_Utils_Hook::pre('delete', self::getEntityName(), $entity->id, $params);
      $entity->delete();
      CRM_Utils_Hook::post('delete', self::getEntityName(), $entity->id, $entity);
    }
  }

  /**
   * @param $cmbHash
   * @return array
   */
  public static function getByHash($cmbHash) {
    $entity = new self();
    $entity->cmb_hash = $cmbHash;
    if ($entity->find(true)) {
      return [
        'id' => $entity->id,
        'event_id' => $entity->event_id,
        'cmb_hash' => $entity->cmb_hash,
        'price_set' => $entity->price_set,
        'contact_id' => $entity->contact_id,
        'first_name' => $entity->first_name,
        'last_name' => $entity->last_name,
        'email' => $entity->email,
        'public_key' => $entity->public_key,
      ];
    }

    return [];
  }

  /**
   * @param $eventId
   * @param $contactId
   * @param $firstName
   * @param $lastName
   * @param $email
   * @return array
   */
  public static function checkByEvent($eventId, $contactId, $firstName, $lastName, $email) {
    $entity = new self();
    $entity->event_id = $eventId;
    $entity->contact_id = $contactId;
    $entity->first_name = $firstName;
    $entity->last_name = $lastName;
    $entity->email = $email;
    if ($entity->find(true)) {
      return [
        'cmb_hash' => $entity->cmb_hash,
      ];
    }

    return [];
  }

  /**
   * Deletes by hash
   *
   * @param $cmbHash
   * @return bool
   */
  public static function deleteByHash($cmbHash) {
    $participant = static::getByHash($cmbHash);

    if (!empty($participant)) {
      self::del($participant['id']);
      return true;
    }

    return false;
  }

  /**
   * @param $eventId
   * @param $contactIds
   * @param $cmbHash
   * @param $priceSet
   * @param $firstName
   * @param $lastName
   * @param $email
   * @param $publicKey
   */
  public static function setInfoData($eventId, $contactIds, $cmbHash, $priceSet, $firstName, $lastName, $email, $publicKey) {
    $participantData = static::getByHash($cmbHash);
    $params = [
      'event_id' =>$eventId,
      'contact_id' => $contactIds,
      'price_set' => $priceSet,
      'first_name' => $firstName,
      'last_name' => $lastName,
      'email' => $email,
      'public_key' => $publicKey,
    ];

    if (!empty($participantData)) {
      $params['id'] = $participantData['id'];
    } else {
      $params['cmb_hash'] = $cmbHash;
    }

    self::create($params);
  }

}
