if (!CMS)
{
	var CMS = new Object ();
}

CMS.modulemanager = 
{
	'init' : function ()
	{
		CMS.modulemanager.makeSectionTabs ();
	},
	
	'makeSectionTabs' : function ()
	{
		// Fetch the elements
		var toggles = $('mm_tablist').select('li a');
		
		for (var i = 0; i < toggles.length; i ++)
		{
			toggles[i].onclick = function () { CMS.modulemanager.toggleSectionPage(this) };
		}
	},
	
	'toggleSectionPage' : function (elSel)
	{
		// Only activate the current one
		var toggles = $('mm_tablist').select('li a');
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
	
	'submitContent' : function (oForm)
	{
		tinyMCE.triggerSave();
		var content = Object.toJSON(oForm.serialize (true));
		new Ajax.Request 
		(
			oForm.action,
			{
				method : 'post',
				postBody : oForm.serialize (),
				onSuccess: function (transport)
				{
					CMS.modulemanager.doneSubmitContent (transport.responseText);
				}
			}
		);
	},
	
	'doneSubmitContent' : function (sResponse)
	{
		var json = JSON.parse(sResponse);
		if (json.status == 'success')
		{
			$('mm_content_list').innerHTML = json.listhtml;
			new Effect.Highlight($('tabs_content'), {  });
		
			if (typeof (json.redirect) != 'undefined')
			{
				window.location = json.redirect;
			}
		}
		else
		{
			alert (json.message);
		}
	},
	
	'removeItem' : function (sUrl)
	{
		if (confirm ('Are you sure you want to remove this item?'))
		{
			new Ajax.Request 
			(
				sUrl,
				{
					method : 'get',
					onSuccess: function (transport)
					{
						CMS.modulemanager.doneRemoveItem (transport.responseText);
					}
				}
			);
		}	
	},
	
	'doneRemoveItem' : function (sResponse)
	{
		var json = JSON.parse(sResponse);
		if (json.status == 'success')
		{
			$('mm_content_list').innerHTML = json.listhtml;
			new Effect.Highlight($('tabs_content'), {  });
		}
		else
		{
			alert (json.message);
		}
	}
}

// Hook the init
Event.observe(window, 'load', CMS.modulemanager.init, false);
