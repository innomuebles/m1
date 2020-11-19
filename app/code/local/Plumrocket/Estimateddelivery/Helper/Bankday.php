<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package	Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Helper_Bankday extends Mage_Core_Helper_Url
{
	protected static $holidays = array();
	protected static $weekends = array(0, 6);

	public static function loadHolidays($type)
	{
		self::$holidays = array();

		if(!$items = json_decode(Mage::getStoreConfig('estimateddelivery/' . $type . '/holidays'), true)) {
			$items = array();
		}

		foreach ($items as $item) {
			switch ($item['date_type']) {
				case 'recurring_date':
					if(!empty($item[ $item['date_type'] ]['month']) && !empty($item[ $item['date_type'] ]['day'])) {
						self::$holidays[] = str_pad($item[ $item['date_type'] ]['month'], 2, '0', STR_PAD_LEFT)
							.'-'. str_pad($item[ $item['date_type'] ]['day'], 2, '0', STR_PAD_LEFT);
					}
					break;

				case 'single_day':
					if(!empty($item[ $item['date_type'] ])) {
						self::$holidays[] = date('m-d-Y', $item[ $item['date_type'] ]);
					}
					break;

				case 'period':
					if(!empty($item[ $item['date_type'] ]['start']) && !empty($item[ $item['date_type'] ]['end'])) {
						$startTs = $item[ $item['date_type'] ]['start'];
						$endTs = $item[ $item['date_type'] ]['end'];
						$end = date('m-d-Y', $endTs);

						self::$holidays[] = date('m-d-Y', $startTs);
						if($startTs < $endTs) {
							for($n = 1; $n <= 50; $n++) {
								$date = date('m-d-Y', strtotime("+{$n} days", $startTs));
								self::$holidays[] = $date;
								if($date == $end) {
									break;
								}
							}
						}
					}
					break;
			}
		}

		// load weekends
		$config = Mage::getStoreConfig('estimateddelivery/' . $type . '/weekend');
		self::$weekends = explode(',', $config);
	}

	// Prepare date
	public static function prepareDate($s)
	{
		if ($s !== null && !is_int($s)) {
			$ts = strtotime($s);
			if ($ts === -1 || $ts === false) {
				throw new Exception('Unable to parse date/time value from input: '.var_export($s, true));
			}
		}
		else {
			$ts = $s;
		}
		return $ts;
	}

	public static function isWeekend($date)
	{
		$ts = self::prepareDate($date);
		return in_array(date('w', $ts), self::$weekends);
	}

	public static function isHoliday($date)
	{
		$ts = self::prepareDate($date);
		return in_array(date('m-d-y', $ts), self::$holidays) 
			|| in_array(date('m-d-Y', $ts), self::$holidays)
			|| in_array(date('m-d', $ts), self::$holidays);
	}

	// Get weekends with holidays
	public static function getHolidays($date, $interval = 60)
	{
		$ts = self::prepareDate($date);
		$holidays = array();

		for ($i = -$interval; $i <= $interval; $i++) {
			$curr = strtotime($i.' days', $ts);

			if (self::isWeekend($curr) || self::isHoliday($curr)) {
				$holidays[] = date('Y-m-d', $curr);
			}
		}

		// move holidays to next work day
		/*
		foreach ($holidays as $date) {
			$ts = self::prepareDate($date);
			if (self::isHoliday($ts) && self::isWeekend($ts)) {
				$i = 0;
				while (in_array(date('Y-m-d', strtotime($i.' days', $ts)), $holidays)) {
					$i++;
				}
				$holidays[] = date('Y-m-d', strtotime($i.' days', $ts));
			}
		}*/

		return $holidays;
	}


	// get date + n bank days
	public static function getEndDate($type, $start, $days, $format = null)
	{
		self::loadHolidays($type);

		$ts = self::prepareDate($start);
		$holidays = self::getHolidays($start);

		if (Mage::getStoreConfig('estimateddelivery/' . $type . '/time_after_enable')) {
			// $timeAfter = Mage::getStoreConfig('estimateddelivery/' . $type . '/time_after');
			// $timeAfter = strtotime( date('Y-m-d', $ts) .' '. str_replace(',', ':', $timeAfter) .':00' );
			list($hour, $minute) = explode(',', Mage::getStoreConfig('estimateddelivery/' . $type . '/time_after'));
			if (date('H', $ts) >= $hour && date('i', $ts) > $minute) {
				$holidays[] = date('Y-m-d', $ts);
			}
		}

		for ($i = 0; $i <= $days; $i++) {
			$curr = strtotime('+'.$i.' days', $ts);
			if (in_array(date('Y-m-d', $curr), $holidays)) {
				$days++;
			}
		}

		if ($format) {
			return date($format, strtotime('+'.$days.' days', $ts));
		}
		else {
			return strtotime('+'.$days.' days', $ts);
		}
	}
}