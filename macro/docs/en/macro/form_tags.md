# Forms and form elements
{{macro}} has basic aids to auto populate form fields with previously sent data. There are also tags for generating <select> tags with options and for rendering validation errors.

Here is the list of the most commonly used {{macro}} tags that can be helpful while dealing with forms:

* [Tag {{form}}](./tags/form_tags/form_tag.md) — an analog of regular html <form> tag . Acts as a data container for child tags, holds initial or posted data as well as server-side validation errors.
* [Tag {{form:errors}}](./tags/form_tags/form_errors_tag.md) — used to extract validation errors from parent {{form}} tag into a local variable that can be used in {{list}} tag to render validation errors.
* [Tag {{input}}](./tags/form_tags/input_tag.md) — an analog of regular html <input> tag. The functionality of {{input}} tag depends on its «type» attribute.
* [Tag {{select}}](./tags/form_tags/select_tag.md) — an analog of regular html <select> tag. Renders options list and marks selected options. The tag supports both single and multiple selections.

## How to use {{macro}} form tags
Let's consider a small example in order to demonstrate the following features:

* How to auto populate form fields using {{macro}} form tags
* How to render server-side validation errors
* How to render <select> tag with {{macro}} {{select}} tag
* How to mix {{macro}} form tags and regular html form tags

Suppose we have a template:

    {{form name="my_form" method="POST"}}
 
    {{form:errors to='$fields_errors'/}}
 
    {{list using='$fields_errors'}}
    Errors:
    <ol class="form_errors">
      {{list:item}}
        <li>{$item.message}</li>
      {{/list:item}}
    </ol>
    {{/list}}
 
    <table>
    <tr>
    <td>{{label for="title" error_class="error"}}Title{{/label}}</td>
    <td>{{input type="text" id="title" name="title" title="Title" error_class="error"/}}</td>
 
    <?php $types = array(array('id' => 10, 'name' => 'TypeA'), array('id' => 20, 'name' => 'TypeB')); ?>
 
    {{select_options_export from="$types" to="$types_as_options" key_field="id" text_field="name"/}}
 
    <td><label for="type">Select type</label></td>
    <td>{{select name="type" id="type" title="Type" options="$types_as_options" error_style="style_of_error"/}}</td>
 
    <tr>
      <td colspan='2'><input type="submit" value='Run' /></td>
    </tr>
    </table>
    <input type="hidden" name="action" value='create'/>
    {{/form}}

We need also a PHP script to run this template:

    <?php
    set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                    'path/to/limb/' . PATH_SEPARATOR .
                    get_include_path());
 
    require_once('limb/macro/common.inc.php');
 
    $config = new lmbMacroConfig($cache_dir = dirname(__FILE__ ) . '/cache/',
                                 $is_force_compile = false,
                                 $is_force_scan = false);
 
    $macro = new lmbMacroTemplate('form.phtml', $config);
 
    if(count($_POST))
    {
      $error_list = array();
      $error_list[] = array('message' => 'Error in {field}', 'fields' => array('field' => 'title'));
      $error_list[] = array('message' => 'Other error in {field}', 'fields' => array('field' => 'type'));
 
      $macro->set('form_my_form_error_list', $error_list); 
      $macro->set('form_my_form_datasource', $_POST); 
    }
 
    echo $macro->render();
    ?>

Let's try to run this PHP script under web-server. For the first time you should get the following html:

    <form name="my_form" method="POST">
 
    <table>
    <tr>
    <td><label for="title">Title</label></td>
    <td><input type="text" id="title" name="title" title="Title" value="" /></td>
 
    <td><label for="type">Select type</label></td>
    <td><select name="type" id="type" title="Type"><option value="10">TypeA</option><option value="20">TypeB</option></select></td>
 
    <tr>
      <td colspan='2'><input type="submit" value='Run' /></td>
    </tr>
    </table>
    <input type="hidden" name="action" value='create'/>
    </form>

Now you can try to fill some of the form fields and submit the form. As a result you should get something like this:

    <form name="my_form" method="POST">
 
    Errors:
    <ol class="form_errors">
        <li>Error in Title</li>
        <li>Other error in Type</li>
    </ol>
 
    <table>
    <tr>
    <td><label for="title" class="error">Title</label></td>
    <td><input type="text" id="title" name="title" title="Title" class="error" value="fds" /></td>
 
    <td><label for="type">Select type</label></td>
    <td><select name="type" id="type" title="Type" style="style_of_error"><option value="10">TypeA</option><option value="20" selected="true">TypeB</option></select></td>
 
    <tr>
      <td colspan='2'><input type="submit" value='Run' /></td>
    </tr>
    </table>
    <input type="hidden" name="action" value='create'/>
    </form>

Please note that {{macro}} [tag {{label}}](./tags/form_tags/label_tag.md) rendered an html <label> tag with «error» css-class attribute since there was a validation error for the corresponding form field.( [fields ⇒ array('field' ⇒ 'title')]) and since **error_class** attribute of {{label}} tag was used. The same feature was used for {{input}} tag.

The next two lines are used to pass data into our {{form}} tag:

    $macro->set('form_my_form_error_list', $error_list); 
    $macro->set('form_my_form_datasource', $_POST); 

* form_xxx_datasource — holds data that {{macro}} form tags can use to auto populate their values.
* form_xxx_error_list — holds server-side validation errors list. Validation errors list is a object of lmbMacroFormErrorList or just a regular array.

xxx — is either **id** attribute or **name** attribute of {{form}} tag.

If you use {{macro}} package with VIEW package you should use lmbMacroView :: **useForm($form_id)**, lmbMacroView :: **setFormDatasource($datasource, $form_id)** to pass data into {{macro}} forms.

## Notes
It's up to you to use {{macro}} form tags or not. You can always use something like <input type='text' name=«title» value=»{$object.title}»> as a simplest way of fields auto population.
