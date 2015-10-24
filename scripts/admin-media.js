(function() {
  
  function AdminMediaAddImageController(buttonEl)
  {
    this.buttonEl = buttonEl
    this.buttonEl.addEventListener('click', this.buttonElClicked.bind(this))
      
    this.uploadEl = document.getElementById(buttonEl.id + '_upload')
    this.uploadEl.addEventListener('change', this.uploadElChanged.bind(this))
    
    this.previewEl = document.getElementById('add_image_preview')
    this.previewWrapEl = document.getElementById('add_image_progress_wrap')
    
    this.imageListEl = document.getElementById('media_image_list')
  }
  
  AdminMediaAddImageController.prototype.buttonElClicked = function(ev)
  {
    var uploadEv = document.createEvent("MouseEvents")
    uploadEv.initEvent("click", true, false)
    this.uploadEl.dispatchEvent(uploadEv)
  }
  
  AdminMediaAddImageController.prototype.uploadElChanged = function(ev)
  {
    var files = ev.target.files // FileList object

    // files is a FileList of File objects. List some properties.
    for (var fileIndex = 0, f; fileIndex < files.length; fileIndex++) {
      var file = files[fileIndex]
      var reader = new FileReader(file)
      
      var imgEl = this.previewEl
      reader.onload = function(fileRead) {
        var dataUri = fileRead.target.result
        
        imgEl.src = dataUri
        this.uploadEl.value = null
        this.previewWrapEl.setAttribute('style', '')
        this.imageListEl.setAttribute('style', 'display: none')
        this.buttonEl.setAttribute('style', 'display: none')
        
        var xhrUri = this.buttonEl.getAttribute('data-submit')
        xhrUri += '&name=' + encodeURIComponent(file.name)
        
        var xhr = new XMLHttpRequest()
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4){// && xhr.status == 200) {
            if (!xhr.responseText.match(/^success: /)) {
              alert(xhr.responseText)
              return
            }
            
            var src = xhr.responseText.substring(9)
            
            window.opener.CKEDITOR.tools.callFunction(this.getUrlParam('CKEditorFuncNum'), src)
            window.close()
          }
        }.bind(this)
        
        xhr.open("POST", xhrUri, true)
        xhr.send(dataUri)
        
      }.bind(this)
      
     reader.readAsDataURL(file)
    }
  }
  
  AdminMediaAddImageController.prototype.getUrlParam = function(name)
  {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]")
    
    var regexS = "[\\?&]" + name + "=([^&#]*)"
    var regex = new RegExp(regexS)
    var results = regex.exec(location.href)
    
    if (results == null) {
      return null;
    }
    
    return results[1]
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var addImageNode = document.getElementById('add_image_button')
    
    addImageNode.controller = new AdminMediaAddImageController(addImageNode)
  })
}())
