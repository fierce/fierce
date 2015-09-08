
<div style="max-width: 400px; margin: 0 auto;">
  <h1>Log In</h1>
  
  {% if message %}
    <p class="message">{{ message }}</p>
  {% endif %}
  
  {% form action='login?do=submit' class='medium' data=loginData %}
    {% field name='return' type='hidden' %}
    
    {% field_row name='email' %}
    {% field_row name='password' type='password' %}
    
    <div class="buttons">
      <input type="submit" value="Continue">
    </div>
  {% endform %}
</div>

<script type="text/javascript">
  document.getElementById('email_field').focus()
</script>
