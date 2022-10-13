<!-- BEGIN: main -->
{data.content}
<!-- END: main -->


<!-- BEGIN: concern -->
<div class="list_concern box_item">
    <h1 class="title">{LANG.product.concern_title}</h1>
    <div class="header_content">
        <div class="search">
            <form action="" method="get">
                <div class="form-group"><input type="text" name="keyword" placeholder="{LANG.product.text_keyword_concern}" value="{data.text_search}"></div>
                <div class="form-group">{data.list_province}</div>
                <div class="form-group"><button type="submit">{LANG.product.search}</button></div>
                <div class="form-group"><div class="reset"><a href="{link_action}"><img src="{CONF.rooturl}resources/images/use/reset.svg" alt="reset"></a></div></div>
            </form>
        </div>
        <ul class="list_none list_action_root">
            <li><button class="delete">{LANG.product.delete}</button></li>
            <li><button class="default active"><a href="{link_action}/?act=add"><i class="fal fa-plus-circle"></i>{LANG.product.add_button}</a></button></li>
        </ul>
    </div>
    <div class="wrap_table">
        <table class="table-responsives">
            <thead>
            <tr>
                <th scope="col" width="62"><div class="checkbox"><input type="checkbox" class="check_all" id="check_all"><label for="check_all"></label></div></th>
                <th scope="col" width="88">{LANG.product.concern_logo}</th>
                <th scope="col" width="auto">{LANG.product.concern_name}</th>
                <th scope="col" width="122">{LANG.product.concern_mst}</th>
                <th scope="col" width="120">{LANG.product.address}</th>
                <th scope="col" width="auto">{LANG.product.website}</th>
                <th scope="col" width="130">{LANG.product.phone}</th>
                <th scope="col" width="120">{LANG.product.action}</th>
            </tr>
            </thead>
            <tbody>
            <!-- BEGIN: item -->
            <tr class="{row.item_id}">
                <td><div class="checkbox"><input type="checkbox" id="check_{row.stt}"><label for="check_{row.stt}"></label></div></td>
                <td><img src="{row.picture}" alt="{row.title}" width="60"></td>
                <td>{row.title}</td>
                <td>{row.tax_number}</td>
                <td>{row.add}</td>
                <td><a href="{row.website}" style="color: #224893; text-decoration: underline" target="_blank" title="{row.website}" class="cut_text">{row.website}</a></td>
                <td>{row.phone}</td>
                <td>
                    <ul class="list_action list_none">
                        <li><a href="{row.link_action}/?act=edit&item={row.item_id}" class="edit"><img src="{CONF.rooturl}resources/images/use/edit.svg" alt="edit"></a></li>
                        <li><button class="delete"><img src="{CONF.rooturl}resources/images/use/delete.svg" alt="delete"></button></li>
                    </ul>
                </td>
            </tr>
            <!-- END: item -->
            </tbody>
        </table>
        <!-- BEGIN: empty -->
        <div class="empty">{LANG.product.no_have_item}</div>
        <!-- END: empty -->
    </div>
    {nav}
</div>
<script>
    $(document).on('click', '.list_action button.delete', function () {
        Swal.fire({
            title: 'Xóa doanh nghiệp?',
            text: 'Bạn có chắc chắn muốn xóa doanh nghiệp này? Sau khi xóa sẽ không khôi phục lại được.',
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.global.cancel}',
            confirmButtonText: '{LANG.global.yes_delete}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var it = $(this).parent().parent().parent().parent().attr('class');
                // loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "product", "f" : "delete_concern", 'item':it, 'lang_cur': lang}
                }).done(function( string ) {
                    loading('hide');
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: data.mess,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
            }
        })
    });
    $(document).on('click', '.list_action_root button.delete', function () {
        Swal.fire({
            title: 'Xóa các doanh nghiệp?',
            text: 'Bạn có chắc chắn muốn xóa các doanh nghiệp đã chọn? Sau khi xóa sẽ không khôi phục lại được.',
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.global.cancel}',
            confirmButtonText: '{LANG.global.yes_delete}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var arit = [];
                $('.table-responsives tbody .checkbox input').each(function (i, v) {
                    if($(this).is(':checked')){
                        arit.push($(this).parent().parent().parent().attr('class'));
                    }
                });
                // console.log(arit);
                // loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "product", "f" : "delete_list_concern", 'arit':arit, 'lang_cur': lang}
                }).done(function( string ) {
                    loading('hide');
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: data.mess,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
            }
        })
    });
