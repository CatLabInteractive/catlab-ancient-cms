/*
	Form checker.
	
	Beste leerkracht,
	We gebruiken het prototype framework omdat dit ... gemakkelijker is.
	Ik vertrouw er echter op dat dit ook mijn javascript kunde bewijst.
	
	Ik ben van plan dit script voor andere projecten te gebruiken,
	vandaar dat ik me niet beperk tot quick&dirty solutions.
*/
CMS.formchecker =
{
	/*
		Returned TRUE als de form in orde is
		en FALSE als dit niet het geval is.
		
		Controle wordt gedaan op de classnaam.
	*/
	'validate' : function (form)
	{
		form = $(form);
	
		// Controlleer alle inputs
		var inputs = form.select ('input', 'textarea');
		var check = true;
		
		for (var i = 0; i < inputs.length; i ++)
		{
			var input = inputs[i];
			if 
			(
				// check if empty
				( input.hasClassName ('required') && input.value == "" )
				
				// check voor email
				|| ( input.hasClassName ('email') && !CMS.formchecker.checkEmail (input.value) )
			)
			{
				input.addClassName ('false');
				check = false;
			}
			
			else
			{
				input.removeClassName ('false');
			}
		}
		
		return check;
	},
	
	'checkEmail' : function (str)
	{
		return (str.indexOf(".") > 2) && (str.indexOf("@") > 0);
	}
}

/*
	Zorg ervoor dat de forms die gevalideert
	moeten worden ook effectief gevalideert worden.
*/
Event.observe 
(
	window, 'load', function ()
	{
		var forms = $$('form.validate');
		for (var i = 0; i < forms.length; i ++)
		{
			forms[i].observe 
			(
				'submit', 
				function (e)
				{
					if (!CMS.formchecker.validate (e.element()))
					{
						e.stop ();
					}
				}
			);
		}
	}
);
