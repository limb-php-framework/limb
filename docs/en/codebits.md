# LImb3 CodeBits
## Limb3 Based Applications
### Syncman — painless remote projects synchronization utility
Syncman is an application which simplifies projects remote deployment and synchronization by providing both nice web UI(great for managers and other non-technical personnel) and basic shell interface.

Features:

* Nice web UI for non-technical personnel
* Simple file based projects configuration
* Public keys infrastructure for secure passwordless authentication
* Efficient rsync based synchronization(but not limited to rsync)
* Subversion integration
* Pre- and Post-syncing hooks support
* Shell based interface

### Buildman — simplistic Continuous Integration tool
Buildman is a simple tool which helps to easily establish a Continuous Integration process for your applications.

Features:

* Simple file based configuration for CI projects(no XML)
* Shell based build process invocation
* Build errors mail notifications
* Subversion repository support
* Customizable layout templates

Both applications are in alpha state and there are no file releases yet. You can download the source code for both applications only via svn. However we have been using these applications for quite some time and they proved to be quite useful and stable.

### limb_unit — advanced SimpleTest tests runner utility
limb_unit is similar in some ways to phpunit utility from PHPUnit library, yet more powerful we believe.

The main features of limb_unit are:

* Can run single tests as well as tests under specified directory recursively
* Hierarchical tests fixtures
* Conditional tests execution
* Tests code coverage

## Limb3 Usage Examples
### CRUD example appication

* Browse online
* Shows how to create a simple Limb3 based project from the scratch.
* Demonstrates how to create, read, update and delete table records using WEB_APP package in Rails like way.
* Introduces powerful [MACRO](../../macro/docs/en/macro.md) template engine and shows how to use the most useful template tags.
* [Browse source in repository](http://code.google.com/p/limb3/source/browse?repo=code-bits#hg/crud)
* Checkout from Mercurial repository(run in shell). Directory **crud**:

        hg clone https://code-bits.limb3.googlecode.com/hg/ limb3-code-bits

### Shop example appication

* Browse online
* The idea of this example is based on Depot application from «Agile Web Development with Ruby on Rails» book but was slightly modified to reveal some Limb3 specific features.

Shows many architectural aspects of Limb3: filter chain, toolkit, session handling, controllers, commands, etc.

* Demonstrates how ACTIVE_RECORD handles different relations between objects(one-to-many, one-to-one, many-to-many).
* Integration of several main Limb3 packages like [WEB_APP](../../web_app/docs/en/web_app.md), [ACTIVE_RECORD](../../active_record/docs/en/active_record.md) and [MACRO](../../macro/docs/en/macro.md).
* [Browse source in repository](http://code.google.com/p/limb3/source/browse/?repo=code-bits#hg/tutorial-shop)
* Checkout from Mercurial repository(run in shell). Directory **tutorial-shop**:

        hg clone https://code-bits.limb3.googlecode.com/hg/ limb3-code-bits

### {{macro}} template engine usage examples

* Browse online
* Runnable examples for mostly all core [MACRO](../../macro/docs/en/macro.md) tags.
* Templates source code, PHP script code and result page are available for every example.
* [Browse source in repository](http://code.google.com/p/limb3/source/browse/?repo=code-bits#hg/macro)
* Checkout from repository(run in shell). Directory macro:

        hg clone https://code-bits.limb3.googlecode.com/hg/ limb3-code-bits

### Old limb-project.com site source

* limb-project.com old site source
* [Browse source in repository](http://code.google.com/p/limb3/source/browse/?repo=code-bits#hg/limb-project)
* Checkout from repository(run in shell). Directory **limb-project**:

        hg clone https://code-bits.limb3.googlecode.com/hg/ limb3-code-bits
