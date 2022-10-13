<!-- BEGIN: checkin -->
<div id="event_checkin">
    <h1>{data.page_title}</h1>    
    <div class="box-info">
        <div class="picture"><img src="{data.picture}" alt="{data.title}" data-zoom="{data.picture_src}" ></div>
        <div class="info">
            <div class="title">{data.title}</div>
            <div class="address">{data.address}</div>
            <div class="datetime">{data.date_begin}</div>
        </div>
    </div>
    <div class="content">
        <button type="button" class="qrcode-reader" id="openreader-single" 
            data-qrr-target="#single" 
            data-qrr-audio-feedback="false" 
            data-qrr-qrcode-regexp="^https?:\/\/">Read QRCode</button>
        <br>
        <div id="video-container">
            <div class="inner">
                <video id="qr-video" disablepictureinpicture="" playsinline=""></video>
                <div class="scan-region-highlight">
                    <svg class="scan-region-highlight-svg" viewBox="0 0 238 238" preserveAspectRatio="none" style="fill:none;stroke:#e9b213;stroke-width:4;stroke-linecap:round;stroke-linejoin:round"><path d="M31 2H10a8 8 0 0 0-8 8v21M207 2h21a8 8 0 0 1 8 8v21m0 176v21a8 8 0 0 1-8 8h-21m-176 0H10a8 8 0 0 1-8-8v-21"></path></svg><svg class="code-outline-highlight" preserveAspectRatio="none" style="display:none;width:100%;height:100%;fill:none;stroke:#e9b213;stroke-width:5;stroke-dasharray:25;stroke-linecap:round;stroke-linejoin:round"><polygon></polygon>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    // overriding path of JS script and audio 
    $.qrCodeReader.jsQRpath = "{data.link_jsQR}";
    $.qrCodeReader.beepPath = "{data.link_audio}";

    // bind all elements of a given class
    $(".qrcode-reader").qrCodeReader();
});
</script>
<!-- END: checkin -->