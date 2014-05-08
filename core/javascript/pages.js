if (!CMS)
{
	var CMS = new Object ();
}

CMS.pages = 
{

	'isSaving' : false,

	'init' : function ()
	{
		// Create the toggle tabs
		CMS.pages.makeLanguageTabs ();
	},
	
	'makeLanguageTabs' : function ()
	{
		// Fetch the elements
		var toggles = document.getElementsByClassName ('langSwitch');
		
		for (var i = 0; i < toggles.length; i ++)
		{
			toggles[i].onclick = function () { CMS.pages.toggleLanguagePage(this) };
		}
	},
	
	'toggleLanguagePage' : function (elSel)
	{		
		// Only activate the current one
		var toggles = document.getElementsByClassName ('langSwitch');
		for (var i = 0; i < toggles.length; i ++)
		{
			var el = toggles[i];
			if (el.id == elSel.id)
			{
				el.addClassName ('active');
				$(el.id.replace ('toggle_', 'content_')).style.display = 'block';
			}
			else
			{
				el.removeClassName ('active');
				$(el.id.replace ('toggle_', 'content_')).style.display = 'none';
			}
		}
	},
	
	'saveContent' : function (aElement)
	{
		if (this.isSaving == false)
		{
			this.isSaving = true;
			this.doSaveContent($('content_form'));
		}
	},
	
	'doSaveContent' : function (aForm)
	{
		tinyMCE.triggerSave();
		
		new Ajax.Request 
		(
			aForm.action,
			{
				method : 'post',
				postBody : aForm.serialize (),
				onSuccess: function (transport)
				{
					CMS.pages.doneSaveContent (transport.responseText);
				}
			}
		);
		
		return false;
	},
	
	'doneSaveContent' : function (responseText)
	{
		var json = JSON.parse(responseText);
		if (json.status == 'success')
		{
			new Effect.Highlight($('language_content'), { afterFinish : function () { CMS.pages.isSaving = false; } });
		}
		else
		{
			alert (json.message);
			CMS.pages.isSaving = false;
		}
	},
	
	'deletePage' : function (jsonUrl, msg)
	{
		if (confirm (msg))
		{
			CMS.pages.doDeletePage (jsonUrl);
		}
		
		return false;
	},
	
	'doDeletePage' : function (jsonUrl)
	{
		new Ajax.Request 
		(
			jsonUrl,
			{
				method : 'get',
				onSuccess: function (transport)
				{
					CMS.pages.doneDeletePage (transport.responseText);
				}
			}
		);
	},
	
	'doneDeletePage' : function (responseText)
	{
		var json = JSON.parse(responseText);
		if (json.status == 'success')
		{
			new Effect.Fade 
			(
				$('content'),
				{
					afterFinish: function () { document.location = json.redirect; }
				}
			);
		}
		else
		{
			alert (json.message);
		}
	}
}

// Hook the init
Event.observe(window, 'load', CMS.pages.init, false);
