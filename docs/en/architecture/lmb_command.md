# Limb3 Commands
## Short description
Limb Commands implement a very simple interface which consists of the only **perform()** method. Commands are the main containers of an application specific logic.

## UML Static Structure
## Frequently used generic commands

Class name | Desription
-----------|-----------
lmbBaseCommand | Holdes cached Request, Response and [lmbToolkit](./lmb_toolkit.md) objects and allows to perform other commands via performCommand() method.
[lmbSetViewCommand](./lmb_set_view_command.md)	| Sets current view ([lmbSimpleView](./lmb_simple_view.md) object) into [lmbToolkit](./lmb_toolkit.md).
[lmbObservableCommand](./lmb_observable_command.md) | Allows to register onPerform event listeners(when command is performed listeners are notified)
[lmbPropertyOptionalCommand](./lmb_property_optional_command.md) | Is a child of lmbObservableCommand which notify listeners if a dataspace contains a specified property. It accepts dataspace and property name in constructor. This class is mostly used in multi-action forms. See [lmbFormCommand](./lmb_form_command.md) description page for a usage example.
[lmbFormCommand](./lmb_form_command.md) | Processes forms, allows to register onPerform, onInit, onValid and onNotValid listeners, validates user input and holds a dataspace with data received from user. Used jointly with [lmbFormView](./lmb_form_view.md).
