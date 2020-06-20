<?php

/*
 * Function: breadcrumb
 * Prints out a linked breadcrumb trail
 * Usage: <minno:breadcrumb separator="&gt;" crumbs="index.html|Home,blah|blah.html" />
 */

function minno_breadcrumb( $params )
{
   $buffer = '';

   $sep = empty($params['separator']) ? '&gt;' : $params['separator'];
   $args = empty($params['crumbs']) ? array() : explode(',',$params['crumbs']);

   //try and guess the path from url
   if ( count($args) == 0 )
   {
      $id = $GLOBALS['id'];
      $parts = explode('/', $id );

      $args[] = 'index.html|Home';

      foreach ( $parts as $index => $part )
      {
         if ( $part != basename($id) )
         {
            $args[] = implode('/', array_slice($parts, 0, $index+1 ) ) . '/index.html|' .
            ucwords( preg_replace('/[\-\_]/',' ',$part));
         }
         else if ( $part != 'index.html' )
         {
            $args[] = $id;
         }
      }
   }

   foreach ( $args as $arg )
   {
      list( $link, $text ) = array_pad(explode( '|', $arg ),2,null);
      $buffer .= ( empty($buffer)? '' : $sep );
      $text   = (empty($text) ? ucwords( preg_replace('/[\-\_]/',' ',basename($link,'.html')) ) : $text );
      $buffer .= ' <a href="/'.htmlspecialchars($link).'">'. htmlspecialchars($text) .'</a> ';
   }

   return $buffer;
}
