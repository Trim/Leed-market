<?php
/*
@name Open Anon
@author Olivier <http://j.cybride.net/olb>
@link http://j.cybride.net/olb
@licence CC by nc sa http://creativecommons.org/licenses/by-nc-sa/2.0/fr/
@version 1.0.0
@description Used to open a link in anonymous way
@note Usage of JS Lib NoReferef (https://github.com/knu/noreferrer)
*/

function openanon_plugin_button(&$event){
	$requete = 'SELECT link FROM '.MYSQL_PREFIX.'event WHERE id = '.$event->getId();
	$query = mysql_query($requete);
	$result = mysql_fetch_row($query);
	$link = $result[0];
	echo '<a title="'._t('P_OPENANON_TITLE').'" target="_blank" href="'.$link.'" rel="noreferrer">'._t('P_OPENANON_LINKNAME').'</a> ';
}

Plugin::addHook("event_post_top_options", "openanon_plugin_button");  
Plugin::addJs("/js/jquery.noreferrer.js");
