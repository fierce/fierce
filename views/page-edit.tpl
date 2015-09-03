{% if formType == 'Add' %}
  <h1>Add {{ noun }}</h1>
{% else %}
  <h1>Edit {{ item.name }}</h1>
{% endif %}

{% form action=formAction class='medium' data=formData onsubmit="if (document.getElementById('email_field').value == '') {alert('Email is a required field.'); return false; }" %}
  
  {% field_row name='name' %}
  {% field_row name='url' class='large' %}
  
  {% field_row name='content' %}
  {#% wysiwyg_row name='content' %#}
  
  <div class="buttons">
    <a href="javascript:void(0)" confirmedHref="{{ controller.url('delete', {'id': item.id}) }}" onclick="
          if (confirm('Are you sure you want to delete this page?')) {
            this.setAttribute('href', this.getAttribute('confirmedHref'))
          }
        " class="grey" style="float: left;">Delete this Page</a>
    <input type="submit" value="Save {{ noun }}">
  </div>
{% endform %}
