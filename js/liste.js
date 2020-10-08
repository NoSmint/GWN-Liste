document.onkeydown = function(evt) {
	if ((evt.ctrlKey && evt.keyCode == 70) || (evt.ctrlKey && evt.altKey && evt.keyCode == 70)) {
        ToggleSearch();
		return false;
    }
	if (evt.keyCode == 27 && document.getElementById("suchfeld").style.maxHeight) {
		document.getElementById("search").blur();
		document.getElementById("suchfeld").style.maxHeight = null;
		document.getElementsByClassName("noData")[0].style.display = "none";
		ul = document.getElementsByClassName("accordion");
		for (b = 0; b < ul.length; b++) {
			ul[b].style.display = "";
		}
		var labels = document.getElementsByClassName("label");
		for (b = 0; b < labels.length; b++) {
			labels[b].style.display = "block";
		}
		document.getElementById('search').value = null;
		if (document.getElementsByClassName("active")[0]) {document.getElementsByClassName("active")[0].scrollIntoView(false);}
		return;
	}
	if (evt.altKey && evt.keyCode == 49) {
		location.replace('?sort=team')
		return;
	}
	if (evt.altKey && evt.keyCode == 50) {
		location.replace('?sort=pos')
		return;
	}
	if (evt.altKey && evt.keyCode == 51 && window.location.href.indexOf('?sort=abc') > -1) {
			location.replace('?sort=zyx');
	} else if (evt.altKey && evt.keyCode == 51) {
			location.replace('?sort=abc');
	}
};

document.onclick = function(evt) {
    evt = evt || window.event;
	if (document.getElementById("sortdropdown").classList.contains("show") && !document.getElementsByClassName("sortdropdown")[0].contains(evt.target) && !document.getElementsByClassName("sortbutton")[0].contains(evt.target)) {
		evt.preventDefault();
		evt.stopPropagation();
		document.getElementById("sortdropdown").style.maxHeight = null;
		document.getElementById("sortdropdown").classList.remove("show");
		scrollFunction();
		return false;
	}
};

document.getElementById("gwn").addEventListener("click", function(evt) {
	if (document.getElementById("sortdropdown").classList.contains("show")) {
		evt.preventDefault();
		return false;
	} else {
		// window.location.href = "../";
	}	
});

document.getElementById("backtohome").addEventListener("click", function(evt) {
	if (document.getElementById("sortdropdown").classList.contains("show")) {
		evt.preventDefault();
		return false;
	} else {
		// window.location.href = "../";
	}	
});

var acc = document.getElementsByClassName("accordion");
var i;
var b;
var currentacc;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function(evt) {
	if (document.getElementById("sortdropdown").classList.contains("show")) {
		evt.preventDefault();
		return false;
	}
	document.body.classList.add("Hover");
	currentacc = this;
	for (b = 0; b < acc.length; b++) {
		if (acc[b] != this) {
			acc[b].classList.remove("active");
			var panel = acc[b].nextElementSibling;
			panel.style.maxHeight = null;
		}
	}
	currentacc.classList.toggle("active");
	var panel = currentacc.nextElementSibling;
	if (panel.style.maxHeight){
	  panel.style.maxHeight = null;
	} else {
	  panel.style.maxHeight = panel.scrollHeight + "px";
	}
  });
}

function ToggleSort() {
	
	var sortpanel = document.getElementById("sortdropdown");
	if (sortpanel.style.maxHeight) {
		sortpanel.style.maxHeight = null;
		document.body.classList.add("Hover");
		sortpanel.classList.remove("show");
		scrollFunction();
	} else {
		document.getElementsByClassName("mobile_navigation")[0].setAttribute("style","opacity: 1;");
		sortpanel.style.maxHeight = sortpanel.scrollHeight + "px";
		document.body.classList.remove("Hover");
		sortpanel.classList.add("show");
	}
}

function ToggleSearch() {
	var ul, i, acc;
	var suchfeld = document.getElementById("suchfeld");
	if (suchfeld.style.maxHeight) {
		document.getElementById("search").blur();
		suchfeld.style.maxHeight = null;
		ul = document.getElementsByClassName("accordion");
		for (b = 0; b < ul.length; b++) {
			ul[b].style.display = "";
		}
		var labels = document.getElementsByClassName("label");
		for (b = 0; b < labels.length; b++) {
			labels[b].style.display = "block";
		}
		document.getElementById('search').value = null;
		document.getElementsByClassName("noData")[0].style.display = "none";
		if (document.getElementsByClassName("active")[0]) {document.getElementsByClassName("active")[0].scrollIntoView(false);}
	} else {
		acc = document.getElementsByClassName("accordion");
		for (i = 0; i < acc.length; i++) {
			acc[i].nextElementSibling.style.maxHeight = null;
			acc[i].classList.remove("active");
		}
		suchfeld.style.maxHeight = suchfeld.scrollHeight + "px";
		document.getElementById("search").scrollIntoView();
		document.getElementById("search").focus();
	}
}

function DoSearch() {
	
    var evt, filter, ul, a, b;
	
	document.getElementById('search').value = document.getElementById('search').value.replace(/\\|\$|%|\/|<|>|\(|\)|#|\||%|"|=/gi, function (x) {
		return "";
	});
	
    filter = document.getElementById('search').value.toUpperCase();
	
	ul = document.getElementsByClassName("accordion");
    for (b = 0; b < ul.length; b++) {
		if (ul[b].firstChild.data.toUpperCase().indexOf(filter) > -1) {
			ul[b].style.display = "";
		} else {
			ul[b].style.display = "none";
		}
    }
	
	var IDs = ['ID_H', 'ID_W', 'ID_AL', 'ID_AH', 'ID_A', 'ID_B', 'ID_C', 'ID_D', 'ID_E', 'ID_F', 'ID_G', 'ID_wA', 'ID_wB', 'ID_wC', 'ID_wD', 'ID_wE', 'ID_wF', 'ID_wG', 'ID_O', 'ID_POS1', 'ID_POS2', 'ID_POS3', 'ID_POS4', 'ID_POS5', 'ID_ABC', 'ID_ZYX'];
	var labelcount = 0;
	IDs.forEach(function(item) {
	
		accCheck = document.getElementsByClassName(item);
		
		if (accCheck.length > 0) {
			var labelshow = false;
			for (i=0; i<accCheck.length; i++) {
				if (accCheck[i].style.display != "none") {
					labelshow = true;
				}
			}
			if (labelshow == true) {
				document.getElementById(item).style.display = "block";
				labelcount = labelcount + 1;
			} else {
				document.getElementById(item).style.display = "none";
			}
		}
	});

	if (labelcount > 0) {
		document.getElementsByClassName("noData")[0].style.display = "none";
	} else {
		document.getElementsByClassName("noData")[0].style.display = "block";
	}
}

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
		document.getElementById("myBtn").setAttribute("style","opacity: 0.8;");
		document.getElementById("myBtn").style.visibility = "visible";
		document.getElementsByClassName("mobile_navigation")[0].setAttribute("style","opacity: 0.7;");
    } else {
		document.getElementById("myBtn").setAttribute("style","opacity:0;");
		document.getElementById("myBtn").style.visibility = "hidden";
		document.getElementsByClassName("mobile_navigation")[0].setAttribute("style","opacity: 1;");
	}
	if (document.getElementById("sortdropdown").classList.contains("show")) {
		document.getElementsByClassName("mobile_navigation")[0].setAttribute("style","opacity: 1;");
	}
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}

