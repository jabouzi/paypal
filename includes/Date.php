<?php


class Date
{

    const SHORT_FORMAT  = 1;
    const MEDIUM_FORMAT = 2;
    const LONG_FORMAT   = 3;
    const FULL_FORMAT   = 4;
    const NO_DAY_FORMAT = 5;
    const MONTH_ONLY    = 6;
    const FULL_DATETIME_FORMAT = 7;
    const ISO_DATETIME_FORMAT = 8;
    
    public static function display($date, $format = self::SHORT_FORMAT, $language = null, $useOrdinalSuffix = true, $timeZone = null)
    {
        $returnValue = '';
        $currentLocale = setlocale(LC_TIME, 0);
        $currentTimeZone = 'America/Montreal';
        $defaultLanguage = 'fr';
        $timeZones = DateTimeZone::listIdentifiers();
        $dateTimeZone = new DateTimeZone(in_array($timeZone, $timeZones) ? $timeZone : $currentTimeZone);
        $currentDateTimeZone = new DateTimeZone($currentTimeZone);

        try {
            if (!is_null($language)) {
                $language = $defaultLanguage;
            }
            /*if (!is_null($language)) {
                $locales = array(
                    'fr_CA' => array('fr_CA.UTF-8', 'fr_CA.utf8', 'fra'),
                    'en_CA' => array('en_CA.UTF-8', 'en_CA.utf8'),
                );
                if (isset($locales[$language])) {
                    setlocale(LC_TIME,  $locales[$language]);
                } else {
                    $language = $defaultLanguage;
                }
            } else {
                $language = strstr($currentLocale, '.', true); // Take only characters before dot
            }*/
            
            if (!empty($date)) { 
                $dateTime = new DateTime(is_numeric($date) ? "@$date" : $date);
                if (!is_null($timeZone)) { // if timezone is not null, display date with the good one.
                    $dateTime->setTimestamp($dateTime->getTimestamp() + $dateTimeZone->getOffset($dateTime));
                }
                $ordinalSuffix = $useOrdinalSuffix ? $dateTime->format('S') : '';
                $returnValue = strftime(self::getFormat($language, $format, $ordinalSuffix), $dateTime->getTimestamp());
            }
        } catch (Exception $e) {
            Error::getInstance()->log('[' . date('Y-m-d H:i:s') . ' ] - SimpleDate::display() - ' . $e->getMessage() . "\n");
            $returnValue = $date;
        }
        setlocale(LC_TIME, $currentLocale);
        return $returnValue;
    }

