# How {{macro}} template gets data for displaying?
{{macro}} usually works with plain PHP scalar values or arrays. However, if you want your objects to be fully supported in {{macro}} as well, make sure that your object supports [ArrayAccess](http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html) (in case of accessing attributes using dotted syntax) or [Iterator](http://www.php.net/~helly/php/ext/spl/interfaceIterator.html) (in case of iteration).

There are two common ways to make some data appear inside a template:

* **push** — data is pushed into template externally with some simple interface like set($var, $name) or assign($var, $name).
* **pull** — template code fetches required data internally by using helpers or callbacks.

{{macro}} supports both of these approaches.

## Push approach
lmbMacroTemplate has **set($variable_name, $value)** method that sets global variable $variable_name in the template. This variable will be available in template as {$#variable_name} or {$this→variable_name} expressions.

For example:

    $macro = new lmbMacroTemplate('page.phtml');
    $macro->set('title', 'Hello');

To render 'title' in {{macro}} template we can use the following expression:

    {$#title}

There is also lmbMacroTemplate :: **setVars($associative_array)** that is equal to several set() method calls for every element of $associative_array. Note: setVars() removes previously assigned variables.

## Pull approach

You can also use arbitrary PHP blocks right in {{macro}} templates to fetch data, e.g.:

    <? $products = lmbActiveRecord :: find('Product'); ?>
    {{list using='$products' as="$product"}}
    <ul>
      {{list:item}}
        <li>{$product.title}</li>
      {{list:item}}
    </ul>
    {{/list}}

### Data from lmbController
If you create an application based on WEB_APP package of Limb3, you may be interested to know that template can access all attributes of the currently executed controller(just like you called lmbMacroTemplate :: set() for every controller attribute).

For example:

    class MyController extends lmbController
    {
      function doDisplay()
      {
        $this->title = 'Hello';
      }
    }

In the corresponding {{macro}} template $title attribute will be available as:

    {$#title}

## Contexts in {{macro}}
{{macro}} has only 2 contexts or data scopes:

* **global** — scope of the generated PHP class of template
* **local** — scope of one method of this class.

Rendering always starts with render() method of the generated PHP class. Some tags like [include](./tags/core_tags/include_tag.md) or [apply](./tags/core_tags/apply_tag.md) generate their code into separate methods and thus create other local scopes. {{include}} and {{apply}} allow to pass any variables into their local scopes with any number of extra attributes (see descriptions of these tags).

See also [output expressions](./expressions.md).
