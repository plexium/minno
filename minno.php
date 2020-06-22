<?php //minno v1.2 beta

require_once('config.php');

session_start();

//convenience define//
define('DS', DIRECTORY_SEPARATOR );

//if file doesn't match this then default to $index//
define('FILE_FILTER', '/^[a-z0-9][a-z0-9_\/\-\*\\\\.]+(\.(?:html|css|js))?$/i' );

//default file to serve, Defaults to index.html//
$index = ( isset($index) ? $index : 'index.html' );

$appdir = dirname(__FILE__) . DS;

//location of the data store on the server defaults to data/ //
$store = ( isset($store) ? $store : $appdir . 'data' . DS );

//url base for the site in case it's in a sub directory//
$base = ( isset($base) ? $base : '' );

//id of the file to serve//
if ( isset($_GET['id']) && preg_match( FILE_FILTER, $_GET['id'] ) ){
  $id = str_replace( '..', '', $_GET['id'] );
} else {
  $id = $index;
}

//check for 'edit' flag which lets you edit the requested page//
$edit = isset($_GET['edit']);

//check for content posted//
if ( isset($_POST['page']) ){
  $pagepost = $_POST['page'];
}

//put together the full web path//
$webpath =  $base . $id;

//in case of installation seed with this xml//
$installation = "<html>\n<head>\n<title></title>\n</head>\n<body>\n<minno:inc/>\n<minno:login/>\n<a href=\"?edit\">[edit]</a></body>\n</html>";

//process a login attempt, if username password match then mark as authed//
if ( isset($_POST['login']) && password_verify($_POST['login'],$pass)) auth( true );

//process a logout attempt//
if ( isset($_POST['submit']) && $_POST['submit'] == "Logout" ) auth( false );

//if page content has been posted and the user is authenticated, save the file//
if ( isset($_POST['page']) && auth() ) file_out( $id, stripslashes($pagepost) );

//include all the minno extentions found in the base directory minno.*.php//
foreach ( glob($appdir . 'minno.*.php') as $php ) include_once( $php );

//figure out what to display first//
$out = 'core';

if ( !$edit && preg_match('/\.(css|js)$/',$id,$matches) )
{
   $out = $id;
   $mime_types = ['css'=>'css','js'=>'javascript'];
   header("Content-Type: text/". $mime_types[$matches[1]] ."\n\n");
}

//recursion detection//
$_8 = [];

echo minno_inc(['id' => $out]);
exit(0);

//core function of minno, params: id, rsort, limit//
function minno_inc( $params = [] )
{
   extract($params);

   //prevent infinite recursion//
   global $_8;

   //flag for default view//
   $default = empty($id);
   $id = ( $default ? $GLOBALS['id'] : $id );
   if ( !($native = validate_path($id)) ) return '';
   $files = glob( $GLOBALS['store'] . $native );

   foreach ($files as $i => $file ){
     $files[$i] = substr($file,strlen($GLOBALS['store']));
   }

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
      $page = isset($files[0]) ? file_in( $files[0] ) : '';
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
      echo ( $id == '404' ? 'File Not Found' : minno_inc(['id'=>'404']) );
   }
   //else load and echo the file(s)//
   else
   {
      foreach ( $files as $f )
      {
         if ( !in_array($f,$_8) )
          {
            array_push( $_8, $f );
            echo preg_replace_callback('/\<minno\:([a-z0-9][a-z0-9_]*)\s*(.*?)\/?\>/i','mtag', file_in( $f ) );
            array_pop( $_8 );
         }
      }

      //if pageinated//
      if ( isset($count) && isset($limit) && $count > $limit )
      {
         echo '<div class="paginate">';
         if ( $page != 1 )
            echo '<a href="?page='. ($page-1) .'" class="pageinate__previous">Previous</a> ';

         for ( $i = 1; $i < ceil($count/$limit)+1; $i++ )
            if ( $i == $page )
               echo '<span class="pageinate__current">'.$i.'</span>';
            else
               echo '<a class="pageinate__index" href="?page='.$i.'">'.$i.'</a>';

         if ( ceil($count / $limit) != $page )
            echo ' <a href="?page='. ($page+1) .'" class="pageinate__next">Next</a>';
         echo '</div>';
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
      return form('<input type="password" id="login" name="login">','Login');
}

//return an html form with $innerhtml, submit button name, action method, and any extra attrs//
function form( $innerhtml, $submit = "Submit", $action = null, $extra = '' )
{
   return '<form action="' . ($action?$action:'/'.$GLOBALS['webpath']) . '" method="post" '. $extra .'>'
          . $innerhtml . '<input type="submit" name="submit" value="'.$submit.'"></form>';
}

//processes any minno namespace tags//
function mtag($info)
{
   preg_match_all( '/(\w+)\=(\"|\')(.*?)\2/i', $info[2], $matches, PREG_SET_ORDER );

   $attrs = [];
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
  static $c = [];

	if ( isset($c[$f]) ) return $c[$f];
  $c[$f] = @file_get_contents($GLOBALS['store'] . $f);
  return isset( $c[$f] ) ? $c[$f] : '';
}

//writes (or deletes) file $f using the data from $p//
function file_out( $f, $p )
{
   $full = $GLOBALS['store'] . $f;
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
   if ( $s === null ) return (isset($_SESSION['auth']) ? $_SESSION['auth'] : false);
   if ( $s ) $_SESSION['auth'] = true;
   else unset($_SESSION['auth']);
}