</script>
<!-- END: concern -->

<!-- BEGIN: add_concern -->
<div class="add_product box_item">
    <h1 class="title">{LANG.product.add_concern_title}</h1>
    <form action="" method="post" id="add_concern">
        <div class="row">
            <div class="form-group col-12">
                <div class="input_picture">
                    <div class="input_title">{LANG.product.concern_picture}</div>
                    <div class="input_view">
                        <input type="file" name="picture" id="photo-add" class="inputfile inputfile-1" accept="image/*" value="">
                        <div class="photo-input {data.show_photo}">
                            <!-- BEGIN: picture -->
                            <img src="{data.src}">
                            <input type="hidden" name="picture_available" value="{data.src_ori}">
                            <!-- END: picture -->
                        </div>
                        <label for="photo-add" class="add_photo"></label>
                    </div>
                </div>
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.concern_name} (<span class="required">*</span>)</label>
                <input type="text" name="title" placeholder="{LANG.product.concern_name}" value="{data.title}" required>
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.concern_mst}</label>
                <input type="text" name="tax_number" value="{data.tax_number}" placeholder="{LANG.product.concern_mst}">
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.concern_country}</label>
                {data.list_country}
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.global.select_province} (<span class="required">*</span>)</label>
                {data.list_province}
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.global.select_district} (<span class="required">*</span>)</label>
                {data.list_district}
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.global.select_ward} (<span class="required">*</span>)</label>
                {data.list_ward}
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.address} (<span class="required">*</span>)</label>
                <input type="text" name="address" value="{data.address}" placeholder="{LANG.product.address}" required>
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.phone}</label>
                <input type="text" name="phone" value="{data.phone}" placeholder="{LANG.product.phone}">
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.email}</label>
                <input type="text" name="email" value="{data.email}" placeholder="{LANG.product.email}">
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.website}</label>
                <input type="text" name="website" value="{data.website}" placeholder="{LANG.product.website}">
            </div>
            <div class="submit form-group col-12 text-right">
                {data.edit_item}
                <button type="button" class="cancel">{LANG.product.cancel}</button>
                <button type="submit" class="submit">{LANG.product.save}</button>
            </div>
        </div>
    </form>
</div>
<script>
    imsLocation.locationChange("country", ".select_location_country");
    imsLocation.locationChange("province", ".select_location_province");
    imsLocation.locationChange("district", ".select_location_district");
    $(document).on('change', 'input[name=layout]', function () {
        $('.list_layout li .item').removeClass('checked');
        $(this).parent().parent().addClass('checked');
    });
    // Hiển thị 1 ảnh khi click chọn thêm ảnh store
    var imagesPreviewOne = function(input, placeToInsertImagePreview){
        if (input.files) {
            $(placeToInsertImagePreview).html('').addClass('show');
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    $(document).on('change', '#photo-add', function() {
        imagesPreviewOne(this, 'div.photo-input');
    });

    // Thêm sản phẩm
    $("#add_concern").validate({
        submitHandler: function() {
            formData = new FormData($("#add_concern")[0]);
            formData.append("f", "add_edit_concern");
            formData.append("m", "product");
            formData.append("lang_cur", lang);
            // loading('show');
            $.ajax({
                type: 'POST',
                url: ROOT+"ajax.php",
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
            }).done(function( string ) {
                loading('hide');
                var data = JSON.parse(string);
                if(data.ok == 1) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        text: data.mess,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    })
                }
            });
            loading('hide');
            return false;
        }
    });
</script>
<!-- END: add_concern -->