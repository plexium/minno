<?php

/*
 * Function: blog
 * Manages a blog on a site.
 * Usage: <minno:blog params="blogname" />
 */

function minno_blog( $params )
{   
   $html = '';
   $blog = empty($params['blog']) ? 'blog' : $params['blog'];
   
   if ( auth() ) 
   {
      if ( $_POST['blog'] )
      {
      
         $fn = preg_replace('/\s+/','-',preg_replace('/[^a-z0-9 ]/i','',$_POST['title'])) . '-' . date('Y-m-d') . '.html';
         //create blog post//
         $content = '<article><header><h1>' . $_POST['title'];
         $content .= '</h1><p>Published: <time pubdate="pubdate">'. date('Y-m-d') .'</time></p></header>';
         $content .= $_POST['blog'] . '</article>';
         file_out( $blog . DS .  $fn, $content );
      }
      
      $html .= form('<input type="text" name="title" placeholder="Title" id="title" /><br /><textarea id="blog" name="blog"></textarea><br />');
   }
   
   $html .= minno_inc(array('id' => $blog . '/*.html'));
   
   return $html;
  
/*
   if ( $_POST['submit'] == 'Upload' && $_POST['subdir'] == $subdir )
   {
      $subdir = validate_path( $subdir ) . '';
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
   
   $html .= form('<input type="hidden" name="subdir" value="'.$subdir.'" /><input type="file" name="upload"/>','Upload',null,'enctype="multipart/form-data"');
   return $html;
*/
}