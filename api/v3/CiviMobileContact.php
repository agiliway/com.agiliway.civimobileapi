<?php

/**
 * @param $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_contact_create($params) {
  try {
    $isFileExist = isset($_FILES['image_file']['name']);
    $isUploadPic = $_POST["method"] == 'UploadPic';
    if (!$isUploadPic || !$isFileExist) {
      return civicrm_api3_create_error(ts("File not exist"));
    }

    $photoName = basename($_FILES['image_file']['name']);
    $fileStructure = pathinfo($photoName);

    $newName = $fileStructure['filename'] . "_" . base64_encode($photoName . date("Y-m-d H:i:s")) . "." . $fileStructure['extension'];
    $pathToCustomFileUploadDir = Civi::paths()->getPath(Civi::settings()
        ->get('customFileUploadDir')) . $newName;

    if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $pathToCustomFileUploadDir)) {
      return civicrm_api3_create_error(ts("Can`t upload image"));
    }

    civicrm_api3('Contact', 'create', [
      'id' => $params['contact_id'],
      'image_URL' => CRM_Utils_System::url('civicrm/contact/imagefile', ['photo' => $newName], TRUE),
    ]);

    return civicrm_api3_create_success("Photo was updated", $params);
  } catch (Exception $e) {
    return civicrm_api3_create_error(ts("Something go wrong with your file"));
  }
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_contact_get_create(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
