# Limb3 Services
## Short description
lmbService — is a group of Actions, related to some domain or functional area. For example, for user authentication we can create and use Login lmbService, to work with site structure - SiteStructure lmbService, etc.

Action — is a functional unit of a Limb-based application. Action always belongs to some lmbService.

Services are the first candicates for customization in every Limb-based applications.

**lmbService** class accepts name, service specific properties and actions properties in constructor:

    $service = new lmbService('404', 
                           array('display' => array('command' => LPKG_CORE_DIR . '/src/command/lmbSet404ErrorViewCommand')),
                           arrray('default_action' => 'display',
                                  'filter' => 'simple'));

You can create lmbService object using [lmbToolkit](./lmb_toolkit.md) :: **createService($name)** method. createService($name) is supported by [lmbBaseTools](../../../toolkit/docs/en/toolkit/lmb_base_tools.md) class.

## How services are defined?
**lmbBaseTools :: createService($name)** delegates lmbService creation to a service factory object. lmbServiceFactory uses ini-files to load lmbService information. lmbService ini-files can be found in /settings/services/ folder of Limb-based application.

lmbService ini-files has .service.ini suffix. For example, Login.service.ini, SiteStructure.service.ini.

Here is an example of service ini-file:

    [display]
    command = {lmb_TEXT_PAGE_DIR}/command/lmbTextPageDisplayCommand

    [create]
    command = {lmb_TEXT_PAGE_DIR}/command/lmbTextPageCreateCommand
    jip = true
    icon = create.gif

    [edit]
    command = {lmb_TEXT_PAGE_DIR}/command/lmbTextPageEditCommand
    jip = true
    icon = edit.gif

    [delete]
    command = {lmb_TEXT_PAGE_DIR}/command/TextPageDeleteCommand
    jip = true
    icon = delete.gif
