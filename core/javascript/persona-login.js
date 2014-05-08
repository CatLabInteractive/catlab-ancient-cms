document.observe("dom:loaded", function() {

	var continueURL = null;

	$$('a.catlab-persona-login').invoke ('observe', 'click', function (e)
	{
		navigator.id.request();
		continueURL = this.attributes['href'].value;

		e.stop ();

		return false;
	});

	$$('a.catlab-persona-logout').invoke('observe', 'click', function ()
	{
		navigator.id.logout();
	});

	//console.log ('loggedInUser: ');
	//console.log (CMS.session.persona.user);

	navigator.id.watch({

		'loggedInUser' : CMS.session.persona.user,

		onlogin : function (assertion)
		{
			var url = CMS.settings.static_url + CMS.settings.language + '/account/api/login';

			new Ajax.Request
			(
				url,
				{
					'method': 'post',
					'parameters': 
					{
						'assertion' : assertion
					},
					'onSuccess': function (transport)
					{
						var json = transport.responseJSON;

						if (json.status === 'success')
						{
							if (continueURL !== null)
							{
								window.location = continueURL;
							}
							else
							{
								window.location.reload();
							}
						}
						else
						{
							alert (json.error);
						}
					},
					'onFailure': function ()
					{
						alert ('I\'m sorry, Dave, I can\'t let you in.');
					}
				}
			);
		},

		'onlogout' : function ()
		{
			console.log ('Logging out.');
			//document.location = '/en/account/logout/';
		}

	});

});