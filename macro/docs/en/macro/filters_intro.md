# Filters. How to modify or format data on rendering.
Filters are used to modify or to apply some special formatting rules to variables in [expressions](./expressions.md). Filters are separated from other parts of an expression by vertical line. Several filters can be combined into a «pipe»:

    {$UserName|trim|capitalize}

In the example above, *trim* filter first removes spaces from the begin and end of the $UserName variable and then *capitalize* filter converts first letter to uppercase. In the compiled PHP file we will get something like this:

    <?php echo ucfirst(trim($UserName)); ?>

## Filters parameters

Filters may also require additional information called **parameters**. The syntax for specifying parameters is:

    {$UserName|default:"anonymous"}

If no value is specified for the UserName variable, then the value of «anonymous» is used. A parameter may be either a number constant, a string constant, or a variable.

Multiple parameters can be specified separated by commas:

    {$value|number:2, '. ', ' '}

Here is an example of using of a variable as a filter parameter:

    <? $size = 10; ?>
    <pre>{$PlainText|wordwrap:$size}</pre>

In most cases macro filters are just wrappers for PHP function like trim, strtoupper, number_format etc. Default macro filters can be found in limb/macro/src/filter folder.
