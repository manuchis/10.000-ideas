/* 20 Aug 2006: This started off as wordbooker_actb.js from
http://www.codeproject.com/jscript/jswordbooker_actb.asp.

But it didn't work without the addEvent stuff that was in the demo page.  Web
browsing suggests that this widely distributed stuff is from another source.
There is a warning about it at quirksmode, but I think I should be immune from
it.  So that was added at the top.

Then I hacked in a hook to do server lookups.  This is the code that uses my
new wordbooker_actb_lastdownload variable.

Then I wrote the download code.  This is a combination of the demo at
http://www.w3schools.com/xml/tryit.asp?filename=try_xmlhttprequest_js1
(although modified to move the state change function into a closure) and the
"explode" function from http://textsnippets.com/

This makes it a huge bag of code with mixed copyrights.  I'm comfortable
saying that the creative commons license that applies to the original wordbooker_actb
code can be taken as applying to this derivative work, but if you wanted to
use this in something that costs real money, you'd better check with your
lawyers

1 Sept 2006 - I noticed a mountain of errors from the Firefox CSS Parser.
Changing the "=" to ":" in wordbooker_actb_hStyle prevented this, and made the
highlighted part bold as well!

1 Sept 2006 - added a default of "sans-serif" to the "arial narrow" font
family, for those of us who don't have arial.

4 Nov 2006 - made "tab" leave the box without selection: much more how I'd
like it to work

August 2008 - changed the format to work better with names coming from SQL

April 2009 - fixed (in a messy way) a bug where the top element didn't
show in some browsers

June 2009 - Added UTF8 accented character folding - so each entry has
a "disp"lay and a "match" version
*/

function addEvent(obj,event_name,func_name){
  if (obj.attachEvent){
    obj.attachEvent("on"+event_name, func_name);
  }else if(obj.addEventListener){
    obj.addEventListener(event_name,func_name,true);
  }else{
    obj["on"+event_name] = func_name;
  }
}

