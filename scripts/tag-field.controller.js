(function() {
  
  function TagFieldController(inputEl)
  {
    this.inputEl = inputEl
    
    
    var tagListEl = document.getElementById(this.inputEl.name + '_tags')
    var itemEls = tagListEl.getElementsByTagName('li')
    for (var itemIndex = 0; itemIndex < itemEls.length; itemIndex++) {
      var itemEl = itemEls[itemIndex]
      if (itemEl.tagName != 'LI') {
        continue
      }
      
      itemEl.addEventListener('click', this.itemClicked.bind(this))
    }
  }
  
  TagFieldController.prototype.itemClicked = function(ev)
  {
    var tag = ev.target.textContent
    
    var value = this.inputEl.value
    if (value.length > 0) {
      value += ', '
    }
    
    value += tag
    
    this.inputEl.value = value
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    var nodes = document.getElementsByClassName('tag_field');
    
    for (var nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
      var node = nodes[nodeIndex]
      if (node.tagName != 'INPUT') {
        continue
      }
      
      node.controller = new TagFieldController(node)
    }
  });
}());
