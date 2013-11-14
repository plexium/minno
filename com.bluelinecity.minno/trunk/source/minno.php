<?php //minno v1.2 beta

session_start();

//convience define//
define('DS', DIRECTORY_SEPARATOR );

//if file doesn't match this then default to $index//
define('FILE_FILTER', '/^[a-z0-9_\/\-\*\\\\.]+(\.(?:html|css|js))?$/i' );

//default file to serve, Defaults to index.html//
$index = ( isset($index) ? $index : 'index.html' );

//location of the data store on the server defaults to data/ //
$store = ( isset($store) ? $store : 'data' . DS );

//url base for the site in case it's in a sub directory//
$base = ( isset($base) ? $base : '' );

//id of the file to serve//
$id = ( !preg_match( FILE_FILTER, $_GET['id'] ) ) ? $index : str_replace('..','',$_GET['id']);

//check for 'edit' flag which lets you edit the requested page//
$edit = isset($_GET['edit']);

//check for content posted//
$pagepost = $_POST['page'];

//put together the full web path//
$webpath =  $base . $id;

//in case of installation seed with this xml//
$installation = "<html>\n<head>\n<title></title>\n</head>\n<body>\n<minno:inc/>\n<minno:login/>\n<a href=\"?edit\">[edit]</a></body>\n</html>";

//process a login attempt, if username password match then mark as authed//
if ( $_POST['login'] == "{$user};{$pass}" ) _auth( true );

//process a logout attempt//
if ( $_POST['submit'] == "Logout" ) _auth( false );

//if page content has been posted and the user is authenticated, save the file//
if ( isset($_POST['page']) && _auth() ) _file_out( $id, stripslashes($pagepost) );

//include all the minno extentions found in the base directory minno.*.php//
foreach ( glob($functions . 'minno.*.php') as $php ) include_once( $php );

//figure out what to display first//
$out = 'core';

if ( !$edit && preg_match('/\.(css|js)$/',$id,$matches) ) 
{ 
   $out = $id;   
   $mime_types = array('css'=>'css','js'=>'javascript');
   header("Content-Type: text/". $mime_types[$mathces[1]] ."\n\n"); 
}     

echo inc($out);
exit(0);

//core function of minno//
function inc( $id = null )
{
   //prevent infinite recursion//
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

//processes any minno namespace tags//
function _mtag($matches)
{	
   list( $match, $func, $params ) = $matches;
   if ( function_exists( $func ) )
      return call_user_func_array( $func, ( empty($params) ? array() : explode(',', $params) ) );

   return '';
}

//reads a file $f from the data store//
function _file_in($f) 
{ 	
	static $c = array();
	if ( isset($c[$f]) ) return $c[$f];
   if ( $file = _validate_path( $f ) )
      $c[$f] = @file_get_contents( $file );
   return isset( $c[$f] ) ? $c[$f] : '';
}

//writes (or deletes) file $f using the data from $p//
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

//validate and clean up a path $f to prevent hack attempts, returns null on dirty paths//
function _validate_path($f)
{    
   if ( preg_match( FILE_FILTER , $f ) ){
      return preg_replace('/[\\/\\\]/',DS,$f);}
   return null;
}

// multi-function, sets authentication level of user or returns the state if called alone//
function _auth( $s = null )
{
   if ( $s === null ) return $_SESSION['auth'];
   if ( $s ) $_SESSION['auth'] = 1;
   else unset($_SESSION['auth']);	
}