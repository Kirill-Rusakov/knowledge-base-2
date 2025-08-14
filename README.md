# knowledge-base-2 "Deal-Contact Manager PHP Application"
A CRM-inspired system for managing deals and contacts with bi-directional relationships, built with pure PHP using OOP principles.

## Features
- **Dual Entity Management**: Handle deals and contacts with cross-references
- **CRUD Operations**: Create, Read, Update, Delete functionality for both entities
- **Relationship Mapping**: 
  - Multiple contacts per deal
  - Multiple deals per contact
- **Interactive UI**: Three-panel layout (Menu → List → Details)
- **Data Persistence**: File-based JSON storage (easily upgradable to DB)

## Tech Stack
- **Backend**: PHP 7.4+ (Object-Oriented)
- **Frontend**: Vanilla HTML/CSS with modal forms
- **Storage**: JSON file storage with automatic initialization

## Project Structure
```bash
knowledge-base-2
├── index.php # Main application entry
├── classes/
│ ├── Database.php # Data persistence layer
│ ├── Entity.php # Abstract base entity
│ ├── Deal.php # Deal business object
│ └── Contact.php # Contact business object
├── assets/
│ └── style.css # Responsive styling
└── data/ # Auto-generated storage
└── data.json # JSON database
```

## Installation
1. Clone repository:
```bash
git clone https://github.com/Kirill-Rusakov/knowledge-base-2.git
```

2.Ensure write permissions for data/ directory:
```bash
chmod 755 knowledge-base-2/data
```

3.Start PHP server:
```bash
cd knowledge-base-2 && php -S localhost:8000
```

4.Open in browser:
```bash
http://localhost:8000
```
