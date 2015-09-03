<div class="sidebar_wrapper">
  <div class="sidebar">
    <h1>Manage {{ nounPlural }}</h1>
    {% for categoryValue, categoryName in categories %}
      <table class="grid" id="{{ noun|lower }}_list">
        <thead>
          <tr>
            <th>{{ categoryName }}</th>
          </tr>
        </thead>
        <tbody>
          {% for sidebarItem in items if attribute(sidebarItem, categoryField) == categoryValue %}
            <tr {% if item and sidebarItem.id == item.id %}class="sidebar_active"{% endif %}>
              <td>
                <a href="{{ controller.url('edit', {'id': sidebarItem.id}) }}">
                  {{ sidebarItem.name }}
                </a>
              </td>
            </tr>            
          {% endfor %}
          
          <tr>
            <td class="buttons">
              <a href="{{ controller.url('add', {'category': categoryValue}) }}" class="button">Add {{ noun }}</a>
            </td>
          </tr>
        </tbody>
      </table>
      <br>
    {% endfor %}
  </div>

  <div class="content">
    {% if crudContentTpl %}
      {% include crudContentTpl %}
    {% endif %}
  </div>
</div>