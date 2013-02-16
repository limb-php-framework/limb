# lmbObservableCommand

lmbObservableCommand allows to register multiple commands onvoked on perform event. lmbObservableCommand :: registerOnPerformListener($listener) accepts and registers a listener - an object of WACT Delegate class.

Here is an example of lmbObservableCommand usage:

    $command = new lmbObservableCommand();
    $command->registerOnPerformListener(new Delegate($this, 'createNewsline'));
    $command->registerOnPerformListener(new Delegate(new CreateNewslineCommand, 'perform'));
    $command->registerOnPerformListener(new lmbCommandDelegate(new CreateNewslineCommand()));
    $command->perform();

Note: The last two lines of the example are equal. lmbCommandDelegate is used just for sugar syntaxing.

You can use any method with Delegate if the method doesn't need any parameters (like [lmbCommand](./lmb_command.md) :: perform()). There is a good example of events and listeners on [lmbFormCommand](./lmb_form_command.md) description page.
