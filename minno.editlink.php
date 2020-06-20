<?php

/*
 * Function: editlink
 * Displays an edit link if the current user is logged in.
 * Usage: <minno:editlink text="link text" />
 */

function minno_editlink( $params )
{
   if ( auth() )
      return '<a class="editlink" href="?edit">'. (empty($params['text'])?'[Edit this page]':$params['text']) .'</a>';
   else
      return '';
}
