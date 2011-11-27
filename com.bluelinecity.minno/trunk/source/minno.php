<?php //minno v1.2 beta

session_start();

define('DS', DIRECTORY_SEPARATOR );
define('FILE_FILTER', '/^[a-z0-9_\/\-\*\\\\.]+(\.(?:html|css|js))?$/i' );

$index = ( isset($index) ? $index : 'index.html' );
$store = ( isset($store) ? $store : 'data' . DS );
$base = ( isset($base) ? $base : '' );
$id = ( !preg_match( FILE_FILTER, $_GET['id'] ) ) ? $index : str_replace('..','',$_GET['id']);
$only = isset($_GET['only']);
$edit = isset($_GET['edit']);
$pagepost = $_POST['page'];
$webpath =  $base . $id;
$installation = "<html>\n<head>\n<title>Minno</title>\n</head>\n<body>\n<minno:inc/>\n<minno:login/>\n<a href=\"?edit\">[edit]</a></body>\n</html>";

if ( $_POST['login'] == "{$user};{$pass}" ) _auth( true );
if ( $_POST['submit'] == "Logout" ) _auth( false );

if ( isset($_POST['page']) && _auth() ) _file_out( $id, stripslashes($pagepost) );

foreach ( glob($functions . 'function.*.php') as $php ) include_once( $php );

if ( !$edit && preg_match('/\.css$/',$id) ) header("Content-Type: text/css\n\n");
echo ( $only ? inc( $id ) : inc('core') );

function inc( $id = null )
{
   static $_8 = array();
   
   $default = empty($id);
   $id = ( $default ? $GLOBALS['id'] : $id );
   if ( !($native = _validate_path($id)) ) return '';
   $files = glob( $GLOBALS['store'] . $native );
   
   //if index file doesn't exist, autologin
   if ( empty($files) && $id == $GLOBALS['index'] ) _auth( true ); 
	 
   ob_start();
   
   if ( _auth() && $default && $id == $GLOBALS['id'] && ( $GLOBALS['edit'] || empty($files) ) )
   {
      $page = _file_in( $files[0] );
      echo _form('<textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars( $page ).'</textarea>', (empty($page)?'Create':'Update') );
   }
   else if ( count($files) == 0 && $id == 'core')
   { 
      _file_out('core', $GLOBALS['installation'] ); 
      _auth(true);
      echo '<html><body>Minno Installed!<br /> Start your site by <a href="?id=core&edit">editing the core file</a>.</body></html>';
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

function _form( $innerhtml, $submit = "Submit", $action = null, $extra = '' )
{
   return '<form action="' . ($action?$action:'/'.$GLOBALS['webpath']) . '" method="post" '. $extra .'>'
          . $innerhtml . '<input type="submit" name="submit" value="'.$submit.'" /></form>';   
}

function _mtag($matches)
{	
   list( $match, $func, $params ) = $matches;
   if ( function_exists( $func ) )
      return call_user_func_array( $func, ( empty($params) ? array() : explode(',', $params) ) );

   return '';
}

function _file_in($f) 
{ 	
	static $c = array();
	if ( isset($c[$f]) ) return $c[$f];
   if ( $file = _validate_path( $f ) )
      $c[$f] = @file_get_contents( $file );
   return isset( $c[$f] ) ? $c[$f] : '';
}

function _file_out( $f, $p )
{ 
   if ( !($path = _validate_path($f)) ) return;

   $full = $GLOBALS['store'] . $path;
   $path = pathinfo( $full );
   @mkdir( $path['dirname'], 0777, true );

   if(empty($p))
         unlink($full);
   else 
   {
      $fp = fopen($full,'w');
      fwrite($fp,$p);
      fclose($fp);
   }
}


function _validate_path($f)
{    
   if ( preg_match( FILE_FILTER , $f ) ){
      return preg_replace('/[\\/\\\]/',DS,$f);}
   return null;
}

function _auth( $s = null )
{
   if ( $s === null ) return $_SESSION['auth'];
   if ( $s ) $_SESSION['auth'] = 1;
   else unset($_SESSION['auth']);	
}