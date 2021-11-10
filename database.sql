create table pfsense_firewalls(
    id varchar(32),
    ip varchar(15),
    hostname varchar(255),
    domainname varchar(255),
    version varchar(30),
    versiondate varchar(100),
    last_checkin int(11),
    primary key(id)
);
