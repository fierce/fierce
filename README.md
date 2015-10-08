# Fierce Web Framework

Fierce is a fast, minimal and secure framework with a user friendly CMS.

It's especially suited to a consulting firm with an experienced team of coders
creating a website on behalf of a non-tech-savvy client who will self manage
the website after the initial build.

***WARNING:*** *Fierce is currently unstable and breaking changes are frequent.*

## Usage

1. In your `DOCUMENT_ROOT` add a `.htaccess`:

  ```apache
  Options +MultiViews -Indexes
  
  RewriteEngine on
  
  # real files are served normally by apache
  RewriteCond %{REQUEST_FILENAME} -f
  RewriteRule ^(.*)$ - [L,QSA]
  
  # homepage is served normally if index.html exists
  RewriteCond %{REQUEST_URI} ^/?$
  RewriteCond %{DOCUMENT_ROOT}/index.html -f [NC]
  RewriteRule ^(.*)$ - [L,QSA]
  
  # redirect all other traffic to the dispatcher
  RewriteCond %{REQUEST_URI} !index.php$
  RewriteRule ^(/?)(.*)$ $1vendor/index.php [L,QSA]
  ```

2. Create an `index.php` file, replacing the example salt with a unique one
(You can use `$ random | shasum`):

  ```php
  <?php
  
  // init composer
  $autoloader = require __DIR__ . '/vendor/autoload.php';
  $autoloader->addPsr4(false, __DIR__ . '/classes/');
  
  // config
  date_default_timezone_set('Australia/Brisbane');
  
  Fierce\Env::set('auth_salt', '51c9ea6d59f22130bb3ae33ae42751cbfa1ed8c0');
  Fierce\Env::set('site_name', 'My Great Website');
  
  // connect to database and hand it over to the CMS
  $db = new Fierce\DB('file', Fierce\Env::get('E') . 'data/');
  Fierce\CMS::handleRequest($db);
  
  ```

3. Create a `classes` directory containing your class definitions. Your classes
will be autoloaded in the global namespace and must be PSR-4 compliant (aside
from the requirement to be outside the global namespace).

4. Add a `views` directory for tpl files (using TWIG).

5. Use composer to add Fierce

  ```json
  {
    "require": {
      "fierce/fierce": "dev-master"
    }
  }
  
  ```

6. Create the your CMS pages, template and at least one Admin user in your
database (this currently requires deep understanding of how Fierce works.
Sorry).
