# Mailkeeper

[![Latest Version](https://img.shields.io/packagist/v/laravolt/mailkeeper)](https://packagist.org/packages/laravolt/mailkeeper)
[![PHP Version](https://img.shields.io/packagist/php-v/laravolt/mailkeeper)](https://packagist.org/packages/laravolt/mailkeeper)
[![License](https://img.shields.io/github/license/laravolt/mailkeeper)](https://github.com/laravolt/mailkeeper/blob/main/LICENSE)

Mailkeeper is a Laravel package that intercepts outgoing emails and stores them in a database instead of sending them immediately. This is particularly useful for development environments, testing, or scenarios where you want to review emails before they're sent.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Commands](#commands)
- [API Reference](#api-reference)
- [Migration Guide](#migration-guide)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Features

- ✅ **Email Interception**: Automatically intercepts all outgoing Laravel emails
- ✅ **Database Storage**: Stores emails in a structured database table
- ✅ **Batch Processing**: Send stored emails in batches via command line
- ✅ **Full Email Support**: Supports all email fields (from, to, cc, bcc, reply-to, priority, etc.)
- ✅ **Content Type Detection**: Automatically detects HTML vs plain text emails
- ✅ **Error Handling**: Tracks sending errors and failed deliveries
- ✅ **Soft Deletes**: Safely archive processed emails
- ✅ **Laravel Integration**: Seamlessly integrates with Laravel's mail system
- ✅ **Environment Control**: Enable/disable via environment variables
- ✅ **Legacy Support**: Compatible with both Symfony Mailer and Swift Mailer

## Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher
- Database connection (MySQL, PostgreSQL, SQLite, etc.)

## Installation

### Option 1: Via Composer (if published as separate package)

```bash
composer require laravolt/mailkeeper
```

### Option 2: Manual Installation (for local development)

If you're working with the Laravolt platform, the mailkeeper package is already included. Simply ensure it's properly registered in your application.

1. **Register the Service Provider** (if not auto-discovered):

   Add to `config/app.php`:

   ```php
   'providers' => [
       // ... other providers
       Laravolt\Mailkeeper\ServiceProvider::class,
   ],
   ```

2. **Publish Configuration** (optional):

   ```bash
   php artisan vendor:publish --provider="Laravolt\Mailkeeper\ServiceProvider" --tag=config
   ```

3. **Run Migrations**:

   ```bash
   php artisan migrate
   ```

   Or if you want to publish the migration first:

   ```bash
   php artisan vendor:publish --provider="Laravolt\Mailkeeper\ServiceProvider" --tag=migrations
   php artisan migrate
   ```

## Configuration

Mailkeeper can be configured via the `config/laravolt/mailkeeper.php` file:

```php
return [
    // Enable/disable mailkeeper.
    // If enabled, every outgoing mail will be intercepted and stored in database.
    // If disabled, emails will be sent normally via your configured mail driver.
    'enabled' => env('MAILKEEPER_ENABLED', false),

    // Number of emails to process in each batch when running the send command
    'take' => 100,

    // Whether to auto-load migrations or require manual publishing
    'migrations' => true,
];
```

### Environment Variables

Add to your `.env` file:

```env
# Enable mailkeeper (set to true to intercept emails, false to send normally)
MAILKEEPER_ENABLED=false

# Optional: Override batch size for sending emails
MAILKEEPER_TAKE=100
```

## Usage

### Basic Usage

Once installed and enabled, Mailkeeper will automatically intercept all emails sent through Laravel's `Mail` facade:

```php
use Illuminate\Support\Facades\Mail;

Mail::to('user@example.com')
    ->subject('Welcome!')
    ->send(new WelcomeEmail($user));
```

Instead of being sent immediately, the email will be stored in the `mail` table with status `0` (pending).

### Checking Stored Emails

You can query stored emails using the `Mail` model:

```php
use Laravolt\Mailkeeper\Mail;

// Get all pending emails
$pendingEmails = Mail::where('status', 0)->get();

// Get emails with errors
$failedEmails = Mail::whereNotNull('error')->get();

// Get recent emails
$recentEmails = Mail::latest()->take(10)->get();
```

### Manual Email Storage

You can also manually store emails using the Mail model:

```php
use Laravolt\Mailkeeper\Mail;

Mail::create([
    'from' => ['sender@example.com' => 'Sender Name'],
    'to' => ['recipient@example.com'],
    'subject' => 'Manual Email',
    'body' => 'This is the email content',
    'content_type' => 'text/html',
    'priority' => 1,
]);
```

## Database Schema

Mailkeeper creates a `mail` table with the following structure:

```sql
CREATE TABLE mail (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status TINYINT UNSIGNED DEFAULT 0,
    from JSON,
    sender JSON NULL,
    to JSON,
    cc JSON NULL,
    bcc JSON NULL,
    reply_to JSON NULL,
    priority SMALLINT UNSIGNED NULL,
    content_type VARCHAR(255) DEFAULT 'text/plain',
    subject VARCHAR(255),
    body TEXT,
    error TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

### Field Descriptions

- **id**: Primary key
- **status**: Email status (0 = pending, 1 = sent, 2 = failed)
- **from**: Sender email address(es) as JSON array
- **sender**: Sender address if different from from
- **to**: Recipient email address(es) as JSON array
- **cc**: CC recipient email address(es) as JSON array
- **bcc**: BCC recipient email address(es) as JSON array
- **reply_to**: Reply-to email address(es) as JSON array
- **priority**: Email priority (1 = highest, 5 = lowest)
- **content_type**: Content type (text/plain or text/html)
- **subject**: Email subject
- **body**: Email content (HTML or plain text)
- **error**: Error message if sending failed
- **timestamps**: Created and updated timestamps
- **deleted_at**: Soft delete timestamp

## Commands

### Send Stored Emails

The main command to send stored emails:

```bash
php artisan laravolt:send-mail
```

This command will:

1. Fetch pending emails from the database (up to the configured `take` limit)
2. Send them using your normal mail configuration
3. Mark them as sent or record errors
4. Soft delete processed emails

**Options:**

- The command processes emails in batches based on your `mailkeeper.take` configuration

**Example Output:**

```bash
Sending 5 emails...
Email sent successfully
Email sent successfully
Failed to send email: Connection timeout
Email sent successfully
Email sent successfully

Finished
```

### Scheduling the Command

You can schedule the command to run periodically using Laravel's task scheduler:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('laravolt:send-mail')
             ->everyMinute()
             ->withoutOverlapping();
}
```

## API Reference

### Mail Model

The `Laravolt\Mailkeeper\Mail` model provides access to stored emails:

```php
use Laravolt\Mailkeeper\Mail;

// Create a new stored email
$mail = Mail::create($data);

// Find by ID
$mail = Mail::find(1);

// Query methods
$pending = Mail::pending()->get();      // status = 0
$sent = Mail::sent()->get();            // status = 1
$failed = Mail::failed()->get();        // status = 2

// Scopes
Mail::withErrors()->get();              // where error is not null
Mail::recent()->get();                  // order by created_at desc
```

### Available Scopes

```php
// Get pending emails
Mail::pending()->get();

// Get sent emails
Mail::sent()->get();

// Get failed emails
Mail::failed()->get();

// Get emails with errors
Mail::withErrors()->get();

// Get recent emails
Mail::recent()->get();

// Get emails by content type
Mail::html()->get();
Mail::text()->get();
```

### Service Provider

The service provider handles the mail interception logic:

```php
use Laravolt\Mailkeeper\ServiceProvider;

// Check if mailkeeper is enabled
$config = config('laravolt.mailkeeper.enabled');
```

## Migration Guide

### From Legacy Swift Mailer to Symfony Mailer

Mailkeeper supports both transport implementations:

**Legacy Transport (Swift Mailer):**

```php
// Automatically used for older Laravel versions
// Uses LegacyDbTransport class
```

**Modern Transport (Symfony Mailer):**

```php
// Automatically used for Laravel 9+
// Uses DbTransport class
```

Both transports provide the same functionality with different underlying implementations.

### Upgrading from Previous Versions

1. **Backup your database** before upgrading
2. Run the migration to ensure the table structure is up to date
3. Update your configuration if needed
4. Test in a development environment first

## Troubleshooting

### Emails Not Being Intercepted

1. **Check Configuration:**

   ```bash
   php artisan config:cache
   php artisan config:clear
   ```

2. **Verify Environment Variable:**

   ```env
   MAILKEEPER_ENABLED=true
   ```

3. **Check Service Provider Registration:**
   Ensure the service provider is registered in `config/app.php`

### Command Not Found

If the `laravolt:send-mail` command is not found:

1. **Clear Cache:**

   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

2. **Check Service Provider:**
   Ensure the service provider is properly loaded

### Database Connection Issues

1. **Verify Migration:**

   ```bash
   php artisan migrate:status
   ```

2. **Check Database Configuration:**
   ```bash
   php artisan config:show database
   ```

### Permission Issues

Ensure the application has proper database write permissions and the web server can execute artisan commands.

### Common Issues

**Issue: Emails are being sent instead of stored**

- Solution: Verify `MAILKEEPER_ENABLED=true` in your `.env` file

**Issue: Command processes 0 emails**

- Solution: Check if there are pending emails: `Mail::pending()->count()`

**Issue: Emails fail to send**

- Solution: Check your mail configuration and ensure SMTP credentials are correct

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

### Development Setup

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/mailkeeper.git`
3. Install dependencies: `composer install`
4. Run tests: `vendor/bin/pest`
5. Create your feature branch: `git checkout -b feature/amazing-feature`
6. Commit your changes: `git commit -m 'Add amazing feature'`
7. Push to the branch: `git push origin feature/amazing-feature`
8. Open a Pull Request

### Testing

Run the test suite:

```bash
vendor/bin/pest
```

Run tests with coverage:

```bash
vendor/bin/pest --coverage
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Note:** This package is part of the Laravolt platform ecosystem. For more packages and tools, visit [laravolt.dev](https://laravolt.dev).
