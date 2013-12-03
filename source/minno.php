<?php //minno v1.2 beta

session_start();

//convience define//
define('DS', DIRECTORY_SEPARATOR );

//if file doesn't match this then default to $index//
define('FILE_FILTER', '/^[a-z0-9][a-z0-9_\/\-\*\\\\.]+(\.(?:html|css|js))?$/i' );

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
if ( $_POST['login'] == "{$user};{$pass}" ) auth( true );

//process a logout attempt//
if ( $_POST['submit'] == "Logout" ) auth( false );

//if page content has been posted and the user is authenticated, save the file//
if ( isset($_POST['page']) && auth() ) file_out( $id, stripslashes($pagepost) );

//include all the minno extentions found in the base directory minno.*.php//
foreach ( glob($functions . 'minno.*.php') as $php ) include_once( $php );

//figure out what to display first//
$out = 'core';

if ( !$edit && preg_match('/\.(css|js)$/',$id,$matches) ) 
{ 
   $out = $id;   
   $mime_types = array('css'=>'css','js'=>'javascript');
   //header("Content-Type: text/". $mime_types[$mathces[1]] ."\n\n"); 
}     

echo minno_inc(array('id' => $out));
exit(0);

//core function of minno, params: id, rsort, limit//
function minno_inc( $params = array() )
{
   extract($params);
   
   //prevent infinite recursion//
   static $_8 = array();
   
   //flag for default view//
   $default = empty($id);
   $id = ( $default ? $GLOBALS['id'] : $id );
   if ( !($native = validate_path($id)) ) return '';
   $files = glob( $GLOBALS['store'] . $native );
   
   if ( !empty($rsort) ) rsort($files);
   
   if ( !empty($limit) )
   {
      $count = count($files);
      $page = empty($_GET['page']) ? 1 : intval($_GET['page']);
      $offset = ($page-1) * intval($limit);
      $files = array_slice( $files, $offset, $limit );
   }
   
   //if index file doesn't exist, autologin
   if ( empty($files) && $id == $GLOBALS['index'] ) auth( true ); 
	 
   ob_start();
   
   //if authed and is the inc in the core and id == requested id and asking to update or create//
   if ( auth() && $default && $id == $GLOBALS['id'] && ( $GLOBALS['edit'] || empty($files) ) )
   {
      $page = file_in( $files[0] );
      echo form('<textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars( $page ).'</textarea>', (empty($page)?'Create':'Update') );
   }
   //else if core doesn't exist, install minno//
   else if ( count($files) == 0 && $id == 'core')
   { 
      file_out('core', $GLOBALS['installation'] ); 
      auth(true);
      echo 'Start your site by <a href="?id=core&edit">editing the core file</a>.';
   }
   //else if no file 404//
   else if ( count($files) == 0 )
   {
      echo ( $id == '404' ? 'File Not Found!' : minno_inc(array('id'=>'404')) );
   }
   //else load and echo the file(s)//
   else
   {
      foreach ( $files as $f )
      {
         if ( !isset($_8[$f]) )
          {		
            $_8[$f] = 1;
            echo preg_replace_callback('/\<minno\:([a-z0-9][a-z0-9_]*)\s*(.*?)\/?\>/i',"mtag", file_in( $f ) );	
            unset( $_8[$f] );
         }
      }
      
      //if pageinated//
      if ( $count > $limit )
      {
         if ( $page != 1 ) 
            echo '<a href="?page='. ($page-1) .'">&lt;&lt; Previous</a>';
            
         if ( ceil($count / $limit) != $page ) 
            echo '<a href="?page='. ($page+1) .'">Next &gt;&gt;</a>';
      }
	}
   
   return ob_get_clean();
}

//return a login form based on users auth status//
function minno_login()
{
   if ( auth() )
      return form('Click to ','Logout');
   else
      return form('<input type="password" id="login" name="login"/>','Login');
}

//return an html form with $innerhtml, submit button name, action method, and any extra attrs//
function form( $innerhtml, $submit = "Submit", $action = null, $extra = '' )
{
   return '<form action="' . ($action?$action:'/'.$GLOBALS['webpath']) . '" method="post" '. $extra .'>'
          . $innerhtml . '<input type="submit" name="submit" value="'.$submit.'" /></form>';   
}

//processes any minno namespace tags//
function mtag($info)
{
   preg_match_all( '/(\w+)\=(\"|\')(.*?)\2/i', $info[2], $matches, PREG_SET_ORDER );

   $attrs = array();
   foreach ( $matches as $match )
   {
      $attrs[$match[1]] = $match[3];
   }
   
   $func = 'minno_' . $info[1];
   if ( function_exists( $func ) ) 
      return call_user_func( $func, $attrs );
   else
      return '';
}

//reads a file $f from the data store//
function file_in($f) 
{ 	
	static $c = array();
	if ( isset($c[$f]) ) return $c[$f];
   if ( $file = validate_path( $f ) )
      $c[$f] = @file_get_contents( $file );
   return isset( $c[$f] ) ? $c[$f] : '';
}

//writes (or deletes) file $f using the data from $p//
function file_out( $f, $p )
{ 
   if ( !($path = validate_path($f)) ) return;

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
function validate_path($f)
{    
   if ( preg_match( FILE_FILTER , $f ) ){
      return preg_replace('/[\\/\\\]/',DS,$f);}
   return null;
}

// multi-function, sets authentication level of user or returns the state if called alone//
function auth( $s = null )
{
   if ( $s === null ) return $_SESSION['auth'];
   if ( $s ) $_SESSION['auth'] = 1;
   else unset($_SESSION['auth']);	
}