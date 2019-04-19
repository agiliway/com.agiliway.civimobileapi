<?php

/**
 * Class provide Contribution helper methods
 */
class CRM_CiviMobileAPI_Utils_Contribution {

  /**
   * Transforms statistic
   *
   * @param $statistic
   *
   * @return array
   */
  public static function transformStatistic($statistic) {
    if (!empty($statistic['total'])) {
      $statistic['total']['avg'] = static::transform($statistic['total']['avg']);
      $statistic['total']['amount'] = static::transform($statistic['total']['amount']);
      $statistic['total']['mode'] = static::transform($statistic['total']['mode']);
      $statistic['total']['median'] = static::transform($statistic['total']['median']);
    }

    if (!empty($statistic['cancel'])) {
      $statistic['cancel']['avg'] = static::transform($statistic['cancel']['avg']);
      $statistic['cancel']['amount'] = static::transform($statistic['cancel']['amount']);
    }

    return $statistic;
  }

  /**
   * Explodes and trims string
   *
   * @param $string
   *
   * @return array
   */
  private static function transform($string) {
    $result = [];
    $exploded = explode(',', str_replace("&nbsp;", "", $string));
    foreach ($exploded as $item) {
      $result[] = trim($item);
    }

    return $result;
  }

}
