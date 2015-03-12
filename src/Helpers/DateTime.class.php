<?php
namespace Helpers;

use \Helpers;

final class DateTime extends \DateTime {

	public function __construct($time = "now", \DateTimeZone $timezone = NULL) {
		parent::__construct($time, $timezone);
	}
	
	/**
	 * @return string
	 */
	public function getDateTime ($format = 'Y-m-d H:i:s') {
		return $this->format($format);
	}
	
	/**
	 * @return string
	 */
	public function getDate($format = 'Y-m-d') {
		return $this->format($format);
	}
	
	/**
	 * @return string
	 */
	public function getTime ($format = 'H:i:s') {
		return $this->format($format);
	}
	
	/**
	 * 
	 */
	public static function getCurrentTimestamp () {
		return date("Y-m-d H:i:s", time());	
	} 
	
	/**
	 * 
	 * @param unknown $datetime
	 * @param unknown $utc_datetime
	 * @param unknown $local_datetime
	 * @param unknown $timezone_label
	 * @param string $is_utc
	 */
	public static function getUTCAndLocalDatetimes ($datetime, &$utc_datetime, &$local_datetime, $timezone_label, $is_utc = FALSE) {
		if ($is_utc) {
			$utc_datetime =  new Helpers\DateTime($datetime, new \DateTimeZone("Europe/London"));
			$local_datetime = new Helpers\DateTime($datetime, new \DateTimeZone("Europe/London"));
			$local_datetime->setTimezone(new \DateTimeZone($timezone_label));
		} else {
			$utc_datetime =  $local_datetime = new Helpers\DateTime($datetime, new \DateTimeZone($timezone_label));
			$utc_datetime->setTimezone(new \DateTimeZone("Europe/London"));
		}
	}
	
	public function __toString () {
		return $this->getDateTime();
	}
}