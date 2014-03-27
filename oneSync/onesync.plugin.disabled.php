<?php
/*
@name OneSync
@author Idleman <idleman@idleman.fr>
@author Cobalt74 <http://www.cobestran.com>
@link http://blog.idleman.fr
@licence CC by nc sa http://creativecommons.org/licenses/by-nc-sa/2.0/fr/
@version 1.3.0
@description Le plugin OneSync ajoute un bouton à coté de chaque flux afin de synchroniser uniquement ce flux
*/

function OneSync_plugin_AddButton(&$feed){
	echo '<span class="pointer onsyncButton" onclick="onesync_validate(\''.$feed['id'].'\');" alt="Synchroniser" title="Synchroniser">↺</span> ';
}

function FolderSync_plugin_AddButton(&$folder){
    echo '<span class="pointer onsyncFolderButton" onclick="foldersync_validate(\''.$folder->getId().'\');" alt="Synchroniser le dossier" title="Synchroniser le dossier">↺</span> ';
}

function OneSync_plugin_SynchronyzeOne(&$_){
	if ($_['action']=='syncronyzeOne'){
		$myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
		if($myUser==false) exit('Vous devez vous connecter pour cette action.');
			if(isset($_['feed']) && $_['feed']!=''){
				$feedManager = new Feed();
				$feed = $feedManager->getById($_['feed']);
				$syncId = time();
				$feed->parse($syncId);
			}
			header('location: ./index.php');
		}
	}

function OneSync_plugin_syncronyzeFolder(&$_){
    if ($_['action']=='syncronyzeFolder'){
        $myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
        if($myUser==false) exit('Vous devez vous connecter pour cette action.');
        if(isset($_['folder']) && $_['folder']!=''){
            error_log('zzz');
            $feedManager = new Feed();
            $feeds = $feedManager->loadAll(array('folder'=>$_['folder']));
            $syncId = time();
            foreach ($feeds as $feed){
                $feed->parse($syncId);
            }
        }
        header('location: ./index.php');
    }
}

Plugin::addJs("/js/main.js"); 
// Ajout de la fonction au Hook situé avant l'affichage des liens de flux
Plugin::addHook("menu_post_feed_link", "OneSync_plugin_AddButton");  
Plugin::addHook("action_post_case", "OneSync_plugin_SynchronyzeOne");
Plugin::addHook("menu_post_folder_link", "FolderSync_plugin_AddButton");
Plugin::addHook("action_post_case", "OneSync_plugin_syncronyzeFolder");
?>