    private static function getFormat($language, $format, $ordinalSuffix) 
    {
        $str = 'Y-m-d';
        if ($language == 'en') {
            switch ($format) {
                case self::SHORT_FORMAT :
                    $str = '%m/%d/%Y';
                    break;
                case self::MEDIUM_FORMAT :
                    $str = '%b %e' . $ordinalSuffix . ', %Y';
                    break;
                case self::LONG_FORMAT :
                    $str = '%B %e' . $ordinalSuffix . ', %Y';
                    break;
                case self::FULL_FORMAT :
                    $str = '%A, %B %e' . $ordinalSuffix . ', %Y';
                    break;
                case self::NO_DAY_FORMAT :
                    $str = '%B %Y';
                    break;
                case self::MONTH_ONLY :
                    $str = '%B';
                    break;
                case self::ISO_DATETIME_FORMAT :
                    $str = '%Y-%m-%d %H:%M:%S';
                    break;
                default: // custom format
                    $str = $format;
                    break;
            }
        } elseif ($language == 'fr') {
            switch ($format) {
                case self::SHORT_FORMAT :
                    $str = '%d/%m/%Y';
                    break;
                case self::MEDIUM_FORMAT :
                    $str = '%e %b %Y';
                    break;
                case self::LONG_FORMAT :
                    $str = '%e %B %Y';
                    break;
                case self::FULL_FORMAT :
                    $str = '%A, %e %B %Y';
                    break;
                case self::NO_DAY_FORMAT :
                    $str = '%B %Y';
                    break;
                case self::MONTH_ONLY :
                    $str = '%B';
                    break;
                case self::ISO_DATETIME_FORMAT :
                    $str = '%Y-%m-%d %H:%M:%S';
                    break;
                default : // same as SHORT_FORMAT
                    $str = $format;
                    break;
            }            
        }
        // Win cross platform compatibility
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        }
        return $str;
    }

    /**
     * Return formatted interval between two dates as MM:SS
     *
     * @param string $from ISO datetime
     * @param string $to ISO datetime
     * @param boolean $fromNow calculate Interval 
     */
    public static function getFormattedInterval($from, $to)
    {
        //$now = gmdate('Y-m-d H:i:s');
        $from = is_numeric($from) ? gmdate('Y-m-d H:i:s', $from) : $from;
        $to = is_numeric($to) ? gmdate('Y-m-d H:i:s', $to) : $to;
        
        if (Validator::validateIsoDate($from) && Validator::validateIsoDate($to)) {
            $fromDate = new DateTime($from);
            $toDate = new DateTime($to);
            if ($toDate->getTimestamp() > $fromDate->getTimestamp()) {
                $interval = $toDate->diff($fromDate);
                $totalDays = $interval->format('%a');
                $hours = $interval->format('%h');
                $minutes = $interval->format('%i');
                $minutes += ($totalDays * 24 * 60) + ($hours * 60);
                return (strlen($minutes) == 1 ? '0' : '') . $minutes . ':' . $interval->format('%S');
            } else {
                return '00:00';
            }
        } else {
            return '';
        }
    }

    public static function validateDateTime($date)
    {
        $date = is_numeric($date) ? gmdate('Y-m-d H:i:s', $date) : $date;
        return Validator::validateIsoDate($date);
    }

    public static function getRemainingTime($time)
    {
        $now = self::getTimestampUTC();
        try {
            if (is_numeric($time)) {
                return $now <= $time ? $time - $now : 0;
            } else {
                return 'NaN';
            }
        } catch (Exception $e) {}
        return 'NaN';
    }

    public static function getDifferenceTime($from, $to)
    {
        if (is_numeric($from) && is_numeric($to)) {
            return $to - $from;
        }
        return '';
    }
    
    public static function getTimestampUTC()
    {
        return time() + date('Z') + -5;
    }
    
    public static function getFirstOfMonthOffset($date)
    {
        return (int)date('N', strtotime($date['year'] . '-' . $date['month']) );
    }
    
    public static function getLastOfMonthOffset($date)
    {
        $day = date('N' , strtotime('-1 second', strtotime('+1 month', strtotime($date['year'] . "-". $date['month']) ) ) );
        return $day ==  7 ? 6 : 6 - $day;
    }
    
    /*
     *  return true if $b is the same day as $a
     * 
     *  @params int $days e.g: 1 to 31
     *  @params int $timestamp
     */
    public static function sameDay($days , $timestamp)
    {
        return date('j', $timestamp) == $days;
    }
    
    public static function getDayTimestampUTC() 
    {
        $year = date('Y', self::getTimestampUTC());
        $month = date('m', self::getTimestampUTC());
        $day = date('d', self::getTimestampUTC());
        
        return gmmktime(0, 0, 0, $month, $day, $year);
    }
    
     /**
     * From a specific timestamp, return the time of the day.
     * 
     * @param integer $timestamp
     * @param string $timeZone timeZone (e.g : 'America/Montreal'
     * @return string getText
     */
    public static function timeOfTheDay($timestamp, $timeZone)
    {
        $currentTimeZone = 'America/Montreal';
        $timeZones = DateTimeZone::listIdentifiers();
        $dateTimeZone = new DateTimeZone(in_array($timeZone, $timeZones) ? $timeZone : $currentTimeZone);
        $dateTime = new DateTime(is_numeric($timestamp) ? "@$timestamp" : $timestamp);

        $timestamp = $timestamp + $dateTimeZone->getOffset($dateTime);

        $hour = date('H', $timestamp);
        if ($hour > 0 && $hour <= 12) {
            return __('morning @');
        } else if ($hour > 12 && $hour <= 18) {
            return __('afternoon @');
        } else {
            return __('night @');
        }
    }

}
