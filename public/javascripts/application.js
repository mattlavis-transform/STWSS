/* global $ */

// Warn about using the kit in production
if (window.console && window.console.info) {
    window.console.info("GOV.UK Prototype Kit - do not use for production");
}

$(document).ready(function () {
    window.GOVUKFrontend.initAll();
});

/***************************************************************************************************************/
// START - measurement units and qualifier

$("#measurement_unit_code").on("change", function () {
    var measurement_unit_code = $(this).children("option:selected").val();
    //console.log("measurement_unit_code changed to " + measurement_unit_code);
    url =
        "/api/v1/measurements/measurement_combinations.php?measurement_unit_code=" +
        measurement_unit_code;
    //console.log(url);
    var data = getJson(url);
    var results = data.results;
    if (results.length == 0) {
        // There are no valid measurement qualifier units
        $("#measurement_unit_qualifier_code").val("Unspecified");
        $("#measurement_unit_qualifier_code").prop("disabled", true);
    } else {
        // There is at least one valid measurement qualifier unit
        $("#measurement_unit_qualifier_code").prop("disabled", false);
        var option_array = [];
        $("#measurement_unit_qualifier_code option").each(function () {
            my_option = $(this).val();
            option_array[my_option] = false;
            $.each(results, function () {
                if (this.measurement_unit_qualifier_code == my_option) {
                    option_array[my_option] = true;
                    return;
                }
            });
            if (option_array[my_option] == true || my_option == "Unspecified") {
                $(this).prop("disabled", false);
            } else {
                $(this).prop("disabled", true);
            }
            //console.log ($(this).val().toLowerCase());
            /*
                if ($(this).val().toLowerCase() == "stackoverflow") {
                  $(this).attr("disabled", "disabled").siblings().removeAttr("disabled");
                }
                */
        });
        //console.log(option_array);
    }
});

function getJson() {
    var text = "";
    $.ajaxSetup({ async: false });
    $.getJSON(url, (data) => {
        text = data;
    });
    $.ajaxSetup({ async: true });
    return text;
}

// END - measurement units and qualifier
/***************************************************************************************************************/
$(document).ready(function () {
    $("#subheader").prop("disabled", true);
    $("#header").on("change", function () {
        var header_id = $(this).children("option:selected").val();
        url = "/api/subheaders.php?header_id=" + header_id;
        var data = getJson(url);
        var results = data.results;
        //console.log(results);
        if (results.length == 0) {
            // There are no valid suheaders
            $("#subheader").val("Unspecified");
            $("#subheader").prop("disabled", true);
        } else {
            $("#subheader").prop("disabled", false);
            var options = $('#subheader').prop('options');
            //console.log (options);
            $("#subheader option[value!='0']").each(function () {
                $(this).remove();
            });
            $.each(results, function () {
                //console.log ("Here");
                options[options.length] = new Option(this.description, this.id);
            });
        }
    });
});
