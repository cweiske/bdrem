*********************************
bdrem - Birthday reminder by mail
*********************************
Birthday reminder that sends out e-mails.

It can also generate ASCII tables on your console/shell and normal HTML pages.


========
Features
========

Data sources
============
- Any SQL database
- An LDAP server
- Birthday reminder files (``.bdf``)

Output formats
==============
- ASCII table
- HTML
- Email (text + HTML parts)


=============
Configuration
=============
Copy ``data/bdrem.config.php.dist`` to ``data/bdrem.config.php`` and
adjust it to your liking.


MS SQL server
=============
Configure the date format in ``/etc/freetds/locales.conf``::

    [default]
        date format = %Y-%m-%d

Also set the charset to UTF-8 in ``/etc/freetds/freetds.conf``::

    [global]
        # TDS protocol version
        tds version = 8.0
        client charset = UTF-8

Restart Apache afterwards.

Use ``dblib`` in the DSN::

    dblib:host=192.168.1.1;dbname=Databasename


============
Dependencies
============
- PHP 5.3 or higher
- PDO
- PEAR packages:

  - Console_CommandLine
  - Mail
  - Mail_mime
  - Console_Table
  - Net_LDAP2


=======
License
=======
``bdrem`` is licensed under the `AGPL v3`__ or later.

__ http://www.gnu.org/licenses/agpl.html


======
Author
======
Written by Christian Weiske, cweiske@cweiske.de
