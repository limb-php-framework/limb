function toggle_selected(toggle_obj)
{
  var parent_form = toggle_obj.form;
  var mark = toggle_obj.checked;

  jQuery("input:checkbox[@name='ids[]']", parent_form).each(function(){
                                        jQuery(this).attr("checked", mark);
                                  });
}

function changed_field_highlighter()
{
  jQuery(this).change(function(){jQuery(this).prev('label').css({color: 'green'})});
}


