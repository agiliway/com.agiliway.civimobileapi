<?php

/**
 * Class handles CiviMobileParticipantPaymentCreate api
 */
class CRM_CiviMobileAPI_Api_CiviMobileParticipantPayment_Create {

  /**
   * Api request params
   */
  private $params;

  /**
   * Contact BAO object
   */
  private $contact;

  /**
   * Event BAO object
   */
  private $event;

  /**
   * CRM_CiviMobileAPI_Utils_Api_CiviMobileParticipantPayment constructor.
   *
   * @param $params
   */
  public function __construct($params) {
    $this->params = $params;
  }

  /**
   * Returns results to api
   *
   * @return array
   * @throws \API_Exception
   */
  public function getResult() {
    $validParams = $this->getValidParams();

    $participant = civicrm_api3('Participant', 'create', $validParams['participant_params']);

    if (!empty($validParams['price_set_selected_values']) && !empty($validParams['participant_params']['fee_amount'])) {
      $contribution = civicrm_api3('Contribution', 'create', [
        'financial_type_id' => $validParams['event_financial_type_id'],
        'total_amount' => $validParams['participant_params']['fee_amount'],
        'contact_id' => $this->contact->id,
        'payment_instrument_id' => "Check",
        'contribution_status_id' => "Pending",
      ]);

      $participantPayment = civicrm_api3('ParticipantPayment', 'create', [
        'participant_id' => $participant['id'],
        'contribution_id' => $contribution['id'],
      ]);
    }

    $result = [
      [
        'participant_id' => $participant['id'],
        'contact_id' => $this->contact->id,
        'event_id' => $this->event->id,
        'contribution_id' => (!empty($contribution['id'])) ? $contribution['id'] : NULL,
        'participant_payment_id' =>  (!empty($participantPayment['id'])) ? $participantPayment['id'] : NULL,
        'participant_payment_fee_amount' => $validParams['participant_params']['fee_amount'],
        'participant_payment_fee_level' => $validParams['participant_params']['fee_level'],
        'is_send_event_confirmation_receipt' => $validParams['send_confirmation']
      ]
    ];

    if ($validParams['send_confirmation'] == 1) {
      if ($this->contact->id == CRM_CiviMobileAPI_Utils_Contact::getCurrentContactId()) {
        CRM_CiviMobileAPI_Utils_Emails_EventConfirmationReceipt::send($participant['id'],'event_online_receipt');
      } else {
        CRM_CiviMobileAPI_Utils_Emails_EventConfirmationReceipt::send($participant['id'], 'event_offline_receipt');
      }
    }

    return $result;
  }

  /**
   * Validates and returns valid params
   *
   * @throws \API_Exception
   */
  private function getValidParams() {
    $this->setContact($this->params['contact_id']);
    if (empty($this->contact)) {
      throw new api_Exception('Contact(id=' . $this->params['contact_id'] . ') does not exist.', 'contact_does_not_exist');
    }

    $this->setEvent($this->params['event_id']);
    if (empty($this->event)) {
      throw new api_Exception('Event(id=' . $this->params['event_id'] . ') does not exist.', 'event_does_not_exist');
    }

    if (empty($this->event->is_monetary)) {
      throw new api_Exception('Event is not monetary.', 'event_not_monetary');
    }

    if ($this->isContactAlreadyRegistered($this->params['contact_id'], $this->params['event_id'])) {
      throw new api_Exception(ts('This contact has already been assigned to this event.'), 'contact_already_registered');
    }

    $validParams = $this->getExpectedParams();

    $priceSetId = CRM_Price_BAO_PriceSet::getFor(CRM_Event_BAO_Event::getTableName(), $this->event->id);
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

    $validParams['price_set_selected_values'] = $this->validatePriceSetItems($validParams['price_set_selected_values'], $priceSetFields['values']);
    $feeData = $this->calculateFeeData($validParams['price_set_selected_values']);
    $validParams['participant_params']['fee_amount'] = $feeData['fee_amount'];
    $validParams['participant_params']['fee_level'] = $feeData['fee_level'];

    return $validParams;
  }

