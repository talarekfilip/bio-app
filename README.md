# Bio Generator

A modern web application that allows users to create and customize their professional portfolio pages.

## Features

- User authentication (register, login, logout)
- Profile customization
  - Avatar upload
  - Display name
  - Bio
  - Location
  - Website
- Responsive design
- Modern UI with smooth animations
- Profile link sharing

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for PHP dependencies)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/talarekfilip/bio-app.git
cd portfolio-generator-v1
```

2. Install PHP dependencies:
```bash
composer install
```

3. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p your_database < app/database/schema.sql
```

4. Configure the application:
   - Update the configuration values in `config.php`

5. Set up your web server:
   - Point the document root to the `app` directory
   - Ensure the `uploads` directory is writable by the web server

## Directory Structure

```
app/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── auth/
├── config/
├── database/
├── vendor/
└── uploads/
```

## Usage

1. Register a new account
2. Log in to your account
3. Customize your profile
4. Share your profile link

## Security

- Passwords are hashed using PHP's password_hash()
- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- CSRF protection
- Secure session handling

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 
