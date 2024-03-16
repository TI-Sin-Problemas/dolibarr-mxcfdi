CREATE TABLE IF NOT EXISTS llx_c_mxsatcatalogs_payment_options(
    rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    code varchar(3) NOT NULL,
    label varchar(40) NOT NULL,
    active smallint NOT NULL
)
