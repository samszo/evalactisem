var Evalactisem = {
  onLoad: function() {
    // initialization code
    this.initialized = true;
  },
  openWindow: function(url, title, width, height) {
    width = (width?width:300);
    height = (height?height:300);    
    /*
    var ww = Components.classes["@mozilla.org/embedcomp/window-watcher;1"].
              getService(Components.interfaces.nsIWindowWatcher);
    var win = ww.openWindow( null, url, title, options, null );    
    return win;    
    var options = "centerscreen,menubar=0,toolbar=0,scrollbars=0,location=1,status=1,resizable=1,width=" + width + ",height=" + height;    
    //var options = "centerscreen,location,resizable,width=" + width + ",height=" + height;    
    var win = window.open(url, title, options);    
    win.focus();   
    */   
      var left = parseInt( ( screen.availWidth / 2 ) - ( width / 2 ) ); 
      var top  = parseInt( ( screen.availHeight / 2 ) - ( height / 2 ) );
      var props = "width=" + width + ",height=" + height + ",left=" + left + ",top=" + top +
         ",menubar=no,personalbar=no,toolbar=no,directories=yes,scrollbars=no,location=no,status=yes,resizable=1";
      var newWindow = window.open( url, "", props );
      newWindow.focus();   
      return newWindow;   
  },

  onMenuItemCommand: function() {
    Evalactisem.openWindow("chrome://evalactisem/content/login.xul", "eval",300,500 );
  }
};

window.addEventListener("load", function(e) { Evalactisem.onLoad(e); }, false); 
