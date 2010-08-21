<?php //minno v1.1
$user = 'root';
$pass = '';
$data = 'data';
$index = 'index.html';
$base = '';

session_start();

define('DS',DIRECTORY_SEPARATOR);

$id = empty( $_GET['id'] ) ? $index : str_replace('..','',$_GET['id']);
$only = isset($_GET['only']);
$edit = isset($_GET['edit']);
$page = $_POST['page'];

if ( $_POST['login'] == "{$user};{$pass}" ) _auth( true );
if ( $_GET['logout'] )             _auth( false );

if ( isset($_POST['page']) && _auth() ) _file_out( $id, stripslashes($page) );

foreach ( glob('*.function.php') as $php ) include_once( $php );
echo ( $only ? inc( $id ) : inc('core.html') );

function inc( $id = null )
{
   static $_8 = array();
     
   $files = glob( _translate_path($id) );
	
   //if index file doesn't exist, autologin
   if ( empty($files) && $id == $GLOBALS['index'] ) _auth( true ); 
	 
   ob_start();
   
   if ( _auth()
        && ( $GLOBALS['edit'] || empty($files) ) 
        && ( $id == $GLOBALS['id'] || $id == $GLOBALS['index']))
   {
	  $page = _file_in( $files[0] );
      echo '<form action="/'.$GLOBALS['base'].$_GET['id'].'?" method="post"><textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars( $page ).'</textarea><input type="submit" value="'.(empty($page)?'create':'update').'"></form>';
   }
   else
      foreach ( $files as $file )
      {
	     if ( !isset($_8[$file]) )
	     {
	        $_8[$file] = _file_in( empty($file) ? _translate_path($id) : $file );
        
	        if ( empty( $_8[$file] ) && $id != '404' )
	           inc('404');
	        else
	           echo preg_replace_callback('/\<minno\:([a-z0-9][a-z0-9_]*)\s*(params\=\"([^\"]*)\")?.*?\/\>/i',"_mtag", $_8[$file] );

	  	    unset( $_8[$file] );
         }
      }	
	
   return ob_get_clean();
}


function _mtag($matches)
{	
	list( $match, $func, $params ) = $matches;
	if ( function_exists( $func ) )
	{
		$params = ( empty( $params ) ? $GLOBALS['id'] : $params );
		return call_user_func_array( $func, explode(',', $params) );
	}
	else
		return '';
}


function _file_in($f) 
{ 
	return @file_get_contents($f);
}


function _file_out( $f, $p )
{ 
   $d = explode('/',$f);  
   for( $c=0; $c<(count($d)-1); $c++ )
      if(!file_exists(_translate_path(implode('/',array_slice($d,0,($c+1))))))
         mkdir(_translate_path(implode('/',array_slice($d,0,($c+1)))),0777);   
   
   if(empty($p))
      unlink(_translate_path($f));
   else 
   {
      $fp = fopen(_translate_path($f),'w');
      fwrite($fp,$p);
      fclose($fp);
   } 
}


function _translate_path($f)
{
	return $GLOBALS['data'].DS.preg_replace('/[\\/]/',DS,$f);
}

function _fe($f){
	return !function_exists($f);
}


function _auth( $state = null )
{
	if ( $state === null ) return $_SESSION['auth'];
	if ( $state ) $_SESSION['auth'] = 1;
	if ( !$state) unset($_SESSION['auth']);	
}