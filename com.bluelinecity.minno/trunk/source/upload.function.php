<?php

/*
 * Function: upload
 * Handles file uploads to authenticated users. Defaults to 'uploads/' but an be changed in index.php
 * by setting $uploads = 'dirname/'
 * Usage: <minno:upload params="uploaddir" />
 */

function upload( $subdir = '' )
{
   if ( !_auth() ) return 'Login to upload';
   
   $html = '';
   $error_types = array(
      1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
           'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
           'The uploaded file was only partially uploaded.',
           'No file was uploaded.',
      6 => 'Missing a temporary folder.',
           'Failed to write file to disk.',
           'A PHP extension stopped the file upload.'
   );



   if ( $_POST['submit'] == 'Upload' )
   {
      $subdir = _validate_path( $subdir ) . '';
      $upload_dir = (($GLOBALS['uploads']) ? $GLOBALS['uploads'] : 'uploads' . DS ) . $subdir;

      @mkdir( $upload_dir, 0777, true );

      if ( $_FILES['upload']['error'] == UPLOAD_ERR_OK )
      {
         if ( is_uploaded_file( $_FILES['upload']['tmp_name'] ) )
	    move_uploaded_file( $_FILES['upload']['tmp_name'], $upload_dir . DS . $_FILES['upload']['name'] );
         $html .= '<a href="'. $GLOBALS['base'] . $upload_dir . '/' . $_FILES['upload']['name'] .'">' . $_FILES['upload']['name'] . '</a>';
         $html .= ' Successfully Uploaded!';
      }
      else
         $html .= 'Error uploading file: ' . $error_types[$_FILES['upload']['error']]; 
   }
   
   $html .= _form('<input type="file" name="upload"/>','Upload',null,'enctype="multipart/form-data"');
   return $html;
}