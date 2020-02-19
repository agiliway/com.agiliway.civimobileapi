<?php

class CRM_CiviMobileAPI_Api_CiviMobileParticipantPaymentLink_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * @return array
   * @throws api_Exception
   */
  public function getResult() {
    $cmbHash = md5(time() . rand(0, 10000). time());
    $result = [];
    $participantPublicKey = NULL;

    if ($tmpData = CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::checkByEvent(
      $this->validParams['event_id'],
      $this->validParams['contact_id'],
      $this->validParams['first_name'],
      $this->validParams['last_name'],
      $this->validParams['email']
    )) {
      if (!CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::deleteByHash($tmpData['cmb_hash'])) {
        throw new api_Exception(ts('Previous Payment cannot be deleted (hash=%1). Please complete payment', [1 => $tmpData['cmb_hash']]), 'participant_previous_payment_not_completed');
      }
    }

    if (!$this->validParams['contact_id']) {
      $participantPublicKey = CRM_CiviMobileAPI_Utils_Participant::generatePublicKey($cmbHash);
      $result['participantPublicKey'] = $participantPublicKey;
    }

    CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::setInfoData(
      $this->validParams['event_id'],
      $this->validParams['contact_id'],
      $cmbHash,
      json_encode($this->validParams['price_set']),
      $this->validParams['first_name'],
      $this->validParams['last_name'],
      $this->validParams['email'],
      $participantPublicKey
    );

    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $url = CRM_Utils_System::url('civicrm/event/register', 'id=' . $this->validParams['event_id'] . '&reset=1&cmbHash=' . $cmbHash);
    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      $url = str_replace("administrator/", "", $url);
    }

    $result['link'] = html_entity_decode($url);

    return $result;
  }

  /**
   * @param $params
   * @return array
   * @throws CiviCRM_API3_Exception
   * @throws api_Exception
   */
  protected function getValidParams($params) {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA) {
      throw new api_Exception(ts('Joomla does not support that functionality'), 'joomla_does_not_support_that_functionality');
    }

    $eventId = (int) $params['event_id'];
    $contactId = (int) $params['contact_id'];
    $selPriceSet = ((is_array($params['price_set'])) ? $params['price_set'] : json_decode($params['price_set'],true));

    try {
      civicrm_api3('Event', 'getsingle', ['id' => $eventId]);
    } catch (CiviCRM_API3_Exception $e) {
      throw new api_Exception(ts('Event(id = %1) User can not be registered because event does not exist.', $eventId), 'event_does_not_exist');
    }

    if ($contactId) {
      $participants = civicrm_api3('Participant', 'get', [
        'return' => ['contact_id'],
        'event_id' => $eventId,
        'contact_id' => $contactId,
        'sequential' => 1,
      ]);
      if (!empty($participants['values'])) {
        throw new api_Exception(ts('Contact(id = %1) is already registered on the Event(id = %2).', [1 => $contactId, 2 => $eventId]), 'participant_already_exist');
      }

      try {
        civicrm_api3('Contact', 'getsingle', ['id' => $contactId]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new api_Exception(ts('Contact(id = %1) does not exist.', [1 => $contactId]), 'contact_does_not_exist');
      }
    } else {
      if (empty($params['first_name'])) {
        throw new api_Exception(ts('First name \'%1\' cannot be empty.', [1 => $params['first_name']]), 'contact_has_invalid_first_name');
      }

      if (empty($params['last_name'])) {
        throw new api_Exception(ts('Last name \'%1\' cannot be empty.', [1 => $params['last_name']]), 'contact_has_invalid_last_name');
      }

      if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
        throw new api_Exception(ts('Email \'%1\' have to be valid.', [1 => $params['email']]), 'contact_has_invalid_email');
      }
    }

    $priceSetId = CRM_Price_BAO_PriceSet::getFor(CRM_Event_BAO_Event::getTableName(), $eventId);
    if (empty($priceSetId)) {
      throw new api_Exception(ts('Can not get price set assigned to event.'), 'event_empty_price_set');
    }

    $priceSet = $this->getPriceSet($priceSetId);
    if (empty($priceSet)) {
      throw new api_Exception(ts('Can not get price set assigned to event.'), 'event_empty_price_set');
    }

    $priceSetFields = CRM_CiviMobileAPI_Utils_PriceSet::getFields($priceSetId);
    if (empty($priceSetFields) && empty($priceSetFields['values'])) {
      throw new api_Exception(ts('Can not get price set fields assigned to event.'), 'event_empty_price_set_fields');
    }

    $this->validatePriceSetItems($selPriceSet, $priceSetFields['values']);

    return [
      'event_id' => $eventId,
      'contact_id' => $contactId,
      'price_set' => $params['price_set'],
      'first_name' => $params['first_name'],
      'last_name' => $params['last_name'],
      'email' => $params['email'],
    ];
  }

  /**
   * @param $priceSetId
   * @return array|bool
   */
  private function getPriceSet($priceSetId) {
    try {
      $priceSet = civicrm_api3('PriceSet', 'getsingle', [
        'sequential' => 1,
        'id' => $priceSetId
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return $priceSet;
  }

  /**
   * @param $selPriceSet
   * @param $priceSetFields
   * @throws api_Exception
   */
  private function validatePriceSetItems($selPriceSet, $priceSetFields) {
    if (!is_array($selPriceSet)) {
      throw new api_Exception(ts('Can not parse selected price set'), 'can_not_parse_selected_price_set');
    }

    if (!empty($selPriceSet)) {
      foreach ($selPriceSet as $psId => $psFieldIds) {
        foreach ($psFieldIds[0] as $psFieldId => $psFieldValues) {
          if (empty($psFieldValues)) {
            throw new api_Exception(ts('Can not parse selected value for field (id = %1) for price set (id = %2)', [1 => $psFieldId, 2 => $psId]), 'field_value_does_not_exist');
          }

          $priceField = $this->findPriceSetFiled($priceSetFields, $psId, $psFieldId);
          if (empty($priceField)) {
            throw new api_Exception(ts('Price Field (id = %1) does not exist for Event\'s', [1 => $psFieldId]), 'field_id_does_not_exist');
          }

          $priceSetFieldValues = $this->getPriceSetFieldValues($priceField['id']);
          if (empty($priceSetFieldValues['values'])) {
            throw new api_Exception(ts('Empty filed values for price set field (id = %1). Please create it in administer.', [1 => $priceField['id']]), 'empty_price_set_field_values');
          }

          foreach ($psFieldValues as $item => $psFieldValueId) {
            if ($priceField['html_type'] == 'Text' && empty($psFieldValueId[key($psFieldValueId)])) {
              throw new api_Exception(ts('"filed_value_count" must be filled for field with "Text" "html_type"'), 'invalid_filed_value_count');
            }

            $priceSetFieldValue = $this->findPriceSetFiledValue($priceSetFieldValues['values'], key($psFieldValueId));
            if (empty($priceSetFieldValue)) {
              throw new api_Exception(ts('Not valid value(id = %1) for price set field (id = %2).', [1 => key($psFieldValueId), 2 => $psFieldId]), 'not_valid_value_for_price_set_field');
            }
          }
        }

        foreach ($priceSetFields as $priceSetField) {
          if ($priceSetField['is_required'] == 1  && !in_array($priceSetField['id'], array_keys($psFieldIds[0]))) {
            throw new api_Exception(ts('Price field (id = %1) is required field for price set(id = %2)', [1 => $priceSetField['id'], 2 => $priceSetField['price_set_id']]), 'required_filed_for_price_set');
          }
        }
      }
    }
  }

  /**
   * Finds 'price set field' in list by 'price set field id'
   *
   * @param $priceSetFields
   * @param $psId
   * @param $psFieldId
   * @return bool
   */
  private function findPriceSetFiled($priceSetFields, $psId, $psFieldId) {
    foreach ($priceSetFields as $field) {
      if ($field['id'] == $psFieldId && $field['price_set_id'] == $psId) {
        return $field;
      }
    }

    return false;
  }

  /**
   * Gets price set field value
   *
   * @param $priceSetFieldId
   * @return array|bool
   */
  private function getPriceSetFieldValues($priceSetFieldId) {
    try {
      $priceFieldValue = civicrm_api3('PriceFieldValue', 'get', [
        'sequential' => 1,
        'price_field_id' => $priceSetFieldId,
        'is_active' => 1
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return $priceFieldValue;
  }

  /**
   * Finds 'price set field value' in list by 'price set field value id'
   *
   * @param $priceSetFieldValues
   * @param $valueId
   * @return bool
   */
  private function findPriceSetFiledValue($priceSetFieldValues, $valueId) {
    foreach ($priceSetFieldValues as $fieldValue) {
      if ($fieldValue['id'] == $valueId) {
        return $fieldValue;
      }
    }

    return false;
  }

}
