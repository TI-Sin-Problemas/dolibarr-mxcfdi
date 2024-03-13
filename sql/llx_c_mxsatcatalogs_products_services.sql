CREATE TABLE IF NOT EXISTS llx_c_mxsatcatalogs_products_services(
    rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    code varchar(8) NOT NULL, -- Product or service code according to SAT's Catalog
    label varchar(150) NOT NULL, -- Name of the product or service
    active smallint NOT NULL -- Enable display
) ENGINE = innodb;
