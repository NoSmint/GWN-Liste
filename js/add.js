var saveresult;
		
function findselected() {
	var result = document.querySelector('select[name="position"]').value;
	if (document.getElementById("inputother").value != "") {
		saveresult = document.getElementById("inputother").value;
	}
	
	
	if(result=="5"){
		document.getElementById("inputother").removeAttribute('disabled');
		document.getElementById("inputother").setAttribute('required', true);
		document.getElementById("inputother").setAttribute('Placeholder', 'Bitte eintragen');
		if (typeof(saveresult) !== 'undefined') {
			document.getElementById("inputother").value = saveresult;
		}
	}
	else{
		document.getElementById("inputother").setAttribute("style","box-shadow: none;")
		document.getElementById("inputother").setAttribute('disabled', true);
		document.getElementById("inputother").removeAttribute('required');
		document.getElementById("inputother").setAttribute('Placeholder', 'Andere Position');
		document.getElementById("inputother").value = "";
	}
}

function validate(valname, inputname) {
	document.getElementById(valname).setAttribute("style","opacity:1");
		document.getElementsByName(inputname)[0].setAttribute("style","box-shadow: none;");
	if (document.getElementsByName(inputname)[0].value == "") {
		document.getElementById(valname).setAttribute("style","opacity:0");
	}
	document.getElementsByName(inputname)[0].value = document.getElementsByName(inputname)[0].value.replace(/\\|\$|%|\/|<|>|\(|\)|#|\||%|"|=/gi, function (x) {
		return "";
	});
	if (document.getElementsByName("plz")[0].value == "48301" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Nottuln"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
	if (document.getElementsByName("plz")[0].value == "48308" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Senden"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
	if (document.getElementsByName("plz")[0].value == "48249" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Dülmen"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
	if (document.getElementsByName("plz")[0].value == "48653" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Coesfeld"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
	if (document.getElementsByName("plz")[0].value == "48329" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Havixbeck"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
	if (document.getElementsByName("plz")[0].value == "48727" && document.getElementsByName("ort")[0].value.trim() == "") { document.getElementsByName("ort")[0].value = "Billerbeck"; document.getElementById("validort").setAttribute("style","opacity:1"); document.getElementsByName("mobil")[0].focus(); }
}

		
var x, i, j, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
for (i = 0; i < x.length; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  /*for each element, create a new DIV that will act as the selected item:*/
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < selElmnt.length; j++) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
        var i, s, h;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        h = this.parentNode.previousSibling;
        for (i = 0; i < s.length; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
			s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
			findselected();
            if (document.querySelector('select[name="position"]').value != "") {document.getElementById("select-pos").setAttribute("style","box-shadow: none;");}
			if (document.querySelector('select[name="mannschaft"]').value != "") {document.getElementById("select-team").setAttribute("style","box-shadow: none;");}
			if (document.querySelector('select[name="verteiler"]').value != "") {document.getElementById("select-verteiler").setAttribute("style","text-align: center; box-shadow: none;");}
			if (document.querySelector('select[name="publish"]').value != "") {document.getElementById("select-publish").setAttribute("style","text-align: center; box-shadow: none;");}
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
	  if (this.nextSibling.scrollHeight > 250) {this.nextSibling.style.overflow = "auto";}
	  //if (this.nextSibling.style.maxHeight == 0 || this.nextSibling.style.maxHeight == null) {this.nextSibling.style.overflow = "hidden";}
      this.classList.toggle("select-arrow-active");
  });
}
function closeAllSelect(elmnt) {
  /*a function that will close all select boxes in the document,
  except the current select box:*/
  var x, y, i, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  for (i = 0; i < y.length; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < x.length; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");

    }
  }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);

document.getElementById("absenden").addEventListener("click", function(event) {
	var absenden = true;
	var focusset = false;
	
	if (document.querySelector('select[name="verteiler"]').value == "") {
		document.getElementById("select-verteiler").setAttribute("style","text-align: center; box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		}
	if (document.querySelector('select[name="publish"]').value == "") {
		document.getElementById("select-publish").setAttribute("style","text-align: center; box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		}
	if (document.querySelector('select[name="mannschaft"]').value == "") {
		document.getElementById("select-team").setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		}
	if (document.querySelector('select[name="position"]').value == "") {
		document.getElementById("select-pos").setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		}
	if (document.querySelector('select[name="position"]').value == "5" && document.getElementById("inputother").value == "") {
		document.getElementById("inputother").setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		focusset = document.getElementById("inputother");
	}
	if (document.getElementsByName("email")[0].value == "" || !document.getElementsByName("email")[0].checkValidity()) {
		document.getElementsByName("email")[0].setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;		
		focusset = document.getElementsByName("email")[0];
		}
	if (document.getElementsByName("last_name")[0].value == "" || !document.getElementsByName("last_name")[0].checkValidity()) {
		document.getElementsByName("last_name")[0].setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		focusset = document.getElementsByName("last_name")[0];
		}
	if (document.getElementsByName("first_name")[0].value == "" || !document.getElementsByName("first_name")[0].checkValidity()) {
		document.getElementsByName("first_name")[0].setAttribute("style","box-shadow:0 0 2px 1px rgba(255, 0, 0, 0.8);");
		absenden = false;
		focusset = document.getElementsByName("first_name")[0];
		}
	if (absenden) {
		return true;
	} else {
		event.preventDefault();
		focusset.focus();
		return false;
	}
});