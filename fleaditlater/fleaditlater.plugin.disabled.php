<?php
/*
@name FleadItLater
@author Idleman <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa http://creativecommons.org/licenses/by-nc-sa/2.0/fr/
@version 1.1.4
@description Le plugin FleadItLater ajoute un bouton permettant de marquer un evenement comme "a lire plus tard" qui s'affichera dans un menu de droite.
*/

function fleaditlater_plugin_AddButton(&$event){
	$eventId = $event->getId();
	$count = mysql_query('SELECT COUNT(id) FROM `'.MYSQL_PREFIX.'plugin_feaditlater` WHERE event='.$eventId);
	$count = mysql_fetch_row($count);
	if(!$count[0]){
		echo '<button onclick="fleadItLater('.$eventId.',\'add\',this);">'._t('P_FLEADITLATER_READLATER').'</button>&nbsp;';
	}
}

function fleaditlater_plugin_displayEvents(&$myUser){
	$query = mysql_query('SELECT le.id,le.title,le.link FROM `'.MYSQL_PREFIX.'event` le INNER JOIN `'.MYSQL_PREFIX.'plugin_feaditlater` fil ON (le.id=fil.event)');
	if($query!=null){
	echo '<aside class="fleaditLaterMenu">
				
				<h3 class="left">'._t('P_FLEADITLATER_TOREAD').'</h3>
					<ul class="clear">  							  								  							  							  								  	
					<li>
						<ul> ';
							
							while($data = mysql_fetch_array($query)){
							echo '<li>
								
								<img src="plugins/fleaditlater/img/read_icon.png">
						
								<a title="'.$data['link'].'" href="'.$data['link'].'" target="_blank">
									'.Functions::truncate($data['title'],38).'
								</a>	  
								<button class="right" onclick="fleadItLater('.$data['id'].',\'delete\',this)" style="margin-left:5px;margin-top:5px;">
									<span title="'._t('P_FLEADITLATER_MARK_AS_READ').'" alt="'._t('P_FLEADITLATER_MARK_AS_READ').'">'._t('P_FLEADITLATER_MARK_AS_READ_SHORT').'</span>
								</button>
								</li>';
							}

						echo '</ul>
						
					</li>
				</ul>
			</aside>';
			}
}

function fleaditlater_plugin_action($_,$myUser){
	if ($_['action']=='fleadItLater') {
        if($myUser==false) exit(_t('P_FLEADITLATER_NOT_CONNECTED_ERROR'));
        if (isset($_['id'])){
            if(isset($_['state']) && $_['state']=='add'){
                $return = mysql_query('INSERT INTO `'.MYSQL_PREFIX.'plugin_feaditlater` (event)VALUES(\''.$_['id'].'\')');
            }else{
                $return = mysql_query('DELETE FROM `'.MYSQL_PREFIX.'plugin_feaditlater` WHERE event=\''.$_['id'].'\'');
            }
            if(!$return) echo mysql_error();
        }
    }
}

Plugin::addJs("/js/main.js"); 
// Ajout de la fonction au Hook situé dans les options d'évenements
Plugin::addHook("event_post_top_options", "fleaditlater_plugin_AddButton");  
//Ajout de la fonction au Hook situé après le menu des fluxs
Plugin::addHook("menu_post_folder_menu", "fleaditlater_plugin_displayEvents");  
//Ajout des actions fleadit
Plugin::addHook("action_post_case", "fleaditlater_plugin_action");  
?>
