function multyRemove(ids)
{
	filename = "multyremove.php?ids=" + ids;
	nm = window.open(filename,"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=500,height=200");
}

function sortSectionDocument(id, objtype)
{
	filename = "sort.php?id=" + id + "&objtype=" + objtype;
	ns = window.open(filename,"","toolbar=no,scrollbars=no,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=200,height=170");
	ns.focus();
}

function newSection(sid)
{
	filename = "section.php?sid=" + sid;
	ns = window.open(filename,"","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=530,height=430");
	ns.focus();
}

function editSection(sid)
{
	filename = "section.php?sid=" + sid+"&action=edit";
	ns = window.open(filename,"","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=530,height=430");
	ns.focus();
}

function deleteSection(sid, namefolder)
{
	if (confirm('Вы действительно хотите удалить папку: ' + namefolder+'?'))
	{
		filename = "section.php?sid=" + sid+"&action=delete";
		ns = window.open(filename,"","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=520,height=400");
		ns.focus();
	}
	else
	{
		Alert('Папка не будет удалена!');
		ns.close();
	}
}

function showDT()
{
	document.getElementById("doctypes").style.display="block";
	event.preventDefault() 
}

function hideDT()
{
	document.getElementById("doctypes").style.display="none";
}

function wopen(path) {
	nm = window.open(path,"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=200,height=200");
	nm.focus();
}

function SetCookie(name, value) {
        var argv = SetCookie.arguments;
        var argc = SetCookie.arguments.length;
        var expires = (argc > 2) ? argv[2] : null;
        var path = (argc > 3) ? argv[3] : null;
        var domain = (argc > 4) ? argv[4] : null;
        var secure = (argc > 5) ? argv[5] : false;
        document.cookie = name + "=" + escape (value) +
                ((expires == null) ? "" : ("; expires=" +
expires.toGMTString())) +
                ((path == null) ? "" : ("; path=" + path)) +
                ((domain == null) ? "" : ("; domain=" + domain)) +
                ((secure == true) ? "; secure" : "");
}

function copyDoc(id) {
  SetCookie('copydocid', id);
  document.location.reload();
}

function newDoc(sid, doctype)
{
	filename = doctype + ".php?sid=" + sid;
	nm = window.open(filename,"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0");
	nm.focus();
}

function editDoc(docid, sid, doctype)
{
	filename = doctype + ".php?docid=" + docid + "&sid=" + sid+"&action=edit";
	var nm = window.open(filename,"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0");
	nm.focus();
}

function propDoc(docid, sid)
{
	filename = "propdoc.php?docid=" + docid + "&sid=" + sid + "&action=edit";
	nm = window.open(filename,"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=500,height=230");
	nm.focus();
}

function deleteDoc(docid, docname, doctype)
{
	if (confirm('Вы действительно хотите этот документ: ' + docname+'?'))
	{
		filename = doctype + ".php?docid=" + docid + "&action=delete";
		ns = window.open(filename,"","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=500,height=400");
		ns.focus();
		parent.location.reload();
	}
	else
	{
		Alert('Документ не будет удален!');
		ns.close();
	}
}


var m_url="";
var eX=0;
var eY=0;
var m_class_id="";
var m_sec_id="";
var menu_active = false;

function mousePageXY(e)
{
var x = 0, y = 0;

if (!e) e = window.event;

if (typeof e != 'undefined') {
  if (e.pageX || e.pageY) {
    x = e.pageX;
    y = e.pageY;
  }
  else if (e.clientX || e.clientY) {
    x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
    y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
  }
}  
  eX=x;
  eY=y;
  
}
