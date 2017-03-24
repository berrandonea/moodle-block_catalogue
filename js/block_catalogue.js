// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Displays a catalogue of all the blocks, modules, reports and customlabels the teacher can use in his course.
 *
 * @package    block_catalogue
 * @copyright     Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 * File : js/block_catalogue.js
 * Javascript functions used in the block.
 */

var xhr = null;

function flipflop(id) {
    if (document.getElementById(id).style.display == "none") {
        document.getElementById(id).style.display = "block";
    } else {
        document.getElementById(id).style.display = "none";
    }
}

function getXhr(){
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    } else if(window.ActiveXObject) {
        try {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
    } else {
        alert("Error : your browser doesn't support XMLHTTPRequest...");
        xhr = false;
    }
}

function showdescr(description) {
    alert(description);
}

function toggle(listname, elementname, toggler, courseid, isdefault, phpscript) {
    getXhr();
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200) {
            response = xhr.responseText;            
            newdata = response.split("£µ£");
            docelementid = toggler + 'tog-' + elementname;
            favzone = document.getElementById('block-catalogue-favorites');
            themefavzone = document.getElementById('theme-catalogue-favorites');
            document.getElementById(docelementid).innerHTML = newdata[0];
            if (newdata[1] && favzone) {
                //~ favzoneid = 'block-catalogue-favorites';
                //~ document.getElementById(favzoneid).innerHTML = newdata[1];
                favzone.innerHTML = newdata[1];
                
            }
            if (newdata[2] && themefavzone) {
				//~ themefavzoneid = 'theme-catalogue-favorites';
                //~ document.getElementById(themefavzoneid).innerHTML = newdata[2];
                themefavzone.innerHTML = newdata[2];
			}
        }
    }
    xhr.open("POST", phpscript, true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    args = "list=" + listname + "&element=" + elementname + "&toggler=" + toggler + "&courseid=" + courseid + "&default=" + isdefault;
    xhr.send(args);
}



function indent(elementname, cmid) {
	getXhr();
	xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200) {
            response = xhr.responseText;
            docelementid = 'jsmod' + cmid;
            document.getElementById(docelementid).innerHTML = response;
		}
	}
	xhr.open("POST", "indent.php", true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    args = "action=" + elementname + "&cmid=" + cmid;
    xhr.send(args);
}
