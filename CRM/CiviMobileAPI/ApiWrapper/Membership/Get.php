<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Membership_Get implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    $result = $this->fillAdditionalInfo($apiRequest, $result);
    $result = $this->fillRelatedCount($apiRequest, $result);
    $result = $this->fillByRelationship($apiRequest, $result);

    return $result;
  }

  /**
   * Adds additional info
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  private function fillAdditionalInfo($apiRequest, $result) {
    if ($apiRequest['action'] == 'getsingle') {
      if (empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'renewal_amount') !== false) {
        $result += $this->getAdditionalInfo($result, $apiRequest);
      }
    }
    else {
      if ($apiRequest['action'] == 'get') {
        foreach ($result['values'] as &$membership) {
          $membership += $this->getAdditionalInfo($membership, $apiRequest);
        }
      }
    }

    return $result;
  }

  /**
   * Gets additional info
   *
   * @param array $membership
   * @param array $apiRequest
   *
   * @return array
   */
  private function getAdditionalInfo($membership, $apiRequest) {
    $config = CRM_Core_Config::singleton();
    $additionalInfo = [];

    if (empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'renewal_amount') !== false) {
      $membershipTypeId = !empty($membership['membership_type_id']) ? $membership['membership_type_id'] : CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'membership_type_id');

      $additionalInfo['renewal_amount'] = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $membershipTypeId, 'minimum_fee') ?: 0;
      $additionalInfo['format_renewal_amount'] = CRM_Utils_Money::format($additionalInfo['renewal_amount'], $config->defaultCurrency);
    }

    if (empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'can_renewal') !== false) {
      $additionalInfo['can_renewal'] = !CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'owner_membership_id') ? 1 : 0;
    }

    $additionalInfo['currency_code'] = $config->defaultCurrency;
    $additionalInfo['currency_symbol'] = $config->defaultCurrencySymbol;

    return $additionalInfo;
  }

  /**
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  private function fillRelatedCount($apiRequest, $result) {
    if (!(empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'related_count') !== false)) {
      return $result;
    }

    if ($apiRequest['action'] == 'getsingle') {
      $result['related_count'] = $this->getRelatedCount($result);
    }
    else {
      if ($apiRequest['action'] == 'get') {
        foreach ($result['values'] as &$membership) {
          $membership['related_count'] = $this->getRelatedCount($membership);
        }
      }
    }

    return $result;
  }

  /**
   * Gets related count
   *
   * @param array $membership
   *
   * @return int
   */
  private function getRelatedCount($membership) {
    $ownerMembershipId = !empty($membership['owner_membership_id']) ? $membership['owner_membership_id'] : CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'owner_membership_id');

    if ($ownerMembershipId) {
      return 0;
    }

    $query = '
      SELECT COUNT(m.id)
      FROM civicrm_membership m
      LEFT JOIN civicrm_membership_status ms ON ms.id = m.status_id
      LEFT JOIN civicrm_contact ct ON ct.id = m.contact_id
      WHERE m.owner_membership_id = %1 AND m.is_test = 0 AND ms.is_current_member = 1 AND ct.is_deleted = 0
    ';

    $numRelated = CRM_Core_DAO::singleValueQuery($query, [
      1 => [$membership['id'], 'Integer']
    ]);

    return $numRelated;
  }

  /**
   * @param array $apiRequest
   * @param array $result
   *
   * @return mixed
   */
  private function fillByRelationship($apiRequest, $result) {
    if (!(empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'by_relationship_contact_id') !== false)) {
      return $result;
    }

    if ($apiRequest['action'] == 'getsingle') {
      $result += $this->getRelatedContact($result);
    }
    else {
      if ($apiRequest['action'] == 'get') {
        foreach ($result['values'] as &$membership) {
          $membership += $this->getRelatedContact($membership);
        }
      }
    }

    return $result;
  }

  /**
   * Gets related Contact by membership
   *
   * @param array $membership
   *
   * @return array
   */
  private function getRelatedContact($membership) {
    $ownerMembershipId = !empty($membership['owner_membership_id']) ? $membership['owner_membership_id'] : CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'owner_membership_id');

    if ($ownerMembershipId) {
      try {
        $contactId = !empty($membership['contact_id']) ? $membership['contact_id'] : CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $membership['id'], 'contact_id');
        $byRelationshipContactId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $ownerMembershipId, 'contact_id');
        $ownerMembershipTypeId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $ownerMembershipId, 'membership_type_id');
        $ownerRelationshipTypes = str_replace(CRM_Core_DAO::VALUE_SEPARATOR, ",", CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $ownerMembershipTypeId, 'relationship_type_id'));

        $sql = "
          SELECT relationship_type_id,
            CASE
              WHEN  contact_id_a = %1 AND contact_id_b = %2 THEN 'b_a'
              WHEN  contact_id_b = %1 AND contact_id_a = %2 THEN 'a_b'
            END AS 'direction'
          FROM civicrm_relationship
          WHERE relationship_type_id IN ($ownerRelationshipTypes) 
            AND (
              (contact_id_a = %1 AND contact_id_b = %2 ) 
              OR (contact_id_b = %1 AND contact_id_a = %2 )
            )
        ";
        $relationship = CRM_Core_DAO::executeQuery($sql, [
          1 => [(int) $byRelationshipContactId, 'Integer'],
          2 => [(int) $contactId, 'Integer']
        ]);

        $label = '';
        while ($relationship->fetch()) {
          $label .= (!empty($label)) ? ', ' : '';
          $label .= CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', $relationship->relationship_type_id, "label_" . $relationship->direction, 'id');
        }

        return [
          'by_relationship_contact_id' => $byRelationshipContactId,
          'by_relationship_contact_id.display_name' => CRM_CiviMobileAPI_Utils_Contact::getDisplayName($byRelationshipContactId),
          'by_relationship_label' => $label,
        ];
      } catch (Exception $e) {
        return [];
      }
    }

    return [];
  }

}
