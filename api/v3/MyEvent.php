<?php

/**
 * Gets an registered events
 *
 * @param array $params
 *   Array per getfields documentation.
 *
 * @return array API result array
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function civicrm_api3_my_event_get($params) {
  _civicrm_api3_my_event_check_permission($params);

  $params = _civicrm_api3_my_event_prepare_params($params);

  $dao = _civicrm_api3_my_event_get_find($params);

  $result = [];
  while ($dao->fetch()) {
    $result[] = _civicrm_api3_my_event_format_result($dao);
  }

  return civicrm_api3_create_success($result, $params);
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
function _civicrm_api3_my_event_prepare_params($params) {
  $newParams = [];
  if (isset($params['options'])) {
    $limit = (int) CRM_Utils_Array::value('limit', $params['options'], 0);
    if ($limit != 0) {
      $newParams['limit'] = $limit;
      $newParams['offset'] = (int) CRM_Utils_Array::value('offset', $params['options'], 0);
    }
    $order = (string) CRM_Utils_Array::value('sort', $params['options']);
  }

  $newParams['contact_id'] = !empty($params['contact_id']) ? (int) $params['contact_id'] : null;
  $newParams['is_active'] = isset($params['is_active']) ? (int) $params['is_active'] : 1;
  $newParams['order'] = !empty($order) ? $order : 'sort_date DESC';
  $newParams['sort_date'] = CRM_Utils_Array::value('sort_date', $params);
  $newParams['start_date'] = CRM_Utils_Array::value('start_date', $params);
  $newParams['end_date'] = CRM_Utils_Array::value('end_date', $params);

  return $newParams;
}

/**
 * Checks permissions
 *
 * @param $params
 *
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function _civicrm_api3_my_event_check_permission($params) {
  $contactID = CRM_Utils_Array::value('contact_id', $params);
  if (!CRM_Core_Permission::check('administer CiviCRM')) {
    if (CRM_Core_Session::singleton()->get('userID') != $contactID) {
      throw new \Civi\API\Exception\UnauthorizedException('Permission denied to see contact\'s events');
    }
  }
}

/**
 * Finds Events by params
 *
 * @param $params
 *
 * @return mixed
 */
function _civicrm_api3_my_event_get_find($params) {
  $conditionSymbols = ['=', '<=', '=<', '>', '<', 'LIKE', '<>', '!=', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'];
  $logicSymbols = ['IS NOT NULL', 'IS NULL'];

  $select = CRM_Utils_SQL_Select::from('civicrm_participant p')
    ->select("p.event_id, p.contact_id, e.start_date, p.register_date, e.end_date, e.title, e.event_type_id, pst.name, pst.label, pst.is_active, IF(e.end_date IS NULL or e.end_date = '', e.start_date, e.end_date) as sort_date")
    ->join('pst', 'left join civicrm_participant_status_type pst on pst.id = p.status_id')
    ->join('e', 'left join civicrm_event e on e.id = p.event_id');

  if ($params['contact_id']) {
    $select->where('contact_id = #contact_id', ['contact_id' => $params['contact_id']]);
  }

  if (!empty($params['sort_date'])) {
    if (is_array($params['sort_date'])) {
      foreach ($params['sort_date'] as $key => $sort_date) {
        if (in_array($key, $conditionSymbols)) {
          $select->having('sort_date ' . $key . ' @sort_date', ['sort_date' => $sort_date]);
        }
        if (in_array($key, $logicSymbols)) {
          $select->having('sort_date ' . $key . ' ');
        }
      }
    }
    else {
      $select->having('sort_date = #sort_date', ['sort_date' => $params['sort_date']]);
    }
  }

  if (!empty($params['start_date'])) {
    if (is_array($params['start_date'])) {
      foreach ($params['start_date'] as $key => $start_date) {
        if (in_array($key, $conditionSymbols)) {
          $select->where('e.start_date ' . $key . ' @start_date', ['start_date' => $start_date]);
        }
        if (in_array($key, $logicSymbols)) {
          $select->where('e.start_date ' . $key . ' ');
        }
      }
    }
    else {
      $select->having('e.start_date = #start_date', ['start_date' => $params['start_date']]);
    }
  }

  if (!empty($params['end_date'])) {
    if (is_array($params['end_date'])) {
      foreach ($params['end_date'] as $key => $end_date) {
        if (in_array($key, $conditionSymbols)) {
          $select->where('e.end_date ' . $key . ' @end_date', ['end_date' => $end_date]);
        }
        if (in_array($key, $logicSymbols)) {
          $select->where('e.end_date ' . $key . ' ');
        }
      }
    }
    else {
      $select->having('e.end_date = #end_date', ['end_date' => $params['end_date']]);
    }
  }

  $select->where('p.is_test = #is_test', ['is_test' => 0])
    ->where('pst.is_active = #is_active', ['is_active' => $params['is_active']])
    ->orderBy($params['order'])
    ->groupBy('p.event_id');

  if (isset($params['limit']) && isset($params['offset'])) {
    $select->limit($params['limit'], $params['offset']);
  }

  return CRM_Core_DAO::executeQuery($select->toSQL());
}

/**
 * Prepares array based on event DAO
 *
 * @param object $dao event dao
 *
 * @return array
 */
function _civicrm_api3_my_event_format_result($dao) {
  $result = [
    'id' => $dao->event_id,
    'contact_id' => $dao->contact_id,
    'start_date' => $dao->start_date,
    'end_date' => $dao->end_date,
    'register_date' => $dao->register_date,
    'sort_date' => $dao->sort_date,
    'title' => $dao->title,
    'event_type_id' => $dao->event_type_id,
    'status_id.name' => $dao->name,
    'status_id.label' => $dao->label,
    'status_id.is_active' => $dao->is_active,
  ];

  return $result;
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_my_event_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => 'Contact id',
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['is_active'] = [
    'title' => 'Is active',
    'description' => 'Is registration active',
    'type' => CRM_Utils_Type::T_BOOLEAN,
  ];
  $params['sort_date'] = [
    'title' => 'Sort Date',
    'description' => "IF(e.end_date IS NULL or e.end_date = '', e.start_date, e.end_date)",
    'type' => CRM_Utils_Type::T_DATE,
  ];
  $params['start_date'] = [
    'title' => 'Start Date',
    'description' => "Start Date",
    'type' => CRM_Utils_Type::T_DATE,
  ];
  $params['end_date'] = [
    'title' => 'End Date',
    'description' => "End Date",
    'type' => CRM_Utils_Type::T_DATE,
  ];
}