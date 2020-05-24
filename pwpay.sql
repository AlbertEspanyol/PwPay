create database pwpay;

use pwpay;

create table transactions(
    id bigint(20) unsigned auto_increment not null,
    source_user int(11),
    dest_user int(11),
    money float,
    tipo varchar(255),
    motiu varchar(255),
    data_actual datetime,
    status varchar(255),
    PRIMARY KEY (id)
);

create table user(
    id int(6) unsigned auto_increment not null,
    email varchar(255),
    password varchar(255),
    birthday varchar(255),
    created_at datetime,
    updated_at datetime,
    money float,
    active tinyint(1),
    token varchar(255),
    pfp_path varchar(255),
    bank_owner varchar(255),
    iban varchar(255),
    PRIMARY KEY (id)
);