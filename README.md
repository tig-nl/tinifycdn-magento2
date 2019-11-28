# TIG TinyCDN for Magento 2
We created this extension to **add immediate image optimization and CDN functionality to Magento 2**. 

## Installation using Composer
<pre>composer require tig/tinycdn-magento2</pre>

## Installation without using Composer
_Clone or download_ the contents of this repository into `app/code/TIG/TinyCDN`.

### Development Mode
After installation, run `bin/magento setup:upgrade` to make the needed database changes and remove/empty Magento 2's generated files and folders.

### Production Mode
After installation, run:
1. `bin/magento setup:upgrade`
2. `bin/magento setup:di:compile`
3. `bin/magento setup:static-content:deploy [locale-codes, e.g. nl_NL en_US`
4. `bin/magento cache:fluche`

Done!

## Configuration

### API credentials
To use this module you need an active TinyCDN account. You can connect to your account using the 'Connect to your Tinify Account' button in _Stores / Configuration / Tinify / TinyCDN_.
