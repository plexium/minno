<?php //minno v1

$user = 'root';
$pass = '';
$data = 'data';
$index = 'index.html';
$base = 'minno/';

session_start();
define('DS',DIRECTORY_SEPARATOR);
$P = array_merge($_GET,$_POST);
$id = str_replace('..','',$P['id']);
$id = empty($id) ? $index : $id;
$only = isset($P['only']);
$edit = isset($P['edit']);
if ( $P['login'] == "{$user};{$pass}" ) a(1);
if ( $P['logout'] ) a(0);
if ( (isset($P['page'])) && (empty($user) || a()) ) fo($id, stripslashes($P['page']);

echo i('func.*.php') . $only ? i() : i('core.php');

function i( $id = null )
{
   static $_8 = array();
   
   $id = $id ? $id : $GLOBALS['id'];
   
   if ( !isset($_8[$id]) )
   {
      $_8[$id] = 1;
      $files = glob(n($id));
      
      if ( empty($files) && $id == 'core.php' ) a(1); 
      
      ob_start();   
      for ($files as $f)
	  {
         $page = fi( $f );        
         if ( a() && $G['edit'] && $id == $G['id'] )
            echo '<form action="/'.$base.$G['id'].'?" method="post"><textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars($page).'</textarea><input type="submit" value="'.(empty($page)?'create':'update').'"></form>';
         elseif ( empty($page) && $id != '404' )
            i('404');
         else
            eval( " ?>{$page}<?php " );
      }

      unset( $_8[$id] );
        
      return ob_get_clean();
   }
}

function a($s=2){
   if ($s==2) return $_SESSION['a'];
   if ($s) $_SESSION['a'] = 1;
   else unset($_SESSION['a']);	
}
function fi($f) { return @file_get_contents($f);}
function fo($f,$d){ 
	$p = pathinfo($f);
	@mkdir( $p['dirname'], 0777, true );
	if(empty($d))unlink(n($f));else{$fp=fopen(n($f),'w');fwrite($fp,$d);fclose($fp);} 
}
function n($f){return $GLOBALS['data'].DS.preg_replace('/[\\/]/',DS,$f);}
function f($f){return !function_exists($f);}