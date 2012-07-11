<?php

/**
 * Laravel Forex
 *
 * An Open Exchange Rates API bundle for Laravel PHP Framework
 *
 * @category  Laravel
 * @package   Forex
 * @author    Michael Dimmock <michael.dimmock@virgin.net>
 * @copyright 2012 - Michael Dimmock
 * @license   MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @version   1.0.1
 * @link      https://github.com/michaeldim/laravel-Forex
 */
class Forex
{
    /**
     * Configuration settings.
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Stores the exchange rates.
     *
     * @var array
     */
    private $_rates = array();

    /**
     * The disclaimer statement.
     *
     * @var string
     */
    private $_disclaimer;

    /**
     * The licence agreement.
     *
     * @var string
     */
    private $_license;

    /**
     * The base currency code.
     *
     * @var string
     */
    private $_base;

    /**
     * The primary currency code.
     *
     * @var string
     */
    private $_primary;

    /**
     * The quoted currency code.
     *
     * @var string
     */
    private $_quote;

    /**
     * Timestamp of exchange rates.
     *
     * @var string
     */
    private $_timestamp;

    /**
     * List of currencies available.
     *
     * @var object
     */
    private $_currencies;

    /**
     * Make a new instance of the API client
     *
     * @param string $date  date to use for exchange rate
     * @param bool   $cache whether to use cached results
     */
    public function __construct($date = null)
    {
        $this->_config = \Config::get('forex::config');

        $fx = null;
        $currencies = null;

        if ($date) {
            if ($this->_config['caching']['enabled'] == true) {
                $fx = \Cache::get('fx_historical_' .$date);

                if (empty($fx)) {
                    $filename = 'historical/' .$date. '.json';
                    $fx = $this->curl($filename);

                    // cache forever as historical will not change
                    \Cache::put('fx_historical_' .$date, $fx, $this->_config['caching']['time']['historical']);
                }
            } else {
                $filename = 'historical/' .$date. '.json';
                $fx = $this->curl($filename);
            }

        } else {
            if ($this->_config['caching']['enabled'] == true) {
                $fx = \Cache::get('fx_latest');

                if (empty($fx)) {
                    $filename = 'latest.json';
                    $fx = $this->curl($filename);

                    \Cache::put('fx_latest' .$date, $fx, $this->_config['caching']['time']['latest']);
                }
            } else {
                $filename = 'latest.json';
                $fx = $this->curl($filename);
            }
        }

        if ($this->_config['caching']['enabled'] == true) {
            $currency = \Cache::get('fx_currencies');

            if (empty($currency)) {
                $filename = 'currencies.json';
                $currencies = $this->curl($filename);
                \Cache::put('fx_currencies', $currencies, $this->_config['caching']['time']['currencies']);
            }
        } else {
            $filename = 'currencies.json';
            $currencies = $this->curl($filename);
        }

        $this->_rates       = $fx->rates;
        $this->_currencies  = $currencies;
        $this->_base        = $fx->base;
        $this->_disclaimer  = $fx->disclaimer;
        $this->_license     = $fx->license;
        $this->_timestamp   = $fx->timestamp;
    }

    /**
     * Get a new instance of the API client
     *
     * @param string $date  date to use for exchange rate
     * @param bool   $cache whether to use cached results
     *
     * @return object
     */
    public static function fetch($date = null)
    {
        return new self($date);
    }

    /**
     * Return the currency exchange rates relative to the base currency (USD).
     *
     * @return object
     */
    public function rates()
    {
        return $this->_rates;
    }

    /**
     * Return the base currency from api
     *
     * @return string
     */
    public function base()
    {
        return $this->_base;
    }

    /**
     * Return the license from api
     *
     * @return string
     */
    public function license()
    {
        return $this->_license;
    }

    /**
     * Return the timestamp from api
     *
     * @return string
     */
    public function timestamp()
    {
        return $this->_timestamp;
    }

    /**
     * Return the disclaimer from api
     *
     * @return string
     */
    public function disclaimer()
    {
        return $this->_disclaimer;
    }

    /**
     * Return the currencies available from api
     *
     * @return object
     */
    public function currencies()
    {
        return $this->_currencies;
    }

    /**
     * Primary currency to convert from
     *
     * @param string $primary iso 4217 currency 3-letter code
     *
     * @return object
     */
    public function from($primary = null)
    {
        if ($primary) {
            $this->_primary = strtoupper($primary);
        } else {
            $this->_primary = $this->_base;
        }

        return $this;
    }

    /**
     * Quote currency to convert to
     *
     * @param string $quote iso 4217 3-letter currency code
     *
     * @return object
     */
    public function to($quote)
    {
        $this->_quote = strtoupper($quote);

        return $this;
    }

    /**
     * Find rate between any two currencies, relative to base currency (USD)
     *
     * @param float $amount amount to convert
     *
     * @return string converted exchange rate
     */
    public function convert($amount = null)
    {
        $rate = $this->_rates->{$this->_quote} * ($this->_rates->{$this->_base}
            / $this->_rates->{$this->_primary});

        if (is_numeric($amount)) {
            return $amount * $rate;
        } else {
            return $rate;
        }
    }

     /**
     * Retrieve data from Open Exchange Rates API
     *
     * @param string $filename api url to access
     *
     * @return array api exchange rates retrieved
     */
    protected function curl($filename)
    {
        if (function_exists('curl_version')) {

            $api_key = $this->_config['api_key'];

            $ch = curl_init('http://openexchangerates.org/' .$filename. '?app_id=' .$api_key);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response);
        }
    }

}
