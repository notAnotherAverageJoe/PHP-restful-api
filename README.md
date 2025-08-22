# PHP RESTful Task API

This is a **RESTful API** built with plain PHP and PDO for managing tasks.  
It supports basic CRUD (Create, Read, Update, Delete) operations on tasks stored in a database.

## Features

- **GET /tasks** → Retrieve all tasks.
- **POST /tasks** → Create a new task.
- **PUT /tasks?id={id}** → Update an existing task by ID.
- **DELETE /tasks?id={id}** → Delete a task by ID.

## Requirements

- PHP >= 7.4
- PDO extension enabled
- A database (e.g., MySQL, PostgreSQL, or SQLite)
- Web server (Apache, Nginx, or PHP built-in server)
