<?php

/*
 * Function: breadcrumb
 * Prints out a linked breadcrumb trail
 * Usage: <minno:breadcrumb params="seperator,path|text,path,..." />
 */

function breadcrumb( )
{
   $args = func_get_args();
   $buffer = '';
   
   $sep = ( count($args) >= 1 ) ? array_shift($args) : '&gt;';

   //try and guess the path from url
   if ( count($args) == 0 )
   {      
      $id = $GLOBALS['id'];
      //$id = 'products/vaccums/dirtdevel.html';
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
      list( $link, $text ) = explode('|',$arg);
      $buffer .= ( empty($buffer)? '' : $sep );
      $text   = (empty($text) ? ucwords( preg_replace('/[\-\_]/',' ',basename($link,'.html')) ) : $text );
      $buffer .= ' <a href="/'.htmlspecialchars($link).'">'. htmlspecialchars($text) .'</a> ';
   }
    
   return $buffer;
}
