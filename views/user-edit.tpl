<h1>{{ formType }} {{ noun }}</h1>

{% set autoHideMessage = 'Changes Saved' %}
{% include 'messages.tpl' %}

{% form action=formAction class='medium' data=formData onsubmit="if (document.getElementById('email_field').value == '') {alert('Email is a required field.'); return false; }" %}
  {% field_row name='name' %}
  {% field_row name='email' class='large' note='the password must be reset if you change the email' %}
  
  <h2>Password Reset</h2>
  {% field_row name='newPassword' %}
  
  <div class="buttons">
    <input type="submit" value="Save {{ noun }}">
  </div>
{% endform %}
