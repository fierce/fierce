# Fierce
Fierce Web Framework

## Usage

In your DOCUMENT_ROOT, add this `.htaccess`:

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
RewriteRule ^(/?)(.*)$ $1fierce/dispatch.php [L,QSA]
```

Add a `classes` directory with all your php class definitions (all of them will be autoloaded).

Add a `views` directory for your tpl files.

Add Fierce as a submodule:

```
git submodule add https://github.com/abhibeckert/Fierce fierce
```

Create some pages and users in your database (.. this step is difficult .. @TODO: make it easier).
