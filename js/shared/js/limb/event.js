/**
 *@requires Limb
 */
Limb.namespace('Limb.Event');

Limb.Event.getEventKeyInfo = function(evt) {
  
  var ret = [];
             
  if (evt.keyCode) //on IE use keycode
    ret['code'] = evt.keyCode;
  else if (evt.which) //on mozilla use wich
    ret['code'] = evt.which;
           
  if (ret['code'] >= 65 && ret['code'] <= 90) //let's just use lower case codes
    ret['code'] = ret['code'] + 32;
           
  ret['ctrl'] = evt.ctrlKey; //is ctrl pressed
  ret['alt'] = evt.altKey; //is alt pressed
  ret['shift'] = evt.shiftKey;  //is shift pressed
       
  return ret;
}

/**
 *Example: Limb.Event.addEvent(window, 'load', init);
 *
 *@param string|object el The html element or id to assign the event handler to
 *@param string eventType The type of event to listen for
 *@param Function handler The handler function the event invokes   
 */
Limb.Event.addEvent = function(el, eventType, handler){
    if('string' === typeof(el))
    el = Limb.get(el);
     
    if(!el)
      return;
      
    if(el.addEventListener)el.addEventListener(eventType, handler, false);
    else if(el.attachEvent){
        el["e" + eventType + handler] = handler;
        el[eventType + handler] = function(){el["e" + eventType + handler](window.event)}
        el.attachEvent("on" + eventType, el[eventType + handler]);
    }
}

/**
 *Example: Limb.Event.addEvent(window, 'load', init);
 */
Limb.Event.removeEvent = function(el,evType,handle){
  if(el.removeEventListener)el.removeEventListener(evType, handle, false);
  else if(el.detachEvent){
    el.detachEvent("on" + evType, el[evType + handle])
    el[evType + handle] = null
    el["e" + evType + handle] = null;
  }
}

Limb.Event.preventDefault = function(event){
    var e = event ? event : window.event;
    if(e.preventDefault){
        e.preventDefault();
    }else{
        e.returnValue = false;
    }
}

Limb.Event.stopPropagation = function(event){    
    var e = event ? event : window.event;
    if(e.stopPropagation){
        e.stopPropagation();
    }else{
        e.cancelBubble = true;
    }    
}