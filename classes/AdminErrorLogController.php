<?php

namespace Fierce;

class AdminErrorLogController extends PageController
{
  public $mainTpl = 'main-admin.tpl';
  
  public function defaultAction()
  {
    Auth::requireRoot();
    
    if (@$_GET['lastWeek']) {
      $minDate = (new \DateTime())->sub(new \DateInterval('P7D'));
      $this->logPeriod = '7 days';
    } else {
      $minDate = (new \DateTime())->sub(new \DateInterval('P1D'));
      $this->logPeriod = '1 day';
    }
    
    $this->maxErrors = 1000;
    
    $this->logFile = Env::get('error_log');
    
    
    $contents = file_get_contents($this->logFile);
    if(!$contents){
        throw new \Exception("Log file does not exist.", 2);
    }
    $lines = explode("\n", $contents);
    $lines = array_reverse($lines);
    
    
    
    $previousNonErrorLinesStr = '';
    $this->errors = [];
    foreach ($lines as $line) {
      $date = false;
      $error = false;
      
      if (preg_match('/^\\[(.+?)\\] (.*)$/', $line, $matches)) {
        $dateStr = $matches[1];
        
        // strip microseconds from the time value ('02:43:47.862198' -> '02:43:47')
        $dateStr = preg_replace('/([0-9][0-9]\\:[0-9][0-9]\\:[0-9][0-9]).[0-9]+/', '$1', $dateStr);
        
        $date = new \DateTime($dateStr);
        
        
        
        $error = $matches[2];
      }
      
      if (!$date) {
        $previousNonErrorLinesStr = $line . "\n" . $previousNonErrorLinesStr;
        continue;
      }
      
      if ($date < $minDate) {
        break;
      }
      
      if (count($this->errors) == 1000) {
        $this->errors[] = (object)[
          'date' => new \DateTime(),
          'message' => 'TOO MANY ERRORS.'
        ];
        break;
      }
      
      $this->errors[] = (object)[
        'date' => $date,
        'message' => str_replace('\\n', "\n", $error . "\n" . $previousNonErrorLinesStr)
      ];
      
      $previousNonErrorLinesStr = '';
    }
    
    $this->pageTitle = 'Error Log';
    
    $this->display('admin-errors.tpl');
  }
}
