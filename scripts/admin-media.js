(function() {
  
  function AdminMediaAddImageController(buttonEl)
  {
    this.buttonEl = buttonEl
    this.buttonEl.addEventListener('click', this.buttonElClicked.bind(this))
      
    this.uploadEl = document.getElementById(buttonEl.id + '_upload')
    this.uploadEl.addEventListener('change', this.uploadElChanged.bind(this))
  }
  
  AdminMediaAddImageController.prototype.buttonElClicked = function(ev)
  {
    var uploadEv = document.createEvent("MouseEvents");
    uploadEv.initEvent("click", true, false);
    this.uploadEl.dispatchEvent(uploadEv);
  }
  
  AdminMediaAddImageController.prototype.uploadElChanged = function(ev)
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
        this.uploadEl.value = null
      }.bind(this)
      
     reader.readAsDataURL(file)
    }
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var addImageNode = document.getElementById('add_image_button')
    
    addImageNode.controller = new AdminMediaAddImageController(addImageNode)
  });
}());
