# Aimon SMS Provider
[Aimon](https://aimon.it) is an italian SMS provider. This CiviCRM extension integrates Aimon SMS provider allowing you to use send SMS with CiviCRM.
## Setting up your new SMS Provider in CiviCRM¶
Aimon and CiviCRM integration allows delivering single and mass short message service (SMS) messages through its Aimon Gateway to mobile phone users.

## Requirements

- PHP v7.4+
- CiviCRM 5.77+

## Installation

Install as a regular CiviCRM extension.

## Configuration
1. After installing the Aimon SMS extension in CiviCRM, go to: **Administer > System Settings > SMS Providers**. Click **Add New Provider**.

2. Set up the provider as follows:
  - Name: select "AIMON"
  - Title: give the SMS provider a title user's will see (e.g. "AIMON SMS")
  - Username: enter your Aimon username
  - Password: enter your Aimon password
  - API type: choose `xml`
  - API URL: enter **https://secure.apisms.it/xmlrpc/BCP/provisioning.py**
  - API Parameters: enter **From=** followed by your Aimon **mobile phone number** in international format with no spaces or **alphabetical sender**. On a second line, enter **api_id=** followed by your API ID that you find in the table "Tipo SMS" in Home Page of your Aimon account. E.g.:
  
```
api_id=12345
From=+393471234567
```
or
```
api_id=12345
From=MyCompanyName
```

## Known Issues

https://github.com/civihost/eu.civihost.aimonsms/issues

## Support

Please post bug reports in the issue tracker of this project on GitHub: https://github.com/civihost/eu.civihost.aimonsms/issues

While we do our best to provide free community support for this extension, please consider financially contributing to support or development of this extension.

This is mantained by Samuele Masetto from [CiviHOST](https://www.civihost.it) who you can contact for help, support and further development.

## Disclaimer

This is still a work-in-progress extension.

