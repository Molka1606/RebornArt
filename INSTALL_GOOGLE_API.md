# Installing Google API Client - Step by Step Guide

## Option 1: Install Composer First (Recommended)

### Step 1: Download and Install Composer

1. Go to: https://getcomposer.org/download/
2. Download `Composer-Setup.exe` for Windows
3. Run the installer
4. Follow the installation wizard
5. Make sure to check "Add to PATH" during installation

### Step 2: Verify Installation

Open a new Command Prompt or PowerShell and run:
```bash
composer --version
```

You should see the Composer version number.

### Step 3: Install Google API Client

Navigate to your project folder:
```bash
cd c:\xampp\htdocs\res_event
```

Then run:
```bash
composer require google/apiclient
```

This will:
- Create a `vendor/` folder
- Download Google API Client and all dependencies
- Create/update `composer.json` and `composer.lock`

## Option 2: Manual Installation (If Composer Not Available)

If you can't install Composer, you can manually download the Google API Client:

### Step 1: Download Google API Client

1. Go to: https://github.com/googleapis/google-api-php-client/releases
2. Download the latest release ZIP file (e.g., `google-api-php-client-2.x.x.zip`)
3. Extract the ZIP file

### Step 2: Copy Files to Project

1. Create a `vendor` folder in your project root if it doesn't exist:
   ```
   c:\xampp\htdocs\res_event\vendor\
   ```

2. Copy the extracted files to:
   ```
   c:\xampp\htdocs\res_event\vendor\google\
   ```

3. The structure should look like:
   ```
   vendor/
   └── google/
       └── apiclient/
           └── src/
               └── Google/
   ```

### Step 3: Create Autoloader

Create a file `vendor/autoload.php`:

```php
<?php
// Simple autoloader for Google API Client
spl_autoload_register(function ($class) {
    $prefix = 'Google_';
    $base_dir = __DIR__ . '/google/apiclient/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('_', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
```

## Option 3: Use Composer.phar (No Installation Required)

1. Download `composer.phar` from: https://getcomposer.org/composer.phar
2. Place it in your project root: `c:\xampp\htdocs\res_event\composer.phar`
3. Run:
   ```bash
   php composer.phar require google/apiclient
   ```

## After Installation

Once Google API Client is installed, you need to:

1. **Update ReservationController.php** to use Gmail API instead of SMTP
2. **Authorize the application** (first-time setup)
3. **Test email sending**

## Next Steps

After installation, see `README_GMAIL_API_SETUP.md` for:
- How to authorize the application
- How to switch from SMTP to Gmail API
- Testing instructions






