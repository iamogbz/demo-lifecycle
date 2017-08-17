Coding Challenge
================

Thank you for your interest in a Software Engineering position at Yes Lifecycle
Marketing's Intelligence group. To work on this app, you need only have PHP 5.5.9+ installed. 
It leverages PHP's built-in web server and a SQLite database for simplicity. This app's
dependencies are managed by `Composer <https://getcomposer.org/>`_.

Please submit a pull request to the git repo adding basic login functionality
(i.e. username and password) including a unit test. Even if you are unable to complete 
the task fully, please submit as much as you can for consideration.

Notes:

- The SQLite binary is located at sqlite/sqlite3
- The database file is located at sqlite/coding-challenge.db
- The database connection is already configured and ready for use

Good luck!

Running the Application
-----------------------

Start the PHP built-in web server with
command:

.. code-block:: console

    $ cd path/to/install
    $ COMPOSER_PROCESS_TIMEOUT=0 composer run

Then, browse to http://localhost:8888/index_dev.php/
