
CKEDITOR.plugins.add( 'fierce-image-select', {
  init: function( editor ) {
    editor.config.filebrowserBrowseUrl = document.getElementById('base_href').getAttribute('data-fierce-base') + 'third-party/ckeditor/plugins/fierce-image-select/select-image';
  }
});
