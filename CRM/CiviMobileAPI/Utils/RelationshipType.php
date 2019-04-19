<?php

/**
 * Class provide RelationshipType helper methods
 */
class CRM_CiviMobileAPI_Utils_RelationshipType {

  /**
   * Add additional info to pure RelationType options
   *
   * @param $relationTypeOptions
   *
   * @return array
   */
  public static function addAdditionalInfoToOptions($relationTypeOptions) {
    if (empty($relationTypeOptions)) {
      return [];
    }

    $preparedRelationType = [];

    foreach ($relationTypeOptions as $relationTypeValueString => $label) {
      $info = static::getRelationTypeInfo($relationTypeValueString, $label);
      if (!empty($info)) {
        $preparedRelationType[] = $info;
      }
    }

    return $preparedRelationType;
  }

  /**
   * Gets additional info for RelationType by value from options
   *
   * @param $relationTypeValueString
   * @param $relationTypeLabel
   *
   * @return bool|array
   */
  private static function getRelationTypeInfo($relationTypeValueString, $relationTypeLabel) {
    $relationTypeData = explode('_', $relationTypeValueString);
    if (empty($relationTypeData[0]) || empty($relationTypeData[1]) || empty($relationTypeData[2])) {
      return FALSE;
    }

    $relationshipTypeId = (int) $relationTypeData[0];
    $relationshipType = static::getById($relationshipTypeId);
    if (empty($relationshipType)) {
      return FALSE;
    }

    return [
      'label' => $relationTypeLabel,
      'value' => $relationTypeValueString,
      'relationship_type_id' => $relationshipTypeId,
      'contact_position_in_relations' => $relationTypeData[1],
      'contact_type_' . $relationTypeData[2] => $relationshipType['contact_type_' . $relationTypeData[2]],
      'contact_type_' . $relationTypeData[1] => $relationshipType['contact_type_' . $relationTypeData[1]]
    ];
  }

  /**
   * Gets RelationshipType by id
   *
   * @param $relationshipTypeId
   *
   * @return array|string
   */
  public static function getById($relationshipTypeId) {
    try {
      $relationshipType = civicrm_api3('RelationshipType', 'getsingle', [
        'id' => $relationshipTypeId,
      ]);
    } catch (CiviCRM_API3_Exception $e) {}

    return ((!empty($relationshipType)) ? $relationshipType: false);
  }

}
