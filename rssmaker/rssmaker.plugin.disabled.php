<?php
/*
@name Rss Maker
@author Idleman <idleman@idleman.fr>
@author Cobalt74 <cobalt74@gmail.com>
@link http://blog.idleman.fr
@licence DWTFYW
@version 2.1.0
@description Créé un flux rss par dossiers de flux, ceci permet de créer de nouveaux flux pour une consultation plus synthétique
*/


function rssmaker_plugin_folder_link(&$folder){
    echo '<a onclick="window.location=\'action.php?action=show_folder_rss&name='.$folder->getName().'&id='.$folder->getId().'\'" style="color:#cecece;font-size:10px;margin:5px 0 0 5px ;">Rss</a>';
}

function rssmaker_plugin_compare($a, $b) {
    return (strtotime($a->get_date())-strtotime($b->get_date()))*-1;
}

function rssmaker_plugin_compare_event($a, $b) {
    return (strtotime($a->getPubdate())-strtotime($b->getPubdate()))*-1;
}

function rssmaker_plugin_action($_,$myUser){

    if($_['action']=='show_folder_rss'){
        header('Content-Type: text/xml; charset=utf-8');
        $feedManager = new Feed();
        $feeds = $feedManager->loadAll(array('folder'=>$_['id']));
        $items = array();
        foreach ($feeds as $feed) {
            $parsing = new SimplePie();
            $parsing->set_feed_url($feed->getUrl());
            $parsing->init();
            $parsing->set_useragent('Mozilla/4.0 Leed (LightFeed Agregator) '.VERSION_NAME.' by idleman http://projet.idleman.fr/leed');
            $parsing->handle_content_type(); // UTF-8 par défaut pour SimplePie
            $items = array_merge($parsing->get_items(),$items);
        }

        $link = 'http://projet.idleman.fr/leed';

        echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
				<title>Leed dossier '.$_['name'].'</title>
				<atom:link href="'.$link.'" rel="self" type="application/rss+xml"/>
				<link>'.$link.'</link>
				<description>Aggrégation des flux du dossier leed '.$_['name'].'</description>
				<language>fr-fr</language>
				<copyright>DWTFYW</copyright>
				<pubDate>'.date('r', gmstrftime(time())) .'</pubDate>
				<lastBuildDate>'.date('r', gmstrftime(time())) .'</lastBuildDate>
				<sy:updatePeriod>hourly</sy:updatePeriod>
				<sy:updateFrequency>1</sy:updateFrequency>
				<generator>Leed (LightFeed Agregator) '.VERSION_NAME.'</generator>';

        usort($items, 'rssmaker_plugin_compare');

        foreach($items as $item){
            echo '<item>
				<title><![CDATA['.$item->get_title().']]></title>
				<link>'.$item->get_permalink().'</link>
				<pubDate>'.date('r', gmstrftime(strtotime($item->get_date()))).'</pubDate>
				<guid isPermaLink="true">'.$item->get_permalink().'</guid>

				<description>
				<![CDATA[
				'.$item->get_description().'
				]]>
				</description>
				<content:encoded><![CDATA['.$item->get_content().']]></content:encoded>

				<dc:creator>'.(''==$item->get_author()? 'Anonyme': $item->get_author()->name).'</dc:creator>
				</item>';

        }

        echo '</channel></rss>';
    }

}
function rssmaker_plugin_unread_action($_,$myUser){

    if ($_['action']=='show_unread_rss') {

        header('Content-Type: text/xml; charset=utf-8');
        $eventManager = new Event();
        $items = $eventManager->loadAll(array("unread"=>1));

        $ConfigManager = new Configuration();
        $ConfigManager->getAll();
        $link = $ConfigManager->get('root');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
				<title>Leed ('._t("UNREAD").')</title>
				<atom:link href="'.$link.'action.php?action=show_unread_rss" rel="self" type="application/rss+xml"/>
				<link>'.$link.'</link>
				<description>Aggrégation des flux non lus</description>
				<language>fr-fr</language>
				<copyright>DWTFYW</copyright>
				<pubDate>'.date('r', gmstrftime(time())) .'</pubDate>
				<lastBuildDate>'.date('r', gmstrftime(time())) .'</lastBuildDate>
				<sy:updatePeriod>hourly</sy:updatePeriod>
				<sy:updateFrequency>1</sy:updateFrequency>
				<generator>Leed (LightFeed Agregator) '.VERSION_NAME.'</generator>';

        usort($items, 'rssmaker_plugin_compare_event');
        foreach($items as $item){
           $xml .= '<item>
				<title><![CDATA['.html_entity_decode($item->getTitle()).']]></title>
				<link>'.$item->getLink().'</link>
				<pubDate>'.date('r', gmstrftime($item->getPubdate())).'</pubDate>
				<guid isPermaLink="true">'.$item->getLink().'</guid>

				<description>
				<![CDATA['.$item->getDescription().'
				]]>
				</description>

				<content:encoded><![CDATA['.$item->getDescription().']]></content:encoded>
				<dc:creator>'.(''==$item->getCreator()? 'Anonyme': $item->getCreator()).'</dc:creator>
				</item>';
        }
        $xml .= '</channel></rss>';
        echo($xml);
    }
}
Plugin::addHook("menu_pre_folder_link", "rssmaker_plugin_folder_link");
$ConfigManager = new Configuration();
$ConfigManager->getAll();
$link_head = $ConfigManager->get('root').'action.php?action=show_unread_rss';
Plugin::addLink("alternate",$link_head, "application/rss+xml", "Unread RSS" );
Plugin::addHook("action_post_case", "rssmaker_plugin_action");
Plugin::addHook("action_post_case", "rssmaker_plugin_unread_action");

$folderManager = new Folder();
$folders = $folderManager->loadAll(array());
foreach ($folders as $folder) {
    $link_head = $ConfigManager->get('root').'action.php?action=show_folder_rss&name='.$folder->getName().'&id='.$folder->getId();
    Plugin::addLink("alternate",$link_head, "application/rss+xml", $folder->getName() );
}

?>