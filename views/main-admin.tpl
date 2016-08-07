<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="{{ base_url }}" id="base_href" data-fierce-base="{{ "#{base_url}#{fierce_src}" }}">
  
  <title>{{ pageTitle | default("") }} – {{ site_name }} Administration</title>

  {% include_css "#{fierce_src}css/admin-base.css" %}
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
      <img src="images/logo.png" width="59" height="48" valign="middle" style="margin-right: 10px">
      
      {% include 'admin-nav.tpl' %}
    </div>
  {% endif %}
  
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
