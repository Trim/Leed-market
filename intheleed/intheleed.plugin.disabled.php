<?php
/*
@name intheleed
@author GAULUPEAU Jonathan <jo.gaulupeau@gmail.com>
@link https://bitbucket.org/jogaulupeau
@licence GPLv3
@version 1.0.2
@description Le plugin intheleed permet de stocker un lien dans son <a target="_blank" href="http://inthepoche.com">poche</a>. Plugin basé sur un sharleed par Idleman.
*/



function intheleed_plugin_button(&$event){
	$configurationManager = new Configuration();
	$configurationManager->getAll();
	$shareOption = $configurationManager->get('plugin_poche_link');
	
	$requete = 'SELECT link, title FROM '.MYSQL_PREFIX.'event WHERE id = '.$event->getId();
	$query = mysql_query($requete);
	$result = mysql_fetch_row($query);
	$link = $result[0];
	$title = $result[1];
  
	echo '
	<a title="'._t('P_INTHELEED_SHARE_WITH_POCHE').'" target="_blank" href="'.$shareOption.'?action=add&url='.base64_encode($link).'">'._t('P_INTHELEED_POCHE_EXCLAMATION').'</a>
	';
}

function intheleed_plugin_setting_link(&$myUser){
	echo '
	<li class="pointer" onclick="$(\'#main section\').hide();$(\'#main #pocheBloc\').fadeToggle(200);">'._t('P_INTHELEED_POCHE').'</li>
	';
}

function intheleed_plugin_setting_bloc(&$myUser){
	$configurationManager = new Configuration();
	$configurationManager->getAll();
	echo '
	<section id="pocheBloc" style="display:none;">
		<form action="action.php?action=intheleed_update" method="POST">
		<h2>'._t('P_INTHELEED_PLUGIN_TITLE').'</h2>
		<p class="pocheBlock">
		<label for="plugin_poche_link">'._t('P_INTHELEED_POCHE_LINK').'</label> 
		<input style="width:50%;" type="text" placeholder="http://poche.mondomaine.com" value="'.$configurationManager->get('plugin_poche_link').'" id="plugin_poche_link" name="plugin_poche_link" />
		<input type="submit" class="button" value="'._t('P_INTHELEED_SAVE').'"><br/>
		</p>
		
		<strong>'._t('P_INTHELEED_NB').'</strong> '._t('P_INTHELEED_NB_INFO').'
		</form>
	</section>
	';
}

function intheleed_plugin_update($_){
	$myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
	if($myUser===false) exit(_t('P_INTHELEED_CONNECTION_ERROR'));
	
	if($_['action']=='intheleed_update'){
        $configurationManager = new Configuration();
        $configurationManager->put('plugin_poche_link',$_['plugin_poche_link']);
		$_SESSION['configuration'] = null;

		header('location: settings.php');
	}
}

Plugin::addHook('event_post_top_options', 'intheleed_plugin_button');  
Plugin::addHook('setting_post_link', 'intheleed_plugin_setting_link');  
Plugin::addHook('setting_post_section', 'intheleed_plugin_setting_bloc');  
Plugin::addHook("action_post_case", "intheleed_plugin_update");  

?>
