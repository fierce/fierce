<div class="sidebar_wrapper">
  <div class="sidebar">
    <h1>Manage {{ nounPlural }}</h1>
    
    {% for categoryValue, category in itemsByCategory %}
      <h3>{{ category.name }}</h3>
      <div class="dd" id="crud_list_{{ categoryValue }}">
        {% if category.items %}
          <ol class="dd-list">
            {% for sidebarItem in category.items %}
              <li data-id="{{ sidebarItem.id }}" class="dd-item {% if item and sidebarItem.id == item.id %}sidebar_active{% endif %}">
                <div class="dd-handle"></div><div class="dd-content">
                  <a href="{{ controller.url('edit', {'id': sidebarItem.id}) }}">{{ sidebarItem.name }}</a>
                </div>
              </li>
            {% endfor %}
          </ol>
        {% else %}
          <div class="dd-empty"></div>
        {% endif %}
      </div>
      <div class="buttons">
        <a href="{{ controller.url('add', {'category': categoryValue}) }}" class="button">Add {{ noun }}</a>
      </div>
    {% endfor %}
    
    <link rel="stylesheet" href="{{ FIERCE_SRC }}third-party/nestable/nestable.css">
    <script src="{{ FIERCE_SRC }}third-party/jquery/1.11.1/jquery.min.js"></script>
    <script src="{{ FIERCE_SRC }}third-party/nestable/jquery.nestable.js"></script>
    <script>
    
    $(document).ready(function()
    {
      $('.dd').each(function() {
        var nestable = $(this).nestable()
        
        nestable.on('change', function(e) {
          console.log('new positions: ' + e.target.getAttribute('id') + ' - ' + window.JSON.stringify($(e.target).nestable('serialize')))
        });
      });
    });
    </script>
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