  /**
   * Validates Price set fields
   *
   * @param $selectedValues
   * @param $priceSetFields
   *
   * @return array
   */
  private function validatePriceSetItems($selectedValues, $priceSetFields) {
    $validSelectedValues = [];
    $validFieldIds = [];

    if (!is_array($selectedValues)) {
      throw new api_Exception('Can not parse selected values for price set.', 'can_not_parse_price_set_selected_values');
    }

    if (!empty($selectedValues)) {
      foreach ($selectedValues as $index => $selectedValue) {
        if (empty($selectedValue)) {
          throw new api_Exception('Can not parse selected values for price set. In item in "' . $index . '" position.', 'can_not_parse_price_set_selected_values');
        }

        if (empty($selectedValue['field_id'])) {
          throw new api_Exception('Can not parse selected values for price set. In item in "' . $index . '" position. Required filed "field_id"', 'field_id_is_required_filed');
        }

        if (empty($selectedValue['filed_value_id'])) {
          throw new api_Exception('Can not parse selected values for price set. In item in "' . $index . '" position. Required filed "filed_value_id"', 'filed_value_id_is_required_filed');
        }

        if (empty($selectedValue['filed_value_count'])) {
          throw new api_Exception('Can not parse selected values for price set. In item in "' . $index . '" position. Required filed "filed_value_count"', 'filed_value_count_is_required_filed');
        }

        $priceSetField = $this->findPriceSetFiled($priceSetFields, $selectedValue['field_id']);
        if (empty($priceSetField)) {
          throw new api_Exception('Field id=(' . $selectedValue['field_id'] . ') does not exist for Event\'s perice set', 'field_id_does_not_exist');
        }

        if ($priceSetField['html_type'] != 'Text' && $selectedValue['filed_value_count'] != 1) {
          throw new api_Exception('"filed_value_count" must be 1 for field with not "Text" "html_type"', 'invalid_filed_value_count');
        }


        $priceSetFieldValues = $this->getPriceSetFieldValues($priceSetField['id']);
        if (empty($priceSetFieldValues['values'])) {
          throw new api_Exception('Empty filed values for price set field (id = ' . $priceSetField['id'] . '). Please create it in administer.', 'empty_price_set_field_values');
        }

        $validPriceSetFieldValues = [];
        foreach ($selectedValue['filed_value_id'] as  $valueId) {
          $priceSetFieldValue = $this->findPriceSetFiledValue($priceSetFieldValues['values'], $valueId);
          if (empty($priceSetFieldValue)) {
            throw new api_Exception('Not valid value('. $valueId .') for price set field (id = ' . $priceSetField['id'] . ').', 'not_valid_value_for_price_set_field');
          }

          $validPriceSetFieldValues[] = $priceSetFieldValue;
        }

        $validSelectedValues[] = [
          'field_id' => $priceSetField['id'],
          'is_required' => $priceSetField['is_required'],
          'html_type' => $priceSetField['html_type'],
          'label' => $priceSetField['label'],
          'filed_value_count' => $selectedValue['filed_value_count'],
          'selected_values' => $validPriceSetFieldValues
        ];

        $validFieldIds[] = $priceSetField['id'];
      }
    }

    foreach ($priceSetFields as $priceSetField) {
      if ($priceSetField['is_required'] == 1  && !in_array($priceSetField['id'], $validFieldIds)) {
        throw new api_Exception('Price field(id = '. $priceSetField['id'] .') is required field for price set.' , 'required_filed_for_price_set');
      }
    }

    return $validSelectedValues;
  }

