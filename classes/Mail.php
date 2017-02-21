<?php

namespace Fierce;

class Mail
{
  public $subject;
  public $to;
  public $ccCompanyEmail = false;
  public $message;
  public $isHtml = false;
  
  public function send()
  {
    if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
      throw new \Exception('Attempt to send email to an invalid email');
    }    
    
    $client = \Aws\Ses\SesClient::factory(array(
      'version'=> 'latest',
      'region' => Env::get('AWS_REGION'),
      'credentials' => [
        'key' => Env::get('AWS_ACCESS_KEY_ID'),
        'secret' => Env::get('AWS_SECRET_ACCESS_KEY')
      ]
    ));
    
    $request = array();
    $request['Source'] = Env::get('company_email_name') . ' <' . Env::get('company_email') . '>';
    
    if (Env::get('mailOverrideAddress')) {
      $request['Destination']['ToAddresses'] = array(Env::get('mailOverrideAddress'));
    } else {
      $request['Destination']['ToAddresses'] = array($this->to);
    }
    
    $request['Message']['Subject']['Data'] = $this->subject;
    
    if ($this->isHtml) {
      $request['Message']['Body']['Html']['Data'] = $this->message;
    } else {
      $request['Message']['Body']['Text']['Data'] = $this->message;
    }

    $result = $client->sendEmail($request);
    $messageId = $result->get('MessageId');
    
    return $messageId;
  }
}
