# laravolt/mailkeeper
Save mail to database for further use

## Usage
### Install Package
`composer require laravolt/mailkeeper`

### Configuration
Enable or disable mailkeeper via `environment variable`:
```
MAILKEEPER_ENABLED=false
```

Other configuration can be read at `config/laravolt/mailkeeper.php`:
```php
<?php

return [
    // Enable/disable mailkeeper.
    // If enable, every outgoing mail will be intercepted.
    // So, instead of send it via SMTP or other mail driver, mailkeeper will store it to database for further use.
    'enabled' => env('MAILKEEPER_ENABLED', false),
    
    // How many rows to take for each "laravolt:send-mail" command
    'take'    => 100,
];
```

### Save Mail to Database
Any existing code to send email, as documented in https://laravel.com/docs/5.7/mail will just works fine. 
You don't need to change anything. Mailkeeper will intercepted outgoing mail automatically and store it to database.

### Send Mail
### Via Command
`php artisan laravolt:send-mail`

### Via Scheduler
Prepare task scheduler, as documented in https://laravel.com/docs/5.7/scheduling.

Register `laravolt:send-mail` schedule:
```php
$schedule->command(\Laravolt\Mailkeeper\SendMailCommand::class)->everyMinute();
```
## Reference
This package was built based on following tutorial https://www.sitepoint.com/mail-logging-in-laravel-5-3-extending-the-mail-driver/
