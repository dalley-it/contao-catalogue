-- Catalog containers (similar to archives)
CREATE TABLE `dait_cc_catalogue` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `schema_key` varchar(64) NOT NULL default '',
  `jumpTo` int(10) unsigned NOT NULL default 0,
  `perPage` smallint(5) unsigned NOT NULL default 0,
  `sortMode` varchar(32) NOT NULL default 'title_asc',
  `dictionaryKey` varchar(64) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Records (organizations, jobs, taxonomy options, etc.)
CREATE TABLE `dait_cc_record` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default 0,
  `tstamp` int(10) unsigned NOT NULL default 0,
  `sorting` int(10) unsigned NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(128) NOT NULL default '',
  `language` varchar(16) NOT NULL default '',
  `languageMain` int(10) unsigned NOT NULL default 0,
  `published` char(1) NOT NULL default '',
  `start` int(10) unsigned NOT NULL default 0,
  `stop` int(10) unsigned NOT NULL default 0,
  `data_json` longtext NULL,
  -- Generic index fields for filtering/relations.
  `idx_taxonomy` varchar(16) NOT NULL default '',
  `idx_relation_id` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY  (`id`),
  KEY `pid_published` (`pid`,`published`),
  KEY `pid_sorting` (`pid`,`sorting`),
  KEY `alias` (`alias`),
  KEY `lang_main` (`languageMain`),
  KEY `idx_taxonomy` (`idx_taxonomy`),
  KEY `idx_relation_id` (`idx_relation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Record items (rows/children). Supports nesting via parent_id.
CREATE TABLE `dait_cc_record_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default 0,
  `tstamp` int(10) unsigned NOT NULL default 0,
  `sorting` int(10) unsigned NOT NULL default 0,
  `parent_id` int(10) unsigned NOT NULL default 0,
  `type` varchar(64) NOT NULL default '',
  `data_json` longtext NULL,
  PRIMARY KEY (`id`),
  KEY `pid_sorting` (`pid`,`sorting`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dictionaries (backend-managed option lists)
CREATE TABLE `dait_cc_dictionary` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `dict_key` varchar(64) NOT NULL default '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dict_key` (`dict_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dait_cc_dictionary_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default 0,
  `tstamp` int(10) unsigned NOT NULL default 0,
  `sorting` int(10) unsigned NOT NULL default 0,
  `code` varchar(32) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `language` varchar(16) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `pid_sorting` (`pid`,`sorting`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
