function openURL(url){
   window.open(url, '_blank');
   return false;
}

function social_toggle_div(main, id) {
  var div = document.getElementById(id);
  var maindiv = document.getElementById(main);
  
  if(div.style.display=="none") {
	div.style.left=maindiv.parentNode.offsetLeft+3+'px';
    div.style.display = "block";
    maindiv.innerHTML = _t('P_SOCIAL_SHARE_MOINS');
  } else {
    div.style.display = "none";
    maindiv.innerHTML = _t('P_SOCIAL_SHARE_PLUS');
  }
}


function OpenUrlWithPostParameters(action, key, url, title){

    var mapForm = document.createElement("form");
    mapForm.target = "Map";
    mapForm.method = "POST"; // or "post" if appropriate
    mapForm.action = action;

    var mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "key";
    mapInput.value = key;
    mapForm.appendChild(mapInput);

    document.body.appendChild(mapForm);

    var mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "url";
    mapInput.value = decodeURIComponent(url.replace(/\+/g, ' '));
    mapForm.appendChild(mapInput);

    document.body.appendChild(mapForm);

    var mapInput = document.createElement("input");
    mapInput.type = "text";
    mapInput.name = "title";
    mapInput.value = decodeURIComponent(title.replace(/\+/g, ' '));;
    mapForm.appendChild(mapInput);

    document.body.appendChild(mapForm);

    mapForm.submit();
}