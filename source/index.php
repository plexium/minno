<?php //minno v1.1

require_once('config.php');

session_start();

define('DS', DIRECTORY_SEPARATOR );
define('FILE_FILTER', '/^[a-z0-9_\/\-\*]*(\.html)?$/i' );

$id = ( empty( $_GET['id'] ) || !preg_match('/^[a-z0-9_\/\-]*(\.html)?$/i',$_GET['id']) ) ? $index : str_replace('..','',$_GET['id']);
$only = isset($_GET['only']);
$edit = isset($_GET['edit']);
$pagepost = $_POST['page'];
$store = $store . DS;
$webpath =  $base . $id;

if ( $_POST['login'] == "{$user};{$pass}" ) _auth( true );
if ( $_POST['submit'] == "Logout" ) _auth( false );

if ( isset($_POST['page']) && _auth() ) _file_out( $id, stripslashes($pagepost) );

foreach ( glob('*.function.php') as $php ) include_once( $php );
echo ( $only ? inc( $id ) : inc('core.html') );

function inc( $id = null )
{
   static $_8 = array();

   $id = ( empty($id) ? $GLOBALS['id'] : $id );
   if ( !($native = _validate_path($id)) ) return '';
   $files = glob( $GLOBALS['store'] . $native );
    
   //if index file doesn't exist, autologin
   if ( empty($files) && $id == $GLOBALS['index'] ) _auth( true ); 
	 
   ob_start();
   
   if ( _auth() && $id == $GLOBALS['id'] && ( $GLOBALS['edit'] || empty($files) ) )
   {
      $page = _file_in( $files[0] );
      echo _form('<textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars( $page ).'</textarea>', (empty($page)?'Create':'Update') );
   }
   else if ( count($files) == 0 )
      echo ( $id == '404' ? 'File Not Found!' : inc('404') );
   else
      foreach ( $files as $f )
      {
         if ( !isset($_8[$f]) )
	 {
	    $_8[$f] = 1;
	    echo preg_replace_callback('/\<minno\:([a-z0-9][a-z0-9_]*)\s*(?:params\=\"([^\"]*)\")?.*?\/?\>/i',"_mtag", _file_in( $f ) );	
	    unset( $_8[$f] );
         }
      }	
	
   return ob_get_clean();
}


function login()
{
   if ( _auth() )
      return _form('Click to ','Logout');
   else
      return _form('<input type="password" name="login"/>','Login');
}

function _form( $innerhtml, $submit = "Submit", $action = null )
{
   return '<form action="' . ($action?$action:'/'.$GLOBALS['webpath'].'?') . '" method="post">'
          . $innerhtml . '<input type="submit" name="submit" value="'.$submit.'" /></form>';   
}

function _mtag($matches)
{	
   list( $match, $func, $params ) = $matches;
   if ( function_exists( $func ) )
      return call_user_func_array( $func, explode(',', $params) );

   return '';
}

function _file_in($f) 
{ 
   if ( $file = _validate_path( $f ) )
      return @file_get_contents( $file);
   return '';
}

function _file_out( $f, $p )
{ 
   $d = explode('/',$f);  
   for( $c=0; $c<(count($d)-1); $c++ )
   {
      $path = $GLOBALS['store'] . _validate_path( implode('/', array_slice($d,0,($c+1))) );
      if( $path && !file_exists($path) )
         mkdir($path,0777);   
   }

   if ( $file = _validate_path($f) )
   {
      $file = $GLOBALS['store'] . $file;

      if(empty($p))
         unlink($file);
      else 
      {
         $fp = fopen($file,'w');
         fwrite($fp,$p);
         fclose($fp);
      }
   } 
}

function _validate_path($f)
{
   if ( preg_match( FILE_FILTER , $f ) )
   	return preg_replace('/[\\/]/',DS,$f);
   return null;
}

function _auth( $s = null )
{
   if ( $s === null ) return $_SESSION['auth'];
   if ( $s ) $_SESSION['auth'] = 1;
   else unset($_SESSION['auth']);	
}	