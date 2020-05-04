<?php

/**
 * Class provide information about roles related to case
 */
class CRM_CiviMobileAPI_Utils_CaseRole {

  /**
   * Id of current case
   *
   * @var int
   */
  private $caseId;

  /**
   * List of case roles
   *
   * @var array
   */
  private $caseRoles;

  /**
   * Id of current contact
   *
   * @var int
   */
  private $contactId;

  /**
   * List of case relations
   *
   * @var array
   */
  private $caseRelationships;

  /**
   * List of you roles in case
   *
   * @var array
   */
  private $yourRoles;

  /**
   * CRM_CiviMobileAPI_Utils_CaseRole constructor.
   *
   * @param $caseId
   * @param $contactId
   */
  public function __construct($caseId, $contactId) {
    $this->caseId = $caseId;
    $this->contactId = $contactId;
    $this->caseRelationships = CRM_Case_BAO_Case::getCaseRoles($contactId, $caseId);
    $this->yourRoles = self::getYourRoles($this->caseRelationships, $this->contactId);
  }

  /**
   * Gets list of roles for current case
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public function getListOfRolesForCurrentCase() {
    $this->setCaseRoles();
    $this->removeAssignedRelation();
    $this->setContactImageUrlForRoles();
    $this->setClientAndUnsignedRoles();

    return $this->caseRelationships;
  }

  /**
   * @param $caseRelationships
   * @param $contactId
   *
   * @return array
   */
  public static function getYourRoles($caseRelationships, $contactId) {
    $yourRoles = [];

    foreach ($caseRelationships as $relationship) {
      if ($relationship['cid'] == $contactId) {
        $yourRoles[] = $relationship['relation'];
      }
    }

    return $yourRoles;
  }

  /**
   * Sets default roles for case
   */
  private function setCaseRoles() {
    $caseTypeName = CRM_Case_BAO_Case::getCaseType($this->caseId, 'name');

    $xmlProcessor = new CRM_Case_XMLProcessor_Process();
    $defaultCaseRoles = $xmlProcessor->get($caseTypeName, 'CaseRoles');
    $this->addClientCaseRole($defaultCaseRoles);
  }

  /**
   * Adds client role to defaults
   *
   * @param $defaultCaseRoles
   */
  private function addClientCaseRole($defaultCaseRoles) {
    $defaultCaseRoles['client'] = CRM_Case_BAO_Case::getContactNames($this->caseId);
    $this->caseRoles = $defaultCaseRoles;
  }

  /**
   * Removes assigns roles from default list
   */
  private function removeAssignedRelation() {
    foreach ($this->caseRelationships as $key => $value) {
      unset($this->caseRoles[$value['relation_type']]);
    }
  }

  /**
   *  Sets contact image url for assigned role
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function setContactImageUrlForRoles() {
    foreach ($this->caseRelationships as $relationshipId => $data) {
      $this->caseRelationships[$relationshipId]['image_URL'] = $this->getContactImageUrl($this->caseRelationships[$relationshipId]['cid']);
    }
  }

  /**
   * Gets image url for current contact
   *
   * @param $contactId
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  private function getContactImageUrl($contactId) {
    return civicrm_api3('Contact', 'getvalue', [
      'return' => "image_URL",
      'id' => $contactId,
    ]);
  }

  /**
   *  Sets clients and unassigned role
   */
  private function setClientAndUnsignedRoles() {
    foreach ($this->caseRelationships as $rel) {
      if (isset($this->caseRoles[$rel['relation_type'] . '_' . $rel['relationship_direction']])) {
        unset($this->caseRoles[$rel['relation_type'] . '_' . $rel['relationship_direction']]);
      }
    }

    foreach ($this->caseRoles as $id => $value) {

      if ($id != "client") {
        $rel = [];
        $rel['relation'] = $value;
        $rel['relation_type'] = $id;
        $rel['name'] = '(not assigned)';
        $rel['phone'] = '';
        $rel['email'] = '';
        $rel['source'] = 'caseRoles';
        $this->caseRelationships[] = $rel;
      }
      else {
        foreach ($value as $clientRole) {
          $relClient = [];
          $relClient['relation'] = 'Client';
          $relClient['name'] = $clientRole['sort_name'];
          $relClient['phone'] = $clientRole['phone'];
          $relClient['email'] = $clientRole['email'];
          $relClient['cid'] = $clientRole['contact_id'];
          $relClient['image_URL'] = $this->getContactImageUrl($clientRole['contact_id']);

          $relClient['source'] = 'contact';
          $this->caseRelationships[] = $relClient;
        }
      }
    }
  }

  /**
   * Converts roles list
   *
   * @param $listOfRoles
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public function convertListOfRoles($listOfRoles) {
    $role = [];

    foreach ($listOfRoles as $id => $relation) {
      $role[] = $this->convertRole($relation);
    }

    return $role;
  }

  /**
   * Converts roles
   *
   * @param $listOfRoles
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public function convertRole($listOfRoles) {
    return [
      'contact_id' => $this->validateEntityExistence($listOfRoles['cid']) ? $listOfRoles['cid'] : '',
      'relation' => $this->validateEntityExistence($listOfRoles['relation']) ? $listOfRoles['relation'] : '',
      'name' => $this->validateEntityExistence($listOfRoles['name']) ? $listOfRoles['name'] : '',
      'relation_type' => $this->getRelationTypeWithDirections($listOfRoles['relation']),
      'relation_id' => $this->validateEntityExistence($listOfRoles['rel_id']) ? $listOfRoles['rel_id'] : '',
      'can_edit' => $this->canEdit(),
      'can_delete' => $this->canEdit(),
      'image_URL' => $this->validateEntityExistence($listOfRoles['image_URL']) ? $listOfRoles['image_URL'] : '',
    ];
  }

  /**
   * @return int
   */
  private function canEdit() {
    if (empty($this->yourRoles)) {
      $editAllContacts = CRM_Core_Permission::check('edit all contacts');
      $editAllCase = CRM_Core_Permission::check('access all cases and activities');

      return $editAllContacts && $editAllCase ? 1 : 0;
    }
    else {
      $editMyContact = CRM_Core_Permission::check('edit my contact');
      $editAllContacts = CRM_Core_Permission::check('edit all contacts');

      return $editMyContact || $editAllContacts ? 1 : 0;
    }
  }

  /**
   * Checks validation existence
   *
   * @param $entity
   *
   * @return bool
   */
  private function validateEntityExistence($entity) {
    return isset($entity) && !empty($entity);
  }

  /**
   * Gets relation type with directions
   *
   * @param $relationTitle
   *
   * @return false|int|string
   * @throws \CiviCRM_API3_Exception
   */
  private function getRelationTypeWithDirections($relationTitle) {
    $directionAtoB = "_a_b";
    $directionBtoA = "_b_a";

    $direction = $directionAtoB;
    $result = $this->getRelationTypeId($direction, $relationTitle);

    if (!isset($result) || empty($result)) {
      $direction = $directionBtoA;
      $result = $this->getRelationTypeId($direction, $relationTitle);
    }

    if (!isset($result) || empty($result)) {
      return "";
    }

    return $result . $direction;
  }

  /**
   * Gets id of relation type
   *
   * @param $direction
   * @param $relationTitle
   *
   * @return mixed
   * @throws \CiviCRM_API3_Exception
   */
  private function getRelationTypeId($direction, $relationTitle) {
    $result = civicrm_api3('RelationshipType', 'get', [
      'sequential' => 1,
      'return' => ["id"],
      "name$direction" => $relationTitle,
    ]);

    return $result['values'][0]['id'];
  }

}