function removeEvent(obj,event_name,func_name){
  if (obj.detachEvent){
    obj.detachEvent("on"+event_name,func_name);
  }else if(obj.removeEventListener){
    obj.removeEventListener(event_name,func_name,true);
  }else{
    obj["on"+event_name] = null;
  }
}
function stopEvent(evt){
  evt || window.event;
  if (evt.stopPropagation){
    evt.stopPropagation();
    evt.preventDefault();
  }else if(typeof evt.cancelBubble != "undefined"){
    evt.cancelBubble = true;
    evt.returnValue = false;
  }
  return false;
}
function getElement(evt){
  if (window.event){
    return window.event.srcElement;
  }else{
    return evt.currentTarget;
  }
}
function getTargetElement(evt){
  if (window.event){
    return window.event.srcElement;
  }else{
    return evt.target;
  }
}
function stopSelect(obj){
  if (typeof obj.onselectstart != 'undefined'){
    addEvent(obj,"selectstart",function(){ return false;});
  }
}
function getCaretEnd(obj){
  if(typeof obj.selectionEnd != "undefined"){
    return obj.selectionEnd;
  }else if(document.selection&&document.selection.createRange){
    var M=document.selection.createRange();
    try{
      var Lp = M.duplicate();
      Lp.moveToElementText(obj);
    }catch(e){
      var Lp=obj.createTextRange();
    }
    Lp.setEndPoint("EndToEnd",M);
    var rb=Lp.text.length;
    if(rb>obj.value.length){
      return -1;
    }
    return rb;
  }
}
function getCaretStart(obj){
  if(typeof obj.selectionStart != "undefined"){
    return obj.selectionStart;
  }else if(document.selection&&document.selection.createRange){
    var M=document.selection.createRange();
    try{
      var Lp = M.duplicate();
      Lp.moveToElementText(obj);
    }catch(e){
      var Lp=obj.createTextRange();
    }
    Lp.setEndPoint("EndToStart",M);
    var rb=Lp.text.length;
    if(rb>obj.value.length){
      return -1;
    }
    return rb;
  }
}
function setCaret(obj,l){
  obj.focus();
  if (obj.setSelectionRange){
    obj.setSelectionRange(l,l);
  }else if(obj.createTextRange){
    m = obj.createTextRange();    
    m.moveStart('character',l);
    m.collapse();
    m.select();
  }
}
function setSelection(obj,s,e){
  obj.focus();
  if (obj.setSelectionRange){
    obj.setSelectionRange(s,e);
  }else if(obj.createTextRange){
    m = obj.createTextRange();    
    m.moveStart('character',s);
    m.moveEnd('character',e);
    m.select();
  }
}
String.prototype.addslashes = function(){
  return this.replace(/(["\\\.\|\[\]\^\*\+\?\$\(\)])/g, '\\$1');
//" the above string confuses emacs syntax highlighting - this restores it
}
String.prototype.trim = function () {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};
function curTop(obj){
  toreturn = 0;
  while(obj){
    toreturn += obj.offsetTop;
    obj = obj.offsetParent;
  }
  return toreturn;
}
function curLeft(obj){
  toreturn = 0;
  while(obj){
    toreturn += obj.offsetLeft;
    obj = obj.offsetParent;
  }
  return toreturn;
}
function isNumber(a) {
    return typeof a == 'number' && isFinite(a);
}
function replaceHTML(obj,text){
  while(el = obj.childNodes[0]){
    obj.removeChild(el);
  };
  obj.appendChild(document.createTextNode(text));
}

function wordbooker_actb(obj,ca,path){
  /* ---- Public Variables ---- */
  this.wordbooker_actb_timeOut = -1; // Autocomplete Timeout in ms (-1: autocomplete never time out)
  this.wordbooker_actb_lim = 10;    // Number of elements autocomplete can show (-1: no limit)
  this.wordbooker_actb_firstText = false; // should the auto complete be limited to the beginning of keyword?
  this.wordbooker_actb_mouse = true; // Enable Mouse Support
  this.wordbooker_actb_delimiter = new Array(';',',');  // Delimiter for multiple autocomplete. Set it to empty array for single autocomplete
  this.wordbooker_actb_startcheck = 3; // Show widget only after this number of characters is typed in.
  this.wordbooker_actb_lastdownload = '';  // last string sent to server
  /* ---- Public Variables ---- */

  /* --- Styles --- */
  this.wordbooker_actb_bgColor = '#FFFFFF';
  this.wordbooker_actb_textColor = '#000000';
  this.wordbooker_actb_hColor = '#c0c0c0';
  this.wordbooker_actb_fFamily = 'Verdana';
  this.wordbooker_actb_fSize = '11px';
  this.wordbooker_actb_hStyle = 'text-decoration:underline;font-weight:bold;';
  this.wordbooker_actb_borderStyle = '1px solid black';
  /* --- Styles --- */

  /* ---- Private Variables ---- */
  var wordbooker_actb_delimwords = new Array();
  var wordbooker_actb_cdelimword = 0;
  var wordbooker_actb_delimchar = new Array();
  var wordbooker_actb_display = false;
  var wordbooker_actb_pos = 0;
  var wordbooker_actb_total = 0;
  var wordbooker_actb_curr = null;
  var wordbooker_actb_rangeu = 0;
  var wordbooker_actb_ranged = 0;
  var wordbooker_actb_bool = new Array();
  var wordbooker_actb_pre = 0;
  var wordbooker_actb_toid;
  var wordbooker_actb_tomake = false;
  var wordbooker_actb_getpre = "";
  var wordbooker_actb_mouse_on_list = 1;
  var wordbooker_actb_kwcount = 0;
  var wordbooker_actb_caretmove = false;
  this.wordbooker_actb_keywords = new Array();
  /* ---- Private Variables---- */
  
  this.wordbooker_actb_keywords = ca;
  var wordbooker_actb_self = this;

  wordbooker_actb_curr = obj;
  
  addEvent(wordbooker_actb_curr,"focus",wordbooker_actb_setup);
  function wordbooker_actb_setup(){
    addEvent(document,"keydown",wordbooker_actb_checkkey);
    addEvent(wordbooker_actb_curr,"blur",wordbooker_actb_clear);
    addEvent(document,"keypress",wordbooker_actb_keypress);
  }

  function wordbooker_actb_clear(evt){
    if (!evt) evt = event;
    removeEvent(document,"keydown",wordbooker_actb_checkkey);
    removeEvent(wordbooker_actb_curr,"blur",wordbooker_actb_clear);
    removeEvent(document,"keypress",wordbooker_actb_keypress);
    wordbooker_actb_removedisp();
  }
  function wordbooker_actb_parse(n){
    if (wordbooker_actb_self.wordbooker_actb_delimiter.length > 0){
      var t = wordbooker_actb_delimwords[wordbooker_actb_cdelimword].trim().addslashes();
      var plen = wordbooker_actb_delimwords[wordbooker_actb_cdelimword].trim().length;
    }else{
      var t = wordbooker_actb_curr.value.addslashes();
      var plen = wordbooker_actb_curr.value.length;
    }
    var tobuild = '';
    var i;

    if (wordbooker_actb_self.wordbooker_actb_firstText){
      var re = new RegExp("^" + t, "i");
    }else{
      var re = new RegExp(t, "i");
    }
    var p = n.match.search(re);

    for (i=0;i<p;i++){
      tobuild += n.disp.substr(i,1);
    }
    tobuild += "<font style='"+(wordbooker_actb_self.wordbooker_actb_hStyle)+"'>"
    for (i=p;i<plen+p;i++){
      tobuild += n.disp.substr(i,1);
    }
    tobuild += "</font>";
      for (i=plen+p;i<n.disp.length;i++){
      tobuild += n.disp.substr(i,1);
    }
    return tobuild;
  }
  function wordbooker_actb_generate(){
    if (document.getElementById('tat_table')){ wordbooker_actb_display = false;document.body.removeChild(document.getElementById('tat_table')); } 
    if (wordbooker_actb_kwcount == 0){
      wordbooker_actb_display = false;
      return;
    }
    a = document.createElement('table');
    a.cellSpacing='1px';
    a.cellPadding='2px';
    a.style.position='absolute';
    a.style.top = eval(curTop(wordbooker_actb_curr) + wordbooker_actb_curr.offsetHeight) + "px";
    a.style.left = curLeft(wordbooker_actb_curr) + "px";
    a.style.backgroundColor=wordbooker_actb_self.wordbooker_actb_bgColor;
    a.id = 'tat_table';
    a.style.border = wordbooker_actb_borderStyle;
    document.body.appendChild(a);
    var i;
    var first = true;
    var j = 1;
    if (wordbooker_actb_self.wordbooker_actb_mouse){
      a.onmouseout = wordbooker_actb_table_unfocus;
      a.onmouseover = wordbooker_actb_table_focus;
    }
    var counter = 0;

    for (i=0;i<wordbooker_actb_self.wordbooker_actb_keywords.length;i++){
      if (wordbooker_actb_bool[i]){
        counter++;
        r = a.insertRow(-1);
        if (first && !wordbooker_actb_tomake){
          r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
          first = false;
          wordbooker_actb_pos = counter;
        }else if(wordbooker_actb_pre == i){
          r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
          first = false;
          wordbooker_actb_pos = counter;
        }else{
          r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
        }
        r.id = 'tat_tr'+(j);
        c = r.insertCell(-1);
        c.style.color = wordbooker_actb_self.wordbooker_actb_textColor;
        c.style.fontFamily = wordbooker_actb_self.wordbooker_actb_fFamily;
        c.style.fontSize = wordbooker_actb_self.wordbooker_actb_fSize;
        c.innerHTML = wordbooker_actb_parse(wordbooker_actb_self.wordbooker_actb_keywords[i]);
        c.id = 'tat_td'+(j);
        c.setAttribute('pos',j);
        if (wordbooker_actb_self.wordbooker_actb_mouse){
          c.style.cursor = 'pointer';
          c.onclick=wordbooker_actb_mouseclick;
          c.onmouseover = wordbooker_actb_table_highlight;
        }
        j++;
      }
      if (j - 1 == wordbooker_actb_self.wordbooker_actb_lim && j < wordbooker_actb_total){
        r = a.insertRow(-1);
        r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
        c = r.insertCell(-1);
        c.style.color = wordbooker_actb_self.wordbooker_actb_textColor;
        c.style.fontFamily = 'arial narrow, sans-serif';
        c.style.fontSize = wordbooker_actb_self.wordbooker_actb_fSize;
        c.align='center';
        replaceHTML(c,'\\/');
        if (wordbooker_actb_self.wordbooker_actb_mouse){
          c.style.cursor = 'pointer';
          c.onclick = wordbooker_actb_mouse_down;
        }
        break;
      }
    }
    wordbooker_actb_rangeu = 1;
    wordbooker_actb_ranged = j-1;
    wordbooker_actb_display = true;
    if (wordbooker_actb_pos <= 0) wordbooker_actb_pos = 1;
  }
  function wordbooker_actb_remake(){
    document.body.removeChild(document.getElementById('tat_table'));
    a = document.createElement('table');
    a.cellSpacing='1px';
    a.cellPadding='2px';
    a.style.position='absolute';
    a.style.top = eval(curTop(wordbooker_actb_curr) + wordbooker_actb_curr.offsetHeight) + "px";
    a.style.left = curLeft(wordbooker_actb_curr) + "px";
    a.style.backgroundColor=wordbooker_actb_self.wordbooker_actb_bgColor;
    a.style.border = wordbooker_actb_borderStyle;
    a.id = 'tat_table';
    if (wordbooker_actb_self.wordbooker_actb_mouse){
      a.onmouseout= wordbooker_actb_table_unfocus;
      a.onmouseover=wordbooker_actb_table_focus;
    }
//    document.body.appendChild(a);
    var i;
    var first = true;
    var j = 1;
    if (wordbooker_actb_rangeu > 1){
      r = a.insertRow(-1);
      r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
      c = r.insertCell(-1);
      c.style.color = wordbooker_actb_self.wordbooker_actb_textColor;
      c.style.fontFamily = 'arial narrow, sans-serif';
      c.style.fontSize = wordbooker_actb_self.wordbooker_actb_fSize;
      c.align='center';
      replaceHTML(c,'/\\');
      if (wordbooker_actb_self.wordbooker_actb_mouse){
        c.style.cursor = 'pointer';
        c.onclick = wordbooker_actb_mouse_up;
      }
    }
    for (i=0;i<wordbooker_actb_self.wordbooker_actb_keywords.length;i++){
      if (wordbooker_actb_bool[i]){
        if (j >= wordbooker_actb_rangeu && j <= wordbooker_actb_ranged){
          r = a.insertRow(-1);
          r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
          r.id = 'tat_tr'+(j);
          c = r.insertCell(-1);
          c.style.color = wordbooker_actb_self.wordbooker_actb_textColor;
          c.style.fontFamily = wordbooker_actb_self.wordbooker_actb_fFamily;
          c.style.fontSize = wordbooker_actb_self.wordbooker_actb_fSize;
          c.innerHTML = wordbooker_actb_parse(wordbooker_actb_self.wordbooker_actb_keywords[i]);
          c.id = 'tat_td'+(j);
          c.setAttribute('pos',j);
          if (wordbooker_actb_self.wordbooker_actb_mouse){
            c.style.cursor = 'pointer';
            c.onclick=wordbooker_actb_mouseclick;
            c.onmouseover = wordbooker_actb_table_highlight;
          }
          j++;
        }else{
          j++;
        }
      }
      if (j > wordbooker_actb_ranged) break;
    }
    if (j-1 < wordbooker_actb_total){
      r = a.insertRow(-1);
      r.style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
      c = r.insertCell(-1);
      c.style.color = wordbooker_actb_self.wordbooker_actb_textColor;
      c.style.fontFamily = 'arial narrow, sans-serif';
      c.style.fontSize = wordbooker_actb_self.wordbooker_actb_fSize;
      c.align='center';
      replaceHTML(c,'\\/');
      if (wordbooker_actb_self.wordbooker_actb_mouse){
        c.style.cursor = 'pointer';
        c.onclick = wordbooker_actb_mouse_down;
      }
    }
    document.body.appendChild(a);

  }
  function wordbooker_actb_goup(){
    if (!wordbooker_actb_display) return;
    if (wordbooker_actb_pos == 1) return;
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
    wordbooker_actb_pos--;
    if (wordbooker_actb_pos < wordbooker_actb_rangeu) wordbooker_actb_moveup();
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list=0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
    // opera sometimes needs coaxing to redraw the element
    if (window.opera) {
	document.getElementById('tat_table').style.display='none';
	document.getElementById('tat_table').style.display='block';
    }
  }
  function wordbooker_actb_godown(){
    if (!wordbooker_actb_display) return;
    if (wordbooker_actb_pos == wordbooker_actb_total) return;
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
    wordbooker_actb_pos++;
    if (wordbooker_actb_pos > wordbooker_actb_ranged) wordbooker_actb_movedown();
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list=0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
    // opera sometimes needs coaxing to redraw the element
    if (window.opera) {
	document.getElementById('tat_table').style.display='none';
	document.getElementById('tat_table').style.display='block';
    }
  }
  function wordbooker_actb_movedown(){
    wordbooker_actb_rangeu++;
    wordbooker_actb_ranged++;
    wordbooker_actb_remake();
  }
  function wordbooker_actb_moveup(){
    wordbooker_actb_rangeu--;
    wordbooker_actb_ranged--;
    wordbooker_actb_remake();
  }

  /* Mouse */
  function wordbooker_actb_mouse_down(){
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
    wordbooker_actb_pos++;
    wordbooker_actb_movedown();
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
    wordbooker_actb_curr.focus();
    wordbooker_actb_mouse_on_list = 0;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list=0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
  }
  function wordbooker_actb_mouse_up(evt){
    if (!evt) evt = event;
    if (evt.stopPropagation){
      evt.stopPropagation();
    }else{
      evt.cancelBubble = true;
    }
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
    wordbooker_actb_pos--;
    wordbooker_actb_moveup();
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
    wordbooker_actb_curr.focus();
    wordbooker_actb_mouse_on_list = 0;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list=0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
  }
  function wordbooker_actb_mouseclick(evt){
    if (!evt) evt = event;
    if (!wordbooker_actb_display) return;
    wordbooker_actb_mouse_on_list = 0;
    wordbooker_actb_pos = this.getAttribute('pos');
    wordbooker_actb_penter();
  }
  function wordbooker_actb_table_focus(){
    wordbooker_actb_mouse_on_list = 1;
  }
  function wordbooker_actb_table_unfocus(){
    wordbooker_actb_mouse_on_list = 0;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list = 0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
  }
  function wordbooker_actb_table_highlight(){
    wordbooker_actb_mouse_on_list = 1;
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_bgColor;
    wordbooker_actb_pos = this.getAttribute('pos');
    while (wordbooker_actb_pos < wordbooker_actb_rangeu) wordbooker_actb_moveup();
    while (wordbooker_actb_pos > wordbooker_actb_ranged) wordbooker_actb_movedown();
    document.getElementById('tat_tr'+wordbooker_actb_pos).style.backgroundColor = wordbooker_actb_self.wordbooker_actb_hColor;
    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list = 0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);
  }
  /* ---- */

  function wordbooker_actb_insertword(a){
    if (wordbooker_actb_self.wordbooker_actb_delimiter.length > 0){
      str = '';
      l=0;
      for (i=0;i<wordbooker_actb_delimwords.length;i++){
        if (wordbooker_actb_cdelimword == i){
          prespace = postspace = '';
          gotbreak = false;
          for (j=0;j<wordbooker_actb_delimwords[i].length;++j){
            if (wordbooker_actb_delimwords[i].charAt(j) != ' '){
              gotbreak = true;
              break;
            }
            prespace += ' ';
          }
          for (j=wordbooker_actb_delimwords[i].length-1;j>=0;--j){
            if (wordbooker_actb_delimwords[i].charAt(j) != ' ') break;
            postspace += ' ';
          }
          str += prespace;
          str += a;
          l = str.length;
          if (gotbreak) str += postspace;
        }else{
          str += wordbooker_actb_delimwords[i];
        }
        if (i != wordbooker_actb_delimwords.length - 1){
          str += wordbooker_actb_delimchar[i];
        }
      }
      wordbooker_actb_curr.value = str;
      setCaret(wordbooker_actb_curr,l);
    }else{
      wordbooker_actb_curr.value = a;
    }
    wordbooker_actb_mouse_on_list = 0;
    wordbooker_actb_removedisp();
  }
  function wordbooker_actb_penter(){
    if (!wordbooker_actb_display) return;
    wordbooker_actb_display = false;
    var word = '';
    var c = 0;
    for (var i=0;i<=wordbooker_actb_self.wordbooker_actb_keywords.length;i++){
      if (wordbooker_actb_bool[i]) c++;
      if (c == wordbooker_actb_pos){
	  word = wordbooker_actb_self.wordbooker_actb_keywords[i].disp;
	word=word.replace('\r','');
        break;
      }
    }
    wordbooker_actb_insertword(word);
    l = getCaretStart(wordbooker_actb_curr);
  }
  function wordbooker_actb_removedisp(){
    if (wordbooker_actb_mouse_on_list==0){
      wordbooker_actb_display = 0;
      if (document.getElementById('tat_table')){ document.body.removeChild(document.getElementById('tat_table')); }
      if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    }
  }
  function wordbooker_actb_keypress(e){
    if (wordbooker_actb_caretmove) stopEvent(e);
    return !wordbooker_actb_caretmove;
  }
  function wordbooker_actb_checkkey(evt){
    if (!evt) evt = event;
    a = evt.keyCode;
    caret_pos_start = getCaretStart(wordbooker_actb_curr);
    wordbooker_actb_caretmove = 0;
    switch (a){
      case 38:
        wordbooker_actb_goup();
        wordbooker_actb_caretmove = 1;
        return false;
        break;
      case 40:
        wordbooker_actb_godown();
        wordbooker_actb_caretmove = 1;
        return false;
        break;
      // NMA: Nov 2006  -  split tab and CR and gave tab new properties
      case 9:
        if (wordbooker_actb_display) {
          wordbooker_actb_caretmove = 1;
	  wordbooker_actb_display = 0;
          if (document.getElementById('tat_table')){ document.body.removeChild(document.getElementById('tat_table')); }
          if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
          return false;
        } else {
          return true;
        }
        break;      
      case 13:
        if (wordbooker_actb_display) {
          wordbooker_actb_caretmove = 1;
          wordbooker_actb_penter();
          return false;
        } else {
          return true;
        }
        break;
      default:
        setTimeout(function(){wordbooker_actb_tocomplete(a)},50);
        break;
    }
  }

  function wordbooker_actb_tocomplete(kc){
    if (kc == 38 || kc == 40 || kc == 13) return;
    var i;
    if (wordbooker_actb_display){ 
      var word = 0;
      var c = 0;
      for (var i=0;i<=wordbooker_actb_self.wordbooker_actb_keywords.length;i++){
        if (wordbooker_actb_bool[i]) c++;
        if (c == wordbooker_actb_pos){
          word = i;
          break;
        }
      }
      wordbooker_actb_pre = word;
    }else{ wordbooker_actb_pre = -1};
    
    if (wordbooker_actb_curr.value == ''){
      wordbooker_actb_mouse_on_list = 0;
      wordbooker_actb_removedisp();
      return;
    }
    if (wordbooker_actb_self.wordbooker_actb_delimiter.length > 0){
      caret_pos_start = getCaretStart(wordbooker_actb_curr);
      caret_pos_end = getCaretEnd(wordbooker_actb_curr);
      
      delim_split = '';
      for (i=0;i<wordbooker_actb_self.wordbooker_actb_delimiter.length;i++){
        delim_split += wordbooker_actb_self.wordbooker_actb_delimiter[i];
      }
      delim_split = delim_split.addslashes();
      delim_split_rx = new RegExp("(["+delim_split+"])");
      c = 0;
      wordbooker_actb_delimwords = new Array();
      wordbooker_actb_delimwords[0] = '';
      for (i=0,j=wordbooker_actb_curr.value.length;i<wordbooker_actb_curr.value.length;i++,j--){
        if (wordbooker_actb_curr.value.substr(i,j).search(delim_split_rx) == 0){
          ma = wordbooker_actb_curr.value.substr(i,j).match(delim_split_rx);
          wordbooker_actb_delimchar[c] = ma[1];
          c++;
          wordbooker_actb_delimwords[c] = '';
        }else{
          wordbooker_actb_delimwords[c] += wordbooker_actb_curr.value.charAt(i);
        }
      }

      var l = 0;
      wordbooker_actb_cdelimword = -1;
      for (i=0;i<wordbooker_actb_delimwords.length;i++){
        if (caret_pos_end >= l && caret_pos_end <= l + wordbooker_actb_delimwords[i].length){
          wordbooker_actb_cdelimword = i;
        }
        l+=wordbooker_actb_delimwords[i].length + 1;
      }
      var ot = wordbooker_actb_delimwords[wordbooker_actb_cdelimword].trim(); 
      var t = wordbooker_actb_delimwords[wordbooker_actb_cdelimword].addslashes().trim();
    }else{
      var ot = wordbooker_actb_curr.value;
      var t = wordbooker_actb_curr.value.addslashes();
    }
    if (ot.length == 0){
      wordbooker_actb_mouse_on_list = 0;
      wordbooker_actb_removedisp();
    }
    if (ot.length < wordbooker_actb_self.wordbooker_actb_startcheck) return this;
    if (wordbooker_actb_self.wordbooker_actb_firstText){
      var re = new RegExp("^" + t, "i");
    }else{
      var re = new RegExp(t, "i");
    }
    if(wordbooker_actb_self.wordbooker_actb_lastdownload == '') {
      Download_Candidates(t,wordbooker_actb_self);
    } else if(t.match(wordbooker_actb_self.wordbooker_actb_lastdownload) == null) {
      Download_Candidates(t,wordbooker_actb_self);
    }          
    wordbooker_actb_total = 0;
    wordbooker_actb_tomake = false;
    wordbooker_actb_kwcount = 0;
    for (i=0;i<wordbooker_actb_self.wordbooker_actb_keywords.length;i++){
      wordbooker_actb_bool[i] = false;
      if (re.test(wordbooker_actb_self.wordbooker_actb_keywords[i].match)){
        wordbooker_actb_total++;
        wordbooker_actb_bool[i] = true;
        wordbooker_actb_kwcount++;
        if (wordbooker_actb_pre == i) wordbooker_actb_tomake = true;
      }
    }

    if (wordbooker_actb_toid) clearTimeout(wordbooker_actb_toid);
    if (wordbooker_actb_self.wordbooker_actb_timeOut > 0) wordbooker_actb_toid = setTimeout(function(){wordbooker_actb_mouse_on_list = 0;wordbooker_actb_removedisp();},wordbooker_actb_self.wordbooker_actb_timeOut);

    wordbooker_actb_generate();
  }

  function Download_Candidates(t,wordbooker_actb_self) {
    xmlhttp=null
    // clear keywords now, so we don't get stuff from old set
    wordbooker_actb_self.wordbooker_actb_keywords = new Array();
    // first for all but IE, else for IE
    if (window.XMLHttpRequest) {
      xmlhttp=new XMLHttpRequest()
    } else if (window.ActiveXObject) {
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
    }
    if (xmlhttp!=null) {
      xmlhttp.onreadystatechange=function() {
        // if xmlhttp shows "loaded"
        if (xmlhttp.readyState==4) {
          // if "OK"
          if (xmlhttp.status==200) {
            var response = xmlhttp.responseText;
            wordbooker_actb_self.wordbooker_actb_keywords = explode('#',response);
	    wordbooker_actb_self.wordbooker_actb_lastdownload = t;
            wordbooker_actb_tocomplete(0);  // dummy value
          } 
        }
      };

     xmlhttp.open("GET","/wp-content/plugins/wordbooker/wordbooker_get_friend.php?match="+t+"&userid="+userid,true)
      xmlhttp.send(null)
    }
  }
  
  function explode(separator, string) {
    var list = new Array();
  
    if (separator == null) return false;
    if (string == null) return false;
  
    var currentStringPosition = 0;
    while (currentStringPosition<string.length) {
      var nextIndex = string.indexOf(separator, currentStringPosition);
      if (nextIndex == -1) break;
      var word = string.slice(currentStringPosition, nextIndex);
      word=word.replace('\r','');
      list.push({disp:word,match:UTF8_Folded(word)});
      currentStringPosition = nextIndex+1;
    }
    if (list.length<1) {
	list.push({disp:string,match:UTF8_Folded(string)});
    } else {
	list.push({disp:string.slice(currentStringPosition, string.length),match:UTF8_Folded(string.slice(currentStringPosition, string.length))});
    }
    return list;
  }
  return this;
}
  
/* This table is made by merging and capitalising two tables by
 * Andreas Gohr(andi@splitbrain.org) taken from his UTF helper
 * functions.  These are released under the GPL. */

var UTF8_table = {
    'à':'a', 'ô':'o', 'ď':'d', 'ḟ':'f', 'ë':'e', 'š':'s', 'ơ':'o', 
    'ß':'ss','ă':'a', 'ř':'r', 'ț':'t', 'ň':'n', 'ā':'a', 'ķ':'k', 
    'ŝ':'s', 'ỳ':'y', 'ņ':'n', 'ĺ':'l', 'ħ':'h', 'ṗ':'p', 'ó':'o', 
    'ú':'u', 'ě':'e', 'é':'e', 'ç':'c', 'ẁ':'w', 'ċ':'c', 'õ':'o', 
    'ṡ':'s', 'ø':'o', 'ģ':'g', 'ŧ':'t', 'ș':'s', 'ė':'e', 'ĉ':'c', 
    'ś':'s', 'î':'i', 'ű':'u', 'ć':'c', 'ę':'e', 'ŵ':'w', 'ṫ':'t', 
    'ū':'u','č':'c', 'ö':'oe', 'è':'e', 'ŷ':'y', 'ą':'a', 'ł':'l', 
    'ų':'u', 'ů':'u', 'ş':'s', 'ğ':'g', 'ļ':'l', 'ƒ':'f', 'ž':'z', 
    'ẃ':'w', 'ḃ':'b', 'å':'a', 'ì':'i', 'ï':'i', 'ḋ':'d', 'ť':'t', 
    'ŗ':'r', 'ä':'ae', 'í':'i', 'ŕ':'r', 'ê':'e', 'ü':'ue', 'ò':'o', 
    'ē':'e','ñ':'n', 'ń':'n', 'ĥ':'h', 'ĝ':'g', 'đ':'d', 'ĵ':'j', 
    'ÿ':'y', 'ũ':'u', 'ŭ':'u', 'ư':'u', 'ţ':'t', 'ý':'y', 'ő':'o', 
    'â':'a', 'ľ':'l', 'ẅ':'w', 'ż':'z', 'ī':'i', 'ã':'a', 'ġ':'g', 
    'ṁ':'m', 'ō':'o', 'ĩ':'i', 'ù':'u', 'į':'i', 'ź':'z', 'á':'a', 
    'û':'u', 'þ':'th', 'ð':'dh', 'æ':'ae', 'µ':'u',
    'À':'A', 'Ô':'O', 'Ď':'D', 'Ḟ':'F', 'Ë':'E', 'Š':'S', 'Ơ':'O', 
    'ß':'SS','Ă':'A', 'Ř':'R', 'Ț':'T', 'Ň':'N', 'ā':'A', 'Ķ':'K', 
    'Ŝ':'S', 'Ỳ':'Y', 'Ņ':'N', 'Ĺ':'L', 'ħ':'H', 'Ṗ':'P', 'Ó':'O', 
    'Ú':'U', 'ě':'E', 'É':'E', 'Ç':'C', 'Ẁ':'W', 'Ċ':'C', 'Õ':'O', 
    'Ṡ':'S', 'Ø':'O', 'Ģ':'G', 'ŧ':'T', 'Ș':'S', 'Ė':'E', 'Ĉ':'C', 
    'Ś':'S', 'Î':'I', 'Ű':'U', 'Ć':'C', 'Ę':'E', 'Ŵ':'W', 'Ṫ':'T', 
    'ū':'U', 'Č':'C', 'Ö':'OE', 'È':'E', 'Ŷ':'Y', 'Ą':'A', 'ł':'L', 
    'Ų':'U', 'Ů':'U', 'Ş':'S', 'Ğ':'G', 'Ļ':'L', 'Ƒ':'F', 'Ž':'Z', 
    'Ẃ':'W', 'Ḃ':'B', 'Å':'A', 'Ì':'I', 'Ï':'I', 'Ḋ':'D', 'Ť':'T', 
    'Ŗ':'R', 'Ä':'AE', 'Í':'I', 'Ŕ':'R', 'Ê':'E', 'Ü':'UE', 'Ò':'O', 
    'ē':'E', 'Ñ':'N', 'Ń':'N', 'Ĥ':'H', 'Ĝ':'G', 'đ':'D', 'Ĵ':'J', 
    'Ÿ':'Y', 'Ũ':'U', 'Ŭ':'U', 'Ư':'U', 'Ţ':'T', 'Ý':'Y', 'Ő':'O', 
    'Â':'A', 'Ľ':'L', 'Ẅ':'W', 'Ż':'Z', 'ī':'I', 'Ã':'A', 'Ġ':'G', 
    'Ṁ':'M', 'ō':'O', 'Ĩ':'I', 'Ù':'U', 'Į':'I', 'Ź':'Z', 'Á':'A', 
    'Û':'U', 'Þ':'TH', 'Ð':'DH', 'Æ':'AE',
};

function UTF8_Folded(inp) {
  var retv = "";
  for(i=0; i<inp.length;i++) {
      if(UTF8_table[inp[i]])
	  retv += UTF8_table[inp[i]];
      else
	  retv += inp[i];
  }
  return retv;
}
