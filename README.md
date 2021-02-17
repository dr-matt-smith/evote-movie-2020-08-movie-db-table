# evote-movie-2020-08-movie-db-table

Edited by Matt in Feb 2021

- add library `pdo-crud-for-free-repositories` to the project using Composer:
    
    ```bash
    composer req mattsmithdev/pdo-crud-for-free-repositories
    ```

- using your MySQL client, create a database named `evote`

- create a new folder `db` containing a PHP script `migrationsAndFixtures.php`

- copy the contents of the `findAll()` method of class `MovieRepository` into file `db/migrationsAndFixtures.php`

    - we'll complete this file later
    
- remove method `findAll()` from class `MovieRepository` and make this empty class inherit from ` Mattsmithdev\PdoCrudRepo\DatabaseTableRepository`:

    ```php
    <?php
    namespace Tudublin;
    
    use Mattsmithdev\PdoCrudRepo\DatabaseTableRepository;
    
    class MovieRepository extends DatabaseTableRepository
    {
    
    }
    ```
  
- add to class `Movie` a constant SQL string to create a `movie` table with all the fields of this class:

    ```php
    class Movie
    {
        const CREATE_TABLE_SQL =
            <<<HERE
    CREATE TABLE IF NOT EXISTS movie (
        id integer PRIMARY KEY AUTO_INCREMENT,
        title text,
        category text,
        price float,
        voteTotal integer,
        numVotes integer
    )
    HERE;
    ```
  
- ::: OUT OF DATE::: add a new folder `/config` and inside it a PHP script `dbConstants.php` defining 4 DB constants:

    ```php
    <?php
    define('DB_HOST', 'localhost:3306');
    define('DB_USER', 'root');
    define('DB_PASS', 'passpass');
    define('DB_NAME', 'evote');
    ```
    
    - replace `passpass` with the MySQL root password for your computer system
    
    
NOTE: **UPDATED** (see the library and sample project: [https://github.com/dr-matt-smith/pdo-crud-for-free-repositories](https://github.com/dr-matt-smith/pdo-crud-for-free-repositories)) the library now uses `.env` files like this:

```env
MYSQL_USER=root
MYSQL_PASSWORD=passpass
MYSQL_HOST=127.0.0.1
MYSQL_PORT=3306
MYSQL_DATABASE=evote
```
    
- edit `db/migrationsAndFixtures.php` to read as follows:

    - read in the DB constants and Composer autoloader, and create an instance of `MovieRepository`:

        ```php
        <?php
        require_once __DIR__ . '/../config/dbConstants.php';
        require_once __DIR__ . '/../vendor/autoload.php';
        
        use Tudublin\Movie;
        use Tudublin\MovieRepository;
        
        $movieRespository = new MovieRepository();
        ```
    - drop any existing table and create a new table (using that constant in `Movie`)
    
        ```php
        // (1) drop then create table
        $movieRespository->dropTable();
        $movieRespository->createTable();
        ```

    - delete any records in the table (just in case)
    
        ```php
        // (2) delete any existing objects
        $movieRespository->deleteAll();
        ```
      
    - create the 5 `Movie` objects `$m1 - $m5` using the code from the old `MovieRepository` 
        
    
        ```php
        // (3) create objects
        $m1 = new Movie();
        $m1->setId(1);
        $m1->setTitle('Jaws');
        $m1->setCategory('thriller');
        $m1->setPrice(10.00);
        $m1->setVoteTotal(5);
        $m1->setNumVotes(1);
        
        $m2 = new Movie();
        $m2->setId(2);
        $m2->setTitle('Jaws II');
        $m2->setCategory('thriller');
        $m2->setPrice(5.99);
        $m2->setVoteTotal(77 * 90);
        $m2->setNumVotes(77);
        
        ... and so on ...
        ```
        
    - insert the objects as rows in the database table
    
        ```php
        // (4) insert objects into DB
        $movieRespository->create($m1);
        $movieRespository->create($m2);
        $movieRespository->create($m3);
        $movieRespository->create($m4);
        $movieRespository->create($m5);
        ```
        
    - test this db setup script by selecting all DB rows and `var_dump`ing them
        
        ```php
        // (5) test objects are there
        $movies = $movieRespository->findAll();
        print '<pre>';
        var_dump($movies);
        ```

- at the command line, execute PHP script `migrationAndFixtures.php`

    ```bash
    matt$ php db/migrationAndFixtures.php 
    --------------- DatabaseTableRepository->createTable() ----------------
    NOTE:: Looking for a constant CREATE_TABLE_SQL defined in the entity class associated with this repository
    -----------------------------------------------------------------------
    <pre>/db/migrationAndFixtures.php:68:
    array(5) {
      [0] =>
      class Tudublin\Movie#11 (6) {
        private $id =>
        string(1) "1"
        private $title =>
        string(4) "Jaws"
        private $category =>
        string(8) "thriller"
        private $price =>
        string(2) "10"
        private $voteTotal =>
        string(1) "5"
        private $numVotes =>
        string(1) "1"
      }
      [1] =>
      class Tudublin\Movie#12 (6) {
        private $id =>
        string(1) "2"
        private $title =>
        string(7) "Jaws II"
        private $category =>
        string(8) "thriller"
        private $price =>

    ... and so on - all 5 objects should be listed
    ```
  
- finally, we need to add a `require_once` statement to our `/public/index.php` script, so that the database constants are available to when we make use of the `MovieRepository` object in the `MainController` method `listMovies()`:

    ```php
    <?php
    // public/index.php
    require_once __DIR__ . '/../config/dbConstants.php';
    require_once __DIR__ . '/../vendor/autoload.php';
    
    use Tudublin\WebApplication;
    
    $app = new WebApplication();
    $app->run();
    ```
