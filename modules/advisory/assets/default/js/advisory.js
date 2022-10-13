imsAdvisory = {
    load_more : function(){
        loading('show');
        var num_cur = $('input[name="start"]').val();
        var group_id = $('.list_advisory').attr("data-group");

        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"advisory", "f":"load_more", 'num_cur':num_cur, 'group_id':group_id}
        }).done(function (string) {
            var data = JSON.parse(string);
            $('.list_advisory .list_item').append(data.html);

            if(data.num > 0){
                $('input[name="start"]').val(data.num);
            }else{
                $('.view_more').remove();
            }
            loading('hide');
        });
    }
}