## Laravel HMVC.

### For Laravel 4, please use the [v1.x branch](https://github.com/teepluss/laravel-hmvc/tree/v1.x)!

HMVC is a tool for making internal request.

### Installation

- [HMVC on Packagist](https://packagist.org/packages/teepluss/hmvc)
- [HMVC on GitHub](https://github.com/teepluss/laravel-hmvc)

To get the lastest version of HMVC simply require it in your `composer.json` file.

~~~
"teepluss/hmvc": "dev-master"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once HMVC is installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

~~~
'providers' => array(

    'Teepluss\Hmvc\HmvcServiceProvider'

)
~~~

HMVC also ships with a facade which provides the static syntax for creating collections. You can register the facade in the `aliases` key of your `config/app.php` file.

~~~
'aliases' => [

    'HMVC' => 'Teepluss\Hmvc\Facades\HMVC',

]
~~~

## Usage

HMVC helping you to work with internal request.

- [Internal testing request](#internal-testing-request)
- [Calling via artisan CLI](#calling-via-artisan-cli)

### Internal testing request.

~~~php
// GET Request.
HMVC::get('user/1');

// POST Request.
HMVC::post('user', array('title' => 'Demo'));

// PATCH Request.
HMVC::patch('user/1', array('title' => 'Changed'));

// PUT Request.
HMVC::put('user/1', array('title' => 'Changed'));

// DELETE Request.
HMVC::delete('user/1');

// Internal request with domain route.
HMVC::invoke('/someinternalpath', 'post', array('param' => 1))

// You can make remote request without changing code also.
HMVC::post('http://api.github.com', array('username' => 'teepluss'));

// Request remote with invokeRemote.
HMVC::invokeRemote('http://api.github.com', 'post', array('username' => 'teepluss'));

// Configure remote client.
$config = array('auth' => array('admin', 'admin'));
echo HMVC::configureRemoteClient($config)->get('http://127.0.0.1:9200');

// Get Guzzle to use other features.
$guzzle = HMVC::getRemoteClient();
~~~
>> Remote request using [Guzzle](http://guzzlephp.org/) as an adapter.

## Support or Contact

If you have some problem, Contact teepluss@gmail.com

[![Support via PayPal](https://rawgithub.com/chris---/Donation-Badges/master/paypal.jpeg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9GEC8J7FAG6JA)