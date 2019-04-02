$(document).ready(function () {
    activaOptionCombo();

    var activities = document.getElementById("V_COD_TIPO_SERV");

    activities.addEventListener("change", function () {
        activaOptionCombo();
    });

});

function activaOptionCombo() {
    var op = document.getElementById("I_COD_ZONA").getElementsByTagName("option");

    switch (document.getElementById("V_COD_TIPO_SERV").selectedIndex) {
        case 0:
            for (var i = 0; i < op.length; i++) {
                // lowercase comparison for case-insensitivity
                (op[i].text.toLowerCase() == "europa") || (op[i].text.toLowerCase() == "internacional")
                        ? op[i].disabled = true
                        : op[i].disabled = false;

                if (op[i].text.toLowerCase() == "provincial") {
                    op[i].selected = true;
                }
            }
            break;
        case 1:
            for (var i = 0; i < op.length; i++) {
                // lowercase comparison for case-insensitivity
                (op[i].text.toLowerCase() == "europa") || (op[i].text.toLowerCase() == "internacional")
                        ? op[i].disabled = true
                        : op[i].disabled = false;

                if (op[i].text.toLowerCase() == "provincial") {
                    op[i].selected = true;
                }
            }
            break;
        case 2:
            for (var i = 0; i < op.length; i++) {
                // lowercase comparison for case-insensitivity
                (op[i].text.toLowerCase() != "europa")
                        ? op[i].disabled = true
                        : op[i].disabled = false;

                if (op[i].text.toLowerCase() == "europa") {
                    op[i].selected = true;
                }
            }
            break;
        case 3:
            for (var i = 0; i < op.length; i++) {
                // lowercase comparison for case-insensitivity
                (op[i].text.toLowerCase() != "europa") && (op[i].text.toLowerCase() != "internacional")
                        ? op[i].disabled = true
                        : op[i].disabled = false;

                if (op[i].text.toLowerCase() == "internacional") {
                    op[i].selected = true;
                }
            }
            break;
    }
}