/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

Limb.namespace('Limb.Window');

var LIMB_WINDOW_WIDTH = null;
var LIMB_WINDOW_HEIGHT = null;
var LIMB_WINDOW_DEFAULT_PARAMS = {};

Limb.Class('Limb.Window',
{
  __construct: function()
  {
    this.parentWindow = null;
    this.windowName = this._generateName();
    this.onLoadEvents = [];
    this.onUnloadEvents = [];
    this.params = {};

    if(arguments.length == 0)
      this.window = window;

    // arguments[0] instanceof Window does not work in IE
    if(typeof(arguments[0]) == 'object')
      this.window = win;

    if(arguments.length > 0)
    {
      this.window = this._createWindow(arguments[0], arguments[1], arguments[2]);
      jQuery(this.window).ready(this.onOpen.bind(this));
      jQuery(this.window).load(this.onLoad.bind(this));
      jQuery(this.window).bind('close', this.onClose.bind(this));
    }
  },

  getWindowObject: function()
  {
    return this.window;
  },

  centreWindow: function(width, height)
  {
    var newWindowRect = this._getRectInParentCenter(width, height);
    this.setRect(newWindowRect);
  },

  _getRectInParentCenter: function(width, height)
  {
    var windowRect = this.parentWindow.getRect();

    var result = new Limb.Coordinates.Rect();
    result.createWithCenter(windowRect.getCenter(), width, height);

    return result;
  },

  _getDefaultParams: function()
  {
    var width = 150;
    var height = 100;

    var newWindowRect = this._getRectInParentCenter(width, height);

    var params = new Limb.Window.Params();

    params.addParameter('left', newWindowRect.getX());
    params.addParameter('top', newWindowRect.getY());
    params.addParameter('width', width);
    params.addParameter('height', height);

    params.addParameter('scrollbars', 'yes');
    params.addParameter('resizable', 'yes');
    params.addParameter('help', 'no');
    params.addParameter('status', 'yes');

    return params;
  },

  _generateName: function()
  {
    return Math.round(Math.random() * 1000) + '_generate';
  },

  _createWindow: function(href, windowName, createParams)
  {
    this.parentWindow = new Limb.Window();

    if(windowName)
      this.windowName = windowName;

    this.params = this._getDefaultParams();
    this.params.merge(createParams || LIMB_WINDOW_DEFAULT_PARAMS);

    var win = window.open(href, this.windowName, this.params.asString());
    return win;
  },

  getRect: function()
  {
    if(Limb.Browser.is_ie)
      return new Limb.Coordinates.Rect(this.window.screenLeft,
                                       this.window.screenTop,
                                       this.window.document.body.clientWidth,
                                       this.window.document.body.clientHeight);
    else
      return new Limb.Coordinates.Rect(this.window.screenX + this.window.outerWidth - this.window.innerWidth,
                                       this.window.screenY + this.window.outerHeight - this.window.innerHeight,
                                       this.window.innerWidth,
                                       this.window.innerHeight);
  },

  setRect: function(rect)
  {
    if(!rect)
      return false;

    this.window.moveTo(rect.getX(), rect.getY());
    this.window.resizeTo(rect.getWidth(), rect.getHeight());

    return true;
  },

  close: function()
  {
    this.window.close();
  },

  onOpen: function()
  {
    Limb.Window.register(this.windowName, this);

    this.autoResize();

    this.centreWindow(this.params.getParameter('width'), this.params.getParameter('height'));

    this.openHandler();
  },

  onLoad: function()
  {
    this.autoResize();

    this.centreWindow(this.params.getParameter('width'), this.params.getParameter('height'));
  },

  autoResize: function()
  {
    if(!(this.params && !this.params.getParameter('noautoresize')) &&
       !(this.window.LIMB_WINDOW_WIDTH || this.window.LIMB_WINDOW_HEIGHT))
      return;

    if(this.window.LIMB_WINDOW_WIDTH)
      this.params.setParameter('width', this.window.LIMB_WINDOW_WIDTH);
    else
      this.params.setParameter('width', this.parentWindow.getRect().getWidth() * 0.85);

    if(this.window.LIMB_WINDOW_HEIGHT)
      this.params.setParameter('height', this.window.LIMB_WINDOW_HEIGHT);
    else
      this.params.setParameter('height', this.parentWindow.getRect().getHeight() * 0.9);
  },

  onClose: function()
  {
    Limb.Window.remove(this.windowName);

    this.closeHandler();
  },

  openHandler: function() {},
  closeHandler: function() {},

  static:
  {
    register: function(windowName, win)
    {
      if(!Limb.isset(Limb.Window.createdWindows))
        Limb.Window.createdWindows = [];

      Limb.Window.createdWindows[windowName] = win;
    },

    remove: function(windowName)
    {
      if(!Limb.isset(Limb.Window.createdWindows)||
         !Limb.isset(Limb.Window.createdWindows[windowName]))
        return;

      Limb.Window.createdWindows[windowName] = null;
      delete Limb.Window.createdWindows[windowName];
    },

    current: function()
    {
     if(!Limb.isset(Limb.Window.createdWindows))
        return null;

      for(var i in Limb.Window.createdWindows)
        if(Limb.Window.createdWindows[i].getWindowObject() == window)
          return Limb.Window.createdWindows[i];

      return null;
    }
  }
});

Limb.Class('Limb.Window.Params',
{
  __construct: function(initArray)
  {
    this.params = initArray || [];
  },

  setParameter: function(name, value)
  {
    this.params[name] = value;
  },

  addParameter: function(name, value)
  {
    if(Limb.isset(this.params[name]))
      return;

    this.setParameter(name, value);
  },

  getParameter: function(name)
  {
     return this.params[name];
  },

  merge: function(params)
  {
    for(var name in params)
      this.params[name] = params[name];
  },

  asString: function()
  {
    var result = '';

    for(var name in this.params)
    {
      if(Limb.isFunction(this.params[name]))
        continue;

      result += name + '=' + this.params[name] + ',';
    }

    return result.slice(0, -1);
  },

  toArray: function()
  {
    return this.params;
  }
});

Limb.namespace('Limb.Coordinates');

Limb.Class('Limb.Coordinates.Point',
{
  __construct: function(x, y)
  {
    this.x = x || 0;
    this.y = y || 0;
  },

  setX: function(x)
  {
    this.x = x;
  },

  setY: function(y)
  {
    this.y = y;
  },

  getX: function()
  {
    return this.x;
  },

  getY: function()
  {
    return this.y;
  }
});

Limb.Class('Limb.Coordinates.Rect').inherits('Limb.Coordinates.Point',
{
  __construct: function(x, y, width, height)
  {
    this.parent.__construct(x, y);

    this.width = width || 0;
    this.height = height || 0;
  },

  setWidth: function(width)
  {
    this.width = width;
  },

  setHeight: function(height)
  {
    this.height = height;
  },

  getWidth: function()
  {
    return this.width;
  },

  getHeight: function()
  {
    return this.height;
  },

  getRight: function()
  {
    return this.x + this.width;
  },

  getBottom: function()
  {
    return this.y + this.height;
  },

  setSize: function(width, height)
  {
    this.width = width || 0;
    this.height = height || 0;
  },

  createWithCenter: function(point, width, height)
  {
    if(!point)
      return;

    this.setSize(width, height);
    this.alignToCenter(point);
  },

  alignToCenter: function(point)
  {
    this.x = point.x - this.width / 2;
    this.y = point.y - this.height / 2;
  },

  getCenter: function()
  {
    return new Limb.Coordinates.Point(this.x + (this.width / 2), this.y + (this.height / 2));
  }
});
