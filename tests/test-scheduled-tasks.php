<?php

namespace Fierce;

$db = $this->mock('DB');
Env::push('db', $db);

$db->ScheduledTask = $this->mock('DBEntity', 'ScheduledTask');

$taskController = new ScheduledTaskController();
$taskController->db = $db;

// check if a scheduled task runs properly
$task = (object)[
  'id' => '63E3BACB-3F7B-4C77-A303-F03FB2EE117C',
  'status' => 'pending',
  'date' => new \DateTime(),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'run',
  'log' => null
];

$db->ScheduledTask->prepareForCall('find', null, [$task]);
$db->ScheduledTask->prepareForCall('byId', null, $task);

$activeWrite = null;
$completeWrite = null;
$db->ScheduledTask->prepareForCall('write', null, function($id, $row, $allowOverwrite) use (&$activeWrite, &$completeWrite) {  
  if ($row->status == 'active') {
    $activeWrite = $row;
    return;
  }
  if ($row->status == 'complete') {
    $completeWrite = $row;
    return;
  }
});

$taskController->defaultAction();

$this->assert($activeWrite != null, 'Task marked active');

$log = json_decode($activeWrite->log);
$logEntry = $log[0];

$this->assert(((new \DateTime())->getTimestamp() - (new \DateTime($logEntry->started))->getTimestamp()) >= 0 &&
              ((new \DateTime())->getTimestamp() - (new \DateTime($logEntry->started))->getTimestamp()) < 10,
'Active task included a valid start date');

$log = json_decode($completeWrite->log);
$logEntry = $log[0];

$this->assertEqual($logEntry->log, 'Hello World', 'Task did run.');

$this->assert($completeWrite != null, 'Task marked complete');
$this->assert(((new \DateTime())->getTimestamp() - (new \DateTime($logEntry->started))->getTimestamp()) >= 0 &&
              ((new \DateTime())->getTimestamp() - (new \DateTime($logEntry->started))->getTimestamp()) < 10,
'Completed task has a valid end date');




// scheduled to run now but is already running so should be skipped
$task = (object)[
  'id' => '63E3BACB-3F7B-4C77-A303-F03FB2EE117C',
  'status' => 'active',
  'date' => new \DateTime(),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'run',
  'log' => null
];

$db->ScheduledTask->prepareForCall('find', null, [$task]);
$db->ScheduledTask->prepareForCall('byId', null, $task);

$writeRow = null;
$db->ScheduledTask->prepareForCall('write', null, function($id, $row, $allowOverwrite) use (&$writeRow) {  
  $writeRow = $row;
});

$taskController->defaultAction();

$this->assertEqual($writeRow, null, 'Already active task is skipped');



// scheduled to run in the future, so will be skipped
$task = (object)[
  'id' => '63E3BACB-3F7B-4C77-A303-F03FB2EE117C',
  'status' => 'pending',
  'date' => (new \DateTime())->add(new \DateInterval('PT1H')),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'run',
  'log' => null
];

$db->ScheduledTask->prepareForCall('find', null, [$task]);
$db->ScheduledTask->prepareForCall('byId', null, $task);

$writeRow = null;
$db->ScheduledTask->prepareForCall('write', null, function($id, $row, $allowOverwrite) use (&$writeRow) {  
  $writeRow = $row;
});

$taskController->defaultAction();

$this->assertEqual($writeRow, null, 'Task scheduled for the future is skipped');



// sheduled to run now, and repeat in 1 day. it should execute and then the date should change, and log should be appended to with old items removed
$threeWeeksAgo = (new \DateTime())->sub(new \DateInterval('P3W'))->format('Y-m-d H:i:s');
$oneWeekAgo = (new \DateTime())->sub(new \DateInterval('P1W'))->format('Y-m-d H:i:s');
$task = (object)[
  'id' => '63E3BACB-3F7B-4C77-A303-F03FB2EE117C',
  'status' => 'pending',
  'date' => new \DateTime(),
  'repeat' => 'P1D',
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'run',
  'log' => '[
    {"started":"' . $threeWeeksAgo . '","completed":"' . $threeWeeksAgo . '","log":"Hello World"},
    {"started":"' . $oneWeekAgo . '","completed":"' . $oneWeekAgo . '","log":"Hello World"}
  ]'
];

$db->ScheduledTask->prepareForCall('find', null, [$task]);
$db->ScheduledTask->prepareForCall('byId', null, $task);

$writeRow = null;
$db->ScheduledTask->prepareForCall('write', null, function($id, $row, $allowOverwrite) use (&$writeRow) {  
  $writeRow = $row;
});

$taskController->defaultAction();

$this->assertEqual($writeRow->status, 'pending', 'Repeating task is pending after completion.');
$this->assertEqual($writeRow->date, $task->date->add(new \DateInterval($task->repeat)), 'Repeating task date is correct.');
$this->assert(count(json_decode($writeRow->log)) == 2, 'Old log entries are removed');


// make sure we properly handle repeating tasks with a very old schedule date (eg the date might be set as part of a
// migration, or a server issue might stop scheduled tasks from running for a perioid of time).
$twoYearsAgo = (new \DateTime())->sub(new \DateInterval('P2YT4M'));
$task = (object)[
  'id' => '63E3BACB-3F7B-4C77-A303-F03FB2EE117C',
  'status' => 'pending',
  'date' => $twoYearsAgo,
  'repeat' => 'PT10M',
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'run',
  'log' => null
];

$db->ScheduledTask->prepareForCall('find', null, [$task]);
$db->ScheduledTask->prepareForCall('byId', null, $task);

$writeRow = null;
$db->ScheduledTask->prepareForCall('write', null, function($id, $row, $allowOverwrite) use (&$writeRow) {  
  $writeRow = $row;
});

$taskController->defaultAction();

$this->assertEqual($writeRow->status, 'pending', 'Old repeating task is re-scheduled');
$this->assert($writeRow->date >= (new \DateTime()), 'Old repeating task is re-scheduled for a future date');





// will throw an exception, status should change to failed
$task = (object)[
  'id' => '3DDA9129-7E48-4945-B3AC-A5BBE7D5752E',
  'status' => 'pending',
  'date' => new \DateTime(),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'runWithException'
];

$this->fail('make sure an exception is flagged as failed');

// throws an uncatchable runtime error, status should change to failed
$task = (object)[
  'id' => 'F9C4DE34-2283-4539-BC8B-54B7ED34C545',
  'status' => 'pending',
  'date' => new \DateTime(),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyTask',
  'method' => 'runError'
];
$this->fail('make sure a runtime error is flagged as failed');

// this task contains a parse error, status should change to failed
$task = (object)[
  'id' => 'C62C4BBE-12B0-453A-83F6-BAE93AB52E84',
  'status' => 'pending',
  'date' => new \DateTime(),
  'repeat' => null,
  'class' => 'Fierce\Test\DummyBadTask',
  'method' => 'run'
];
$this->fail('make sure a parse error is flagged as failed');

// this task failed two weeks ago should be archived
$this->fail('make sure failed task from over two weeks ago is archived');

// active but started two weeks ago should be archived
$this->fail('make sure active task from two weeks is archived');

// task completed over two weeks ago should be archived
$this->fail('make sure completed task from over two weeks ago is archived');

