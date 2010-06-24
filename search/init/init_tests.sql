DROP TABLE IF EXISTS `full_text_uri_content_index`;
CREATE TABLE `full_text_uri_content_index` (
  `id` bigint(20) NOT NULL auto_increment,
  `uri` varchar(150) NOT NULL default '',
  `content` text NOT NULL,
  `last_modified` bigint(20) default NULL,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `uri` (`uri`),
  FULLTEXT KEY `content` (`content`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
