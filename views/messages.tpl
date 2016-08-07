{% if formErrors is defined and formErrors %}
  <p>Please correct the following issues:</p>
  <ul class="form-errors">
    {% for error in formErrors %}
      <li>{{ error }}</li>
    {% endfor %}
  </ul>
{% endif %}

{% if message is defined and message %}
  {% if autoHideMessage is defined and (autoHideMessage is sameas(true) or autoHideMessage is sameas(message)) %}
    <p class="message" id="message-auto-hide" style="opacity: 1; transition: opacity 1s ease;">{{ message }}</p>
    <script type="text/javascript">
      setTimeout(function() {
        document.getElementById('message-auto-hide').style.opacity = '0';
      }, 5000);
    </script>
  {% else %}
    <p class="message">{{ message }}</p>
  {% endif %}
{% endif %}
