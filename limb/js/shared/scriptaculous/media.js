function GetXMLHttpRequest()
{
  if ( window.XMLHttpRequest )		// Gecko
    return new XMLHttpRequest() ;
  else if ( window.ActiveXObject )	// IE
    return new ActiveXObject("MsXml2.XmlHttp") ;
}

function AddSelectOption( selectElement, optionText, optionValue )
{
  var oOption = document.createElement("OPTION") ;

  oOption.text	= optionText ;
  oOption.value	= optionValue ;

  selectElement.options.add(oOption) ;

  return oOption ;
}

var oConnector	= window.parent.oConnector ;
var oIcons		= window.parent.oIcons ;

var FCKXml = function(){}

FCKXml.prototype.GetHttpRequest = function()
{
  if ( window.XMLHttpRequest )		// Gecko
    return new XMLHttpRequest() ;
  else if ( window.ActiveXObject )	// IE
    return new ActiveXObject("MsXml2.XmlHttp") ;
}

FCKXml.prototype.LoadUrl = function( urlToCall, asyncFunctionPointer )
{
  var oFCKXml = this ;

  var bAsync = ( typeof(asyncFunctionPointer) == 'function' ) ;

  var oXmlHttp = this.GetHttpRequest() ;

  oXmlHttp.open( "GET", urlToCall, bAsync ) ;

  if ( bAsync )
  {
    oXmlHttp.onreadystatechange = function()
    {
      if ( oXmlHttp.readyState == 4 )
      {
        oFCKXml.DOMDocument = oXmlHttp.responseXML ;
        if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 )
          asyncFunctionPointer( oFCKXml ) ;
        else
          alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
      }
    }
  }

  oXmlHttp.send( null ) ;

  if ( ! bAsync )
  {
    if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 )
    {
      this.DOMDocument = oXmlHttp.responseXML ;
      return oFCKXml ;
    }
    else
    {
      alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
    }
  }
}

FCKXml.prototype.SelectNodes = function( xpath )
{
  if ( document.all )		// IE
    return this.DOMDocument.selectNodes( xpath ) ;
  else					// Gecko
  {
    var aNodeArray = new Array();

    var xPathResult = this.DOMDocument.evaluate( xpath, this.DOMDocument,
        this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), XPathResult.ORDERED_NODE_ITERATOR_TYPE, null) ;
    if ( xPathResult )
    {
      var oNode = xPathResult.iterateNext() ;
      while( oNode )
      {
        aNodeArray[aNodeArray.length] = oNode ;
        oNode = xPathResult.iterateNext();
      }
    }
    return aNodeArray ;
  }
}

FCKXml.prototype.SelectSingleNode = function( xpath )
{
  if ( document.all )		// IE
    return this.DOMDocument.selectSingleNode( xpath ) ;
  else					// Gecko
  {
    var xPathResult = this.DOMDocument.evaluate( xpath, this.DOMDocument,
        this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), 9, null);

    if ( xPathResult && xPathResult.singleNodeValue )
      return xPathResult.singleNodeValue ;
    else
      return null ;
  }
}
