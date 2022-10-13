<!-- BEGIN: main -->
{data.content}
<!-- END: main --> 

<!-- BEGIN: list_item -->
    <!-- BEGIN: row_item -->    
    <div class="item"><a href="{row.link}" class="item"><img src="{row.picture}" alt="{row.title}" title="{row.title}">
        <h3>{row.title}</h3>
        {row.price}
    </a></div>
    <!-- END: row_item --> 
    <!-- BEGIN: row_empty -->
    <div class="row_empty">{row.mess}</div>
    <!-- END: row_empty --> 
<!-- END: list_item --> 