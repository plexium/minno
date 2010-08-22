<?php

function editarea()
{
   if ( $GLOBALS['edit'] )
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