CREATE TABLE IF NOT EXISTS `board` (
  `idx` int(11) NOT NULL auto_increment,
  `postedtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `lasttime` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `replyto` int(11) NOT NULL default '0',
  `subject` varchar(255) collate utf8_unicode_ci default NULL,
  `message` text collate utf8_unicode_ci,
  `ip` varchar(15) collate utf8_unicode_ci default NULL,
  KEY `idx` (`idx`),
  KEY `replyto` (`replyto`),
  KEY `author` (`author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `users` (
  `idx` int(11) NOT NULL auto_increment,
  `joined` datetime NOT NULL default '0000-00-00 00:00:00',
  `uname` varchar(31) collate utf8_unicode_ci NOT NULL default '',
  `pass` varchar(31) collate utf8_unicode_ci NOT NULL default '',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `prevlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `logintoken` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  KEY `idx` (`idx`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
