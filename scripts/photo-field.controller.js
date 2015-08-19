(function() {
  
  function PhotoFieldController(uploadEl)
  {
    this.uploadEl = uploadEl
    this.uploadEl.addEventListener('change', this.uploadElChanged.bind(this))
    
    this.hiddenValueEl = document.getElementById(uploadEl.name + '_field')
    
    this.previewEl = document.getElementById(uploadEl.name + '_preview')
  }
  
  PhotoFieldController.prototype.uploadElChanged = function(ev)
  {
    var files = ev.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    for (var fileIndex = 0, f; fileIndex < files.length; fileIndex++) {
      var file = files[fileIndex]
      var reader = new FileReader(file)
      
      var imgEl = this.previewEl
      reader.onload = function(fileRead) {
        var dataUri = fileRead.target.result
        
        imgEl.src = dataUri
        this.hiddenValueEl.value = dataUri
        this.uploadEl.value = null
      }.bind(this)
      
     reader.readAsDataURL(file)
    }
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var nodes = document.getElementsByClassName('photo_upload');
    
    for (var nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
      var node = nodes[nodeIndex];
      if (node.tagName != 'INPUT') {
        continue;
      }
      
      node.controller = new PhotoFieldController(node);
    }
  });
}());
