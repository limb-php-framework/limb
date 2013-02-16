# Getting Limb3
## SourceForge.net
All official releases of Limb3 can be found at [release section on SourceForge.net](http://sourceforge.net/projects/limb/files/). There you can download bundled archive with stable versions of mostly all Limb3 packages. Note that we put on SourceForge.net archives of most significant releases only and usually after major changes or improvements in several packages. If you want to work with recent versions of Limb3 packages then consider over types of Limb3 distribution (SVN, nightly builds or PEAR channel).

## Limb3 PEAR channel
Limb3 PEAR channel is a good alternative to SourceForge.net since we make releases of PEAR channel often (as soon as possible after any big changes in some package) and PEAR installer automatically resolves dependencies between packages. Here is an example howto install WEB_APP package with all dependencies using PEAR installed:

    $ pear install PEAR-alpha 
    $ pear channel-discover pear.limb-project.com
    $ pear install limb/web_app-alpha 

We strongly encourage to use this way to get Limb3 packages if you want to build your applications using Limb3 but don't plan to participate in Limb3 development.

You may also want to install so called BUNDLE package that simply «pulls» almost all Limb3 packages with it:

    $ pear channel-discover pear.limb-project.com
    $ pear install limb/bundle-beta

If you already have some Limb3 installation via PEAR channel use the following lines to install **BUNDLE** package and upgrade your installation:

    $ pear channel-discover pear.limb-project.com
    $ pear install -f limb/bundle-beta
    $ pear upgrade -f limb/bundle-beta

## Nightly builds
Every package is builded, tested and released on Limb3 build server every night. There is also a bundle of all packages named as **limb-3.x-bundle_YYYY_mm_dd-HH-ss**.

We recommend this way of Limb3 distribution only if you want to get acquaintance with Limb3 source code otherwise you risk to download partially refactored package (even if all tests were passed).

## Subversion
Bleeding edge «lovers» may extract Limb3 source code right from out Subversion repository. [Read more about Limb3 SVN repository](./svn.md).

To get trunk branches of all packages just type:

    svn co https://svn.limb-project.com/3.x/trunk/limb

SVN access is commonly used by active Limb3 users or Limb3 developers. We also recommend to fix on some particular revision number using svn:externals if you release your project build with SVN version of Limb3.
