# Shieldon :shield:

[![Build Status](https://travis-ci.org/terrylinooo/shieldon.svg?branch=master)](https://travis-ci.org/terrylinooo/shieldon)

Shieldon, a PHP library that provides anti-scraping, XSS filitering and traffic controll for your web application. As if you are using a shield on your web applicaion to fight against bad-behavior bots, crawlers or vulnerability scanning and so on.

`version: 1.0.0.alpha`

Don't use it until first release come.

## Get started

Shieldon requires at least `PHP 7.1` to run.

### Install

Use PHP Composer:
```shell
composer require terrylinooo/shieldon
```
Or, download it and include the Shieldon autoloader.
```php
require 'Shieldon/src/autoload.php';
```

### How to use

Here is a full example let you know how Shieldon works.

```php
$shieldon = new \Shieldon\Shieldon();

// Use SQLite as the data driver.
$dbLocation = APPPATH . 'cache/shieldon.sqlite3';
$pdoInstance = new \PDO('sqlite:' . $dbLocation);
$shieldon->setDriver(new \Shieldon\Driver\SqliteDriver($pdoInstance));

// Set core components.
$shieldon->setComponent(new \Shieldon\Component\Ip());
// This compoent will only allow popular search engline.
// Other bots will go into the checking process.
$shieldon->setComponent(new \Shieldon\Component\Robot());

// You can ignore this setting if you only use one Shieldon on your web application. This is for multiple instances.
$shieldon->setChannel('web_project');

// Only allow 10 sessions to view current page.
// The defailt expire time is 300 seconds.
$shieldon->limitSession(10);

// Set a Captcha servie. For example: Google recaptcha.
$shieldon->setCaptcha(new \Shieldon\Captcha\Recaptcha([
    'key' => '6LfkOaUUAAAAAH-AlTz3hRQ25SK8kZKb2hDRSwz9',
    'secret' => '6LfkOaUUAAAAAJddZ6k-1j4hZC1rOqYZ9gLm0WQh',
]));

// Start protecting your website!

$result = $shieldon->run();

// The result:
// 0 - banned
// 1 - passed
// 2 - busy (limit online sessions)
if ($result === 0) {
    if ($shieldon->captchaResponse()) {
        // Unban current session.
        $shieldon->unban();
    } else {
        // Output the result page with HTTP status code 200.
        $shieldon->output(200);
    }
} elseif ($result === 2) {
    // Outupt the busy page.
    $shieldon->output();
}


```

## Screenshots

When users or robots are trying to view many your web pages at a very short period of time. They will temporarily get banned. 

![](https://i.imgur.com/lzWiw4V.png)

When an user has reached the online session limit.
![](https://i.imgur.com/oQrlzH6.png)

You can set the online session limit by using `limitSession` API.

## Other usages:
```php
// Ban an IP address 33.125.12.87 immediately.
$shieldon->banIP('33.125.12.87');

// Remove XSS string from $_GET['key'];
$shielon->xssClean('GET', 'key');
// Other examples:
$shielon->xssClean('POST', 'content');
$shielon->xssClean('COOKIE', 'tracking');
$shielon->xssClean('POST');

// Limit 500 sessions in 300 seconds.
$shieldon->limitSession(500, 300);

// Set a custom error page. It will display to the vistors who are blocked.
// Blocked user must solve CAPTCHA to continue browsering.
$shieldon->setHtml($html, 'stop');
```

---

## Drivers

- MySQL
- File (Todo)
- Redis (Todo)
- SQLite

### MySQL
```php
$db = [
    'host' => '127.0.0.1',
    'dbname' => 'test_projects',
    'user' => 'root',
    'pass' => 'test1234',
    'charset' => 'utf8',
];

$pdoInstance = new \PDO(
    'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'],
    $db['user'],
    $db['pass']
);

$shieldon->setDriver(new \Shieldon\Driver\MysqlDriver($pdoInstance));
```

### SQLite
```php
$dbLocation = APPPATH . 'cache/shieldon.sqlite3';
$pdoInstance = new \PDO('sqlite:' . $dbLocation);
$shieldon->setDriver(new \Shieldon\Driver\SqliteDriver($pdoInstance));
```

### Redis

Ongoing...
```php
$redis = new \Redis();
$redis->connect('127.0.0.1', 6319);
$shieldon->setDriver(new \Shieldon\Driver\RedisDriver($redis));
```

### File

Ongoing...
```php
$shieldon->setDriver(
    new \Shieldon\Driver\FileDriver([
        'directory' => APPPATH . '../cache/shieldon',
        'extension' => 'json',
    ])
);
```

---

## APIs

- setDriver
- setComponent
- setProperty
- setProperties
- setChannel
- setCaptcha
- captchaResponse
- setHtml
- createDatabase
- allow
- deny
- ban
- unban
- limitSession
- xssClean
- run

The public APIs can be chaining yet `SetDriver` must be the first and `run` must be the last.

### setDriver
```php
/**
 * @param DriverProvider
 * @return $this
 */
$dbLocation = APPPATH . 'cache/shieldon.sqlite3';
$pdoInstance = new \PDO('sqlite:' . $dbLocation);
$shieldon->setDriver(new \Shieldon\Driver\SqliteDriver($pdoInstance));
```

### setComponent
```php
/**
 * @param ComponentInterface
 * @return $this
 */
$shieldon->setComponent(new \Shieldon\Component\Ip());
```

### setProperty
```php
/**
 * @param string $key
 * @param mixed  $value
 */
$shieldon->setProperty('time_unit_quota', [
    ['s' => 4, 'm' => 20, 'h' => 60, 'd' => 240]
]);

// default settings
private $properties = [
    'time_unit_quota'        => ['s' => 2, 'm' => 10, 'h' => 30, 'd' => 60],
    'time_reset_limit'       => 3600,
    'interval_check_referer' => 5,
    'interval_check_session' => 30,
    'limit_unusual_behavior' => ['cookie' => 5, 'session' => 5, 'referer' => 10],
    'cookie_name'            => 'ssjd',
    'cookie_domain'          => '',
];
```

### setChannel
```php
/**
 * @param string Channel name.
 */
$shieldon->setChannel('web_project');

// Start new shieldon each day.
$shieldon->setChannel('web_project_' . date('Ymd'));
```

### setCaptcha

```php
/**
 * @param CaptchaInterface
 * @return $this
 */
$shieldon->setCaptcha(new \Shieldon\Captcha\Recaptcha([
    'key' => '6LfkOaUUAAAAAH-AlTz3hRQ25SK8kZKb2hDRSwz9',
    'secret' => '6LfkOaUUAAAAAJddZ6k-1j4hZC1rOqYZ9gLm0WQh',
    'version' => 'v2',
    'lang' => 'en',
]));

```

### captchaResponse
```php
/**
 * @return bool true: Captcha is solved successfully, false overwise.
 */
$result = $this->captchaResponsse();
```

### createDatabase
```php
/**
 * @param bool true or false (defaukt: true)
 */
$this->createDatabase(false);
```

### setHtml
```php
/**
 * @param string HTML text.
 * @return $this
 */
$htmlText = '<html>...bala...bala...</html>';
$this->setHTML($htmlText);
```

### xssClean
```php
/**
 * @param string variable type: POST, COOKIE, GET, GLOBAL
 * @param string key name
 * @param bool   true: replacd with null if contains illegal charactor.
 *               false: replacd with filitered string.
 * @return $this
 */
$shielon->xssClean('POST');
```

### deny

```php
/**
 * @param string Single IP address or IP range
 * @return $this
 */
$shieldon->deny('33.125.12.1');  
$shieldon->deny('33.125.12.1/24'); // deny 33.125.12 C class.
```

### allow
```php
/**
 * @param string Single IP address or IP range
 * @return $this
 */
$shieldon->allow('33.125.12.1');  
$shieldon->allow('33.125.12.1/24'); // allow 33.125.12 C class.
```

### ban

```php
/**
 * @param string Single IP address
 * @return $this
 */
$shieldon->ban('33.125.12.87');

```

### unban

```php
/**
 * @param string Signle IP address
 * @return $this
 */
$shieldon->unban('33.125.12.87');

```

### limitSession

```php
/**
 * @param integer Maximum amount of online vistors.
 * @param integer Period. (Unit: second)
 * @return $this
 */
$shieldon->setSession(500, 300);
```

### run

```php
/**
 * @return integer Reponse code. 0 => banned. 1 => passed. 2 => session limit.
 */
$result = $shieldon->run();
```

## Component API

- [Ip](https://github.com/terrylinooo/shieldon/wiki/Component-Ip)
- [Robot](https://github.com/terrylinooo/shieldon/wiki/Component-Robot)

---

## License

MIT

## Author

- [Terry Lin](https://terryl.in)
