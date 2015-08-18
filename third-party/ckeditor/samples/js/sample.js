/**
 * Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/* exported initSample */

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	CKEDITOR.tools.enableHtml5Elements( document );

// The trick to keep the editor in the sample quite small
// unless user specified own height.
CKEDITOR.config.height = 600;
CKEDITOR.config.width = 'auto';

CKEDITOR.config.extraPlugins = 'codemirror,image2,widget,lineutils,imageuploader';

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

CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss, 'http://localhost/sequelpro.com/fierce/third-party/ckeditor/samples/css/contents.css']

console.log(CKEDITOR.config.contentsCss);

var initSample = ( function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
		var editorElement = CKEDITOR.document.getById( 'editor' );

		// :(((
		if ( isBBCodeBuiltIn ) {
			editorElement.setHtml(
				'Hello world!\n\n' +
				'I\'m an instance of [url=http://ckeditor.com]CKEditor[/url].'
			);
		}

		// Depending on the wysiwygare plugin availability initialize classic or inline editor.
		if ( wysiwygareaAvailable ) {
			CKEDITOR.replace( 'editor' );
		} else {
			editorElement.setAttribute( 'contenteditable', 'true' );
			CKEDITOR.inline( 'editor' );

			// TODO we can consider displaying some info box that
			// without wysiwygarea the classic editor may not work.
		}
	};

	function isWysiwygareaAvailable() {
		// If in development mode, then the wysiwygarea must be available.
		// Split REV into two strings so builder does not replace it :D.
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}

		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}
} )();

