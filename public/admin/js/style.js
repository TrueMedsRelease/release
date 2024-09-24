var selected_template_image_id = "";
var prev_template_image_id = "";
var cur_template_image_id = "";
function showImage(id){
    prev_template_image_id = cur_template_image_id;
    cur_template_image_id = id;
    $('#' + prev_template_image_id).css("display", "none");
    $('#' + cur_template_image_id).css("display", "");
}
function selectImage(id){
    selected_template_image_id = id;
    prev_checkout_template_image_id = cur_template_image_id;
    cur_template_image_id = selected_template_image_id;
}
function resetImage(){
    $('#' + cur_template_image_id).css("display", "none");
    $('#' + selected_template_image_id).css("display", "");
    cur_template_image_id = selected_template_image_id;
    prev_template_image_id = cur_template_image_id;
}