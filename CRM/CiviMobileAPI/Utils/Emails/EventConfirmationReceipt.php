<?php

/**
 * Class sends 'event_offline_receipt'/'event_online_receipt' message templates
 */
class CRM_CiviMobileAPI_Utils_Emails_EventConfirmationReceipt {

  /**
   * Sends email to Participant
   *
   * @param $participantId
   * @param $messageTemplateValueName
   */
  public static function send($participantId, $messageTemplateValueName) {
    if (!($messageTemplateValueName == 'event_offline_receipt' || $messageTemplateValueName == 'event_online_receipt')) {
      return;
    }

    $participant = new CRM_Event_BAO_Participant();
    $participant->id = $participantId;
    $participantExist = $participant->find(TRUE);
    if (empty($participantExist)) {
      throw new api_Exception('Participant does not exist.', 'participant_does_not_exist');
    }

    $contactId = $participant->contact_id;
    $eventId = $participant->event_id;
    $qrCodeInfo  = CRM_CiviMobileAPI_Utils_ParticipantQrCode::getQrCodeInfo($participantId);
    $details = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactId);
    $userDisplayName = $details[0];
    $userEmail = $details[1];
    $senderEmail = CRM_Core_BAO_Domain::getNameAndEmail();
    $senderEmailName = $senderEmail[0];
    $senderEmailAddress = $senderEmail[1];
    $event = self::prepareEvent($eventId, $participant->role_id);
    $prepareLineItems = self::prepareLineItems($participantId);
    $isShowLocation = (!empty($event['is_show_location']) && $event['is_show_location'] == 1) ? 1 : false;
    $location = CRM_Core_BAO_Location::getValues(['entity_id' => $eventId, 'entity_table' => 'civicrm_event'], TRUE);
    $registerDate = (!empty($participant->register_date)) ? $participant->register_date : false;
    $contribution = CRM_CiviMobileAPI_Utils_Participant::getParticipantContribution($participantId);
    $totalAmount = (!empty($contribution->total_amount)) ? $contribution->total_amount : false;
    $currency = (!empty($contribution->currency)) ? $contribution->currency : false;
    $checkNumber = (!empty($contribution->checkNumber)) ? $contribution->checkNumber : false;
    $trxnId = (!empty($contribution->trxn_id)) ? $contribution->trxn_id : false;
    $isPayLater = (!empty($event['is_pay_later'])) ? $event['is_pay_later'] : false;
    $isOnWaitlist = (!empty($event['has_waitlist'])) ? $event['has_waitlist'] : false;
    $payLaterReceipt = (!empty($event['pay_later_receipt'])) ? $event['pay_later_receipt'] : false;
    $isPrimary = (!empty($event['is_monetary']) && $event['is_monetary'] == 1) ? 1 : false;
    $isAmountZero = ($totalAmount <= 0) ? TRUE : FALSE;
    $defaultRole = (!empty($event['default_role_id'])) ? $event['default_role_id'] : false;
    if (empty($totalAmount) && !empty($participant->fee_amount)) {
      $totalAmount = $participant->fee_amount;
    }

    //fixes:
    $isAmountZero = TRUE;//for hide billing address block,fix in future

    if (!empty($contribution->financial_type_id)) {
      $financialTypeName = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialType', $contribution->financial_type_id, 'name');
    } else {
      $financialTypeName = false;
    }

    if (!empty($contribution->payment_instrument_id)) {
      $paidBy = CRM_CiviMobileAPI_Utils_Membership::getPaymentInstrumentLabel($contribution->payment_instrument_id);
    } else {
      $paidBy = false;
    }

    $params = [
      'groupName' => 'msg_tpl_workflow_event',
      'valueName' => $messageTemplateValueName,
      'contactId' => $contactId,
      'from' => $senderEmailName . " <" . $senderEmailAddress . ">",
      'toName' => $userDisplayName,
      'toEmail' => $userEmail,
      'isTest' => false,
      'tplParams' => [
        'file_name' => $qrCodeInfo['qr_code_image'],
        'event' => $event,
        'eventID' => $eventId,
        'lineItem' => $prepareLineItems,
        'contactID' => $contactId,
        'participantID' => $participantId,
        'isShowLocation' => $isShowLocation,
        'is_pay_later' => $isPayLater,
        'location' => $location,
        'isPrimary' => $isPrimary,
        'totalAmount' => $totalAmount,
        'register_date' => $registerDate,
        'email' => $userEmail,
        'financialTypeName' => $financialTypeName,
        'paidBy' => $paidBy,
        'checkNumber' => $checkNumber,
        'pay_later_receipt' => $payLaterReceipt,
        'trxn_id' => $trxnId,
        'defaultRole' => $defaultRole,
        'isAmountzero' => $isAmountZero,
        'isOnWaitlist' => $isOnWaitlist,
        'currency' => $currency
      ]
    ];

    if ($messageTemplateValueName == 'event_online_receipt') {
      //adds profiles data to message template by smarty:
      $smarty = CRM_Core_Smarty::singleton();
      $ufJoinParams = [
        'entity_table' => 'civicrm_event',
        'module' => 'CiviEvent',
        'entity_id' => $eventId,
      ];

      $profilesInfo= CRM_Core_BAO_UFJoin::getUFGroupIds($ufJoinParams);
      $customPreProfileId = $profilesInfo[0];
      $customPostProfileId = $profilesInfo[1];

      CRM_Event_BAO_Event::buildCustomDisplay($customPreProfileId, 'customPre', $contactId, $smarty, $participantId, 0);
      CRM_Event_BAO_Event::buildCustomDisplay($customPostProfileId, 'customPost', $contactId, $smarty, $participantId, 0);
    }

    //fix currency on mail.
    //'crmMoney' in Smarty gets currency from CiviCRM config
    //sets currency from event to default currency from CiviCRM config
    $config = CRM_Core_Config::singleton();
    $defaultCurrency = $config->defaultCurrency;
    $config->defaultCurrency = CRM_Utils_Array::value('currency', $event, $config->defaultCurrency);

    CRM_Core_BAO_MessageTemplate::sendTemplate($params);

    //set right default currency to CiviCRM config
    $config->defaultCurrency = $defaultCurrency;
  }

  /**
   * Prepare Event data
   *
   * @param $eventId
   * @param $participantRoleIds
   *
   * @return array
   */
  private static function prepareEvent($eventId, $participantRoleIds) {
    try {
      $event = civicrm_api3('Event', 'getsingle', ['id' => $eventId]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    if (!empty($participantRoleIds)) {
      $rolesIds = explode(CRM_Core_DAO::VALUE_SEPARATOR, $participantRoleIds);
      $rolesNames = [];
      foreach ($rolesIds as $rolesId) {
        $rolesName = CRM_Event_PseudoConstant::participantRole($rolesId, FALSE);
        if (!empty($rolesName)) {
          $rolesNames[$rolesId] = $rolesName;
        }
      }
      $event['participant_role'] = implode(', ', $rolesNames);
    } else {
      $event['participant_role'] = false;
    }

    return $event;
  }

  /**
   * Prepares Participant's line items
   *
   * @param $participantId
   *
   * @return array|bool
   */
  private static function prepareLineItems($participantId) {
    $updatedLineItem = CRM_Price_BAO_LineItem::getLineItems($participantId, 'participant', FALSE, FALSE);

    $lineItem = array();
    if ($updatedLineItem) {
      $lineItem[] = $updatedLineItem;
    }

    return  empty($lineItem) ? FALSE : $lineItem;
  }

}
