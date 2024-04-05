<?php
dol_include_once("core/lib/files.lib.php");

/**
 * Download the catalog database file, extract its contents, and save it to disk.
 *
 * @param string $dir The directory where the database file will be saved.
 * @throws Exception if there are any issues with the download, extraction, or saving process.
 */
function download_catalog_db($filePath = 'catalogs.db')
{
    // Delete the database file if already exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Set the URL of the file to download
    $url = 'https://github.com/phpcfdi/resources-sat-catalogs/releases/latest/download/catalogs.db.bz2';

    // Set the destination temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'dolibarr-mxsatcatalogs-');

    // Use the cURL library to download the file
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $fileContents = curl_exec($ch);
    curl_close($ch);

    // Save the downloaded file to disk
    file_put_contents($tempFile, $fileContents);

    // Use the bz2 library to extract the contents of the downloaded file
    $bz2File = bzopen($tempFile, 'r');
    $uncompressedFile = fopen($filePath, 'wb');
    while (!feof($bz2File)) {
        fwrite($uncompressedFile, gzread($bz2File, 4096));
    }
    bzclose($bz2File);
    fclose($uncompressedFile);

    // Delete the temporary file
    unlink($tempFile);
}

/**
 * Retrieves all records from the specified table.
 *
 * @param string $table The name of the table to retrieve records from.
 * @return SQLite3Result The result of the query execution.
 */
function get_query($dbPath, $table): SQLite3Result
{
    $db = new SQLite3($dbPath);
    return $db->query('SELECT * FROM ' . $table);
}

/**
 * Update a specific table based on table name.
 *
 * @param String $table The name of the table to update.
 * @param DoliDB $db The database object.
 * @param String $sqliteDbPath The path to the SQLite database.
 */
function update_table(String $table, DoliDB $db, String $sqliteDbPath)
{
    switch ($table) {
        case 'c_mxsatcatalogs_payment_methods':
            update_payment_methods($db, $sqliteDbPath);
            break;

        case 'c_mxsatcatalogs_payment_options':
            update_payment_options($db, $sqliteDbPath);
            break;

        case 'c_mxsatcatalogs_products_services':
            break;

        case 'c_mxsatcatalogs_units_of_measure':

            break;
    }
}



/**
 * Updates the payment methods in the database based on the information retrieved from the SQLite database.
 *
 * @param DoliDB $db The DoliDB object for the Dolibarr database
 * @param String $sqliteDbPath The path to the SQLite database
 * @throws Exception When an error occurs during the database operations
 */
function update_payment_methods(DoliDB $db, String $sqliteDbPath)
{
    $query = get_query($sqliteDbPath, 'cfdi_40_formas_pago');
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        $code = $row['id'];
        $label = $db->escape($row['texto']);

        $tableName = "c_mxsatcatalogs_payment_methods";
        $sql = "SELECT label FROM " . MAIN_DB_PREFIX . "{$tableName} WHERE code = '{$code}'";
        $response =  $db->query($sql);
        if ($response) {
            $selectResponse = $db->fetch_array($response);
            if ($selectResponse) {
                if ($selectResponse['label'] == $label) {
                    continue;
                }
                $sql = "UPDATE " . MAIN_DB_PREFIX . "{$tableName} SET label = '{$label}' WHERE code = '{$code}'";
            } else {
                $sql = "INSERT INTO " . MAIN_DB_PREFIX . "{$tableName} (code, label, active) VALUES ('{$code}', '{$label}', 0)";
            }
            if (!$db->query($sql)) {
                dol_print_error($db);
            }
        } else {
            dol_print_error($db);
        }
    }
}

/**
 * Updates payment options based on data from a SQLite database.
 *
 * @param DoliDB $db The instance of the DoliDB class.
 * @param String $sqliteDbPath The path to the SQLite database.
 */
function update_payment_options(DoliDB $db, String $sqliteDbPath)
{
    $query = get_query($sqliteDbPath, 'cfdi_40_metodos_pago');
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        $code = $row['id'];
        $label = $db->escape($row['texto']);

        $tableName = "c_mxsatcatalogs_payment_options";
        $sql = "SELECT label FROM " . MAIN_DB_PREFIX . "{$tableName} WHERE code = '{$code}'";
        $response =  $db->query($sql);
        if ($response) {
            $selectResponse = $db->fetch_array($response);
            if ($selectResponse) {
                if ($selectResponse['label'] == $label) {
                    continue;
                }
                $sql = "UPDATE " . MAIN_DB_PREFIX . "{$tableName} SET label = '{$label}' WHERE code = '{$code}'";
            } else {
                $sql = "INSERT INTO " . MAIN_DB_PREFIX . "{$tableName} (code, label, active) VALUES ('{$code}', '{$label}', 0)";
            }
            if (!$db->query($sql)) {
                dol_print_error($db);
            }
        } else {
            dol_print_error($db);
        }
    }
}
