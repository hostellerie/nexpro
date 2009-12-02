function dynamicSelect(id1, id2, after_value) {
    // Browser and feature tests to see if there is enough W3C DOM support
    var agt = navigator.userAgent.toLowerCase();
    var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
    var is_mac = (agt.indexOf("mac") != -1);
    if (!(is_ie && is_mac) && document.getElementById && document.getElementsByTagName) {
        // Obtain references to both select boxes
        var sel1 = document.getElementById(id1);
        var sel2 = document.getElementById(id2);
        // Clone the dynamic select box
        if (sel2)
        {
            var clone = sel2.cloneNode(true);
            // Obtain references to all cloned options
            var clonedOptions = clone.getElementsByTagName("option");
            // Onload init: call a generic function to display the related options in the dynamic select box
            refreshDynamicSelectOptions(sel1, sel2, clonedOptions);
            // Onchange of the main select box: call a generic function to display the related options in the dynamic select box
            sel1.onchange = function() {
                refreshDynamicSelectOptions(sel1, sel2, clonedOptions);
            };
            if (after_value != '') {
                var selected_item = searchList(sel2, after_value);
                if (selected_item != null) {
                    selected_item.selected = true;
                }
            }
        }
    }
}
function refreshDynamicSelectOptions(sel1, sel2, clonedOptions) {
    var agt = navigator.userAgent.toLowerCase();
    var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
    // Delete all options of the dynamic select box
    while (sel2.options.length) {
        sel2.remove(0);
    }
    // Create regular expression objects for "select" and the value of the selected option of the main select box as class names
    var pattern1 = /( |^)(select)( |$)/;
    var pattern2 = new RegExp("( |^)(" + sel1.options[sel1.selectedIndex].value + ")( |$)");
    // Iterate through all cloned options
    for (var i = 0; i < clonedOptions.length; i++) {
        // If the classname of a cloned option either equals "select" or equals the value of the selected option of the main select box
        if (clonedOptions[i].className.match(pattern1) || clonedOptions[i].className.match(pattern2)) {
            // Clone the option from the hidden option pool and append it to the dynamic select box
            sel2.appendChild(clonedOptions[i].cloneNode(true));
        }
    }
    if (is_ie) {
        sel2.fireEvent('onchange');
    }
}
function searchList(sel, field_value) {
    var i;
    for (i = 0; i < sel.length; i++) {
        if (sel.options[i].value == field_value) {
           return sel.options[i];
        }
    }
    return null;
}