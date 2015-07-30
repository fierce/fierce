(function() {
  
  function WysiwygFieldController(textareaEl)
  {
    this.textareaEl = textareaEl
    this.textareaEl.addEventListener('input', this.textareaInput.bind(this))
    
    this.wysiwygEl = document.createElement('div')
    this.wysiwygEl.className = 'wysiwyg-editor'
    this.wysiwygEl.innerHTML = this.textareaEl.value
    
    // this.wysiwygEl.contentEditable = true
    // this.wysiwygEl.addEventListener('input', this.wysiwygInput.bind(this))
    
    this.textareaEl.parentNode.appendChild(this.wysiwygEl)
  }
  WysiwygFieldController.prototype.unescapeHTML = function(html)
  {
    var elem = document.createElement('div')
    elem.innerHTML = html
    
    var result = ''
    for (var i = 0; i < elem.childNodes.length; i++) {
      result = result + elem.childNodes[i].nodeValue
    }
    
    return result
  }
  WysiwygFieldController.prototype.wysiwygInput = function()
  {
    this.textareaEl.value = this.wysiwygEl.innerHTML
  }
  WysiwygFieldController.prototype.textareaInput = function()
  {
    this.wysiwygEl.innerHTML = this.textareaEl.value
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var nodes = document.getElementsByClassName('wysiwyg');
    
    for (var nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
      var node = nodes[nodeIndex];
      if (node.tagName != 'TEXTAREA') {
        continue;
      }
      
      node.controller = new WysiwygFieldController(node);
    }
  });
}());