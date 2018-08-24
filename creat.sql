CREATE DATABASE IF NOT EXISTS RSBA;

USE RSBA;

CREATE TABLE IF NOT EXISTS activity(
	`id`             INT AUTO_INCREMENT PRIMARY KEY        COMMENT '活动ID',
    `type`           TINYINT UNSIGNED  NOT NULL DEFAULT 0  COMMENT '活动类型，0志愿，1福利',
    `title`          VARCHAR(255)      NOT NULL DEFAULT '' COMMENT '活动标题',
    `publisher`      VARCHAR(255)      NOT NULL DEFAULT '' COMMENT '活动发起人',
    `details`        VARCHAR(255)      NOT NULL DEFAULT '' COMMENT '活动详情',
    `time`           DATETIME          NOT NULL            COMMENT '开始时间',
    `award`          SMALLINT UNSIGNED NOT NULL DEFAULT 0  COMMENT '奖品数',
    `member`         SMALLINT UNSIGNED NOT NULL DEFAULT 0  COMMENT '限制人数',
    `current_member` SMALLINT UNSIGNED NOT NULL DEFAULT 0  COMMENT '报名人数',
    `created_at`     DATETIME          NOT NULL            COMMENT '创建时间',
    `updated_at`     DATETIME          NOT NULL            COMMENT '修改时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='活动表';

CREATE TABLE IF NOT EXISTS user(
	`id`             INT AUTO_INCREMENT PRIMARY KEY,
    `stuno`          VARCHAR(20)       NOT NULL DEFAULT '0'    COMMENT '学号',
    `name`           VARCHAR(20)       NOT NULL DEFAULT ''     COMMENT '用户',
    `department`     TINYINT UNSIGNED  NOT NULL DEFAULT 0      COMMENT '所属部门',
    `grp`            VARCHAR(20)       NOT NULL DEFAULT '干事' COMMENT '组别',
    `tele`           VARCHAR(20)       NOT NULL DEFAULT '0'    COMMENT '电话',
    `created_at`     DATETIME          NOT NULL                COMMENT '创建时间',
    `updated_at`     DATETIME          NOT NULL                COMMENT '修改时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='用户表';

CREATE TABLE IF NOT EXISTS activity_user(
	`id`             INT AUTO_INCREMENT PRIMARY KEY,
    `activity_id`    INT UNSIGNED      NOT NULL DEFAULT 0  COMMENT '活动id',
    `user_id`        INT UNSIGNED      NOT NULL DEFAULT 0  COMMENT '用户id',  
    `created_at`     DATETIME          NOT NULL                COMMENT '创建时间',
    `updated_at`     DATETIME          NOT NULL                COMMENT '修改时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='活动用户报名中间表';

CREATE TABLE IF NOT EXISTS member_list(
	`id`             INT AUTO_INCREMENT PRIMARY KEY COMMENT '活动ID',
    `0`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `1`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `2`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `3`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `4`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `5`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `6`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `7`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `8`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `9`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`     DATETIME          NOT NULL            COMMENT '创建时间',
    `updated_at`     DATETIME          NOT NULL            COMMENT '修改时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='部门限制人数表';

CREATE TABLE IF NOT EXISTS current_member_list(
	`id`             INT AUTO_INCREMENT PRIMARY KEY COMMENT '活动ID',
    `0`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `1`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `2`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `3`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `4`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `5`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `6`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `7`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `8`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `9`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`     DATETIME          NOT NULL            COMMENT '创建时间',
    `updated_at`     DATETIME          NOT NULL            COMMENT '修改时间'
)ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='部门报名人数表';