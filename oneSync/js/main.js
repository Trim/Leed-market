
function onesync_validate(feed){
	if(confirm('Etes vous sûr de vouloir synchroniser uniquement ce flux?'))window.location='action.php?action=syncronyzeOne&feed='+feed;
}

function foldersync_validate(folder){
    if(confirm('Etes vous sûr de vouloir synchroniser ce dossier?'))window.location='action.php?action=syncronyzeFolder&folder='+folder;
}