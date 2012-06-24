#Laravel Forex

Open Exchange Rates Provider API (http://openexchangerates.org/) bundle for Laravel

##Installation

Install using artisan for Laravel :

    php artisan bundle:install forex

Now simply add forex to your `application/bundles.php` with auto start enabled :

    return array('forex' => array('auto' => true));

##Usage

Convert one currency to another:

    echo Forex::fetch()->from('USD')->to('GBP')->convert();
    // 0.64377

Convert currencies using historical date:

    echo Forex::fetch('2012-01-01')->from()->to('GBP')->convert();
    // 0.643232 (USD is base rate)

Convert specified amount of currency:

    echo Forex::fetch()->from('eur')->to('usd')->convert(10.50);
    // 13.17855

Fetch unix timestamp of rates retrieved:

    echo Forex::fetch()->timestamp();
    // 1339687716

Get exchange rate list or rate for a particular currency:

    echo Forex::fetch->rates()->{'JPY'};
    // 79.317001

Show the national currency by iso code (3-letter abbreviation):

    echo Forex::currencies()->{'EUR'};
    // Euro

Get the base currency used by the api

    echo Forex::fetch()->base();
    // USD

Show details of the api license:

    echo Forex::fetch()->license();
    // Data collected from various providers with public-facing APIs; copyright may apply; not for...

Show details of the api disclaimer:

    echo Forex::fetch()->disclaimer();
    // This data is collected from various providers and provided free of charge for informational...

---

See the [Open Exchange Rates Provider API documentation](http://openexchangerates.org/documentation/) for API details.
