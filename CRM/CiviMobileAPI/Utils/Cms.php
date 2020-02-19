<?php

/**
 * Gives you the ability to work with CMS
 */
class CRM_CiviMobileAPI_Utils_Cms {

  /**
   * Returns site`s name in different CMS`
   *
   * @return string|null
   */
  public static function getSiteName() {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS ) {
      return get_bloginfo('name');
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      return JFactory::getConfig()->get('sitename');
    }
    elseif ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6 || $currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7) {
      return variable_get('site_name', '');
    }

    return null;
  }

}
