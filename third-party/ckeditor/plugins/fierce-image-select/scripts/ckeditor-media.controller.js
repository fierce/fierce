function useImage(imgSrc) {
            function getUrlParam( paramName ) {
                var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
                var match = window.location.search.match(reParam) ;

                return ( match && match.length > 1 ) ? match[ 1 ] : null ;
            }
            var funcNum = getUrlParam( 'CKEditorFuncNum' );
            var imgSrc = imgSrc;
            var fileUrl = imgSrc;
            window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
            window.close();
        }
        
(function() {
  
  function CKEditorMediaController(imageCellEl)
  {
    this.imageCellEl = imageCellEl
    this.imageCellEl.addEventListener('click', this.cellClicked.bind(this))
    
    this.imageSrc = this.imageCellEl.getAttribute('data-url')
  }
  
  CKEditorMediaController.prototype.cellClicked = function(ev)
  {
    ev.preventDefault()
    
    window.opener.CKEDITOR.tools.callFunction(this.getUrlParam('CKEditorFuncNum'), this.imageSrc);
    
    window.close();
  }
  
  CKEditorMediaController.prototype.getUrlParam = function(name)
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
    var nodes = document.getElementsByClassName('image_cell');
    
    for (var nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
      var node = nodes[nodeIndex];
      if (node.tagName != 'DIV') {
        continue;
      }
      
      node.controller = new CKEditorMediaController(node);
    }
  });
}());
