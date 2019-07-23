# hlock
## Blocks SQL Injection, bad bots, file uploads and bad IPs

**Join our global network to protect your pages from hackers.** 

**Autodetects WordPress,TYPO3,Keimeno**

- central server for bad ips,bots and injection filter
- injection protection
- file upload filter
- mime type validation
- reports to central server
- every webproject, which uses hlock, helps other projects to get protected
- totally free to use
- internet security should be for free

This project should help us developer to protect our PHP projects from hacking. Bad IPs will be reported to central server 
and hlock updates hisself with a current list of bad ips, bots and SQL injection rules.
Be part of the network and help us to get the web safer!
 
This version is compatible with keimeno CMS, but can easly changed to your individuel product. It autodetects Wordpress, Joomla and Typo3.
 
### Quick implementation
**add in your project index.php:**

```php
  <?PHP
  require ('./includes/hlock.class.php');
  hlock::run(dirname(__FILE__));
```
 
The keimenbo CMS includes the hlock project already, it is in the core implemented.
