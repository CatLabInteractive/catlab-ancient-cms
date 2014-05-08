if (!CMS)
{
	var CMS = new Object ();
}

/* Tiny MCE */
CMS.initTinyMCE = function ()
{
	tinyMCE.init({
		plugins : 'fullscreen,media,upload',
		theme : "advanced",
		theme_advanced_toolbar_align : "left",
		theme_advanced_toolbar_location : "top",
		mode : "textareas",
		width : "650",
		height: "400",
		theme_advanced_resizing : true,
		theme_advanced_buttons2 : 'bullist,numlist,|,outdent,indent,|,link,unlink,anchor,|,undo,redo,|,forecolor,backcolor,|,upload,media,|,help,code,|,fullscreen',
		theme_advanced_buttons3 : '',
		spellchecker_languages : "English=en,Danish=da,+Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",
		file_browser_callback : 'CMS.upload.openManager',
		document_base_url : CMS.settings.static_url,
		valid_elements : "@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|"
			+ "onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|"
			+ "onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|"
			+ "name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u,"
			+ "#p[align],-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|"
			+ "src|border|alt=|title|hspace|vspace|width|height|align|about],-sub,-sup,"
			+ "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|"
			+ "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|"
			+ "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,"
			+ "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor"
			+ "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,"
			+ "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face"
			+ "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],"
			+ "object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width"
			+ "|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,"
			+ "button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|"
			+ "valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],"
			+ "input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],"
			+ "kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],"
			+ "q[cite],samp,select[disabled|multiple|name|size],small,"
			+ "textarea[cols|rows|disabled|name|readonly],tt,var,big,"
			+ "iframe[width|height|src|frameborder|allowfullscreen]"
	});
}

/* Common object */
CMS.common = 
{
	'langContainers' : new Array (),
	
	'init' : function ()
	{
		CMS.common.makeLanguageTabs ();
		CMS.initTinyMCE ();
	},
	
	'getClassnameExcerpt' : function (obj, sSearchFor)
	{
		var aExcerpts = obj.className.split(' ');
		for (var i = 0; i < aExcerpts.length; i ++)
		{
			if (aExcerpts[i].search (sSearchFor) >= 0)
			{
				return aExcerpts[i];
			}
		}
		return false;
	},
	
	'makeLanguageTabs' : function ()
	{
		// Fetch the containers
		var containers = $$('div.language_container');
		
		for (var i = 0; i < containers.length; i ++)
		{
			// Make a container
			var objContainer =
			{
				'container' : containers[i],
				
				'init' : function ()
				{
					var toggles = this.container.select('div.language_toggles li a');
					var cnt = this;
					
					for (var j = 0; j < toggles.length; j ++)
					{
						toggles[j].onclick = function () 
						{ 
							cnt.toggleLanguagePage(cnt, this);
						};
					}
				},
				
				'toggleLanguagePage' : function (cnt, objSelected)
				{
					// Only activate the current one
					var toggles = cnt.container.select('div.language_toggles li a');
					var toggleClass = CMS.common.getClassnameExcerpt (objSelected, 'toggle_');
					
					for (var i = 0; i < toggles.length; i ++)
					{
						var sNewClass = CMS.common.getClassnameExcerpt (toggles[i], 'toggle_');
						
						if (sNewClass == toggleClass)
						{
							toggles[i].addClassName ('active');
							cnt.container.select('div.'+sNewClass.replace('toggle_', 'content_'))[0].style.display = 'block';
						}
						else
						{
							toggles[i].removeClassName ('active');
							cnt.container.select('div.'+sNewClass.replace('toggle_', 'content_'))[0].style.display = 'none';
						}
					}
				}
			}
			
			objContainer.init ();
			CMS.common.langContainers.push (objContainer);
		}
	}
}

Event.observe(window, 'load', CMS.common.init, false);
