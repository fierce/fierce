
{% include_css 'vendor/fierce/fierce/css/public-news.css' %}

<div class="news-view-post">
  <h1>{{ post.title }}</h1>
  
  <div class="post-date">{{ post.date|date('j M Y') }}</div>
  {{ post.content|raw }}
</div>

<p><a class="cta" href="news/archive"><img alt="" height="36" src="images/ico-news-archive.svg" style="float:left" width="36"><strong>Archive</strong><br>
View All News Posts</a></p>
