<!-- BEGIN: main -->
{data.content}
<!-- END: main -->

<!-- BEGIN: main_content -->
<div class="form">
    <div class="pic_form" style="background: url('{data.form_img}') no-repeat center; background-size: cover"><img src="{data.form_img}" alt="img"></div>
    <div id="form">
        <div class="form_title">{LANG.support.form_title}</div>
        <form action="" method="post" id="support_form">
            <div class="form-group">
                <div class="select">
                    <select name="department" id="department">
                        <option value="">{LANG.support.department_select}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="full_name" placeholder="{LANG.support.full_name}">
            </div>
            <div class="form-group">
                <input type="text" name="email" placeholder="{LANG.support.email}">
            </div>
            <div class="form-group">
                <input type="text" name="phone" placeholder="{LANG.support.phone}">
            </div>
            <div class="form-group submit">
                <button type="button">{LANG.support.send}</button>
            </div>
        </form>
    </div>
</div>
{data.other_content}
{data.focus_news}
<div id="complete" style="display: none">
    <a href="{data.home_link}">
        <img src="{data.complete_picture}" alt="complete">
    </a>
</div>
<script>
    /*$("#support_form").validate({
        submitHandler: function () {
            var fData = $("#support_form").serializeArray();
            // loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "global", "f": "support_form", "data": fData, "lang_cur": lang}
            }).done(function (string) {
                var data = JSON.parse(string);
                loading('hide');
                if (data.ok == 1) {

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    });
                }
            });
            return false;
        }
    });*/
    $(document).on('click', '.form-group.submit button', function () {
        $.fancybox.open($('#complete'));
    });
</script>
<!-- END: main_content -->

<!-- BEGIN: partner -->
<div class="partner">
    <div class="list_item list_partner{data.group_id}">
        <!-- BEGIN: item -->
        <div class="item"><img src="{picture}" alt="partner"></div>
        <!-- END: item -->
    </div>
</div>
<script>
    $('.list_partner{data.group_id}').slick({
        autoplay: true,
        slidesToShow: 7,
        dots: false,
        arrows: false,
        swipeToSlide: true,
        rows: 2,
        responsive: [
            {
                breakpoint: 800,
                settings: {
                    slidesToShow: 6,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 5,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 4,
                }
            }
        ]
    });
</script>
<!-- END: partner -->

<!-- BEGIN: content -->
<div class="content_about">
    <div class="section_title">{data.title}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
            <div class="info">
                <div class="title">{row.title}</div>
                <div class="content">{row.content}</div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: content -->

<!-- BEGIN: about -->
<div class="about">
    <div class="section_title">{data.title}</div>
    <div class="list_item list_about{data.group_id}">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
            <div class="info">
                <div class="title">{row.title}</div>
                <div class="content">{row.content}</div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<script>
    $('.list_about{data.group_id}').slick({
        autoplay: true,
        slidesToShow: 4,
        dots: false,
        arrows: false,
        swipeToSlide: true,
        responsive: [
            {
                breakpoint: 1101,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 901,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });
</script>
<!-- END: about -->

<!-- BEGIN: support -->
<div class="support">
    <div class="left">
        <div class="title">{data.title}</div>
        {data.short}
        <div class="content">{data.content}</div>
        <div class="link_form"><a href="#form" class="goto">{LANG.support.send_message}</a></div>
    </div>
    <div class="picture"><img src="{data.picture}" alt="{data.title}"></div>
</div>
<!-- END: support -->

<!-- BEGIN: customer_review -->
<div class="customer_review">
    <div class="section_title">{LANG.support.customer_review_title}</div>
    <div class="wrap_list">
        <div class="list_item">
            <!-- BEGIN: item -->
            <div class="item">
                <div class="wrap_item">
                    <div class="img"><a href="{row.picture_zoom}" data-fancybox><img src="{row.picture}" alt="{row.title}"></a></div>
                    <div class="info">
                        <div class="title">{row.title}</div>
                        <div class="content">{row.content}</div>
                        <div class="name">{row.name}</div>
                        <div class="job">{row.job}</div>
                    </div>
                </div>
            </div>
            <!-- END: item -->
        </div>
    </div>
</div>
<script>
    $('.customer_review .list_item').slick({
        autoplay: false,
        slidesToShow: 1,
        dots: false,
        arrows: true,
        swipeToSlide: true,
        infinite: true
    });
</script>
<!-- END: customer_review -->

<!-- BEGIN: news -->
<div class="news">
    <div class="section_title">{LANG.support.news_title}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="wrap_item">
                <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
                <div class="info">
                    <div class="title"><a href="{row.link}">{row.title}</a></div>
                    <div class="short">{row.short}</div>
                </div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<script>
    $('.news .list_item').slick({
        autoplay: true,
        slidesToShow: 4,
        dots: false,
        arrows: false,
        swipeToSlide: true,
        responsive: [
            {
                breakpoint: 901,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });
</script>
<!-- END: news -->