  /**
   * Finds 'price set field value' in list by 'price set field value id'
   *
   * @param $priceSetFieldValues
   * @param $fieldValueId
   *
   * @return bool
   */
  private function findPriceSetFiledValue($priceSetFieldValues, $fieldValueId) {
    foreach ($priceSetFieldValues as $fieldValue) {
      if ($fieldValue['id'] == $fieldValueId) {
        return $fieldValue;
      }
    }

    return false;
  }

  /**
   * Finds 'price set field' in list by 'price set field id'
   *
   * @param $priceSetFields
   * @param $fieldId
   *
   * @return bool
   */
  private function findPriceSetFiled($priceSetFields, $fieldId) {
    foreach ($priceSetFields as $field) {
      if ($field['id'] == $fieldId) {
        return $field;
      }
    }

    return false;
  }

  /**
   * Gets Price set
   *
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
   * Is Contact already registered to the Event
   *
   * @param $contactId
   * @param $eventId
   *
   * @return bool
   */
  private function isContactAlreadyRegistered($contactId, $eventId) {
    $participant = new CRM_Event_BAO_Participant();
    $participant->contact_id = $contactId;
    $participant->event_id = $eventId;
    $participantExist = $participant->find(TRUE);

    return !empty($participantExist);
  }

  /**
   * Sets Contact by Contact id
   *
   * @param $contactId
   */
  private function setContact($contactId) {
    $contact = new CRM_Contact_BAO_Contact();
    $contact->id = $contactId;
    $contactExistence = $contact->find(TRUE);
    if (empty($contactExistence)) {
      $this->contact = false;
    }

    $this->contact = $contact;
  }

  /**
   * Sets Event by Event id
   *
   * @param $eventId
   */
  private function setEvent($eventId) {
    $event = new CRM_Event_BAO_Event();
    $event->id = $eventId;
    $eventExistence = $event->find(TRUE);
    if (empty($eventExistence)) {
      $this->event = false;
    }

    $this->event = $event;
  }

  /**
   * Gets valid params
   *
   * @return array
   */
  private function getExpectedParams() {
    $expectedParams = [];
    $participantParam = [];
    $expectedParticipantParams = [
      'role_id', 'participant_status_id', 'text_qty',
      'radio_qty', 'select_qty', 'checkbox_qty',
      'fee_currency', 'contact_id', 'event_id'
    ];

    foreach ($expectedParticipantParams as $expectedParticipantParam) {
      if (isset($this->params[$expectedParticipantParam])) {
        $participantParam[$expectedParticipantParam] = $this->params[$expectedParticipantParam];
      }
    }

    $participantParam['status_id'] = $participantParam['participant_status_id'];
    $participantParam['send_confirmation'] = 0;
    if (empty($participantParam['fee_currency'])) {
      if (!empty($this->event->currency)) {
        $participantParam['fee_currency'] = $this->event->currency;
      }
    }

    $expectedParams['event_financial_type_id'] = $this->event->financial_type_id;
    $expectedParams['send_confirmation'] = (int) $this->params['send_confirmation'];
    $expectedParams['participant_params'] = $participantParam;
    $expectedParams['price_set_selected_values'] = $this->params['price_set_selected_values'];

    return $expectedParams;
  }

  /**
   * Calculates price set field values
   *
   * @param $priceSetSelectedValues
   *
   * @return array
   */
  private function calculateFeeData($priceSetSelectedValues) {
    $total = 0;
    $label = '';

    foreach ($priceSetSelectedValues as $priceSetField) {
      foreach ($priceSetField['selected_values'] as $fieldValue) {
        $total += $fieldValue['amount'] * $priceSetField['filed_value_count'];
        if ($priceSetField['filed_value_count'] != 1) {
          $label .= $fieldValue['label'] . ' - ' . $priceSetField['filed_value_count'] . '; ';
        } else {
          $label .= $fieldValue['label'] . '; ';
        }
      }
    }

    return [
      'fee_amount' => $total,
      'fee_level' => $label
    ];
  }

}
