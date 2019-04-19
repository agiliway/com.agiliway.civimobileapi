<?php

/**
 * Gets Relationship
 *
 * @param array $params
 *
 * @return array API result array
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function civicrm_api3_civi_mobile_active_relationship_get($params) {
  _civicrm_api3_relationship_check_permission($params);

  $params = _civicrm_api3_civi_mobile_active_relationship_prepare_params($params);
  $dao = _civicrm_api3_civi_mobile_active_relationship_get_find($params);

  $result = [];
  while ($dao->fetch()) {
    $result[] = _civicrm_api3_mobile_active_relationship_format_result($dao);
  }

  return civicrm_api3_create_success($result, $params);
}

/**
 * Prepare array based on event DAO
 *
 * @param object $dao event dao
 *
 * @return array
 */
function _civicrm_api3_mobile_active_relationship_format_result($dao) {
  $result = [
    'id' => $dao->id,
    'relationship_type_id' => $dao->relationship_type_id,
    'start_date' => $dao->start_date,
    'end_date' => $dao->end_date,
    'is_active' => $dao->is_active,
    'description' => $dao->description,
    'case_id' => $dao->case_id,
    'is_permission_a_b' => $dao->is_permission_a_b,
    'is_permission_b_a' => $dao->is_permission_b_a,
    'contact_id_a' => $dao->contact_id_a,
    'contact_id_b' => $dao->contact_id_b,
    'contact_id_a.image_URL' => $dao->image_url_a,
    'contact_id_b.image_URL' => $dao->image_url_b,
    'relationship_type_id.label_a_b' => $dao->label_a_b,
    'relationship_type_id.label_b_a' => $dao->label_b_a,
    'contact_id_a.display_name' => $dao->display_name_a,
    'contact_id_b.display_name' => $dao->display_name_b,
    'contact_id_a.contact_type' => $dao->contact_type_a,
    'contact_id_b.contact_type' => $dao->contact_type_b,
  ];

  return $result;
}

/**
 * Checks permissions
 *
 * @param $params
 *
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function _civicrm_api3_relationship_check_permission($params) {
  if (!CRM_Core_Permission::check('access CiviCRM')) {
    throw new \Civi\API\Exception\UnauthorizedException('Permission denied.');
  }
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 *
 * @param array $params array or parameters determined by getfields
 *
 * @return array
 */
function _civicrm_api3_civi_mobile_active_relationship_prepare_params($params) {
  $newParams = [];

  if (isset($params['options'])) {
    $limit = (int) CRM_Utils_Array::value('limit', $params['options'], 0);
    if($limit != 0) {
      $newParams['limit'] = $limit;
      $newParams['offset'] = (int) CRM_Utils_Array::value('offset', $params['options'], 0);
    }
    $order = (string) CRM_Utils_Array::value('sort', $params['options']);
  }

  $newParams['contact_id'] = !empty($params['contact_id']) ? (int) $params['contact_id'] : '';
  $newParams['order'] = !empty($order) ? $order : 'id';
  $newParams['end_date'] = CRM_Utils_Array::value('end_date', $params);
  $newParams['case'] = !empty($params['case']) ? CRM_Utils_Array::value('case', $params) : '';

  if (isset($params['contact_id_a.is_deleted'])) {
    $newParams['contact_id_a.is_deleted'] = (int) $params['contact_id_a.is_deleted'];
  }

  if (isset($params['contact_id_b.is_deleted'])) {
    $newParams['contact_id_b.is_deleted'] = (int) $params['contact_id_b.is_deleted'];
  }

  return $newParams;
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_active_relationship_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID ',
    'description' => 'Contact id',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['end_date'] = [
    'title' => 'End date',
    'description' => 'End date',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_DATE,
  ];
  $params['case'] = [
    'title' => 'Case',
    'description' => 'Case',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['contact_id_a.is_deleted'] = [
    'title' => 'Contact id(a) is deleted?',
    'description' => ts('Contact id(a) is deleted?'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['contact_id_b.is_deleted'] = [
    'title' => 'Contact id(b) is deleted?',
    'description' => ts('Contact id(b) is deleted?'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  ];
}

/**
 * Finds Relationships by params
 *
 * @param $params
 *
 * @return mixed
 */
function _civicrm_api3_civi_mobile_active_relationship_get_find($params) {
  $conditionSymbols = ['=', '<=', '>=', '>', '<', '<>', 'BETWEEN', 'NOT BETWEEN'];
  $select = CRM_Utils_SQL_Select::from('civicrm_relationship r');

  $select->select(" r.*")
    ->select(" c_a.display_name AS display_name_a, c_b.display_name AS display_name_b ")
    ->select(" c_a.image_URL AS image_url_a, c_b.image_URL AS image_url_b ")
    ->select(" c_a.contact_type AS contact_type_a, c_b.contact_type AS contact_type_b ")
    ->select(" rt.label_b_a AS label_b_a, rt.label_a_b AS label_a_b ")
    ->join('c_a', ' LEFT JOIN civicrm_contact AS c_a ON c_a.id = r.contact_id_a')
    ->join('c_b', ' LEFT JOIN civicrm_contact AS c_b ON c_b.id = r.contact_id_b')
    ->join('rt', ' LEFT JOIN civicrm_relationship_type AS rt ON rt.id = r.relationship_type_id')
    ->where('contact_id_a = #contact_id OR contact_id_b = #contact_id', [
      'contact_id' => $params['contact_id'],
    ]);

  if (isset($params['contact_id_a.is_deleted'])) {
    $select->where('c_a.is_deleted = #contact_id_a_is_deleted', [
      'contact_id_a_is_deleted' => $params['contact_id_a.is_deleted']
    ]);
  }

  if (isset($params['contact_id_b.is_deleted'])) {
    $select->where('c_b.is_deleted = #contact_id_b_is_deleted', [
      'contact_id_b_is_deleted' => $params['contact_id_b.is_deleted']
    ]);
  }

  if (!empty($params['end_date'])) {
    $isNullSql = ' OR (r.end_date IS NULL AND r.is_active = 1)';
    if (is_array($params['end_date'])) {
      foreach ($params['end_date'] as $key => $end_date) {
        if (in_array($key, $conditionSymbols)) {
          $select->where('r.end_date ' . $key . ' #end_date AND r.is_active = #is_active ' . $isNullSql, [
            'end_date' => $params['end_date'],
            'is_active' => 1
          ]);
        }
      }
    }
    else {
      $select->where('r.end_date = #end_date AND r.is_active = #is_active ' . $isNullSql, [
        'end_date' => $params['end_date'],
        'is_active' => 1
      ]);
    }
  }

  if (!empty($params['case']) && is_array($params['case'])) {
    foreach ($params['case'] as $key => $case) {
      if ($key == 'IS NOT NULL' and $case == 1) {
        $caseSql = ' r.case_id IS NOT NULL';
      }
      if ($key == 'IS NULL' and $case == 1) {
        $caseSql = ' r.case_id IS NULL';
      }
    }

    if (!empty($caseSql)) {
      $select->where($caseSql);
    }
  }

  $select->orderBy('r.' . $params['order']);

  if (isset($params['limit']) && isset($params['offset'])) {
    $select->limit($params['limit'], $params['offset']);
  }

  return CRM_Core_DAO::executeQuery($select->toSQL());
}
