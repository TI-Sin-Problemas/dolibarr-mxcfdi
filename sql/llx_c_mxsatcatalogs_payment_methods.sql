CREATE TABLE IF NOT EXISTS llx_c_mxsatcatalogs_payment_methods(
    rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    code varchar(8) NOT NULL UNIQUE,
    label varchar(40) NOT NULL,
    active smallint NOT NULL
) ENGINE = innodb;
