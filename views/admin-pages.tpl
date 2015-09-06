<div class="sidebar_wrapper">
  <div class="sidebar">
    <h1>Manage Pages</h1>
    
    {% include 'admin-pages-list.tpl' %}
  </div>

  <div class="content">
    {% if crudContentTpl %}
      {% include crudContentTpl %}
    {% else %}
      <p style="text-align: center; padding-top: 140px;">
        <em>Select a page on the left to edit.</em>
      </p>
    {% endif %}
  </div>
</div>
