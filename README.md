# Yazan - SQL-To-CI-Builder
SQL to Codeigniter Query Builder, A Converter written in PHP

### Features
- Converts SQL Queries to Codeigniter Query Builder. 
- Assists on creating optimal query as instructed in Codeigniter Documentation.  
- Provides options to interact with, for generating different results. 

### Supports 
- Codeigniter 3 
- Codeigniter 4 

### Demo

##### Online demo
Live demo and free usage is available <a href='https://sql-to-ci-builder.herokuapp.com/'>here</a>.

### Get Started
##### Install by a manual download: 
Download the repository and install required packages by composer.json.

##### Packagist
You can also install it from packagist by running the following command:
```html
composer require rexshijaku/sql-to-ci-builder
```

### Usage
##### Simple example

```php
<?php

use RexShijaku\SQLToCIBuilder\SQLToCIBuilder;

require_once dirname(__FILE__) . './vendor/autoload.php';

$options = array('civ' => 4);
$converter = new SQLToCIBuilder($options);

$sql = "SELECT COUNT(*) FROM members";
echo $converter->convert($sql);
```
This will produce the following result: 
```php
$this->db->countAll('members');
```
##### A more complex example :

```php
$sql = "SELECT * FROM members WHERE age > 30 
                            OR (name LIKE 'J%' OR (surname='P' AND name IS NOT NULL)) AND AGE !=30";
$converter->convert($sql);
```
and this will generate the result below :
```php
$db->table('members')
 ->where('age >',30)
 ->orGroupStart()
     ->like('name','J','after')
     ->orGroupStart()
         ->where('surname','P')
         ->where('name !=',null)
     ->groupEnd()
 ->groupEnd()
 ->where('AGE !=',30)
 ->get();
```
##### Notice 
In both of these examples Codeigniter 4 was used. If you need to change it, or get more comprehensive understanding of provided options then see the following section of Options.
There are dozens of examples for every use case explained in the Query Builder documentation for both version 3 and version 4 located in their respecitve folders inside the <a href="https://github.com/rexshijaku/sql-to-ci-builder/tree/main/examples">examples</a> folder.

### Options
Some important options are briefly explained below:
| Argument  | DataType    | Default  | Description |
| ----- |:----------:| -----:| -----:|
| civ  | integer | 3 |  Your Codeigniter version. |
| db_instance  |  string | $this->db | Object in which database was loaded and initialized. |
| use_from |   boolean  | false  | In CodeIgniter 3, wether it should use 'from' command instead of 'get' to select data from a table. |
| group |   boolean | true  | Whether it should group key value pairs into a php array, or generate commands for each key value pair. |
| single_line |  boolean  | true |  When this argument is true, then converter tries to generate a single command instead of multiple. |

### How does it works ?
SQL-To-CI-Builder is built on top of <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a>. While <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a> is responsible for parsing the given SQL Query as input. The result of the  <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a> is the input of SQL-To-CI-Builder.

The structure has three main parts : 
1) Extractors classes - which help to pull out SQL Query parts in a way which are more understandable and processable by Builders. 
2) Builder classes - which help to construct Query Builder methods.
3) Creator - which orchestrates the process between Extractors and Builders in order to produce parts of Query Builder.

### Known issues
- It is not tested in all cases. They should be added.
- Poor error handling.

### Contributions 
Feel free to contribute on development, testing or eventual bug reporting.

### Support
For general questions about Yazan - SQL-To-CI-Builder, tweet at @rexshijaku or write me an email on rexhepshijaku@gmail.com.
To have a quick tutorial check the <a href="https://github.com/rexshijaku/sql-to-ci-builder/tree/main/examples">examples</a> folder provided in the repository.

### Author
##### Rexhep Shijaku
 - Email : rexhepshijaku@gmail.com
 - Twitter : https://twitter.com/rexshijaku
 
### Thank you
All contributors who created and are continuously improving <a href="hhttps://github.com/greenlion/PHP-SQL-Parser">PHP-SQL-Parser</a>, without it, this project would be much harder to be realized. 

### In memoriam
For the innocent lives lost (including Yazan al-Masri, aged just two) during the 2021 Israel–Palestine crisis.

### License
MIT License

Copyright (c) 2021 | Rexhep Shijaku

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
