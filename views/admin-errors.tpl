<h1>{{ pageTitle }}</h1>

<p>Errors from the last {{ logPeriod }} in {{ logFile }}</p>

{% if logPeriod == '1 day' %}
  <p class="buttons"><a href="{{ controller.url('default', {'lastWeek': 1}) }}">Show the last week</a></p>
{% endif %}

<table class="grid">
  <thead>
    <tr>
      <th>Date</th>
      <th>Error</th>
    </tr>
  </thead>
  <tbody>
    {% for error in errors %}
      <tr>
        <td style="white-space: nowrap; vertical-align: top;">
          {{ error.date|date('Y-m-d\nH:i:s')|nl2br }}
        </td>
        <td>{{ error.message|nl2br }}</td>
      </tr>
    {% endfor %}
  </tbody>
</table>
