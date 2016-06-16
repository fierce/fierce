{% if formType == 'Add' %}
  <h1>Add {{ noun }}</h1>
{% else %}
  <h1>Edit {{ item.title }}</h1>
{% endif %}

{% form action=formAction class='medium' data=formData onsubmit="if (document.getElementById('title_field').value == '') {alert('Title is a required field.'); return false; }" %}
  {% field_row name='title' %}
  {% date_field_row name='date' %}
  
  {% wysiwyg_row name='content' %}
  
  <div class="buttons">
    <a href="javascript:void(0)" confirmedHref="{{ controller.url('delete', {'id': item.id}) }}" onclick="
          if (confirm('Are you sure you want to delete this page?')) {
            this.setAttribute('href', this.getAttribute('confirmedHref'))
          }
        " class="grey" style="float: left;">Delete this {{ noun }}</a>
    <input type="submit" value="Save {{ noun }}">
  </div>
{% endform %}
