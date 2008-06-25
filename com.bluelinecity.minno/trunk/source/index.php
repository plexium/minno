<?php
session_start();

require_once( 'config.php' );

$DB = mysql_connect($host,$user,$pass);
mysql_select_db( $database, $DB );

if ( isset($_POST['page']) )
{
    $id = (get_magic_quotes_gpc() ? $_GET['id'] : addslashes($_GET['id']) );
    $page = (get_magic_quotes_gpc() ? $_POST['page'] : addslashes($_POST['page']) );    
    if ( empty($page) )     
        $sql = "DELETE FROM {$table} WHERE id = '{$id}'";
    else
        $sql = "INSERT INTO {$table} VALUES ('{$id}','{$page}') ON DUPLICATE KEY UPDATE page = '{$page}'";

    mysql_query( $sql, $DB ) or die(mysql_error() . "<pre>" . h($sql) . "</pre>");
}

echo i('header',false); 
i();
echo i('footer',false);

function i( $s = null, $echo = true )
{
   global $DB, $table;
   static $stack = array();
    
   $s = ( $s === null ? ( empty($_GET['id']) ? 'index' : $_GET['id'] ) : $s );
   $s = (get_magic_quotes_gpc() ? $s : addslashes($s) );

   if ( !isset($stack[$s]) )
   {
      $stack[$s] = count($stack);
      $result = mysql_query( "SELECT * FROM {$table} WHERE id LIKE '{$s}'", $DB );
     
      if ( $result == null ) 
         mysql_query("CREATE TABLE {$table} (`id` VARCHAR( 255 ) NULL ,`page` TEXT NULL ,UNIQUE (`id`),FULLTEXT (`page`)) ENGINE = MYISAM;", $DB ) or die(mysql_error());
      
      ob_start();

      $row = mysql_fetch_array( $result );      
      do
          if ( ( isset($_GET['edit']) || !$row ) && $s == ($_GET['id'] || $s == 'index') && $echo == true && $stack[$s] == 0 )
              echo '<form action="'.h($_GET['id']).'?" method="post"><textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars($row['page']).'</textarea><input type="submit" value="'.($row?'update':'create').'"></form>';
          else
              eval( " ?>{$row['page']}<?php " );
      while ( $row = mysql_fetch_array( $result ) );

      unset( $stack[$s] );
        
      if ( $echo ) 
         echo ob_get_clean();
      else 
         return ob_get_clean();
   }
}

function h($s) { return htmlspecialchars($s); }

?>