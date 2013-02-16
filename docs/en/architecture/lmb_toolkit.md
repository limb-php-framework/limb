# lmbToolkit

Any web-application has several objects which should be shared among many components and have global meaning. For example, Request, Response, data base connection, different class factories, etc.

Some developers make these classes global, some implement them as Singletons, some pass them across the entire application. We prefer not not use Singletons or any of these methods since they cause difficulties for unit testing.

There are nice alternatives to Singleton pattern — one of them called lmbService Locator patten was implemented in Limb as a lmbToolkit class.lmbService Locator pattern is one of the Inversion of Control techniques, you can read more about it in the following articles:

* http://martinfowler.com/articles/injection.html
* http://java.sun.com/blueprints/corej2eepatterns/Patterns/ServiceLocator.html

The key idea of lmbService Locator pattern is to provide an easy-accessible way for clients to get frequently used services and objects.

Here is an example of lmbToolkit usage:

    $toolkit = lmbToolkit :: instance();
    $dao = $toolkit->createDAO('ArticleDAO');

Or more compact:

    $dao = lmbToolkit :: instance()->createDAO('ArticleDAO');

lmbToolkit internal state can be saved and restored to simplify unit testing. When you save lmbToolkit a new fresh copy (new instance) of lmbToolkit is created and the old one is saved in [lmbRegistry](../../../toolkit/docs/en/toolkit/lmb_registry.md). When you restore lmbToolkit the current instance is removed and the previous copy is become active. You can save/restore lmbToolkit multiple times.

    $toolkit = lmbToolkit :: save();
 
    $tree = $toolkit->getTree();
    $dao = $toolkit->createDAO('SomeDAO');
   
    lmbToolkit :: restore();

In fact, lmbToolkit looks up an appropriate tool for any method invokation and delegates this method to the tool. Every tool should implement lmbToolkitTools interface:

    interface lmbToolkitTools
    {
      function getToolsSignatures();
    }

getToolsSignatures() must return an array of methods that implementor supports. This array has the following structure:

    array('method1' => $implementor,
          'method2' => $implementor);

You can easily add, substitute, merge tools inside lmbToolkit:

* lmbToolkit :: **setup($tools)** — replaces the current set of tools with new tools.
* lmbToolkit :: **merge($tools)** — merges the current set of tools with new tools. The new tools have more priority than the old one. It proved to be very useful for unit testing.
* lmbToolkit :: **extend($tools)** — extends the current set of tools with new tools. However if the current set of tools has a method that also supported by new tools an exception is thrown.

[lmbBaseTools](../../../toolkit/docs/en/toolkit/lmb_base_tools.md) is registered in lmbToolkit by default.

## Other lmbToolkit sub-system classes

Class name | Description
-----------|------------
[lmbBaseTools](../../../toolkit/docs/en/toolkit/lmb_base_tools.md) | Contains the most common factory methods and objects for a web based application such as Request, Response, DbConnection, etc.
[lmbStaticTools](../../../toolkit/docs/en/toolkit/lmb_static_tools.md) | Defines a static set of tools. Useful with unit testing.
[lmbAbstractTools](../../../toolkit/docs/en/toolkit/lmb_abstract_tools.md) | An abstract class that returns all class methods from getToolsSignatures(). It's common to inherit from this class in order to create your own tools.
[lmbMockToolsWrapper](../../../toolkit/docs/en/toolkit/lmb_mock_tools_wrapper.md) | Allows to use mock objects with lmbToolkit.
[lmbCompositeToolkitTools](../../../toolkit/docs/en/toolkit/lmb_composite_toolkit_tools.md) | Allows to compose several tools within one container.
[lmbCompositeNonItersectingToolkitTools](../../../toolkit/docs/en/toolkit/lmb_composite_non_itersecting_toolkit_tools.md) | A child of lmbCompositeToolkitTools class. This class prevents tools from intersection.
