# CMS Assignment - Code Structure & Function Documentation

This document provides detailed explanations of all PHP files, functions, and their logic throughout the CMS application.

## Table of Contents

1. [Application Overview](#application-overview)
2. [Controllers](#controllers)
3. [Models](#models)
4. [Configuration Files](#configuration-files)
5. [Database Structure](#database-structure)
6. [Function Reference Guide](#function-reference-guide)

---

## Application Overview

The CMS is built on **CodeIgniter 3** (MVC Framework) with the following architecture:

```
Request → Router → Controller → Model → View → Response
```

**Key Components:**
- **Controllers**: Handle requests and business logic
- **Models**: Manage database operations
- **Views**: Display data to users
- **Config**: Application settings and routing

---

## Controllers

Controllers handle HTTP requests and coordinate between models and views.

### 1. Auth Controller (`application/controllers/Auth.php`)

**Purpose:** Handle user authentication (login, registration, logout)

#### Functions:

##### `__construct()`
- Called when Auth controller is instantiated
- Loads the `User_model` model
- Loads the `form_validation` library for input validation
- **Logic:** Initializes dependencies

##### `login()`
- **Purpose:** Authenticate user credentials
- **Process:**
  1. Checks if form is posted (`$this->input->post()`)
  2. Sets validation rules:
     - Email must be valid format
     - Password is required
  3. If validation passes:
     - Gets email and password from form
     - Queries database for user by email via `User_model->get_user_by_email()`
     - Verifies password using `password_verify()` against hashed password
     - If credentials match:
       - Creates session data with user_id, name, role, logged_in flag
       - Redirects to admin/dashboard (if admin) or user/dashboard (if user)
     - If credentials don't match:
       - Sets flashdata error message
       - Redirects back to login page
- **Security:** Uses BCrypt password hashing verification

##### `register()`
- **Purpose:** Create new user account
- **Process:**
  1. Checks if form is posted
  2. Sets validation rules:
     - Name: required, minimum 3 characters
     - Email: required, valid format, must be unique in database
     - Password: required, minimum 6 characters
  3. If validation passes:
     - Creates user data array with hashed password (BCrypt)
     - Sets role to 'user' (hardcoded - cannot be set via form)
     - Inserts into database via `User_model->insert_user()`
     - Shows success message
     - Redirects to login page
- **Security:** Passwords hashed, email uniqueness enforced, role fixed to 'user'

##### `logout()`
- **Purpose:** Destroy user session and log out
- **Logic:** 
  - Destroys current session using `$this->session->sess_destroy()`
  - Redirects to login page

---

### 2. Admin Controller (`application/controllers/Admin.php`)

**Purpose:** Administrative functions - manage users and view dashboard

**Authorization:** 
- Requires user to be logged in
- Requires user role to be 'admin'
- Redirects to login if not authenticated

#### Functions:

##### `__construct()`
- Loads models: `User_model`, `Post_model`, `Api_model`
- Loads `form_validation` library
- Checks if user is logged in, redirects to login if not
- Checks if user role is 'admin', shows error if not

##### `dashboard()`
- **Purpose:** Display admin overview/statistics
- **Logic:**
  1. Gets total users count via `User_model->get_total_users()`
  2. Gets total posts count via `Post_model->get_total_posts()`
  3. Gets 5 most recent posts via `Post_model->get_all_posts(5, 0)`
  4. Fetches random cat fact via `Api_model->get_cat_fact()`
     - This calls external API to get interesting cat facts
  5. Passes all data to view for display
- **Data Returned:** Users count, posts count, recent posts, cat fact

##### `users()`
- **Purpose:** Display list of all users in the system
- **Logic:**
  1. Gets all users from database via `User_model->get_all_users()`
  2. Passes user list to view 'admin/users' for display
- **Data Returned:** Array of all users with their details

##### `user_create()`
- **Purpose:** Display form to create new user
- **Logic:**
  - Simple view rendering - loads 'admin/user_form' template
- **Action:** Admin fills form and submits to `user_store()`

##### `user_store()`
- **Purpose:** Process new user creation
- **Validation Rules:**
  - Name: required, minimum 3 characters
  - Email: required, valid format, must be unique
  - Password: required, minimum 6 characters
  - Role: required, must be 'admin' or 'user'
- **Process:**
  1. Validates all inputs
  2. If validation fails, redirects back to user_create form
  3. If validation passes:
     - Gets clean inputs via `$this->input->post('field', TRUE)`
     - Hashes password using BCrypt
     - Inserts user record via `User_model->insert_user()`
     - Sets success message
     - Redirects to users list
- **Security:** Input sanitization, strong password hashing

##### `user_edit($id)`
- **Purpose:** Display form to edit existing user
- **Parameters:** `$id` - User ID to edit
- **Logic:**
  - Gets user data via `User_model->get_user($id)`
  - Passes user data to edit form view
  - User data pre-populated in form

##### `user_update($id)`
- **Purpose:** Process user update
- **Parameters:** `$id` - User ID to update
- **Process:**
  1. Gets posted form data
  2. Validates inputs
  3. Updates user record via `User_model->update_user($id, $data)`
  4. Sets success message
  5. Redirects to users list

##### `user_delete($id)`
- **Purpose:** Delete user account
- **Parameters:** `$id` - User ID to delete
- **Process:**
  - Calls `User_model->delete_user($id)` to remove from database
  - Cascading delete removes user's posts (via FOREIGN KEY constraint)
  - Redirects back to users list

---

### 3. Posts Controller (`application/controllers/Posts.php`)

**Purpose:** Handle post creation, editing, viewing, and deletion

**Authorization:**
- Requires user to be logged in
- Works for both admin and user roles (with restrictions for users)

#### Functions:

##### `__construct()`
- Loads models: `Post_model`, `Category_model`
- Loads `form_validation` library
- Checks if user is logged in

##### `index()`
- **Purpose:** Display paginated list of posts
- **Logic:**
  1. Determines user role from session
  2. Determines which posts to show based on role:
     - **Admin:** All posts in database
     - **User:** Only their own posts
  3. Gets total count of posts
  4. Sets up pagination configuration:
     - 10 posts per page
     - Bootstrap CSS classes for styling
     - Creates pagination links
  5. Gets current page offset from URL segment 3
  6. Retrieves paginated posts based on role
  7. Passes posts and pagination links to view
- **Pagination:** Uses CodeIgniter pagination library with custom styling

##### `create()`
- **Purpose:** Display form to create new post
- **Logic:**
  1. Gets all categories via `Category_model->get_all_categories()`
  2. Loads create post form view with category dropdown
- **Form Fields:** Title, content, category selection, status

##### `store()`
- **Purpose:** Save new post to database
- **Validation Rules:**
  - Title: required, minimum 3 characters, maximum 255
  - Content: required, minimum 10 characters
- **Process:**
  1. Validates form inputs
  2. If validation fails, redirects back to create form
  3. Gets user_id from session
  4. Creates post data array with title, content, status
  5. Inserts post via `Post_model->insert_post($data)`
  6. Gets newly inserted post ID
  7. Assigns categories via `Category_model->assign_categories()`
  8. Sets success message
  9. Redirects to posts list
- **Logic:** User automatically becomes post author (user_id from session)

##### `edit($id)`
- **Purpose:** Display form to edit existing post
- **Parameters:** `$id` - Post ID to edit
- **Logic:**
  1. Gets post data via `Post_model->get_post($id)`
  2. Checks user authorization:
     - Admin can edit any post
     - User can only edit own posts
  3. Gets all categories via `Category_model->get_all_categories()`
  4. Gets post's current categories via `Category_model->get_post_categories($id)`
  5. Passes post, categories, and assigned categories to form view

##### `update($id)`
- **Purpose:** Save post updates
- **Parameters:** `$id` - Post ID to update
- **Process:**
  1. Validates inputs (same as store validation)
  2. Checks authorization (user can only update own posts)
  3. Gets posted data (title, content, status)
  4. Updates post via `Post_model->update_post($id, $data)`
  5. Updates category assignments via `Category_model->assign_categories()`
  6. Sets success message
  7. Redirects to posts list

##### `delete($id)`
- **Purpose:** Delete post
- **Parameters:** `$id` - Post ID to delete
- **Authorization:** Admin can delete any post, users can only delete own
- **Process:**
  - Checks authorization
  - Deletes post via `Post_model->delete_post($id)`
  - Category assignments auto-deleted (via foreign key)
  - Redirects to posts list

##### `view($id)`
- **Purpose:** Display individual post with details
- **Parameters:** `$id` - Post ID to view
- **Logic:**
  1. Gets post data via `Post_model->get_post($id)`
  2. Gets post categories via `Category_model->get_post_categories($id)`
  3. Gets user who created post
  4. Passes all data to single post view
- **Display:** Shows title, content, author, date, categories

---

### 4. User Controller (`application/controllers/User.php`)

**Purpose:** User account management and dashboard

**Authorization:**
- Requires logged-in user with 'user' role (not admin)

#### Functions:

##### `__construct()`
- Loads models: `Post_model`, `User_model`
- Loads `form_validation` library
- Checks if user is logged in
- Checks if user role is 'user' (not admin)

##### `dashboard()`
- **Purpose:** Display user overview page
- **Logic:**
  1. Gets user_id from session
  2. Gets user's posts via `Post_model->get_user_posts($user_id)`
  3. Counts user's posts
  4. Gets total system users via `User_model->get_total_users()`
  5. Passes data to user dashboard view
- **Data Shown:** Personal post count, blog post stats, system info

##### `profile()`
- **Purpose:** Display user profile information
- **Logic:**
  1. Gets user_id from session
  2. Retrieves user data via `User_model->get_user($user_id)`
  3. If user not found, shows 404 error
  4. Passes user data to profile view
- **Data Shown:** Name, email, role, creation date

##### `profile_update()`
- **Purpose:** Update user profile information
- **Validation Rules:**
  - Name: required, minimum 3 characters
  - Email: required, valid format
  - Password: optional, minimum 6 characters if provided
- **Process:**
  1. Validates inputs
  2. If validation fails, redirects back to profile
  3. Updates user record with new data
  4. If password provided, hashes and updates it
  5. Updates session data with new information
  6. Sets success message
  7. Redirects back to profile
- **Logic:** User can change name, email, or password

---

## Models

Models handle all database operations and business logic for data management.

### 1. User_model (`application/models/User_model.php`)

**Purpose:** Handle all user-related database operations

#### Functions:

##### `get_user_by_email($email)`
- **Purpose:** Find user by email address
- **Parameter:** `$email` - Email address to search
- **Returns:** Single user object or NULL if not found
- **Query:** `SELECT * FROM users WHERE email = ?`
- **Usage:** Used in login to verify credentials

##### `insert_user($data)`
- **Purpose:** Create new user account
- **Parameter:** `$data` - Array with user info (name, email, password, role)
- **Returns:** TRUE/FALSE based on success
- **Query:** `INSERT INTO users (name, email, password, role) VALUES (...)`
- **Usage:** Called during registration and admin user creation

##### `get_total_users()`
- **Purpose:** Get count of all users
- **Returns:** Integer count of users
- **Query:** `SELECT COUNT(*) FROM users`
- **Usage:** Dashboard statistics

##### `get_user($id)`
- **Purpose:** Get single user by ID
- **Parameter:** `$id` - User ID
- **Returns:** Single user object or NULL
- **Query:** `SELECT * FROM users WHERE id = ?`
- **Usage:** Profile viewing, editing

##### `get_all_users()`
- **Purpose:** Get all users in system (ordered by newest first)
- **Returns:** Array of user objects
- **Query:** `SELECT * FROM users ORDER BY created_at DESC`
- **Usage:** Admin user management list

##### `update_user($id, $data)`
- **Purpose:** Update user information
- **Parameters:** 
  - `$id` - User ID to update
  - `$data` - Array of fields to update
- **Returns:** TRUE/FALSE
- **Query:** `UPDATE users SET ... WHERE id = ?`
- **Usage:** Profile updates, admin user edits

##### `delete_user($id)`
- **Purpose:** Delete user account
- **Parameter:** `$id` - User ID to delete
- **Returns:** TRUE/FALSE
- **Query:** `DELETE FROM users WHERE id = ?`
- **Side Effect:** All user's posts deleted via CASCADE
- **Usage:** Admin user removal

---

### 2. Post_model (`application/models/Post_model.php`)

**Purpose:** Handle all post-related database operations

#### Functions:

##### `get_all_posts($limit = 0, $offset = 0)`
- **Purpose:** Get published posts with optional pagination
- **Parameters:**
  - `$limit` - Number of posts to retrieve (0 = all)
  - `$offset` - Starting position for pagination
- **Returns:** Array of post objects with author names
- **Query:** Joins posts with users table, filters by published status
- **Ordering:** Newest posts first (DESC by created_at)
- **Usage:** Homepage, public post listings

##### `get_user_posts($user_id, $limit = 0, $offset = 0)`
- **Purpose:** Get all posts by specific user (draft and published)
- **Parameters:**
  - `$user_id` - Author's user ID
  - `$limit` - Results per page
  - `$offset` - Pagination offset
- **Returns:** Array of user's posts
- **Logic:** Returns all posts regardless of status (user can see drafts)
- **Usage:** User's personal post list, dashboard

##### `get_published_user_posts($user_id, $limit = 0, $offset = 0)`
- **Purpose:** Get only published posts by user
- **Parameters:** Same as above
- **Returns:** Array of published posts only
- **Usage:** Public author profile, published work display

##### `get_post($id)`
- **Purpose:** Get single post by ID
- **Parameter:** `$id` - Post ID
- **Returns:** Single post object
- **Query:** `SELECT * FROM posts WHERE id = ?`
- **Usage:** Viewing/editing individual post

##### `insert_post($data)`
- **Purpose:** Create new post
- **Parameter:** `$data` - Array (user_id, title, content, status)
- **Returns:** TRUE/FALSE
- **Query:** `INSERT INTO posts ...`
- **Default Status:** 'draft' if not specified

##### `update_post($id, $data)`
- **Purpose:** Update post content
- **Parameters:**
  - `$id` - Post ID
  - `$data` - Fields to update (title, content, status)
- **Returns:** TRUE/FALSE
- **Query:** `UPDATE posts SET ... WHERE id = ?`

##### `delete_post($id)`
- **Purpose:** Delete post
- **Parameter:** `$id` - Post ID
- **Returns:** TRUE/FALSE
- **Side Effect:** Post categories auto-deleted via FOREIGN KEY
- **Query:** `DELETE FROM posts WHERE id = ?`

##### `get_total_posts()`
- **Purpose:** Count all posts in system
- **Returns:** Integer count
- **Query:** `SELECT COUNT(*) FROM posts`
- **Usage:** Statistics, pagination total

##### `filter_posts($author = '', $date = '')`
- **Purpose:** Search/filter posts by author name and/or date
- **Parameters:**
  - `$author` - Author name pattern (optional)
  - `$date` - Creation date YYYY-MM-DD (optional)
- **Returns:** Array of filtered posts
- **Security:**
  - Sanitizes inputs (strips to max 100 chars)
  - Validates date format (YYYY-MM-DD)
  - Implements access control (users see only own posts or published)
- **Query:** Uses LIKE for author, DATE() function for date matching
- **Role Logic:**
  - Admin: Sees all posts
  - User: Sees own posts only
  - Others: See published posts only

##### `search_posts($keyword = '', $limit = 0, $offset = 0)`
- **Purpose:** Full-text search posts by title and content
- **Parameters:**
  - `$keyword` - Search term (minimum 2 characters)
  - `$limit` - Results per page
  - `$offset` - Pagination offset
- **Returns:** Array of matching posts
- **Security:**
  - Validates keyword length minimum 2 chars
  - Sanitizes to prevent injection
  - Implements role-based filtering
- **Search:** Searches in title and content using LIKE
- **Role Logic:** Same as filter_posts

---

### 3. Category_model (`application/models/Category_model.php`)

**Purpose:** Manage post categories and category assignments

#### Functions:

##### `get_all_categories()`
- **Purpose:** Get all categories in system
- **Returns:** Array of category objects
- **Query:** `SELECT * FROM categories ORDER BY name ASC`
- **Usage:** Category dropdown in post forms, category listings

##### `get_category($id)`
- **Purpose:** Get single category by ID
- **Parameter:** `$id` - Category ID
- **Returns:** Single category object
- **Query:** `SELECT * FROM categories WHERE id = ?`

##### `get_post_categories($post_id)`
- **Purpose:** Get all categories assigned to a post
- **Parameter:** `$post_id` - Post ID
- **Returns:** Array of category objects
- **Query:** Joins post_categories with categories table
- **Usage:** Displaying post tags, form pre-selection

##### `assign_categories($post_id, $category_ids = array())`
- **Purpose:** Update category assignments for a post
- **Parameters:**
  - `$post_id` - Post to update
  - `$category_ids` - Array of category IDs
- **Process:**
  1. Deletes all existing category assignments
  2. Inserts new category assignments
- **Logic:** Clears old then adds new (complete replacement)
- **Usage:** Post creation/update category assignment

##### `get_posts_by_category($category_id, $limit = 0, $offset = 0)`
- **Purpose:** Get all published posts in a category
- **Parameters:**
  - `$category_id` - Category ID
  - `$limit` - Results per page
  - `$offset` - Pagination offset
- **Returns:** Array of posts with author names
- **Query:** Joins posts, users, and post_categories tables
- **Filters:** Only published posts
- **Usage:** Category archive pages

##### `get_total_posts_by_category($category_id)`
- **Purpose:** Count posts in a category
- **Parameter:** `$category_id` - Category ID
- **Returns:** Integer count
- **Usage:** Pagination total for category pages

---

### 4. Api_model (`application/models/Api_model.php`)

**Purpose:** Handle external API integrations

#### Functions:

##### `get_cat_fact()`
- **Purpose:** Fetch random cat fact from external API
- **External API:** https://catfact.ninja/fact
- **Returns:** String containing a cat fact
- **Process:**
  1. Initializes cURL connection
  2. Sets cURL options:
     - URL: Cat fact API endpoint
     - RETURNTRANSFER: Returns response as string
     - TIMEOUT: 5 second timeout
     - SSL_VERIFYPEER: Disabled for compatibility
  3. Executes request and gets response
  4. Checks for cURL errors - logs and returns error message
  5. Checks HTTP response code - logs and returns error if not 200
  6. Parses JSON response
  7. Validates JSON parsing - returns error if invalid
  8. Extracts 'fact' field from response
- **Error Handling:** Returns readable error messages for each failure type
- **Logging:** Logs errors to `application/logs/` for debugging
- **Security:** Uses proper error handling, validates response
- **Usage:** Display on admin dashboard for engagement

---

## Configuration Files

### 1. Database Configuration (`application/config/database.php`)

**Purpose:** Database connection settings

**Key Settings:**
```
'hostname' => 'localhost'        // MySQL server location
'username' => 'root'             // MySQL user
'password' => ''                 // MySQL password
'database' => 'cms_assignment'   // Database name
'dbdriver' => 'mysqli'           // Use MySQLi driver
'char_set' => 'utf8mb4'          // UTF-8 character encoding
'db_debug' => TRUE               // Show errors (set FALSE in production)
```

### 2. Application Configuration (`application/config/config.php`)

**Purpose:** Core application settings

**Key Settings:**
```
'base_url' => 'http://localhost/cms_assignment/'  // Site URL
'index_page' => ''                                 // URL rewriting enabled
'encryption_key' => '...'                          // Used for encryption
```

### 3. Routes Configuration (`application/config/routes.php`)

**Purpose:** URL routing rules

**Default Routes:**
- `login` → Auth controller login method
- `register` → Auth controller register method
- `logout` → Auth controller logout method
- `admin/...` → Admin controller methods
- `user/...` → User controller methods
- `posts/...` → Posts controller methods

---

## Database Structure

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

**Fields:**
- `id`: Unique user identifier
- `name`: User's full name
- `email`: Email (must be unique)
- `password`: BCrypt hashed password (255 chars for hash)
- `role`: Either 'admin' or 'user'
- `created_at`: Auto-set creation date

### Posts Table
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft','published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
```

**Fields:**
- `id`: Unique post identifier
- `user_id`: Author's user ID (foreign key)
- `title`: Post headline
- `content`: Post body text
- `status`: 'draft' (unpublished) or 'published'
- `created_at`: Post creation timestamp
- **CASCADE**: Deleting user deletes their posts

### Categories Table
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

**Fields:**
- `id`: Unique category identifier
- `name`: Category name (stored unique)
- `slug`: URL-friendly version
- `created_at`: Creation timestamp

### Post_Categories Table (Junction)
```sql
CREATE TABLE post_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY (post_id, category_id)
)
```

**Purpose:** Many-to-many relationship between posts and categories

**Fields:**
- `id`: Record identifier
- `post_id`: Reference to post
- `category_id`: Reference to category
- **Constraints:** Each post-category pair is unique, deletion cascades

---

## Function Reference Guide

### Authentication Flow

```
1. User submits login form
   ↓
2. Auth::login() validates inputs
   ↓
3. User_model::get_user_by_email() retrieves user
   ↓
4. password_verify() checks password
   ↓
5. Session set with user data
   ↓
6. Redirect to dashboard (admin or user)
```

### Post Creation Flow

```
1. User clicks "Create Post"
   ↓
2. Posts::create() loads form with categories
   ↓
3. User submits form
   ↓
4. Posts::store() validates inputs
   ↓
5. Post_model::insert_post() creates post record
   ↓
6. Category_model::assign_categories() links categories
   ↓
7. Redirect to posts list with success message
```

### Role-Based Access Control

**Admin Access:**
- `/admin/*` - Admin panel
- Can manage all users
- Can edit/delete any post
- Can view all posts (published and draft)

**User Access:**
- `/user/*` - User dashboard
- Can only manage own posts
- Cannot access admin panel
- In posts list, can only see own posts

**Guest Access:**
- `/login` - Login page
- `/register` - Registration page
- All other pages require login

### Security Measures

1. **Password Hashing:** BCrypt with automatic salt
2. **Input Validation:** Form validation library catches invalid inputs
3. **SQL Injection Prevention:** CodeIgniter Query Builder escapes all inputs
4. **Session Security:** User roles verified on every request
5. **Authorization:** Role checks in __construct of restricted controllers
6. **CSRF Protection:** CodeIgniter CSRF token (if enabled)
7. **Email Uniqueness:** Database constraint prevents duplicate emails

---

## Common Operations

### How to Add a New User
```
1. Admin goes to Admin → Users → Create
2. Admin::user_create() displays form
3. Admin fills Name, Email, Password, Role
4. Form submitted to Admin::user_store()
5. Validation checks inputs
6. Password hashed with BCrypt
7. User_model::insert_user() saves to database
8. Success message shown, list refreshed
```

### How to Create/Edit a Post
```
1. User goes to Posts → Create
2. Posts::create() loads form
3. Form displays dropdown from Category_model::get_all_categories()
4. User fills Title, Content, selects Categories, sets Status
5. Form submitted to Posts::store() (new) or Posts::update() (edit)
6. Validation ensures required fields
7. Post_model saves/updates post
8. Category_model::assign_categories() updates category links
9. User redirected to posts list
```

### How Search Works
```
1. User enters search term on posts page
2. Form submitted to Posts::search()
3. Post_model::search_posts() runs with keyword
4. Query searches title and content fields
5. Results filtered by user role:
   - Admin: All posts
   - User: Own posts only
6. Results displayed with pagination
```

---

## Performance Considerations

1. **Database Indexing:** Email, user_id, post_id are indexed
2. **Pagination:** Large result sets use pagination (10 posts per page)
3. **Query Optimization:** Joins used efficiently to reduce queries
4. **Caching:** Posts can be cached (optional, disabled by default)
5. **API Timeout:** External API calls timeout after 5 seconds

---

## Error Handling

**Types of Errors:**
1. **Validation Errors:** Form validation messages shown on form page
2. **Database Errors:** Logged to `application/logs/` (debug mode)
3. **Authorization Errors:** show_error() displays "Unauthorized access"
4. **Not Found Errors:** show_404() displays 404 page
5. **API Errors:** Graceful fallback messages for failed API calls

**Debug Mode:**
- Enable in `config.php`: `define('ENVIRONMENT', 'development')`
- Shows detailed error messages
- Set to 'production' to hide errors from users

---

## Summary

This CMS provides:
- **User Management:** Registration, login, profile editing
- **Post Management:** Create, edit, publish, categorize posts
- **Admin Panel:** User management, statistics, system overview
- **Role-Based Access:** Different permissions for admin and users
- **Data Integrity:** Foreign keys, cascading deletes, validation
- **Security:** Password hashing, input validation, access control
- **API Integration:** External content (cat facts)

All code follows CodeIgniter conventions and MVC patterns for maintainability and scalability.

