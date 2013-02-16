# Using PHP code in {{macro}} templates
As you may know {{macro}} compiles initial templates into a regular PHP class. That's why you can insert any PHP blocks into {{macro}} templates, e.g.:

    <select name='my_selector'>
     <?php 
      foreach($this->items as $item) 
      {
        echo '<option value="'. $item['value'] . '" ';
         if(some_condition())
          echo 'selected';
        echo '>'. $item['title'] .'</option>';
      }
     ?>
    </select>

You should be aware that any PHP block you insert into {{macro}} template will be placed into one of the methods of the generated class. In what exactly method is depending on if any of {{include}}, {{into}}, {{apply}} or {{wrap}} tags were used. That's why if you need some particular variables in your PHP block you need to make them available globally (make them attributes of the generated class) or pass them into the local scope using extra attributes of the mentioned tags.

## When it's OK to use PHP code in {{macro}} templates
Here is the list of situations when we prefer to use raw PHP code in {{macro}} templates:

* Conditional logic (if/else). {{macro}} does not have analogs of WACT <core:optional> and <core:default> tags at the moment(and probably never will)
* Complex computations (remember though: don't place business logic into templates).
* Any non-standard situations when built-in {{macro}} tags are don't suit and you feel lazy creating your own {{macro}} tags.
