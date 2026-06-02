# 🎓 Student Management System

A simple, clean, and well-structured **CRUD (Create, Read, Update, Delete)** web application built with **PHP**, **MySQL**, and **Bootstrap**. This project demonstrates basic database operations and is ideal for beginners learning backend web development.

---

## 🚀 Features

* **Create**: Add new student records (Name, Email, Phone, Course, etc.) to the database.
* **Read**: View a structured list of all registered students in a clean data table.
* **Update**: Edit existing student information easily with pre-filled forms.
* **Delete**: Remove student profiles from the database securely.
* **Responsive UI**: Built with Bootstrap to ensure seamless viewing across mobile, tablet, and desktop screens.

---

## 🛠️ Tech Stack

* **Frontend**: HTML5, CSS3, Bootstrap 5
* **Backend**: PHP (Procedural/OOP)
* **Database**: MySQL

---

## 📂 Project Structure

```text
├── config.php      # Database connection configuration
├── index.php       # Dashboard / View all students (Read)
├── create.php      # Form to add a new student (Create)
├── edit.php        # Form to modify student data (Update)
├── DBMS.txt        # Database schema / SQL queries setup
└── README.md       # Project documentation
```

---

## ⚙️ Installation & Setup

Follow these steps to run the project locally on your machine (using XAMPP/WAMP):

### 1. Clone the Repository
```bash
git clone https://github.com
```
*Move the cloned folder into your local server directory (e.g., `htdocs` for XAMPP).*

### 2. Database Setup
1. Open your browser and go to `http://localhost/phpmyadmin/`.
2. Create a new database named `student_db` (or as specified in your `DBMS.txt`).
3. Import the SQL queries from `DBMS.txt` to create the required tables.

### 3. Configure Connection
Open `config.php` and make sure your database credentials match your local server environment:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";
```

### 4. Run the Application
Open your browser and navigate to:
```text
http://localhost/Student-Management-System/index.php
```

---

## 👨‍💻 Author

* **Mj Owaleullah** - [GitHub Profile](https://github.com/mjowaleullah)

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
