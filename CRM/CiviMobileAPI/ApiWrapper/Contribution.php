<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Contribution implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    $result = $this->fillFinancialTypeName($apiRequest, $result);
    $result = $this->fillFormatTotalAmount($apiRequest, $result);

    return $result;
  }

  /**
   * @param array $apiRequest
   * @param array $result
   *
   * @return mixed
   */
  private function fillFinancialTypeName($apiRequest, $result) {
    if (empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'financial_type_name') !== FALSE) {
      if ($apiRequest['action'] == 'getsingle') {
        $result['financial_type_name'] = $this->getFinancialTypeName($result);
      }
      else if ($apiRequest['action'] == 'get') {
        foreach ($result['values'] as &$contribution) {
          $contribution['financial_type_name'] = $this->getFinancialTypeName($contribution);
        }
      }
    }

    return $result;
  }

  /**
   * @param array $apiRequest
   * @param array $result
   *
   * @return mixed
   */
  private function fillFormatTotalAmount($apiRequest, $result) {
    if (empty($apiRequest['params']['return']) || stristr($apiRequest['params']['return'], 'total_amount') !== FALSE) {
      if ($apiRequest['action'] == 'getsingle') {
        $result['format_total_amount'] = CRM_Utils_Money::format($result['total_amount'], $result['currency']);
      }
      else if ($apiRequest['action'] == 'get') {
        foreach ($result['values'] as &$contribution) {
          $contribution['format_total_amount'] = CRM_Utils_Money::format($contribution['total_amount'], $contribution['currency']);
        }
      }
    }

    return $result;
  }

  /**
   * @param array $contribution
   *
   * @return string
   */
  private function getFinancialTypeName($contribution) {
    if (!empty($contribution['financial_type_id'])) {
      return CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialType', $contribution['financial_type_id'], 'name');
    }
    else {
      $financialTypeId = CRM_Core_DAO::getFieldValue('CRM_Contribute_DAO_Contribution', $contribution['id'], 'financial_type_id');

      return CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialType', $financialTypeId, 'name');
    }
  }
}
