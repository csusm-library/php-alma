## php-alma

Utilities for interacting with Alma Web Services in PHP. Currently read access is provided for three SOAP service: User Info services, Course services, and Holdings services. 

### Installation

`php-alma` uses the [Composer](http://getcomposer.org/) dependency management system. To install 

1. If you haven't already, [install `composer.phar`](http://getcomposer.org/doc/00-intro.md#installation-nix). To install `composer.phar` in the `/usr/bin` directory on Linux/OS X:
 
		sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin

2. Create a `composer.json` file. The example below will install `php-alma`:


		{
		  "name":"your-org/your-project",
		  "description": "Describe your project",
		  "license":"MIT",
		  "repositories": [
		    {
		        "type": "vcs",
		        "url": "https://github.com/BCLibraries/php-alma"
		    },
		    {
            "type": "pear",
            "url": "http://pear.php.net"
            }
		  ],
		    "require": {
		            "bclibraries/php-alma" : "master",
		            "pear-pear/File_MARC": "*"
		    }, 
		    "minimum-stability": "dev"
		 }
   
   Transitive composer installs don't work with PEAR repositories, so you'll have to specifically include the PEAR install in your `composer.json`.
    
3. Install using `composer.phar`:

		php composer.phar install


Composer will load all the required dependencies and create an `vendor/autoload.php` file to handle autoloading classes.

### Connecting

To start:

```php
use BCLib\Alma\AlmaServices;

require_once __DIR__.'/vendor/autoload.php';

$soap_user = ''; // e.g. 'webservice'
$soap_institution = ''; // e.g. '01BC_INST'
$soap_pass = '';

AlmaServices::initialize($soap_user, $soap_pass, $soap_institution);
```

### Users 

To load a user:

```php

$user_services = AlmaServices::userInfoServices();

if ($user = $user_services->getUser('florinb')) {
    
    echo $user->last_name . ", " . $user->first_name . $user->middle_name . "\n";
    echo $user->email . "\n";
    
    if ($user->is_active) {
        echo "User is active.\n";
    } else {
        echo "User is not active.\n";
    }

    //Identifiers
    foreach ($user->identifiers as $id) {
        echo "\t" . $id->code . " is " . $id->value . "\n";
    }
    
    // Blocks
    foreach ($user->blocks as $block) {
        echo "\t" . $block->code . " " . $block->_type . " " . $block->status . " ";
        echo $block->creation_date . " " . $block->modification_date . "\n";
    }
}
```

See *user-demo.php* for a full example.

### Courses

To load a course:

```php
$course_services = AlmaServices::courseServices();

if ($courses = $course_services->getCourses('AD100', '03', 0, 10)) {
    foreach ($courses as $course) {

        echo $course->identifier . "\n";
        echo $course->name . "\n";

        foreach ($course->complete_lists as $list) {
            echo "\t" . $list->identifier . "\n";
            echo "\t" . $list->name . "\n";

            foreach ($list->citations as $citation) {
                echo "\t\t" . $citation->title . "\n";
                echo "\t\t" . $citation->author . "\n";
                echo "\t\t" . $citation->open_url . "\n";
            }
        }
    }
} else {
    echo $course_services->lastError()->message;
}
```

### Holdings

To load holdings for an or set of items:

```php
$service = Alma\AlmaServices::holdingsServices();

// Pass in an array of MMS IDs.
$bib_records = $service->getHoldings(
    array('99131822450001021', '99106869560001021')
);

foreach ($bib_records as $bib_record) {
    echo $bib_record->mms . "\n";
    
    // Returns a list of PEAR File_MARC_Field objects.
    foreach ($bib_record->getMARCField('245') as $marc_field) {
        echo $marc_field->getSubfield('a')->getData() . "\n";
    }

    foreach ($bib_record->holdings as $holding) {
        echo $holding->availability . "\n";
        echo $holding->call_number . "\n";
        echo $holding->institution . "\n";
        echo $holding->library . "\n";
        echo $holding->location . "\n";
    }
}
```

## License

See MIT-LICENSE