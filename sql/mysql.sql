CREATE TABLE userpage (
  up_pageid int(10) unsigned NOT NULL auto_increment,
  up_uid mediumint(8) NOT NULL default '0',
  up_title varchar(255) NOT NULL default '',
  up_text text NOT NULL,
  up_created int(10) unsigned NOT NULL default '0',
  up_hits int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (up_pageid),
  KEY up_uid (up_uid),
  KEY up_title (up_title(100)),
  KEY up_hits (up_hits)
) ENGINE=MyISAM;
