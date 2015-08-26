# Fierce Web Framework

Fierce is a fast, minimal and secure framework with a user friendly CMS.

It's especially suited to a consulting form with an experienced team of coders
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
  RewriteCond %{REQUEST_URI} !dispatch.php$
  RewriteRule ^(/?)(.*)$ $1vendor/fierce/fierce/dispatch.php [L,QSA]
  ```

2. Create a `fierce-config.php` file, replacing the example salt with a unique one
(You can use `$ random | shasum`):

  ```php
  <?php
  
  define('F_AUTH_SALT', '8d6f6390017eb415bcf468a050d893628e40d12f');
  
  if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define('F_DISABLE_CACHE', true);
  }
  
  ```

3. Create a `classes` directory containing your class definitions. Your classes
will be autoloaded in the global namespace and must be PSR-4 compliant aside
from the requirement to be outside the global namespace).

4. Add a `views` directory for tpl files (using inline PHP).

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
