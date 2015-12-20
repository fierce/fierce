<?php

namespace Fierce;

class ContactPageController extends PageController
{
  public function callbackSubmitAction()
  {
    $phone = trim(@$_POST['phone']);
    
    if (!$phone) {
      HTTP::redirect($this->url(false, ['msg' => 'Please fill in your phone number.']));
    }
    
    $m = new Mail();
    $m->subject = 'Callback Request';
    $m->to = Env::get('company_email');
    $m->message = "Callback phone: $phone";
    $m->send();
    
    HTTP::redirect($this->url(false, ['do' => 'thanks']));
  }
  
  public function thanksAction()
  {
    $tplVars = [
      'controller' => $this,
      'pageTitle' => $this->page->name,
      'contentViewTpl' => '<h1>Message Sent</h1><p>Your message has been sent and we will respond as soon as possible.</p>',
      'page' => $this->page
    ];
    
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderString($this->page->content, $tplVars);
      return;
    }
    
    View::main($this->mainTpl, false, $tplVars);
  }
  
  public function newsletterSubmitAction()
  {
    $first_name = trim(@$_POST['first_name']);
    $last_name = trim(@$_POST['last_name']);
    $email = trim(@$_POST['email']);
    
    $m = new Mail();
    $m->subject = 'Newsletter Signup Request';
    $m->to = Env::get('company_email');
    $m->message = "First Name: $first_name\nLast Name: $last_name\nEmail: $email";
    $m->send();
    
    HTTP::redirect($this->url(false, ['do' => 'thanks-newsletter']));
  }
  
  public function thanksNewsletterAction()
  {
    $tplVars = [
      'controller' => $this,
      'pageTitle' => $this->page->name,
      'contentViewTpl' => '<h1>Signup Successful</h1><p>Your request to sign up for the newsletter has been submitted.</p>',
      'page' => $this->page
    ];
    
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderString($this->page->content, $tplVars);
      return;
    }
    
    View::main($this->mainTpl, false, $tplVars);
  }
}
