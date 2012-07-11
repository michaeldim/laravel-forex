<?php

return array(
	/**
	 * Api Key
	 *
	 * This is required to make requests to the Open Exchange Rates api
	 */
	'api_key' => '',

	'caching' => array(
		/**
		 * Caching
		 *
		 * Enable caching of rates and currencies data.
		 */
		'enabled' => true,

		/**
		 * Time
		 *
		 * The time in minutes to cache the rates/currencies data for.
		 */
		'time' => array(
			'historical' => 44640, // 1 month
			'latest' => 15, // 15 minutes (Open Exchange Rates refreshes latest.json every 15 mins)
			'currencies' => 1440 // 1 day
		),
	),
);
