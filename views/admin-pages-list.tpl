{% for categoryValue, category in itemsByCategory %}
  <h3>{{ category.name }}</h3>
  
  <div class="dd" id="page_list_{{ categoryValue }}">
    {% if category.items %}
      <ol class="dd-list">
        {% set navDepth = 0 %}
        {% for navPage in category.items %}
          
          {# no change to depth? close the list item we just left open #}
          {% if not loop.first and navPage.nav_position_depth == navDepth %}
            </li>
          {% endif %}
          
          {# increase depth? #}
          {% if navPage.nav_position_depth > navDepth %}
            {% for i in (navPage.nav_position_depth - navDepth) .. 0 %}
              {% if not loop.first %}
                <ol class="dd-list">
                {% set navDepth = navDepth + 1 %}
              {% endif %}
            {% endfor %}
          {% endif %}
          
          {# reduce depth? #}
          {% if navPage.nav_position_depth < navDepth %}
            {% for i in (navDepth - navPage.nav_position_depth) .. 0 %}
              {% if not loop.first %}
                </li></ol>
                {% set navDepth = navDepth - 1 %}
              {% endif %}
            {% endfor %}
            </li>
          {% endif %}
          
          {# now generate the actual nav item #}
          <li data-id="{{ navPage.id }}" class="dd-item {% if item and navPage.id == item.id %}sidebar_active{% endif %}">
            <div class="dd-handle"></div><div class="dd-content">
              <a href="{{ controller.url('edit', {'id': navPage.id}) }}">{{ navPage.name }}</a>
            </div>
        {% endfor %}
        {% for i in navDepth .. 0 %}
          </li></ol>
        {% endfor %}
    {% else %}
      <div class="dd-empty"></div>
    {% endif %}
  </div>
  {% if categoryValue == 'not_linked' %}
  <div class="buttons">
    <a href="{{ controller.url('add') }}" class="button grey">Add Page</a>
  </div>
  {% endif %}
{% endfor %}

<link rel="stylesheet" href="{{ fierce_src }}third-party/nestable/nestable.css">
<script src="{{ fierce_src }}third-party/jquery/1.11.1/jquery.min.js"></script>
<script src="{{ fierce_src }}third-party/nestable/jquery.nestable.js"></script>
<script>

$(document).ready(function()
{
  $('.dd').each(function() {
    var nestable = $(this).nestable()
    
    // would like to do this, but we'd have to expand/reveal the "current" item
    // $(this).nestable('collapseAll')
    
    nestable.on('change', function(e) {
      
      function saveChangesToInput(listEl, items, position, depth) {
        $(listEl).children('li').each(function() {
          var item = {
            'id': this.getAttribute('data-id'),
            'position': position,
            'depth': depth
          }
          items.push(item)
          position++
          
          $(this).children('ol').each(function() {
            position = saveChangesToInput(this, items, position, depth + 1)
          })
          
          item.position_right = position
          position++
          
          
        })
        
        return position
      }
      
      $('.dd').each(function() {
        var items = []
        var position = 1
        var depth = 0
        
        $(this).children('ol').each(function() {
          position = saveChangesToInput(this, items, position, depth)
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

{% form action=controller.url('update-positions', {'return': request_url}) %}
  {% for categoryValue, category in itemsByCategory %}
    <input type="hidden" name="page_list[{{ categoryValue }}]" id="page_list_{{ categoryValue }}_field">
  {% endfor %}
  
  <div class="buttons" id="page_list_save_buttons" style="display: none;">
    <input type="submit" class="button" value="Save Nav Changes">
  </div>
{% endform %}
