<?php

namespace Fierce;

$page = Page::createNew();
$page->url = '/';
$page->name = 'Home';
$page->content = '<p>Hello World</p>';
$page->nav = 'main';
$page->navPositionLeft = 1;
$page->navPositionRight = 2;
$page->navPositionDepth = 0;
$page->modifiedBy = 'setup';
$page->save();

$page = Page::createNew();
$page->url = '/404';
$page->name = '404 Not Found';
$page->content = "
  <h1>Not Found</h1>
  
  <p>The requested URL <span id=\"url\"></span> was not found on this server.</p>
  
  <script type=\"text/javascript\">
    var url = document.location.toString().replace(/.*abhibeckert.com/, '');
  
    document.getElementById('url').innerHTML = url;
  </script>
";
$page->modifiedBy = 'setup';
$page->save();

$page = Page::createNew();
$page->url = '/login';
$page->name = 'Login';
$page->content = '';
$page->class = 'Fierce\LoginController';
$page->modifiedBy = 'setup';
$page->save();

$page = Page::createNew();
$page->url = '/admin';
$page->name = 'Pages';
$page->content = '';
$page->class = 'Fierce\PagesController';
$page->modifiedBy = 'setup';
$page->save();

$page = Page::createNew();
$page->url = '/admin/users';
$page->name = 'Users';
$page->content = '';
$page->class = 'Fierce\UsersController';
$page->modifiedBy = 'setup';
$page->save();

$user = User::createNew();
$user->name = 'Admin';
$user->email = 'admin';
$user->newPassword = 'test';
$user->modifiedBy = 'setup';
$user->save(false);
