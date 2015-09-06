{% for categoryValue, category in itemsByCategory %}
  <h3>{{ category.name }}</h3>
  
  <div class="dd" id="page_list_{{ categoryValue }}">
    {% if category.items %}
      <ol class="dd-list">
        {% set lastRight = 0 %}
        {% set navDepth = 0 %}
        {% for sidebarItem in category.items %}
          {% if sidebarItem.nav_position > lastRight %}
          {% for i in lastRight + 1 .. sidebarItem.nav_position %}
            {% if not loop.first %}
              </ol>
              {% set navDepth = navDepth - 1 %}
            {% endif %}
          {% endfor %}
          {% endif %}
          
          <li data-id="{{ sidebarItem.id }}" class="dd-item {% if item and sidebarItem.id == item.id %}sidebar_active{% endif %}">
            <div class="dd-handle"></div><div class="dd-content">
              <a href="{{ controller.url('edit', {'id': sidebarItem.id}) }}">{{ sidebarItem.name }}</a>
            </div>
            
            {% if sidebarItem.nav_position_right - sidebarItem.nav_position > 1 %}
              <ol class="dd-list">
              {% set navDepth = navDepth + 1 %}
            {% endif %}
          </li>
          {% set lastRight = sidebarItem.nav_position_right %}
        {% endfor %}
        {% for i in navDepth .. 0 %}
          </ol>
        {% endfor %}
    {% else %}
      <div class="dd-empty"></div>
    {% endif %}
  </div>
  <div class="buttons">
    <a href="{{ controller.url('add', {'category': categoryValue}) }}" class="button grey">Add Page</a>
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
      
      function saveChangesToInput(listEl, items, position) {
        $(listEl).children('li').each(function() {
          var item = {
            'id': this.getAttribute('data-id'),
            'position': position
          }
          items.push(item)
          position++
          
          $(this).children('ol').each(function() {
            position = saveChangesToInput(this, items, position)
          })
          
          item.position_right = position
          position++
          
          
        })
        
        return position
      }
      
      $('.dd').each(function() {
        var items = []
        var position = 1
        
        $(this).children('ol').each(function() {
          position = saveChangesToInput(this, items, position)
          position++
        })
        
        var inputEl = document.getElementById(this.getAttribute('id') + '_field')
        inputEl.setAttribute('value', JSON.stringify(items))
      })
      
      document.getElementById('page_list_save_buttons').setAttribute('style', '')
    });
  });
});
</script>

{% form action=controller.url('update-positions', {'return': REQUEST_URL}) %}
  {% for categoryValue, category in itemsByCategory %}
    <input type="hidden" name="page_list[{{ categoryValue }}]" id="page_list_{{ categoryValue }}_field">
  {% endfor %}
  
  <div class="buttons" id="page_list_save_buttons" style="display: none;">
    <input type="submit" class="button" value="Save Nav Changes">
  </div>
{% endform %}
