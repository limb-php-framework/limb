# Tags
Tags — are elements of {{macro}} template that generates PHP code (in most cases).

Tags can have a *short* form like {{tag_name attrr1='value1'...att2='valueM'/}} and a full form with opening and closing tags {{tag_name attr1='value1'...attr2='valueM'}}..tag content..{{/tag_name}}

MACRO requires that tags must have both opening and closing tags in the same template file. You can't, for example, open a tag in one template and close it in another.

## Variables in attribute values

Now can use variables as tags attribute values. The simplest form is just using php variable as attribute value, like:

    {{list using='$photos'}} 
    or
    {{list using='$#photos'}}

It's also possible to use **composite** attribute values that consists of expression and literals, e.g.:

    {{input name='field_{$title}'/}} 
    or
    {{input name='my_{$title}_field'/}} 
