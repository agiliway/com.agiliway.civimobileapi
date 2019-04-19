<?php

/**
 * Class provide extension version helper methods
 */
class CRM_CiviMobileAPI_Utils_Membership {

  /**
   * Renewals membership
   *
   * @param array $params
   */
  public static function renewal($params) {
    try {
      $membershipContribution = FALSE;
      $membership = CRM_Member_BAO_Membership::findById($params['id']);
      $membershipType = CRM_Member_BAO_MembershipType::findById($membership->membership_type_id);
      list($userName) = CRM_Contact_BAO_Contact_Location::getEmailDetails(CRM_Core_Session::singleton()->get('userID'));

      $source = $membershipType->name . ' Membership: Offline membership renewal (by ' . $userName . ')';

      $renewalDate = !empty($params['renewal_date']) ? $params['renewal_date'] : date('YmdHis');
      $amount = !empty($params['renewal_amount']) ? $params['renewal_amount'] : $membershipType->minimum_fee;
      $financialTypeId = !empty($params['renewal_financial_type_id']) ? $params['renewal_financial_type_id'] : $membershipType->financial_type_id;

      $lineItems = self::getLineItems([
        'membership_id' => $membership->id,
        'membership_type_id' => $membershipType->id,
        'financial_type_id' => $financialTypeId,
        'receive_date' => $renewalDate,
        'amount' => $amount,
        'source' => $source
      ]);

      CRM_Member_BAO_Membership::processMembership(
        $membership->contact_id, $membership->membership_type_id, 0,
        $renewalDate, NULL, [], 1, $membership->id,
        FALSE,
        $membership->contribution_recur_id, NULL, FALSE, $membership->campaign_id
      );

      if ($amount) {
        $contributionParams = [
          'membership_id' => $membership->id,
          'contribution_recur_id' => $membership->contribution_recur_id,
          'campaign_id' => $membership->campaign_id,
          'contact_id' => $membership->contact_id,
          'receive_date' => $renewalDate,
          'total_amount' => $amount,
          'financial_type_id' => $financialTypeId,
          'membership_type_id' => $membershipType->id,
          'contribution_source' => $source,
          'lineItems' => $lineItems,
          'processPriceSet' => TRUE
        ];

        if (!empty($params['renewal_invoice_id'])) {
          $contributionParams['invoice_id'] = $params['renewal_invoice_id'];
        }

        $membershipContribution = CRM_Member_BAO_Membership::recordMembershipContribution($contributionParams);
      }

      static::sendEmail($membership->contact_id, $membership->id, $membershipType, $membershipContribution);
    }
    catch (Exception $e) {}
  }

  /**
   * Gets price set id
   *
   * @param int $membershipId
   *
   * @return int
   */
  private static function getPriceSetId($membershipId) {
    $contributionPageId = CRM_Member_BAO_Membership::getContributionPageId($membershipId);

    if ($contributionPageId) {
      $priceSetId = CRM_Price_BAO_PriceSet::getFor('civicrm_contribution_page', $contributionPageId);
    }
    else {
      $priceSetId = reset(CRM_Price_BAO_PriceSet::getDefaultPriceSet('membership'))['setID'];
    }

    return $priceSetId;
  }

  /**
   * @param array $params
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  private static function getLineItems($params) {
    $priceSetParams = [
      'membership_type_id' => $params['membership_type_id'],
      'financial_type_id' => $params['financial_type_id'],
      'total_amount' => $params['amount'],
      'receive_date' => $params['receive_date'],
      'record_contribution' => 1,
      'contribution_source' => $params['source'],
      'is_pay_later' => FALSE
    ];

    $priceSetId = self::getPriceSetId($params['membership_id']);

    $priceFields = civicrm_api3('PriceField', 'get', [
      'price_set_id' => $priceSetId
    ]);

    $fields = $priceFields['values'];

    foreach ($fields as &$field) {
      $priceFieldValues = civicrm_api3('PriceFieldValue', 'get', [
        'price_field_id' => $field['id']
      ]);

      $field['options'] = $priceFieldValues['values'];
      $priceSetParams['price_' . $field['id']] = $field['id'];
    }

    $lineItems = [];

    CRM_Price_BAO_PriceSet::processAmount($fields, $priceSetParams, $lineItems[$priceSetId], '', $priceSetId);

    return $lineItems;
  }

  /**
   * Sends "Memberships - Receipt (off-line)"
   *
   * @param $contactId
   *
   * @param $membershipId
   * @param $membershipType
   * @param $membershipContribution
   *
   * @throws \Exception
   */
  private static function sendEmail($contactId, $membershipId, $membershipType, $membershipContribution) {
    $membership = CRM_Member_BAO_Membership::findById($membershipId);
    $details = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactId);
    $userDisplayName = $details[0];
    $userEmail = $details[1];
    $senderEmail = CRM_Core_BAO_Domain::getNameAndEmail();
    $senderEmailName = $senderEmail[0];
    $senderEmailAddress = $senderEmail[1];

    $params = [
      'groupName' => 'msg_tpl_workflow_membership',
      'valueName' => 'membership_offline_receipt',
      'contactId' => $contactId,
      'from' => $senderEmailName . " <" . $senderEmailAddress . ">",
      'toName' => $userDisplayName,
      'toEmail' => $userEmail,
      'isTest' => false,
      'tplParams' => [
        'receive_date' => $membershipContribution->receive_date,
        'mem_start_date' => CRM_Utils_Date::customFormat($membership->start_date),
        'mem_end_date' => CRM_Utils_Date::customFormat($membership->end_date),
        'membership_name' => CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $membershipType->id),
        'receiptType' => 'membership renewal',
        'formValues'=> $formValues = [
          'paidBy' => static::getPaymentInstrumentLabel($membershipContribution->payment_instrument_id),
          'total_amount' => $membershipContribution->total_amount
        ],
      ]
    ];

    CRM_Core_BAO_MessageTemplate::sendTemplate($params);
  }

  /**
   *Gets label for payment instrument
   *
   * @param $paymentInstrumentValue
   *
   * @return array|string
   */
  public static function getPaymentInstrumentLabel($paymentInstrumentValue) {
    try {
      $label = civicrm_api3('OptionValue', 'getvalue', [
        'return' => "label",
        'option_group_id' => "payment_instrument",
        'value' => $paymentInstrumentValue,
      ]);
    } catch (CiviCRM_API3_Exception $e) {}

    return ((!empty($label)) ? $label: '');
  }

}
