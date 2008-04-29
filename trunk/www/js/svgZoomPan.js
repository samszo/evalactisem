
if (!window)
  window = this;

function handleLoad(evt)
{
  if (!document)
    window.document = evt.target.ownerDocument;
}

/**
 * Event handlers to change the current user space for the zoom and pan
 * controls to make them appear to be scale invariant.
 */

function handleZoom(evt)
{
  try {
    if (evt.newScale === undefined) throw 'bad interface';
    // update the transform list that adjusts for zoom and pan
    var tlist = document.getElementById('zoomControls').transform.baseVal;
    tlist.getItem(0).setScale(1/evt.newScale, 1/evt.newScale);
    tlist.getItem(1).setTranslate(-evt.newTranslate.x, -evt.newTranslate.y);
  }
  catch (e) {
    // work around difficiencies in non-moz implementations (some don't
    // implement the SVGZoomEvent or SVGAnimatedTransform interfaces)
    var de = document.documentElement;
    var tform = 'scale(' + 1/de.currentScale + ') ' +
                'translate(' + (-de.currentTranslate.x) + ', ' + (-de.currentTranslate.y) + ')';
    document.getElementById('zoomControls').setAttributeNS(null, 'transform', tform);
  }
}

function handlePan(evt)
{
  var ct = document.documentElement.currentTranslate;
  try {
    // update the transform list that adjusts for zoom and pan
    var tlist = document.getElementById('zoomControls').transform.baseVal;
    tlist.getItem(1).setTranslate(-ct.x, -ct.y);
  }
  catch (e) {
    // work around difficiencies in non-moz implementations (some don't
    // implement the SVGAnimatedTransform interface)
    var tform = 'scale(' + 1/document.documentElement.currentScale + ') ' +
                'translate(' + (-ct.x) + ', ' + (-ct.y) + ')';
    document.getElementById('zoomControls').setAttributeNS(null, 'transform', tform);
  }
}

/**
 * Functions to do zoom and pan.
 */

function zoom(type)
{
  var de = document.documentElement;
//  de.suspendRedraw();

  // zoom:

  var oldScale = de.currentScale;
  var oldTranslate = { x: de.currentTranslate.x, y: de.currentTranslate.y };
  var s = 2;
  if (type == 'in')
    de.currentScale *= s;
  if (type == 'out')
    de.currentScale /= s;

  // correct currentTranslate so zooming is to the center of the viewport:

  var vp_width, vp_height;
  try {
    vp_width = de.viewport.width;
    vp_height = de.viewport.height;
  }
  catch (e) {
    // work around difficiency in moz ('viewport' property not implemented)
    vp_width = window.innerWidth;
    vp_height = window.innerHeight;
  }
  de.currentTranslate.x = vp_width/2 - (de.currentScale/oldScale) * (vp_width/2 - oldTranslate.x);
  de.currentTranslate.y = vp_height/2 - (de.currentScale/oldScale) * (vp_height/2 - oldTranslate.y);

//  de.unsuspendRedraw();
//  de.forceRedraw();
}

function pan(type)
{
  var de = document.documentElement;
//  de.suspendRedraw();

  var ct = de.currentTranslate;
  var t = 30;
  if (type == 'right')
    ct.x += t;
  if (type == 'down')
    ct.y += t;
  if (type == 'left')
    ct.x -= t;
  if (type == 'up')
    ct.y -= t;

//  de.unsuspendRedraw();
//  de.forceRedraw();
}
