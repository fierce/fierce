<? namespace Fierce ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="{{ BASE_URL }}" id="base_href" data-fierce-base="{{ "#{BASE_URL}#{FIERCE_SRC}" }}">
  
  <title>{{ pageTitle }} – {{ SITE_NAME }} Administration</title>

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

<div id="header">
  {% if loggedInUser %}
    <div id="nav">
      {% include 'admin-nav.tpl' %}
    </div>
  {% endif %}
  
  <div id="header-title">
    {{ SITE_NAME }}
  </div>
  
  {% if loggedInUser %}
    <div id="login-status">
      <span class="username">{{ loggedInUser.email }}</span>
      <a href="login?do=logout">Log Out</a>
    </div>
  {% endif %}
</div>

<div id="content">
  {{ contentViewHtml|raw }}
</div>

</body>
</html>
