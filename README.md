# CapstoneRemodel

## Instructions for environment setup and Laravel 5.6

* [Laravel 5.6](https://laravel.com/docs/5.6) - The documents for the web framework used
* [PHP](http://us1.php.net/manual/en/langref.php) - Manual for language reference
* [Composer](https://getcomposer.org/download/) - Updated Composer
* [Shibboleth@OSU](https://webauth.service.ohio-state.edu/~shibboleth/) - OSU Shibboleth

## Useful Commands 

### Test

Command: "phpunit" under the root directory
If the phpunit doesn't work, try "composer global require phpunit/phpunit". Set the phpunit command as the global scope.
Test the following features with corresponding commands in command window for your local repository.

### Write Resident_Education_Report.yearmonthday.csv into Database

e.g. Insert data from Resident_Education_Report.20180615.csv file into database table, and combine data sets 
for the next three days.

```
php artisan educationReport:add 20180615 --process=2018-06-16 --process=2018-06-17 --process=2018-06-18
```

### Check PHP version

```
php -v (or php -i)
```

### Environment Setup
* Clone the repository
* Open git bash or run through terminal the following commands (update database accordingly):

```
cp .env.example .env
php artisan key:generate
php artisan migrate
```


## NOTES

Code in views/schedules/resident/schedule_basic.blade.php might be different based on the servers.

```
var url = current_url.search('/filter/') > -1 ? current_url.substr(0, current_url.search('/filter/')) : current_url;
url = url + "/filter/" + doctor_selected + "_" + start_after_selected + "_" + end_before_selected;
```

## Setup OCIO server
* SFTP: The SFTP button may help you access the server. The instructions on the right provide you the access link.
* DATABASE: Click on the DATABASE section. Click on phpmyadmin on the right. The username is xgl. And password is xgl. No uppercase. 
* SHELL: Try to acess the shell by Putty. The FastX doesn't work here. You could see the shell link after Dr.Stahl has added you to one of the shell users.

## Future extension 
* Mail: You could see the mail section setup in the Laravel mail setup documents. https://laravel.com/docs/5.6/mail
* Google API: https://developers.google.com/sheets/api/quickstart/php 
* The above link provides the Google sheets API setup. The project now could access the google account(google sheets), but you need the private key updating from your personal google account.
* Scheduling task on server: Refer to Crontab in linux tutorial.
* Survey page: The "static survey page" represents the page that residents and attendings can add their feedback after completing the 
surgery. Currently, the url of this page is: https://remodel.anesthesiology_dev.org.ohio-state.edu/laravel/public/survey/YYYYMMDD. e.g. 
Suppose "resident" "Test Resident" visits https://remodel.anesthesiology_dev.org.ohio-state.edu/laravel/public/survey/20180710 after 
07/10/2018. He will view the summary of the surgery conducted on 07/10/2018 and is able to submit his comments for that surgery (or the 
attending). 

