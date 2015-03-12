 alter table product change status_id status varchar(15) not null;
 alter table product add column description blob null default null after img;
 
 CREATE TABLE `file` (
  `id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mime_type` varchar(25) NOT NULL,
  `ext` char(5) NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `data` longblob NOT NULL,
  `thumbnail` mediumblob,
  `thumbnail_mime_type` varchar(15) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table product change img img_id varchar(32) null default null;