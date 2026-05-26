# My Notebook: Secure Full-Stack Personal Workspace

An optimized, high-performance web platform designed to deliver a desktop-grade note-taking experience directly in the web browser. By manipulating the DOM natively and optimizing backend data pipelines, the application achieves near-zero latency while maintaining a strict, defensive security posture without the overhead of heavy third-party frameworks.

**Live Demo:** [https://gict.xsrv.jp/rk_web/notebook/](https://gict.xsrv.jp/rk_web/notebook/)

**Project Repository:** [https://github.com/helloreese1217/My-Notebook](https://github.com/helloreese1217/My-Notebook)

---

## Tech Stack & Architecture

* **Frontend:** Vanilla JavaScript (ES6+), Semantic HTML5, CSS3 Grid/Flexbox Layouts
* **Backend:** Object-Oriented PHP 8.x (RESTful PDO Data Pipelines)
* **Database:** MySQL (Relational Schema with Automated Cascading Pruning)
* **Dev Environment:** Fully configured for local development via XAMPP (Port 3306) and MAMP (Port 8889)

```
+--------------------------------------------------------+
|                      Client Browser                    |
|  [3-Column UI Layout] <--> [JS Event & Debounce Engine]|
+--------------------------------------------------------+
^                                                        ^
| HTTP Requests (Page Load)                              | AJAX / Fetch API
v                                                        v
+-------------------------+              +-----------------------+
|    index.php Routing    |              |   save_page.php API   |
|   & Sanitation Layer    |              |  (Asynchronous Save)  |
+-------------------------+              +-----------------------+
^                                                        ^
| SQL Queries (PDO)                                      | SQL Queries (PDO)
v                                                        v
+----------------------------------------------------------------+
|                        MySQL Database                          |
|    [notebooks table] <--(ON DELETE CASCADE)--> [pages table]   |
+----------------------------------------------------------------+

```

---

## Key Engineering & Performance Deliverables

### 1. Latency Mitigation via Asynchronous Network Debouncing

To eliminate server-side performance bottlenecks and redundant database writes during long-form text editing, the application features a custom auto-save pipeline. The client-side JavaScript intercepts user keystrokes, clearing the previous execution handle and deferring the network payload by a strict **750ms buffer**. An asynchronous request is dispatched via the **Fetch API** only when the user pauses typing, consolidating hundreds of rapid writes into a single, clean database operation.

### 2. High-Efficiency UI Event Delegation Engine

Instead of allocating independent, memory-heavy click listeners to every single notebook folder or page row dynamically rendered in the interface, the frontend utilizes a centralized **Event Delegation Engine**. A single listener bound to a parent container captures events as they bubble up through the DOM, dynamically managing active column routing, panel states, and spawning inline editing forms with an exceptionally low memory footprint.

### 3. Defensive Security Matrix & Cryptographic Gateways

The backend architecture maintains a Zero-Trust data handling system across all logical routes:

* **SQL Injection Defused:** Relational database transactions pass through native **PDO Parameterized Prepared Statements**. Input tokens are strictly isolated from query command compilation.
* **Type-Gate Sanitation:** High-risk entry points parse incoming URL parameters through strict integer type-casting (`intval()`), neutralizing arbitrary script payloads before queries are compiled.
* **Cross-Site Scripting (XSS) Mitigation:** Data rendered into the browser layout passes through `htmlspecialchars()`, encoding malicious script markers into inert, safe browser strings.

### 4. Relational Data Integrity via Cascading Rules

The storage tier incorporates rigorous structural rules to prevent stale data accumulation or orphaned records. By explicitly declaring an **`ON DELETE CASCADE`** relationship on the foreign key inside the `pages` table, removing a parent notebook folder triggers an atomic, low-overhead database cleanup that wipes all nested pages automatically.

---

## Installation & Local Setup

### 1. Project Directory Placement

Clone or move the project folder directly into your local server root path:

* **XAMPP Windows:** `C:\xampp\htdocs\notebook`
* **MAMP Mac:** `/Applications/MAMP/htdocs/notebook`

### 2. Relational Schema Initialization

Open your local database portal (e.g., `http://localhost/phpmyadmin/`), establish an empty database named `notebook`, and run the following structural definitions in your SQL layout terminal:

```sql
CREATE TABLE IF NOT EXISTS notebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notebook_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (notebook_id) REFERENCES notebooks(id) ON DELETE CASCADE
);

```

### 3. Database Runtime Configuration

Open `db.php` and ensure your centralized database connector options mirror your target environment parameters:

**For XAMPP Default Setup:**

```php
<?php
$host     = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$database = "notebook";
$port     = 3306; // Standard MySQL port
$charset  = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

```

### 4. Launch Local Instance

Activate your target web server and database modules. Open your browser layout and visit:

* **XAMPP Workspace:** `http://localhost/notebook/index.php`
* **MAMP Workspace:** `http://localhost:8888/notebook/index.php`

---

## Technical Refactoring Roadmap

* **Migration to MVC Framework (Laravel):** Transitioning the procedural scripts into an object-oriented Model-View-Controller paradigm with Laravel. This update will automate input token authorization (CSRF validation), move database queries to Eloquent ORM, and isolate logic boundaries using specialized routing middleware.
* **Component-Driven View Management:** Refactoring the frontend tracking code into a component-driven ecosystem like Vue.js or React to extract active page content buffers and sidebar trees into dedicated state management containers.
* **RESTful API Service Layer:** Fully decoupling the backend from the layout engine by transforming the PHP scripts into a stateless RESTful JSON API, allowing alternative client applications to access the note workspace securely.

---

## Contact

* **Developer:** Ryo Koga
* **Email:** ryokoga2004@gmail.com
* **Project Repository:** https://github.com/helloreese1217/My-Notebook
