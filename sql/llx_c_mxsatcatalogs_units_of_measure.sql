CREATE TABLE IF NOT EXISTS llx_c_mxsatcatalogs_units_of_measure(
    rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    code varchar(3) NOT NULL, -- Unit of measure code according to SAT's Catalog
    label varchar(255) NOT NULL, -- Name of the unit of measure
    description varchar(560), -- Additional description
    symbol varchar(30), -- Symbol that represents the unit of measure
    active smallint NOT NULL -- Enable display
) ENGINE = innodb;
