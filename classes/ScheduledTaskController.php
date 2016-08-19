<?php

namespace Fierce;

class ScheduledTaskController extends PageController
{
  public function defaultAction()
  {
    $tasks = $this->db->ScheduledTask->find();
    
    $taskCount = 0;
    foreach ($tasks as $task) {
      // re-fetch task, incase it's status is out of date
      $task = $this->db->ScheduledTask->byId($task->id);
      
      // is the task pending?
      if ($task->status != 'pending') {
        continue;
      }
      
      // scheduled for the future?
      if ($task->date > (new \DateTime())) {
        continue;
      }
      
      // lock this task as something we're about to start on
      $logEntry = (object)[
        'started' => (new \DateTime())->format('Y-m-d H:i:s'),
        'completed' => null,
        'log' => null
      ];
      
      // load (or create) log
      $log = $task->log ? json_decode($task->log) : [];
      
      // kill old entries
      $twoWeeksAgo = (new \DateTime())->sub(new \DateInterval('P2W'));
      $log = array_filter($log, function($logEntry) use ($twoWeeksAgo) {
        return $twoWeeksAgo > new \DateTime($logEntry->started);
      });
      
      // add this entry
      $log[] = $logEntry;
      $this->db->ScheduledTask->write($task->id, (object)[
        'status' => 'active',
        'log' => json_encode($log)
      ], true);
      
      // package the task to be completed and an authentication hash
      $classMethod = json_encode([$task->class, $task->method]);
      $password = hash('sha256', (new \DateTime())->format('Y-m-d H:i:s') . $task->id . Env::get('auth_salt'));
      $mac = hash_hmac('sha256', $classMethod, $password);
      
      // execute the task
      print('<h3>Running ' . $task->class . '->' . $task->method . '()</h3>');
      $url = Env::get('base_url') . 'vendor/fierce/fierce/maint/tasks.php?' . http_build_query([
        'do' => 'run-task',
        'id' => $task->id,
        'method' => $classMethod,
        'auth' => $mac
      ]);
      $response = file_get_contents($url);
      print '<pre>' . htmlspecialchars($response) . '</pre>';
      
      // create a log entry with the response
      $logEntry->completed = (new \DateTime())->format('Y-m-d H:i:s');
      $logEntry->log = $response;
      
      $writeRow = (object)[
        'log' => json_encode($log)
      ];
      
      // repeat?
      if ($task->repeat) {
        $writeRow->date = $task->date->add(new \DateInterval($task->repeat));
        while ($writeRow->date < (new \DateTime())) {
          $writeRow->date = $task->date->add(new \DateInterval($task->repeat));
        }
        
        $writeRow->status = 'pending';
      } else {
        $writeRow->status = 'complete';
      }
      
      // write to db
      $this->db->ScheduledTask->write($task->id, $writeRow, true);
      $taskCount++;
    }
    
    print("<p>Finished running $taskCount tasks</p>");
  }
  
  public function runTaskAction()
  {
    // load details
    $taskId = @$_GET['id'];
    $classMethod = @$_GET['method'];
    $mac = @$_GET['auth'];
    
    // verify hash
    $date = (new \DateTime());
    $password = hash('sha256', $date->format('Y-m-d H:i:s') . $taskId . Env::get('auth_salt'));
    $expectedMac = hash_hmac('sha256', $classMethod, $password);
    
    // if hash is invalid, try again with a 1 second ago date
    if ($mac != $expectedMac) {
      $date = (new \DateTime())->sub(new \DateInterval("PT1S"));
      $password = hash('sha256', $date->format('Y-m-d H:i:s') . $taskId . Env::get('auth_salt'));
      $expectedMac = hash_hmac('sha256', $classMethod, $password);
    }
    
    if ($mac != $expectedMac) {
      throw new \Exception('Invalid auth token');
    }
    
    $classMethod = json_decode($classMethod);
    $class = $classMethod[0];
    $method = $classMethod[1];
    
    $obj = new $class();
    $obj->$method();
  }
}
