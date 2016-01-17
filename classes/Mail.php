<?php

namespace Fierce;

class Mail
{
  public $subject;
  public $to;
  public $message;
  
  public function send()
  {
    $headers = 'From: ' . Env::get('company_email') . "\r\n";
    
    mail($this->to, $this->subject, $this->message, $headers);
  }
}
