<?php
if (!defined ('CONTACTFORM_SENDTO'))
{
	define ('CONTACTFORM_SENDTO', 'thijs@catlab.be');
}

require_once ('lib/recaptchalib.php');

if (!defined ('RECAPTCHA_PUBLIC_KEY'))
{
	define ('RECAPTCHA_PUBLIC_KEY', '6LcCKcESAAAAADTQnm6xNZSnDiKnAgAqpqBBLLii');
}

if (!defined ('RECAPTCHA_PRIVATE_KEY'))
{
	define ('RECAPTCHA_PRIVATE_KEY', '6LcCKcESAAAAAJ-coZMBGI-Iy97mSt6SgqzTgBqV');
}

/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_Contactform extends Modules_Page
{
	/*
	public function getPluginEditor ($id)
	{
		$page = new Core_Template ();
		
		$aContent = $this->getMetaContent ($id, true);
		
		$title = array ();
		$url = array ();
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$page->addListValue
			(
				'languages',
				array
				(
					'id' => $k,
					'name' => $v
				)
			);
			
			$title[$k] = Core_Tools::output_input ($aContent['title_'.$k]);
			$url[$k] = Core_Tools::output_input ($aContent['text_'.$k], 250);
		}
		
		$page->set ('title', $title);
		$page->set ('url', $url);
		
		$page->set ('content_action', $this->objCMS->getAdminUrl ('pages', 'edit', $aContent['n_id'], 'output=json'));
		
		return $page->parse ('modules/contactform/editor.phpt');
	}
	
	public function saveContent ($id)
	{

	}
	*/
	
	/*
	public function getNavigationRow ($page)
	{
		$row = parent::getNavigationRow ($page);
		$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], null, null, 'cpid='.$page['n_id']);
		return $row;
	}
	*/
	
	public function getEditorActions ($id = null)
	{
		$page = new Core_Template ();
		$page->set ('remove_url', $this->objCMS->getAdminUrl ('pages', 'delete', $id, 'output=json'));
		return $page->parse ('modules/page/actions.phpt');
	}
	
	public function getContent ()
	{	
		$email = Core_Tools::getInput ('_POST', 'contact_mail', 'varchar');
		$subject = Core_Tools::getInput ('_POST', 'contact_subject', 'varchar');
		
		$id = $this->objCMS->getRecordId ();
		
		$okay = false;
		
		$input = array ();
		
		if ($email && $subject)
		{
			//$text = Core_Tools::getInput ('_POST', 'contact_text', 'varchar');
			//$website = Core_Tools::getInput ('_POST', 'contact_website', 'varchar');
		
			//$text .= "\n\n" . "Sent by ".$email.' '.$website;
			
			$challenge = Core_Tools::getInput ('_POST', "recaptcha_challenge_field", 'varchar');
			$response = Core_Tools::getInput ('_POST', "recaptcha_response_field", 'varchar');
			
			$resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $challenge, $response);
			
			$mailtext = '';
			foreach ($_POST as $k => $v)
			{
				$prefix = 'contact_';
				if (substr ($k, 0, strlen ($prefix)) == $prefix)
				{
					if (!empty ($v))
					{
						$mailtext .= ucfirst (substr ($k, strlen ($prefix))) . ":\n" . $v . "\n\n";
						
						$input[$k] = $v;
					}
				}
			}
			
			if (!$response || !$resp->is_valid)
			{
				$error = '<p class="false">The reCAPTCHA wasn\'t entered correctly. Go back and try it again.</p><p class="false">' . $resp->error . '</p>';
			}
			else
			{			
				$sendto = CONTACTFORM_SENDTO;
			
				if (defined ('CONTACTFORM_SENDTO_'.$id))
				{
					$sendto = constant ('CONTACTFORM_SENDTO_'.$id);
				}
				
				$mailtext .= "Sent from: " . CMS_FULL_URL;
		
				$out = '<h2>Contact form</h2><p>Your mail has been sent. We will get back to you as soon as possible.</p>';
				mail ($sendto, $subject, $mailtext, 'From: '.$email);
				
				$okay = true;
			}
		}
		
		if (!$okay)
		{
			$html = $this->getPageHTML ($id);
			if (!empty ($html))
			{
				//return $html;
				$out = $html;
			}
			else
			{			
				$content = $this->getMetaContent ($id);
		
				$title = '<h2>'.$content['title'].'</h2>';
		
				$out = '
					<form method="post" class="validate">
						<fieldset>
							<legend class="first">Your message</legend>
							<ol>
								<li>
									<label for="contact_subject">Subject:</label>
									<input type="text" name="contact_subject" id="contact_subject" class="text required" value="{contact_subject_value}" />
								</li>
								<li>
									<label for="contact_text">Content:</label>
									<textarea type="text" name="contact_text" id="contact_text" class="text required">{contact_text_value}</textarea>
								</li>';

				$out .=  '	</ol>
					</fieldset>
				
					<fieldset>
						<legend>Your details</legend>
						<ol>
							<li>
								<label for="contact_subject">Email address:</label>
								<input type="text" name="contact_mail" id="contact_mail" class="email required" value="{contact_mail_value}" />
							</li>
							<li>
								<label for="contact_website">Website:</label>
								<input type="text" name="contact_website" id="contact_website" class="text" value="{contact_website_value}" />
							</li>
						
							<li class="captcha">{captcha}</li>
						
							<li>
								<button type="submit"><span>Send mail</span></button>
							</li>
						</ol>
					</fieldset>
				</form>';
			}
				
			$out = str_replace ('{captcha}', recaptcha_get_html (RECAPTCHA_PUBLIC_KEY), $out);
			
			if ($error)
			{
				$out = $out . $error;
			}
			
			foreach ($input as $k => $v)
			{
				$out = str_replace ('{' . $k . '_value}', $v, $out);
			}
			
			$out = preg_replace ('%{contact_([^}])*}%i', '', $out);
			
			$out = $title . $out;
		}
			
		return $out;
	}
}
?>
