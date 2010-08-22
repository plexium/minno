<?php

/*
 * Function: editarea
 * Turns the textarea into a nicer texteditor with syntax highlighting.
 * Usage: <minno:editarea /> in the <head> area
 */

function editarea()
{
   $path = _validate_path( $GLOBALS['id'] );
   if (!$path) return;

   if ( $GLOBALS['edit'] || !file_exists( $GLOBALS['store'] . $path ) )
   {
?>
<script language="javascript" type="text/javascript" src="/<?php echo $GLOBALS['base']; ?>functions/edit_area/edit_area_full.js"></script>
<script language="javascript" type="text/javascript">
editAreaLoader.init({
	id : "page"		        // textarea id
	,syntax: "css"			// syntax to be uses for highgliting
	,start_highlight: true		// to display with highlight mode on start-up
});
</script>
<?php
    }
}