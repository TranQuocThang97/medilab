imsLocation = {
    locationChange: function (type, element) {
        $(document).on("change", element, function (e) {
            if (type == "area"){
                if ($(this).data("country")) {
                    imsLocation.loadLocation($(this), "country");
                }
                if ($(this).data("province")) {
                    $('#' + $(this).data("province")).html('<option value="">' + lang_js['select_province'] + '</option>');
                }
                if ($(this).data("district")) {
                    $("#" + $(this).data("district")).html('<option value="">' + lang_js['select_district'] + '</option>');
                }
                if ($(this).data('ward')) {
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_ward'] + '</option>');
                }
            } else if (type == "country") {
                if ($(this).data("province")) {
                    imsLocation.loadLocation($(this), "province");
                }
                if ($(this).data("district")) {
                    $("#" + $(this).data("district")).html('<option value="">' + lang_js['select_district'] + '</option>');
                }
                if ($(this).data('ward')) {
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_ward'] + '</option>');
                }
            } else if (type == "province") {
                if ($(this).data("district")) {
                    imsLocation.loadLocation($(this), "district");
                }
                if ($(this).data('ward')) {
                    // $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_title'] + '</option>');
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_ward'] + '</option>');
                }
            }else if(type == "district") {
                if ($(this).data("ward")) {
                    imsLocation.loadLocation($(this), "ward");
                }
            }
        });
    },
    loadLocation: function (parent_html, type="ward") {
        var html_id = parent_html.data(type);
        if (!html_id) {
            return false;
        }
        loading('show');
        var parent_id = parent_html.val();
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "global", "f": "loadLocationWith", "type": type, "parent_id": parent_id}
        }).done(function (string) {
            loading('hide');
            var data = JSON.parse(string);
            if (data.ok == 1) {
                $('#' + html_id).html(data.html);
            }
        });
    },


    locationChanges: function (type, element) {
        $(document).on("change", element, function (e) {
            if (type == "area"){
                if ($(this).data("country")) {
                    imsLocation.loadLocations($(this), "country");
                }
                if ($(this).data("province")) {
                    $('#' + $(this).data("province")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
                if ($(this).data("district")) {
                    $("#" + $(this).data("district")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
                if ($(this).data('ward')) {
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
            } else if (type == "country") {
                if ($(this).data("province")) {
                    imsLocation.loadLocations($(this), "province");
                }
                if ($(this).data("district")) {
                    $("#" + $(this).data("district")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
                if ($(this).data('ward')) {
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
            } else if (type == "province") {
                if ($(this).data("district")) {
                    imsLocation.loadLocations($(this), "district");
                }
                if ($(this).data('ward')) {
                    $("#" + $(this).data("ward")).html('<option value="">' + lang_js['select_title'] + '</option>');
                }
            }else if(type == "district") {
                if ($(this).data("ward")) {
                    imsLocation.loadLocations($(this), "ward");
                }
            }
        });
    },
    loadLocations: function (parent_html, type="ward") {
        var html_id = parent_html.data(type)+'s';
        if (!html_id) {
            return false;
        }
        loading('show');
        var parent_id = parent_html.val();
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "global", "f": "loadLocationWith", "type": type, "parent_id": parent_id}
        }).done(function (string) {
            loading('hide');
            var data = JSON.parse(string);
            if (data.ok == 1) {
                $('#' + html_id).html(data.html);
            }
        });
    }
};
