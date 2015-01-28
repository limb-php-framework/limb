/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.

	config.toolbar_Full = [
		['Source'],
		['Undo','Redo'],
		['Cut','Copy','Paste','PasteText','PasteFromWord'],
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['Format'],
		'/',
		['NumberedList','BulletedList'],
		['RemoveFormat'],
		['Outdent','Indent'],
		['Blockquote'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor'],
		['Image','Table','HorizontalRule','SpecialChar']
	];

	config.toolbar_Basic = [
		['Undo','Redo'],
		['Cut','Copy','PasteText'],
		['Bold','Italic','Format','RemoveFormat'],
		['NumberedList','BulletedList']
	];

	config.extraPlugins = 'justify,pastetext';
	config.height = 500;
	config.format_tags = 'p;div;h2;h3;h4;h5;h6;pre';
	config.uiColor = '#fafafa';

	config.defaultLanguage = 'ru';
	config.language = 'ru';
	config.contentsLanguage = 'ru';

	project_browse_path = '/shared/wysiwyg/kcfinder/browse.php';
	project_upload_path = '/shared/wysiwyg/kcfinder/upload.php';

	config.filebrowserBrowseUrl = project_browse_path + '?type=files&opener=ckeditor';
	config.filebrowserImageBrowseUrl = project_browse_path + '?type=images&opener=ckeditor';
	config.filebrowserFlashBrowseUrl = project_browse_path + '?type=flash&opener=ckeditor';

	config.filebrowserUploadUrl = project_upload_path + '?type=files&opener=ckeditor';
	config.filebrowserImageUploadUrl = project_upload_path + '?type=images&opener=ckeditor';
	config.filebrowserFlashUploadUrl = project_upload_path + '?type=flash&opener=ckeditor';

};
