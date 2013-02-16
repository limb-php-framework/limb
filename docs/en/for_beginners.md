# If You're New To Limb3...
## General description
Limb3 is not a solid framework like Symfony, for example, it's rather a library which consists of the set of relatively independent [packages](./packages_architecture.md), each designed to solve some specific problem.

Limb3 must be installed in the folder accessible within get_include_path(). You can either edit your php.ini file or change the include_path right in some setup file of the project:

    set_include_path('path/to/limb/installation' . PATH_SEPARATOR . get_include_path());

'path/to/limb/installation' should point to the folder where Limb is located, but not the folder with Limb3 packages itself, e.g. **/var/dev/lib/** instead of **/var/dev/lib/limb/**.

## Download Limb3
Probably the easiest way to download the Limb3 library is to grab the bundle release of all packages from [Limb3 SourceForge releases section](http://sourceforge.net/projects/limb/files/limb3/). The downside of this approach is that bundle releases are made not very often(once in a month or two).

The other way is to download specific packages via Limb3 PEAR channel. Using the channel you can get the latest releases without having to wait for the next SourceForge bundled release. The most straight-forward way to grab all packages at once is to use virtual **BUNDLE** package, e.g:

    $ pear channel-discover pear.limb-project.com  
    $ pear install limb/bundle-beta

If you're a 'bleeding edge' fan you can grab packages from nightly builds server or directly from Subversion repository. See section [Where and how to download Limb3](./how_to_download.md) for more details.

## Tutorials for the beginners
To become familiar with Limb3 it makes sense to read the following step-by-step tutorials:

* [basic example](./tutorials/basic.md), where the most basic ways of WEB_APP, ACTIVE_RECORD and WACT package usage are shown.
* [example of building a simple e-shop](./tutorials/shop.md), where the majority of common Limb3 instruments are explained.

The basic example can also be found it the limb/web_app/examples/crud folder. Also there is an empty «template» project for a Limb3-based project in the same folder.

Other examples are found in the SVN repository (nowhere else so far):

    svn co https://svn.limb-project.com/limb/3.x/examples

Checking out sources from the above address you'll get:

* http://limb-project.com main site sources based on Limb3, of course
* simple e-shop example
* simple corporate example site with a catalog.

Examples of WACT templating system usage are in the limb/wact/examples folder.

## Documentation
Limb3 English documentation is gradually getting updated to the level of Russian documentation. The most exact sections are those describing [ACTIVE_RECORD](../../active_record/docs/en/active_record.md) usage. These sections are recommended to read after studying the [basic example](./tutorials/basic.md).

There also is the documentation for most of the [packages](./packages_architecture.md) of Limb3. These sections have some information partially, or sometimes very much, outdated. We are improving this situation, but it takes a lot of time, so it isn't progressing very quickly. So if anything seems unclear to you, feel free to ask at [the forum](http://forum.limb-project.com/) — we always try to answer all the questions on-the-fly and comprehensively.

We're also looking for volunteers ready to improve English documentation. Original authors of Limb3 are Russian speakers, so if any part of documentation sounds unnatural or simply bad please don't hesitate fixing it. It's a wiki after all, where everyone can contribute.

## Unit tests
Most of the classes and subsystems of Limb3 are covered with unit tests. Those developers who can read the tests will find a lot of examples of usage of classes there. See [corresponding section](./how_to_run_tests.md) to learn more about the unit tests and the ways to run them.

## If you're stuck
Don't hesitate to ask your questions at [the forum](http://forum.limb-project.com/). If you prefer mailing lists to forums you can try one of the Limb3 mailing list: limb-dev or limb-usage. We always try to be responsive and helpful regardless you ask for feedback at the forum or mailing list.
