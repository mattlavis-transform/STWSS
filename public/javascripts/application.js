/* global $ */

// Warn about using the kit in production
if (window.console && window.console.info) {
    window.console.info("GOV.UK Prototype Kit - do not use for production");
}

$(document).ready(function () {
    window.GOVUKFrontend.initAll();
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

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

// END - measurement units and qualifier
/***************************************************************************************************************/
$(document).ready(function () {

    id = getUrlParameter('id');
    if (id == "") {
        $("#subheader").prop("disabled", true);
    }

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
