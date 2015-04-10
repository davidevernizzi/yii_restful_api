# yii_restful_api
A simple REST API written with Yii 1.1.x

# Installation
0. Install a LAMP environment and composer
1. `git clone`
2. `cd protected`
3. `composer install`
4. run migrations
5. run tests:
  * `cd protected/tests`
  * `../vendor/bin/phpunit unit/`

# Usage
Create a new controller for the resource you need:
```php
<?php

class FooController extends ApiController
{
    public function actionIndex()
    {
    /* Implementation for GET */
    }

    public function actionCreate()
    {
    /* Implementation for POST */
    }

    public function actionUpdate()
    {
    /* Implementation for PUT */
    }

    public function actionDelete()
    {
    /* Implementation for DELETE */
    }
}
```

If any verb is not used, simply do not write its function. The following example shows a resource that only responds to GET. For other verbs a `501: not implemented` will be returned
```php
<?php

class BarController extends ApiController
{
    public function actionIndex()
    {
    /* Implementation for GET */
    }
}
```
That's all.

You can also create mockups. The following API returns data from the file `protected/mockups/foobar_index` (in general, filename convention is controller_action). If the file is not found, it returns a `501: not implemented`.
```php
<?php

class FoobarController extends ApiController
{
    public function actionIndex()
    {
        $this->mock();
    }
}
```



# TODO
* create table for api tokens
* API Authentication
  * Get token out of band
  * Get token through username/password (see http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified @ Authorization/Other/Password)
* Client signup/login
  * Google+
  * FB ?
  * Twitter ?
  * Yii usergroups ?
* Log
  * All calls with params + response
  * Last usage
* API Token namagement
  * CRUD
  * Expiration
* Consider using HAL http://stateless.co/hal_specification.html


# References
* Quick description of OAuth: http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified
* RESTFul Cookbook: http://restcookbook.com/
* How to compute API call digest: http://broadcast.oreilly.com/2009/12/principles-for-standardized-rest-authentication.html @ Query Authentication

