if (!CMS)
{
	var CMS = new Object ();
}

CMS.navigation = 
{
	'iCounter' : 1000,
	'treeObj' : null,
	'action' : null,
	'isSaving' : false,

	'init' : function ()
	{		
		CMS.navigation.treeObj = new JSDragDropTree();
		CMS.navigation.treeObj.setTreeId('navigation');
		CMS.navigation.treeObj.setRenameAllowed (false);
		CMS.navigation.treeObj.setDeleteAllowed (false);
		
		CMS.navigation.treeObj.initTree();
		CMS.navigation.treeObj.expandAll();
	},
	
	'doSaveTree' : function ()
	{
		if (!CMS.navigation.isSaving)
		{
			CMS.navigation.isSaving = true;
			var nodelist = CMS.navigation.treeObj.getNodeOrders();
			new Ajax.Request 
			(
				CMS.navigation.action,
				{
					method : 'post',
					postBody : 'nodelist='+nodelist,
					onSuccess: function (transport)
					{
						CMS.navigation.doneSaveTree (transport.responseText);
					}
				}
			);
		}
	},
	
	'doneSaveTree' : function (responseText)
	{
		var json = JSON.parse(responseText);
		if (json.status == 'success')
		{
			new Effect.Highlight($('navigation'), { afterFinish : function () { CMS.navigation.isSaving = false; } });
		}
		else
		{
			alert (json.message);
			CMS.navigation.isSaving = false;
		}
	},
	
	'doRemovePage' : function (id)
	{
		if (confirm ('Are you sure you want to remove this page?'))
		{
			new Ajax.Request 
			(
				CMS.navigation.removeurl.replace ('{id}', id),
				{
					method : 'get',
					onSuccess: function (transport)
					{
						CMS.navigation.doneRemovePage (transport.responseText);
					}
				}
			);
		}
	},
	
	'doneRemovePage' : function (responseText)
	{
		var json = JSON.parse(responseText);
		if (json.status == 'success')
		{
			new Effect.Fade ($('nav_'+json.id), { afterFinish : function (eff) { eff.element.remove (); } });
		}
		else
		{
			alert (json.message);
		}
	}
}

// Hook the init
Event.observe(window, 'load', CMS.navigation.init, false);
