<?php
/*
@name Shaarleed
@author Idleman <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence WTFPL
@version 3.0.1
@description Le plugin Shaarleed permet de partager un lien d'evenement directement sur son script <a target="_blank" href="http://sebsauvage.net/wiki/doku.php?id=php:shaarli">shaarli</a>
*/



function shaarleed_plugin_button(&$event){
	$configurationManager = new Configuration();
	$configurationManager->getAll();
	$shareOption = $configurationManager->get('plugin_shaarli_link');
	
	$requete = 'SELECT link, title FROM `'.MYSQL_PREFIX.'event` WHERE id = '.$event->getId();
	$query = mysql_query($requete);
	$result = mysql_fetch_row($query);
	$link = $result[0];
	$title = $result[1];
  
	echo '
	<a title="'._t('P_SHARLEED_SHARE_WITH_SHAARLI').'" target="_blank" href="'.$shareOption.'?post='.rawurlencode($link).'&title='.$title.'&amp;source=bookmarklet">'._t('P_SHARLEED_SHAARLI_EXCLAMATION').'</a>
	';
}

function shaarleed_plugin_setting_link(&$myUser){
	echo '<li><a class="toggle" href="#shaarli">'._t('P_SHARLEED_SHAARLI_EXCLAMATION').'</a></li>';
}

function shaarleed_plugin_setting_bloc(&$myUser){
	$configurationManager = new Configuration();
	$configurationManager->getAll();
	echo '
	<section id="shaarli" name="shaarli" class="shaarli">
		<form action="action.php?action=shaarleed_update" method="POST">
		<h2>'._t('P_SHARLEED_PLUGIN_TITLE').'</h2>
		<p class="shaarliBlock">
		<label for="plugin_shaarli_link">'._t('P_SHARLEED_SHAARLI_LINK').'</label> 
		<input style="width:50%;" type="text" placeholder="http://mon.domaine.com/shaarli/" value="'.$configurationManager->get('plugin_shaarli_link').'" id="plugin_shaarli_link" name="plugin_shaarli_link" />
		<input type="submit" class="button" value="'._t('P_SHARLEED_SAVE').'"><br/>
		</p>
		
		<strong>'._t('P_SHARLEED_NB').'</strong> '._t('P_SHARLEED_NB_INFO').'
		</form>
	</section>
	';
}

function shaarleed_plugin_update($_){
	if($_['action']=='shaarleed_update'){
        $configurationManager = new Configuration();
        $configurationManager->put('plugin_shaarli_link',$_['plugin_shaarli_link']);
		$_SESSION['configuration'] = null;

		header('location: settings.php');
	}
}

Plugin::addHook('event_post_top_options', 'shaarleed_plugin_button');  
Plugin::addHook('setting_post_link', 'shaarleed_plugin_setting_link');  
Plugin::addHook('setting_post_section', 'shaarleed_plugin_setting_bloc');  
Plugin::addHook("action_post_case", "shaarleed_plugin_update");  

?>
