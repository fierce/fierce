(function() {
  
  function WysiwygFieldController(textareaEl)
  {
  
    this.textareaEl = textareaEl
    
    if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	    CKEDITOR.tools.enableHtml5Elements( document );
    
    CKEDITOR.config.extraPlugins = 'codemirror,image2,widget,lineutils,imageuploader';
    
    CKEDITOR.config.height = 400;
    
    CKEDITOR.config.toolbarGroups = [
		    { name: 'styles', groups: [ 'styles' ] },
		    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		    { name: 'colors', groups: [ 'colors' ] },
		    { name: 'links', groups: [ 'links' ] },
		    { name: 'insert', groups: [ 'insert' ] },
		    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
	    ];
    
    CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,PasteFromWord,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Find,Replace,CreateDiv,BidiLtr,BidiRtl,Language,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About,Maximize,Font,FontSize,Format';
    
    CKEDITOR.stylesSet.add( 'default', [
        // Block Styles
        { name: 'Normal', element: 'p'},
        { name: 'Page Heading', element: 'h1' },
        { name: 'Heading', element: 'h2' },
        { name: 'Sub-Heading', element: 'h3' },
    
        // Inline Styles
        { name: 'Marker: Yellow',   element: 'span',    styles: { 'background-color': 'Yellow' } },
        { name: 'Marker: Green',    element: 'span',    styles: { 'background-color': 'Lime' } },
        { name: 'Button',    element: 'a',    attributes: { 'class': 'button' } }
    ] );
  
    CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss, document.getElementById('base_href').href + 'fierce/third-party/ckeditor/css/contents.css']
    
	  CKEDITOR.replace( this.textareaEl );
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