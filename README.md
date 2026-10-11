# Apttitude

A modern placement preparation & assessment web platform designed for students preparing for campus placements, competitive exams, and technical interviews.

## 🚀 Features

- **Practice & Assessments**: Quantitative Aptitude, Logical Reasoning, Verbal Ability, and Technical subjects (C, C++, Java, Python, SQL, DSA, etc.).
- **Company-Specific PYQs**: Curated question banks and previous years' questions for top hiring companies (TCS, Infosys, Wipro, Cognizant, Innovature, etc.).
- **Gamified Learning**: Badges, streaks, achievement unlocks, and performance analytics.
- **Responsive & Modern UI**: Smooth dark/light theme switching, responsive design optimized for mobile and desktop screens.
- **Admin Dashboard**: Comprehensive question and category management, user progress tracking, and platform analytics.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native PDO, REST endpoints, clean modular architecture)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Vanilla JavaScript (ES6+), CSS3 (Custom design system with CSS custom properties)
- **Authentication**: Session-based auth with Google Sign-In (OAuth 2.0) support

## 📁 Project Structure

```text
Apttitudee/
├── app/                  # Application core (Controllers, Models, Views)
├── config/               # Database and app configuration
├── data/                 # JSON data sets and question imports
├── database/             # Schema definitions and migrations
├── public/               # Public assets (CSS, JS, images)
│   ├── css/              # Stylesheets & themes
│   ├── js/               # Client-side scripts
│   └── index.php         # Main application entry point
├── .gitignore
└── README.md
```

## ⚙️ Setup & Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/alanjoyt/Apttitude.git
   ```

2. **Web Server Setup**:
   - Place inside your local web server root (e.g., `wamp64/www/Apttitudee` or `htdocs/Apttitudee`).
   - Ensure Apache rewrite module (`mod_rewrite`) is enabled.

3. **Database Configuration**:
   - Import `database/schema.sql` into MySQL.
   - Configure your database credentials in `config/db.php` or provide environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
   - Run seed scripts if needed.

4. **Launch**:
   - Navigate to `http://localhost/Apttitudee/public/` in your browser.
