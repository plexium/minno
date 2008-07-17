<?php
session_start();

define('DS',DIRECTORY_SEPARATOR);
require_once( 'config.php' );

if ( isset($_POST['page'])&& $_SESSION['auth'] )
    fo((empty($_GET['id']) ? 'index.html': str_replace('..','',$_GET['id'])), stripslashes($_POST['page']));

ob_start();
if(!isset($_GET['only']))echo i('header.part',false);  
i(); 
if(!isset($_GET['only']))echo i('footer.part',false);  
echo ob_get_clean();

function i( $s = null, $echo = true )
{
   global $DB, $data, $index;
   static $stack = array();
    
   $s = ( $s === null ? ( empty($_GET['id']) ? 'index.html' : $_GET['id'] ) : $s );
   $s = str_replace('..','',$s);

   if ( !isset($stack[$s]) )
   {
      $stack[$s] = count($stack);
      $files = glob(n($s));
      
      if ( empty($files) && $s == 'index.html' ) $_SESSION['auth']=true; 
         
      ob_start();
      
      for ($c=0;$c<count($files)||$c==0;$c++){
         $page = fi( empty($files[$c]) ? n($s) : $files[$c] );         
         if ( $_SESSION['auth'] && ( isset($_GET['edit']) || empty($page) ) && ($s == $_GET['id'] || $s == 'index.html') && $echo == true && $stack[$s] == 0 )
            echo '<form action="'.dirname($_SERVER['SCRIPT_NAME']).'/'.h($_GET['id']).'?" method="post"><textarea cols="80" rows="20" id="page" name="page">'.h($page).'</textarea><input type="submit" value="'.(empty($page)?'create':'update').'"></form>';
         elseif ( empty($page) && $s != '404' )
            i('404');
         else
            eval( " ?>{$page}<?php " );
      }

      unset( $stack[$s] );
        
      if ( $echo ) 
         echo ob_get_clean();
      else 
         return ob_get_clean();
   }
}

function h($s) { return htmlspecialchars($s); }
function fi($f) { return @file_get_contents($f);}
function fo($f,$p){ 
   $d = explode('/',$f);   
   for($c=0;$c<(count($d)-1);$c++)
      if(!file_exists(n(implode('/',array_slice($d,0,($c+1))))))
         mkdir(n(implode('/',array_slice($d,0,($c+1)))),0777);   
   if(empty($p))unlink(n($f));else{$fp=fopen(n($f),'w');fwrite($fp,$p);fclose($fp);} 
   }
function n($f){return $GLOBALS['data'].DS.preg_replace('/[\\/]/',DS,$f);}
?>