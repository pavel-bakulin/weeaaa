/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
                        
config.toolbar =
[
	['Undo','Redo'],
	['Cut','Copy','Paste','PasteText','PasteWord','-'],	                
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['NumberedList','BulletedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	['Find','Replace','-','SelectAll','RemoveFormat'],	['Source','Maximize'],
	'/',
	['Link','Unlink','Anchor'],
	['Image','Table','HorizontalRule','SpecialChar'],	
	['Font','FontSize','Styles','Format'],
	['TextColor','BGColor']
];

};
