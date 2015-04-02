# yii_restful_api
A simple REST API written with Yii 1.1.x

# Installation
0. Install a LAMP environment and composer
1. `git clone`
2. `cd protected`
3. `composer install`
4. run tests:
  * `cd protected/tests`
  * `../vendor/bin/phpunit unit/`

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
* API Token namagement
  * CRUD
  * Expiration
* Consider using HAL http://stateless.co/hal_specification.html

# References
* Quick description of OAuth: http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified
* RESTFul Cookbook: http://restcookbook.com/
* How to compute API call digest: http://broadcast.oreilly.com/2009/12/principles-for-standardized-rest-authentication.html @ Query Authentication

