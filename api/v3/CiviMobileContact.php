<?php

/**
 * Uploads picture for Contact
 *
 * @param $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_contact_create($params) {
  if (empty($params['contact_id'])) {
    return civicrm_api3_create_error(ts("Required field 'contact_id'."));
  }

  if ($_POST["method"] == 'DeletePic') {
    $result = CRM_CiviMobileAPI_Utils_Contact::removeContactAvatar($params['contact_id']);

    return civicrm_api3_create_success("Photo was deleted", $params);
  }

  try {
    $isFileExist = isset($_FILES['image_file']['name']);
    $isUploadPic = $_POST["method"] == 'UploadPic';
    if (!$isUploadPic || !$isFileExist) {
      return civicrm_api3_create_error(ts("File not exist"));
    }

    $photoName = basename($_FILES['image_file']['name']);
    $fileStructure = pathinfo($photoName);
    $contactSalt = "33a76s3as3162aq2e4cg5d68d64eefe" . time();
    $fileSalt = "e787ada2e9a69a3bc67d14893ac3sdf3a67a21a2a" . time();
    $newName = md5($params['contact_id'] . $contactSalt);
    $newName .= md5($photoName . $fileSalt) . time() . '.' . $fileStructure['extension'];
    $pathToCustomFileUploadDir = CRM_CiviMobileAPI_Utils_File::getUploadDirPath() . $newName;

    if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $pathToCustomFileUploadDir)) {
      return civicrm_api3_create_error(ts("Can`t upload image"));
    }

    CRM_CiviMobileAPI_Utils_Contact::removeContactAvatar($params['contact_id']);
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $imageUrl = CRM_Utils_System::url('civicrm/contact/imagefile', ['photo' => $newName], TRUE);
    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      $imageUrl = preg_replace('/administrator\//', 'index.php', $imageUrl);
    }

    civicrm_api3('Contact', 'create', [
      'id' => $params['contact_id'],
      'image_URL' => $imageUrl,
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
function _civicrm_api3_civi_mobile_contact_create_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
