<?php

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

namespace Fierce;

class TestsController extends PageController
{
  protected $passCount = 0;
  protected $failCount = 0;
  
  function assert($value, $msg, $msgIsHtml=false)
  {
    if ($value) {
      $this->log('pass', $msg, $msgIsHtml);
      $this->passCount++;
      return;
    }
    
    $this->log('fail', $msg, $msgIsHtml);
    $this->failCount++;
  }
  
  function assertEqual($a, $b, $msg, $msgIsHtml=false)
  {
    if (!$msgIsHtml) {
      $msg = htmlspecialchars($msg);
    }
    
    if ($a == $b) {
      $this->assert(true, $msg, true);
      return;
    }
    
    ob_start();
    var_dump($a);
    $aDump = trim(ob_get_clean());
    
    ob_start();
    var_dump($b);
    $bDump = trim(ob_get_clean());
    
    $this->assert(false, "$msg\n     Got: <pre>$aDump</pre>\nExpected: <pre>$bDump</pre>", true);
  }
  
  function assertContains($array, $value, $msg, $msgIsHtml=false)
  {
    if (!$msgIsHtml) {
      $msg = htmlspecialchars($msg);
    }
    
    foreach ($array as $arrayValue) {
      if ($arrayValue === $value) {
        $this->assert(true, $msg, true);
        return;
      }
    }
    
    ob_start();
    var_dump($value);
    $valueDump = trim(ob_get_clean());
    
    ob_start();
    var_dump($array);
    $arrayDump = trim(ob_get_clean());
    
    $this->assert(false, "$msg\nCannot find: <pre>$valueDump</pre>\n         In: <pre>$arrayDump</pre>", true);
  }
  
  function assertDoesntContain($array, $value, $msg, $msgIsHtml=false)
  {
    if (!$msgIsHtml) {
      $msg = htmlspecialchars($msg);
    }
    
    foreach ($array as $arrayValue) {
      if ($arrayValue === $value) {
        $this->assert(false, $msg, true);
        return;
      }
    }
    
    ob_start();
    var_dump($value);
    $valueDump = trim(ob_get_clean());
    
    ob_start();
    var_dump($array);
    $arrayDump = trim(ob_get_clean());
    
    $this->assert(true, "$msg\nCannot find: <pre>$valueDump</pre>\n         In: <pre>$arrayDump</pre>", true);
  }
  
  function fail($msg, $msgIsHtml=false)
  {
    $this->assert(false, $msg, $msgIsHtml);
  }
  
  function mock($className, $displayName=null)
  {
    return new Mock($className, $this, $displayName);
  }
  
  function testFiles()
  {
    $filesById = [];
    
    $files = glob(Env::get('fierce_path') . 'tests/*.php');
    sort($files, SORT_NATURAL);
    foreach ($files as $file) {
      $filesById[sha1($file)] = $file;
    }
    
    $files = glob(Env::get('base_path') . 'tests/*.php');
    sort($files, SORT_NATURAL);
    foreach ($files as $file) {
      $filesById[sha1($file)] = $file;
    }
    
    return $filesById;
  }
  
