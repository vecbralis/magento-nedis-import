# Magento Nedis import

This is a Magento module to import the products from the Nedis catalog into Magento.

## Note

This is an unofficial module and it is not supported or maintained by Nedis.

## Installation

### Installing with composer

1. Add the following repository:

    ```
    {
        "type": "vcs",
        "url": "https://github.com/nicovogelaar/magento-nedis-import.git"
    }
    ```

1. Add the following dependency requirement:

    ```
    "nicovogelaar/magento-nedis-import": "dev-master"
    ```

1. composer update

See the full example of a `composer.json` file [here](composer-example.json)

## Usage

### Magento Admin Panel
1. Go to the Magento Admin Panel -> `System` -> `Import/Export` -> `Nedis import`
2. Upload the catalog file: `nedis_catalog_[date]_[locale]_[customer number]_v1-0_xml.xml`

### CLI
1. Upload the catalog file to: `/path/to/magento/var/nedisimport`
2. Start the import: `php shell/nedisimport.php --file <filename> [--output]`

Only the XML format is supported. A full import can take longer than one hour.