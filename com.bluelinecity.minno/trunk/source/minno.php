<?php /*minno v1*/ $user='root';$pass='';$data='data';$index='index.html';$base='minno/';session_start();define('DS',DIRECTORY_SEPARATOR);$G=$_GET;$P=$_POST;$G['id']=str_replace('..','',$G['id']);if(isset($P['page'])&&(empty($user)||$_SESSION['auth']))fo((empty($G['id'])?$index:$G['id']),stripslashes($P['page']));echo i('*.function').(isset($G['only'])?'':i('header.html')).i().(isset($G['only'])?'':i('footer.html'));function i($id=null){extract($GLOBALS);static $_8=array();$edit=($id===null);$id=($edit?(empty($G['id'])?$index:$G['id']):$id);if(!isset($_8[$id])){$_8[$id]=1;$files=glob(n($id));if(empty($files)&&$id==$index)$_SESSION['auth']=true;ob_start();for($c=0;$c<count($files)||$c==0;$c++){$page=fi(empty($files[$c])?n($id):$files[$c]);if((empty($user)||$_SESSION['auth'])&&(isset($G['edit'])||empty($page))&&($id==$G['id']||$id ==$index)&&$edit)echo '<form action="/'.$base.$G['id'].'?" method="post"><textarea cols="80" rows="20" id="page" name="page">'.htmlspecialchars($page).'</textarea><input type="submit" value="'.(empty($page)?'create':'update').'"></form>';elseif(empty($page)&&$id!='404')i('404');else eval(" ?>{$page}<?php ");}unset($_8[$id]);return ob_get_clean();}}function fi($f){return @file_get_contents($f);}function fo($f,$p){$d=explode('/',$f);for($c=0;$c<(count($d)-1);$c++)if(!file_exists(n(implode('/',array_slice($d,0,($c+1))))))mkdir(n(implode('/',array_slice($d,0,($c+1)))),0777);if(empty($p))unlink(n($f));else{$fp=fopen(n($f),'w');fwrite($fp,$p);fclose($fp);}}function n($f){return $GLOBALS['data'].DS.preg_replace('/[\\/]/',DS,$f);}function f($f){return !function_exists($f);}?>