# C H A T T E R
C H A T T E R is a responsive web chat app that merges simplicity and functional services with modern design. It enables you to communicate with your friends in the most simple and intuitive way.
#Motivation
My academic professor inspired me to make this app by share list of projects topics.
#Screenshots
#Technologies
Project is built with:
- HTML 5
- CSS 3
- JavaScript EcmaScript 6
- PHP 7.3
- composer 1.10.15
- phpunit 4.3.5
#Features
- Remember me functionality enable user to use their account after session expired until user does a logout
- User can check if another user is active
#Installation
After git clone operation, you need to ensure you have php installed on your computer. If not, download it from [this site](https://www.php.net/downloads), unzip and follow installer.
You can check whether php is installed by running `php -v` in console.
 
 Next step is to install composer if you don't have one installed. One of the simplest ways to do that is downloading installer from [this site](https://getcomposer.org/download/).
 You can check whether composer is installed by running `composer -v` in console.
 
 Finally, install phpunit to the project directory by running this command in command prompt, where working directory is project directory: 
 `composer require "phpunit/phpunit=4.3.*"`
 
You can check whether phpunit is installed by running `phpunit -v` in console.

phpunit.xml file is a configuration file for phpunit.

Note that you have to add absolute paths of executable files before all above checks excluding phpunit to avoid using absolute paths to executable files in console.
#Tests
You can run tests by running command `phpunit` in console.