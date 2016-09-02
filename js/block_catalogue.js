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

function toggle(listname, elementname, toggler, courseid, isdefault) {
    getXhr();
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200) {
            response = xhr.responseText;
            docelementid = toggler + 'tog-' + elementname;
            document.getElementById(docelementid).innerHTML = response;
        }
    }
    xhr.open("POST", "toggle.php", true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    args = "list=" + listname + "&element=" + elementname + "&toggler=" + toggler + "&courseid=" + courseid + "&default=" + isdefault;
    xhr.send(args);
}
