
<div class="buttons media-buttons">
  <a href="javascript:void(0)" id="add_image_button" data-submit="{{ base_url}}{{ fierce_src }}third-party/ckeditor/plugins/fierce-image-select/select-image?do=upload">Add Image</a>
  <input type="file" id="add_image_button_upload" accept="image/*">
</div>

<h1>{{ displayName }}</h1>

<div id="add_image_progress_wrap" style="display: none">
  <img id="add_image_preview" width="200">
  <p>Uploading...</p>
</div> 
<div class="media_list_wrapper">
  <table class="grid" id="media_image_list">
    <tbody>
      <tr>
        <td>
          {% for item in imageItems %}
            <div class="image_cell" data-url="{{ item.url|trim('/') }}">
              <a href="{{ item.url|trim('/') }}">
                <div class="image_wrapper">
                  <img src="{{ item.url }}" width="70" height="70">
                </div>
              </a>
              <span class="name">{{ item.name }}</span>
              
              {#
              <div class="buttons">
                <a href="javascript:void(0)" confirmedHref="<?= $controller->url('delete', ['id' => $item->id]) ?>" onclick="
                  if (confirm('Are you sure you want to delete <?= addslashes(htmlspecialchars($item->name)) ?>?')) {
                    this.setAttribute('href', this.getAttribute('confirmedHref'))
                  }
                ">Delete </a>
              </div>
              #}
            </div>
          {% else %}
            <p>No images.</p>
          {% endfor %}
        </td>
      </tr>
    </tbody>
  </table>
</div>
