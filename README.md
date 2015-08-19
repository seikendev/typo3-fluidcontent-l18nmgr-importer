# TYPO3 Fluidcontent l18nmgr Importer
This is the PHP script allowing to import language XML file into the TYPO3 by using l18nmgr extension when using Fluid Content Elements (fluidcontent_content)

## 1. Export all content by l10nmgr
You simply export all the pages

## 2. Import translated content by l10nmgr
This is straightforward, doesn't neeed any adjustments

## 3. Run the l18n.php
Adjust db.php according to your needs (server,login,password,db), then run the script via command-line :
php l18n.php [sys_language_uid to translate to] [page_uid of the page needed to be fixed]

###Enjoy having the fluid content translated ;)

## Known issues :
- for now on images are not properly localized, I will fix it in next releases.
