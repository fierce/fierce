<?

namespace F;

class Migration
{
  function run()
  {
    throw new \exception('subclass must implement this');
  }
  
  function log($str)
  {
    print '<p>' . nl2br(htmlspecialchars($str)) . '</p>';
  }
}
