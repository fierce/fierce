# Fierce Web Framework

Fierce is a fast, minimal and secure framework with a user friendly CMS.

It's especially suited to a consulting firm with an experienced team of coders
creating a website on behalf of a non-tech-savvy client who will self manage
the website after the initial build.

***WARNING:*** *Fierce is currently unstable and breaking changes are frequent.*

## Setup a Feirce website

Export a copy of the example template:

    svn export https://github.com/fierce/site-template.git/trunk example.com

Use [Composer](https://getcomposer.org) to load dependencies:

    cd example.com
    composer install

Update `config.php`, especially:
  - specify your timezone
  - generate a new auth_salt using `uuidgen | shasum`
  - specify your site name
  - create a  and enter connection details

Run migrations by loading http://localhost/example.com/vendor/fierce/fierce/maint/migrate.php

Navigate to the http://localhost/example.com/admin and login with user `admin` password `test`. From here you can setup pages and specify a new password.

The site design can be changed by editing `views/main-public.tpl`. Add any custom PHP classes to the `classes` directory. Any subclass of `PageController` can be attached to a URL by creating a new entry in the `Page` databse table.

