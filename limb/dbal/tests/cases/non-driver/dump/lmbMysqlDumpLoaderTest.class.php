<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlDumpLoaderTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/dump/lmbMysqlDumpLoader.class.php');
lmb_require(dirname(__FILE__) . '/lmbSQLDumpLoaderTestBase.class.php');

if(!lmbToolkit :: instance()->getDefaultDbConnection() instanceof lmbMysqlConnection)
  SimpleTest :: ignore('lmbMysqlDumpLoaderTest');

class lmbMysqlDumpLoaderTest extends lmbSQLDumpLoaderTestBase
{
  function _createLoader($file=null)
  {
    return new lmbMysqlDumpLoader($file);
  }

  function testFullBlownMysqlDump()
  {
    $sql = <<< EOD
/*
SQLyog Enterprise v4.06 RC1
Host - 4.0.12-max-debug : Database - all-limb-tests
*********************************************************************
Server version : 4.0.12-max-debug
*/

SET FOREIGN_KEY_CHECKS=0;

create database if not exists `foo`;

/*Table structure for table `bar` */

drop table if exists `bar`;

CREATE TABLE `bar` (
  `id` int(11) NOT null auto_increment,
  `url` varchar(255) NOT null default '',
  `description` varchar(255) default null,
  `img_src` varchar(255) default null,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`,`oid`)
) TYPE=InnoDB;

/*Data for the table `baz` */;
LOCK TABLES `baz` WRITE;

UNLOCK TABLES;

INSERT INTO `article` VALUES (8,101,2,'TemplateView','Template View','wiki');
EOD;

    $this->_writeDump($sql, $this->file_path);

    $loader = $this->_createLoader($this->file_path);

    $statements = $loader->getStatements();

    $this->assertEqual(sizeof($statements), 7);

    $this->assertEqual($statements[0], 'SET FOREIGN_KEY_CHECKS=0');
    $this->assertEqual($statements[1], 'create database if not exists `foo`');
    $this->assertEqual($statements[2], 'drop table if exists `bar`');
    $this->assertEqual($statements[3], "CREATE TABLE `bar` (
  `id` int(11) NOT null auto_increment,
  `url` varchar(255) NOT null default '',
  `description` varchar(255) default null,
  `img_src` varchar(255) default null,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`,`oid`)
) TYPE=InnoDB");
    $this->assertEqual($statements[4], 'LOCK TABLES `baz` WRITE');
    $this->assertEqual($statements[5], 'UNLOCK TABLES');
    $this->assertEqual($statements[6], "INSERT INTO `article` VALUES (8,101,2,'TemplateView','Template View','wiki')");

    $this->assertEqual($loader->getAffectedTables(), array('article'));
  }
}

?>
