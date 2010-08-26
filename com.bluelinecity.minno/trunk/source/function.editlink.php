<?php

/*
 * Function: editlink
 * Displays an edit link if the current user is logged in.
 * Usage: <minno:editlink params="link text" />
 */

function editlink( $text = '' )
{
   if ( !_auth() ) 
      return '';   
   else
      return '<a href="?edit">'. (empty($text)?'[Edit this page]':$text) .'</a>';
}