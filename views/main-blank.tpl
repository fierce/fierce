<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <base href="{{ base_url }}" id="base_href" data-fierce-base="{{ "#{base_url}#{fierce_src}" }}">
  
  <title>{{ pageTitle | default("") }}</title>
  
  {% include_css "#{fierce_src}css/plain.css" %}
  
  {% for cssUrl in cssUrls %}
    {% include_css cssUrl %}
  {% endfor %}
  
  {% for scriptUrl in scriptUrls %}
    {% include_script scriptUrl %}
  {% endfor %}
</head>
<body>

{{ contentViewHtml|raw }}

</body>
</html>
