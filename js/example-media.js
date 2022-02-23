

function changeExample(){
    jQuery("[example]").each(function(){
        var text = jQuery(this).attr("example");
        var id= jQuery("#exemple-id").val();
        text  = text.replace("#horse-id#", id);
        jQuery(this).text(text);
    });
}

jQuery(document).ready(function () {
    changeExample();
    jQuery("#exemple-id").change(changeExample);
    jQuery("#exemple-id").keyup(changeExample);
    jQuery("#exemple-id").keydown(changeExample);
   
});