# RebornArt Event Reservation System

A comprehensive event management and reservation system built for RebornArt, enabling users to create, manage, and reserve events with integrated email notifications and admin dashboard functionality.

## 📋 Table of Contents

- [Features](#features)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Technologies](#technologies)
- [Email Setup](#email-setup)
- [Development](#development)

## ✨ Features

- **Event Management**
  - Create and manage events with detailed descriptions
  - Support for multiple event categories
  - Event listing with filtering and search capabilities
  - Event detail views with reservation information

- **Reservation System**
  - Easy-to-use reservation booking interface
  - Reservation status tracking (pending, confirmed, cancelled)
  - User reservation history and management
  - Admin reservation overview and controls

- **User Management**
  - User registration and authentication
  - User profile management
  - Email verification with codes
  - Admin user management interface

- **Email Notifications**
  - Gmail API integration for email delivery
  - PHPMailer fallback support
  - Automated confirmation emails
  - Reservation status notifications
  - Admin alerts for new reservations

- **Admin Dashboard**
  - Comprehensive event statistics
  - PDF report generation for events and reservations
  - User and reservation management
  - System health checks

## 📁 Project Structure

```
res_event/
├── app/                          # Core application logic
│   ├── controller/              # Application controllers
│   │   ├── EventController.php
│   │   └── ReservationController.php
│   ├── model/                   # Data models
│   │   ├── Database.php
│   │   ├── Event.php
│   │   └── Reservation.php
│   ├── service/                 # Business services
│   │   ├── GmailApiService.php
│   │   └── MailService.php
│   └── view/                    # View templates
│       ├── admin_list.php
│       ├── dashboard.php
│       ├── event_*.php
│       └── home.php
├── assets/                      # Static resources
│   ├── css/                     # Stylesheets
│   ├── js/                      # JavaScript files
│   ├── fonts/                   # Web fonts
│   └── images/                  # Images and graphics
├── config/                      # Configuration files
│   ├── credentials.json         # Gmail API credentials
│   ├── email_config.php         # Email settings
│   └── gmail_config.php         # Gmail API configuration
├── controller/                  # Legacy controllers
│   ├── adminController.php
│   ├── userController.php
│   └── authentication handlers
├── logs/                        # Application logs
├── model/                       # Legacy models
│   ├── config.php
│   └── Utilisateur.php
├── public/                      # Entry point
│   └── index.php
├── uploads/                     # User uploads
│   └── events/
└── views/                       # Admin panel views
    ├── admin/
    ├── Utilisateur/
    └── template files
```

## 🚀 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Apache/XAMPP

### Steps

1. **Clone/Download the Repository**
   ```bash
   cd c:\xampp\htdocs\
   git clone https://github.com/Molka1606/RebornArt.git
   cd res_event
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Database**
   - Create a MySQL database for the event system
   - Import the database schema (if provided)
   - Update database credentials in `model/config.php`

4. **Set Up Environment**
   - Copy and configure `config/email_config.php` with your settings
   - Set up credentials for email service (SMTP or Gmail API)

5. **Create Directories**
   ```bash
   mkdir -p logs uploads/events
   chmod 755 logs uploads
   ```

6. **Access the Application**
   - Navigate to: `http://localhost/res_event/public/`

## ⚙️ Configuration

### Email Configuration

Edit `config/email_config.php`:

```php
// SMTP Configuration
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_FROM', 'noreply@rebornart.com');
```

### Gmail API Setup (Optional)

For enhanced Gmail integration:

1. Follow instructions in `README_GMAIL_API_SETUP.md`
2. Place credentials in `config/credentials.json`
3. Configure in `config/gmail_config.php`

### Database Configuration

Edit `model/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'rebornart_events');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
```

## 📖 Usage

### For Users

1. **Browse Events**
   - Navigate to the events page
   - View event details and availability

2. **Register/Login**
   - Create a user account
   - Verify email with code sent to your inbox

3. **Make Reservations**
   - Select an event and click "Reserve"
   - Fill in reservation details
   - Receive confirmation email

4. **Manage Reservations**
   - View your reservation history
   - Cancel or modify reservations (if allowed)

### For Administrators

1. **Access Admin Dashboard**
   - Navigate to admin panel: `http://localhost/res_event/admin/`
   - Login with admin credentials

2. **Manage Events**
   - Create, edit, and delete events
   - View event statistics and attendees

3. **View Reservations**
   - See all reservations across events
   - Update reservation status
   - Send manual notifications

4. **Generate Reports**
   - Export event data to PDF
   - View analytics and charts

## 🛠️ Technologies

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Email**: PHPMailer, Gmail API
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Custom MVC-based architecture
- **Dependencies**: Composer packages (PHPMailer, etc.)

## 📧 Email Setup

### Quick Start with Gmail

1. **Enable 2-Factor Authentication** on your Google Account
2. **Generate App Password** at: https://myaccount.google.com/apppasswords
3. **Update `config/email_config.php`**:
   - Use your Gmail address as `MAIL_USERNAME`
   - Use the app password (16 characters) as `MAIL_PASSWORD`

### Troubleshooting Email

Run the email test:
```bash
php test_email.php
```

Check the Gmail setup:
```bash
php check_gmail_setup.php
```

See detailed setup guides:
- `README_EMAIL_SETUP.md` - Email configuration guide
- `README_GMAIL_API_SETUP.md` - Gmail API integration
- `SWITCH_TO_GMAIL_API.md` - Switching email services
- `QUICK_FIX_EMAIL.md` - Common email issues

## 👨‍💻 Development

### Running Tests

```bash
# Test email configuration
php test_email.php

# Check Gmail API setup
php check_gmail_setup.php

# Setup Gmail API
php setup_gmail_api.php
```

### Code Organization

- **MVC Pattern**: Models, Views, Controllers separated
- **Services**: Business logic isolated in service classes
- **Configuration**: Centralized in `config/` directory

### Adding New Features

1. Create model in `app/model/` if needed
2. Create controller in `app/controller/`
3. Add views in `app/view/`
4. Register routes in `public/index.php`

## 📝 License

Part of the RebornArt project. See main repository for license details.

## 👥 Contributors

- Development Team - RebornArt

## 📞 Support

For issues, questions, or contributions:
1. Check documentation files in root directory
2. Review code comments
3. Submit issues to the repository

## 🔗 Related Modules

- **User Management**: User authentication and profiles
- **Blog Module**: Content management for RebornArt
- **Admin Panel**: Central administration interface

---

**Last Updated**: December 2025  
**Version**: 1.0.0  
**Status**: Active Development
