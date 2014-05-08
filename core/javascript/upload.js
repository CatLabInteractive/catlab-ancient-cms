CMS.upload = 
{

	'uwindow' : null,
	'inElement' : null,
	'isEditor' : null,

	'openManager' : function (inElement, isEditor)
	{
		if (typeof (isEditor) == 'undefined')
		{
			isEditor = false;
		}
	
		CMS.upload.uwindow = new Window
		(
			{
				className: "alphacube", 
				title: "Image Upload", 
				width:600, 
				height:400, 
				url: CMS.settings.url_upload
			}
		);
	
		CMS.upload.uwindow.setDestroyOnClose();
		CMS.upload.uwindow.showCenter(true);
		
		CMS.upload.inElement = inElement;
		CMS.upload.isEditor = isEditor;
	},
	
	'closeManager' : function ()
	{
		CMS.upload.uwindow.close ();
	},
	
	'makeToggleTabs' : function ()
	{
		// Fetch the elements
		var toggles = document.getElementsByClassName ('upload_tabs');
		
		for (var i = 0; i < toggles.length; i ++)
		{
			toggles[i].onclick = function () { CMS.upload.toggleTab(this) };
		}
	},
	
	'toggleTab' : function (elSel)
	{		
		// Only activate the current one
		var toggles = document.getElementsByClassName ('upload_tabs');
		for (var i = 0; i < toggles.length; i ++)
		{
			var el = toggles[i];
			if (el.id == elSel.id)
			{
				el.addClassName ('active');
				$(el.id.replace ('upload_tab_', 'upload_content_')).style.display = 'block';
			}
			else
			{
				el.removeClassName ('active');
				$(el.id.replace ('upload_tab_', 'upload_content_')).style.display = 'none';
			}
		}
	},
	
	'insertImageIntoEditor' : function (thumbImg, realImage)
	{
		if (CMS.upload.isEditor)
		{
			CMS.upload.inElement.execCommand('mceInsertRawHTML', false, '<img src="'+thumbImg+'" class="thumbnail" about="'+realImage+'" />');
			CMS.upload.inElement.execCommand('mceRepaint');
		}
		else
		{
			alert (aurl);
		}
		CMS.upload.uwindow.close ();
	}
}

// Hook the init
Event.observe(window, 'load', CMS.upload.makeToggleTabs, false);
