imsStatistic = {
    get_config: function () {

        //Localhost
        imsStatistic.root = ROOT;
        //Website
        //imsStatistic.root = "";

        imsStatistic.config_default = ({ "full_zero": true, "split_char": true });
        imsStatistic.config = (imsStatistic.config) ? imsStatistic.config : imsStatistic.config_default;
        for (x in imsStatistic.config_default) {
            imsStatistic.config[x] = (imsStatistic.config[x]) ? imsStatistic.config[x] : imsStatistic.config_default[x];
        }

        imsStatistic.aVal = ({});
        imsStatistic.htmlout = ({});

        imsStatistic.oItem = ({ "sonline": "sonline", "syesterday": "syesterday", "sday": "sday", "sweeklast": "sweeklast", "sweek": "sweek", "smonthlast": "smonthlast", "smonth": "smonth", "syearlast": "syearlast", "syear": "syear", "stotal": "stotal" });
        for (x in imsStatistic.oItem) {
            var tmp = document.getElementById("ims-" + imsStatistic.oItem[x]);
            if (tmp) {
                imsStatistic.htmlout[x] = tmp;
                //eval("imsStatistic.htmlout."+x+" = "+tmp+";");
                eval("imsStatistic.aVal." + x + " = '';");
                //console.log(imsStatistic.htmlout[x]);
            } else {
                delete imsStatistic.oItem[x];
            }
        }

        //console.log('htmlout='+imsStatistic.htmlout['sonline']);

        if (window.encodeURIComponent)
            imsStatistic.imsEscape = encodeURIComponent;
        else if (window.encodeURI)
            imsStatistic.imsEscape = encodeURI;
        else
            imsStatistic.imsEscape = escape;
    },
    screenWidth: function () {
        if (window.screen) {
            return (screen.width);
        } else {
            return (0);
        }
    },
    screenHeight: function () {
        if (window.screen) {
            return (screen.height);
        } else {
            return (0);
        }
    },
    get_client: function () {
        var str_info = "";

        var screenWidth = imsStatistic.screenWidth();
        str_info += "&screen_width=" + screenWidth;

        var screenHeight = imsStatistic.screenHeight();
        str_info += "&screen_height=" + screenHeight;

        str_info += "&referrer_link=" + imsStatistic.imsEscape(document.referrer);

        return str_info;
    },
    out_statistic: function () {
        //console.log('called');
        var outItem = ({});

        for (x in imsStatistic.oItem) {
            if (imsStatistic.aVal.stotal && imsStatistic.config.full_zero == true) {
                if (x != 'stotal') {
                    var tmplength = String(imsStatistic.aVal[x]).length;
                    for (var i = tmplength; i < String(imsStatistic.aVal.stotal).length; i++) {
                        imsStatistic.aVal[x] = "0" + imsStatistic.aVal[x];
                    }
                }
            }
            outItem[x] = imsStatistic.aVal[x];

            //console.log('outItem[x]='+outItem[x]);

            if (imsStatistic.config.split_char == true) {
                var tmpArr = String(outItem[x]).split("")
                outItem[x] = '';
                for (i in tmpArr) {
                    outItem[x] += '<span class="ims-num_' + tmpArr[i] + '">' + tmpArr[i] + '</span>';
                }
            }

            if (imsStatistic.htmlout[x]) {
                imsStatistic.htmlout[x].innerHTML = outItem[x];
            }
        }

        setTimeout(function () {
            imsStatistic.out_statistic();
        }, 1000);

        return true;
    },
    do_statistic: function () {

        var str_info = imsStatistic.get_client();
        //alert(str_info);

        var str_rand = Math.floor((Math.random() * 1000) + 1);
        //image_tmp = new Image();
        //image_tmp.src = imsStatistic.root+"ajax/statistic.php?do=statistic"+str_info+"&rand="+str_rand;   
        var url = imsStatistic.root + "ajax.php?m=statistic&f=statistic" + str_info + "&rand=" + str_rand;

        if (document.getElementById('ims-statistic')) {
            imsStatistic.oBody = document.getElementById('ims-statistic');
        } else {
            imsStatistic.oBody = document.createElement('div');
            imsStatistic.oBody.id = "ims-statistic";
            imsStatistic.oBody.style = "width:0px; height:0px; overflow:hidden";
            document.body.appendChild(imsStatistic.oBody);
        }

        if (document.getElementById("ims-statistic_out")) {
            imsStatistic.oBody.removeChild(imsStatistic.oScript);
        }

        imsStatistic.oScript = document.createElement("script");
        imsStatistic.oScript.id = "ims-statistic_out";
        imsStatistic.oScript.type = "text/javascript";
        imsStatistic.oScript.src = url;
        imsStatistic.oBody.appendChild(imsStatistic.oScript);

        //        setTimeout(function () {
        //            imsStatistic.out_statistic();
        //        }, 100);
    }

};
    imsStatistic.get_config();
    imsStatistic.do_statistic();
    imsStatistic.out_statistic();