  function defaultAction()
  {
    $this->printHead();
    
    print '
      <h1>Tests</h1>
      
      <table border="0">
        <tr><td>Passes:</td><td id="pass-count">0</td></tr>
        <tr><td>Fails:</td><td id="fail-count">0</td></tr>
        <tr><td>Duration:</td><td id="test-duration">0s</td></tr>
      </table>
      <hr>
    ';
    
    print '<ul id="test-list">';
    
    $files = glob(Env::get('fierce_path') . 'tests/*.php');
    foreach ($this->testFiles() as $id => $file) {
      $name = $this->nameForTestFile($file);
      
      print '
        <li><a href="test.php?do=run&test=' . $id . '" class="test-link status-waiting">' . htmlspecialchars($name)  . '</a></li>
      ';
    }
    
    print '</ul>';
    
    print "
      <script type=\"text/javascript\">
        var passCount = 0
        var failCount = 0
        var durationStart = new Date().getTime()
        
        var links = document.getElementsByClassName('test-link')
        for (var linkIndex = 0; linkIndex < links.length; linkIndex++) {
          var link = links[linkIndex]
          
          beginTest(link)
        }
        
        function beginTest(link)
        {
          var xhr = new XMLHttpRequest()
          
          xhr.onreadystatechange = function() {
            if (xhr.readyState != XMLHttpRequest.DONE) {
              updateStats()
              return
            }
            
            if (xhr.status != 200) {
              setStatus(link, 'fail')
              failCount++
              updateStats()
              return
            }
            
            var matches = xhr.responseText.match(/<span id=\"test-status-pass-count\">([0-9]+)<\\/span>/)
            if (!matches) {
              setStatus(link, 'fail')
              failCount += parseInt(matches[1])
              updateStats()
              return
            }
            if (matches[1] > 0) {
              passCount += parseInt(matches[1])
              document.getElementById('pass-count').innerHTML = passCount;
            }
            
            var matches = xhr.responseText.match(/<span id=\"test-status-fail-count\">([0-9]+)<\\/span>/)
            if (!matches) {
              setStatus(link, 'fail')
              failCount += parseInt(matches[1])
              updateStats()
              return
            }
            if (matches && matches[1] > 0) {
              setStatus(link, 'fail')
              failCount += parseInt(matches[1])
              updateStats()
              return
            }
            
            setStatus(link, 'pass')
            updateStats()
          }
          
          xhr.open('GET', link.href, true)
          xhr.send(null)
        }
        
        function setStatus(link, status)
        {
          link.setAttribute('class', 'test-link status-' + status)
        }
        
        function updateStats()
        {
          document.getElementById('pass-count').innerHTML = passCount;
          document.getElementById('fail-count').innerHTML = failCount;
          
          var seconds = Math.floor((new Date().getTime() - durationStart) / 1000);
          
          var minutes = Math.floor(seconds / 60)
          seconds -= (minutes * 60)
          
          var hours = Math.floor(minutes / 60)
          minutes -= (hours * 60)
          
          var duration = '';
          if (hours > 0) {
            duration += ' ' + hours + 'h';
          }
          if (minutes > 0) {
            duration += ' ' + minutes + 'm';
          }
          if (seconds > 0 || duration == '') {
            duration += ' ' + seconds + 's';
          }
          
          document.getElementById('test-duration').innerHTML = duration;
        }
      
      </script>
    ";
    
    $this->printFoot();
  }
  
  function runAction()
  {
    ErrorHandler::$customHandler = function($errno, $errstr, $errfile, $errline, $errcontext, $errorName) {
      $this->fail("$errorName $errstr\n$errfile on line $errline");
      
      print '<hr>';
      print "<p>
        Aborted with <span id=\"test-status-pass-count\">$this->passCount</span> passe" . ($this->passCount == 1 ? '' : 's') . ",
        <span id=\"test-status-fail-count\">$this->failCount</span> fail" . ($this->failCount == 1 ? '' : 's') . ".
      </p>";
      $this->printFoot();
      exit;
    };
    
    try {
    $files = $this->testFiles();
    $testFile = $files[$_GET['test']];
    if (!$testFile) {
      die('cannot find test');
    }
    $this->__testFile = $testFile;
    
    $this->__buffered = preg_match('/-with-buffer.php/', $testFile);
    if ($this->__buffered) {
      ob_start();
    }
    
    $this->printHead();
    
    print '<h3>' . htmlentities($this->nameForTestFile($testFile)) . '</h3>';
    $relativePath = substr($testFile, strlen(Env::get('base_path')));
    print "<pre>$relativePath</pre>";
    print '<hr>';
    print '<p><a href="test.php">« Back to test list</a></p>';
    print '<hr>';
    print '<ul id="test-list">';
    
    require $testFile;
    
    print '</ul>';
    
    print '<hr>';
    print "<p>
      Completed with <span id=\"test-status-pass-count\">$this->passCount</span> passe" . ($this->passCount == 1 ? '' : 's') . ",
      <span id=\"test-status-fail-count\">$this->failCount</span> fail" . ($this->failCount == 1 ? '' : 's') . ".
    </p>";
    
    $this->printFoot();
    
    if ($this->__buffered) {
      ob_end_flush();
    }
    
    } catch (\Exception $e) {
      $this->fail('Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
      
      print '<hr>';
      print "<p>
        Aborted with <span id=\"test-status-pass-count\">$this->passCount</span> passe" . ($this->passCount == 1 ? '' : 's') . ",
        <span id=\"test-status-fail-count\">$this->failCount</span> fail" . ($this->failCount == 1 ? '' : 's') . ".
      </p>";
      $this->printFoot();
      exit;
    }
  }
  
  function log($status, $msg, $msgIsHtml)
  {
    if (!$msgIsHtml) {
      $msg = htmlspecialchars($msg);
    }
    
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $testLineHtml = '';
    foreach ($backtrace as $entry) {
      if (!isset($entry['file']) || $entry['file'] != $this->__testFile) {
        continue;
      }
      
      $testLineHtml = '<span class="test-line">Line ' . $entry['line'] . ':</span> ';
    }
    
    print('<li class="status-' . $status . '">' . $testLineHtml . $msg . '</li>');
    
    if (!$this->__buffered) {
      flush();
    }
  }
  
  function printHead()
  {
    print '
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
        <title>Tests</title>
        <style>
          body
          {
            font-family: monospace; font-size: 12px
          }
          
          ul#test-list
          {
            margin: 0 20px 0 0;
            padding: 0;
            list-style-type: none;
          }
          
          ul#test-list li
          {
            margin: 10px 0;
            position: relative;
            padding: 3px 5px 3px 20px;
            white-space: pre;
          }
          
          ul#test-list a
          {
            left: 0;
            display: inline-block;
            text-decoration: none;
            color: #000;
          }
          
          ul#test-list li pre
          {
            margin: 0 4px 4px 0;
            display: inline-block;
            vertical-align: top;
            background-color: #eee;
          }
          
          hr
          {
            border: none;
            border-top: 1px solid #ddd;
            margin: 10px 0;
          }
          
          ul#test-list hr:first-child,
          ul#test-list hr:last-child
          {
            display: none;
          }
          
          ul#test-list a.status-waiting:before
          {
            position: absolute;
            left: 0;
            content: \'⏳ \';
          }
          
          ul#test-list a.status-pass:before,
          ul#test-list li.status-pass:before
          {
            position: absolute;
            left: 0;
            content: \'✅ \';
          }
          
          ul#test-list a.status-fail:before,
          ul#test-list li.status-fail:before
          {
            position: absolute;
            left: 0;
            content: \'‼️ \';
          }
          
        </style>
      </head>
      <body>
    ';
  }
  
  function printFoot()
  {
    print '</body></html>';
    exit;
  }
  
  protected function nameForTestFile($file)
  {
    $name = pathinfo($file, PATHINFO_FILENAME);
    $name = preg_replace('/-with-buffer$/', '', $name);
    $name = str_replace('-', ' ', $name);
    $name = ucwords($name);
    
    return $name;
  }
}
