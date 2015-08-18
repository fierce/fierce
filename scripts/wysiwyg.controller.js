(function() {
  
  function WysiwygFieldController(textareaEl)
  {
    // load text area
    this.textareaEl = textareaEl
    
    // create wysiwyg view
    this.wysiwygEl = document.createElement('div')
    this.wysiwygEl.className = 'wysiwyg-editor'
    this.wysiwygEl.innerHTML = this.textareaEl.value
    
    // create code/preview cells
    var codeCell = document.createElement('div')
    codeCell.className = 'wysiwyg_cell wysiwyg_code_cell'
    textareaEl.parentNode.appendChild(codeCell)
    codeCell.appendChild(this.textareaEl);
    
    var previewCell = document.createElement('div')
    previewCell.className = 'wysiwyg_cell wysiwyg_preview_cell'
    codeCell.parentNode.appendChild(previewCell)
    
    previewCell.appendChild(this.wysiwygEl)
    
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
    this.codeMirrorEditor.on("change", function() {
      if (!this.updatingCodeMirror) {
        updatePreview()
        clearTimeout(delay);
        delay = setTimeout(updatePreview, 10);
      }
    }.bind(this));
    
    updatePreview = function() {
      var html = this.codeMirrorEditor.getValue()
      this.textareaEl.value = html
      
      this.wysiwygEl.innerHTML = html
    }.bind(this)
    setTimeout(updatePreview, 10);
    
    // init wysiwyg field
    this.wysiwygEl.contentEditable = true
    this.wysiwygEl.addEventListener('input', this.wysiwygInput.bind(this))
    this.wysiwygEl.addEventListener('keypress', this.wysiwygKeypress.bind(this))
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
    var html = this.wysiwygEl.innerHTML
    
    this.textareaEl.value = html
    
    this.updatingCodeMirror = true
    this.codeMirrorEditor.setValue(html)
    this.updatingCodeMirror = false
  }
  
  WysiwygFieldController.prototype.wysiwygKeypress = function(ev)
  {
    // return
    if (ev.keyCode == '13' && !ev.shiftKey) {
      ev.preventDefault()
      
      document.execCommand('insertParagraph', false)
      document.execCommand('formatBlock', false, 'p')
      
      return
    }
    if (ev.keyCode == '13' && ev.shiftKey) {
      ev.preventDefault()
      
      var docFragment = document.createDocumentFragment();
  

      //add the br, or p, or something else
      newEle = document.createElement('br');
      docFragment.appendChild(newEle);

      //make the br replace selection
      var range = window.getSelection().getRangeAt(0);
      range.deleteContents();
      range.insertNode(docFragment);

      //create a new range
      range = document.createRange();
      range.setStartAfter(newEle);
      range.collapse(true);

      //make the cursor there
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(range);
      
      this.wysiwygInput()
      
      return
    }
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