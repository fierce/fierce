<? namespace Fierce ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="{{ BASE_URL }}" id="base_href" data-fierce-base="{{ "#{BASE_URL}#{FIERCE_SRC}" }}">
  
  <title>{{ pageTitle }}</title>
  
  {% include_css "#{FIERCE_SRC}css/admin-base.css" %}
  {% include_css 'css/admin.css' %}
  
  {% for cssUrl in cssUrls %}
    {% include_css cssUrl %}
  {% endfor %}
  
  {% for scriptUrl in scriptUrls %}
    {% include_script scriptUrl %}
  {% endfor %}
</head>
<body>

{% if loggedInUser %}
<div id="header" class="buttons">
  <div id="login-status">
    <span class="username">{{ loggedInUser.email }}</span>
    <a href="login?do=logout">Log Out</a>
  </div>
  
  <div id="nav">
    {% include 'admin-nav.tpl' %}
  </div>
</div>
{% endif %}

{{ contentViewHtml|raw }}

</body>
</html>
