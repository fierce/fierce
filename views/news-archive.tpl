
{% include_css 'vendor/fierce/fierce/css/public-news.css' %}

<h1>News Archive</h1>


<div class="news-archive">
  {% set currentYear = false %}
  
  {% for post in posts %}
    {% if currentYear != post.date.format('Y') %}
      {% if currentYear != false %}
        </ul>
      {% endif %}
      
      {% set currentYear = post.date.format('Y') %}
      <h3>{{ currentYear }}</h3>
      <ul>
    {% endif %}
    <li>
      <a href="{{ post.permalink }}">
        <span class="date">{{ post.date|date('M j') }}</span>
        <span class="title">{{ post.title }}</span>
      </a>
    </li>
  {% endfor %}
  
  {% if currentYear != false %}
    </ul>
  {% else %}
    <p>(no posts found)</p>
  {% endif %}
</div>