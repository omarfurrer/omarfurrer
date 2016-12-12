<?php return array(

    '{prefix}gg_folders' => 'CREATE TABLE `{prefix}gg_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_galleries' => 'CREATE TABLE `{prefix}gg_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `settings_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_galleries_resources' => 'CREATE TABLE `{prefix}gg_galleries_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` enum(\'folder\',\'photo\') CHARACTER SET utf8 NOT NULL,
  `resource_id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_photos' => 'CREATE TABLE `{prefix}gg_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_id` int(11) NOT NULL DEFAULT \'0\',
  `album_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT \'9000\',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_photos_settings' => 'CREATE TABLE `{prefix}gg_photos_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_settings_sets' => 'CREATE TABLE `{prefix}gg_settings_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `gallery_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_stats' => 'CREATE TABLE `{prefix}gg_stats` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `code` varchar(255) NOT NULL,
 `visits` int(11) NOT NULL,
 `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8',

    '{prefix}gg_settings_presets' => 'CREATE TABLE `{prefix}gg_settings_presets` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `title` varchar(255) NOT NULL,
 `settings_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_photos_pos' => 'CREATE TABLE `{prefix}gg_photos_pos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `photo_id` int(11) NOT NULL,
 `scope` enum(\'main\',\'folder\',\'gallery\') NOT NULL,
 `scope_id` int(11) NOT NULL DEFAULT \'0\',
 `position` int(11) NOT NULL DEFAULT \'2147483647\',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_galleries_excluded' => 'CREATE TABLE `{prefix}gg_galleries_excluded` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `folder_id` int(11) NOT NULL,
 `photo_id` int(11) NOT NULL,
 `gallery_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

    '{prefix}gg_tags' => 'CREATE TABLE `{prefix}gg_tags` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `pid` int(11) NOT NULL,
 `tags` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8',

);
