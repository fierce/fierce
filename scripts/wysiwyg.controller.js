(function() {
  
  function WysiwygFieldController(textareaEl)
  {
    // load text area
    this.textareaEl = textareaEl
    this.textareaEl.addEventListener('input', this.textareaInput.bind(this))
    
    // load wysiwyg view
    this.wysiwygEl = document.createElement('div')
    this.wysiwygEl.className = 'wysiwyg-editor'
    this.wysiwygEl.innerHTML = this.textareaEl.value
    
    // this.wysiwygEl.contentEditable = true
    // this.wysiwygEl.addEventListener('input', this.wysiwygInput.bind(this))
    
    this.textareaEl.parentNode.appendChild(this.wysiwygEl)
    
    // load CodeMirror
    CodeMirror.defineMode("mustache", function(config, parserConfig) {
      var mustacheOverlay = {
        token: function(stream, state) {
          var ch;
          if (stream.match("{{")) {
            while ((ch = stream.next()) != null)
              if (ch == "}" && stream.next() == "}") {
                stream.eat("}");
                return "mustache";
              }
          }
          while (stream.next() != null && !stream.match("{{", false)) {}
          return null;
        }
      };
      return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), mustacheOverlay);
    });
    this.codeMirrorEditor = CodeMirror.fromTextArea(document.getElementById("content_field"), {
      mode: "mustache",
      lineWrapping: false
    });
    
    var delay;
    // Initialize CodeMirror editor with a nice html5 canvas demo.
    this.codeMirrorEditor.on("change", function() {
      clearTimeout(delay);
      delay = setTimeout(updatePreview, 10);
    });
    
    updatePreview = function() {
      this.textareaEl.value = this.codeMirrorEditor.getValue();
      
      this.textareaInput();
    }.bind(this)
    setTimeout(updatePreview, 10);
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