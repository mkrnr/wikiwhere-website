function removeVariableFromURL(url_string, variable_name) {
  var URL = String(url_string);
  var regex = new RegExp( "\\?" + variable_name + "=[^&]*&?", "gi");
  URL = URL.replace(regex,'?');
  regex = new RegExp( "\\&" + variable_name + "=[^&]*&?", "gi");
  URL = URL.replace(regex,'&');
  URL = URL.replace(/(\?|&)$/,'');
  regex = null;
  return URL;
}
