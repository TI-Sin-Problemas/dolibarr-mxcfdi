CREATE TABLE IF NOT EXISTS llx_mxsatcatalogs_payments(
    rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    code varchar(8) NOT NULL,
    label varchar(40) NOT NULL,
    active smallint NOT NULL
) ENGINE = innodb;
