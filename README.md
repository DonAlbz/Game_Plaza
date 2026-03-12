# Game Plaza

A school project gamer's social platform built with PHP, MySQL, Bootstrap, and vanilla JavaScript.

## Features

- User registration, login, and profile management
- Game library browsing and personal game collection
- Matchmaking suggestions with social follow prioritization
- Match creation, joining, and participant management
- Social follow and feed experience for followed players
- Simple responsive UI using Bootstrap 5 and vanilla JS

## Project Structure

- `api/` - JSON endpoints for auth, users, games, matchmaking, social, and matches
- `models/` - Data layer and database access classes using PDO
- `views/` - PHP front-end pages and layout templates
- `assets/` - CSS and JavaScript assets for pages
- `config/` - Application settings and database configuration
- `game_plaza.sql` - Database schema and seed data

## Requirements

- PHP 7.4 or newer
- MySQL / MariaDB
- Web server (Apache, Nginx) or PHP built-in server

## Installation

1. Copy the project to your web server document root or your local workspace.
2. Create the MySQL database and import the schema:

```bash
mysql -u root -p < game_plaza.sql
```

3. Update database settings in `config/config.php` if needed:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'game_plaza');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. Set the application base URL if you are not serving from `/game-plaza`:

```php
define('APP_BASE_URL', '/game-plaza');
```

If you serve the site from the server root, change this to `/`.

## Running Locally

### Option 1: PHP built-in server

From the project root:

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000
```

If `APP_BASE_URL` is `/game-plaza`, use:

```text
http://localhost:8000/game-plaza
```

### Option 2: Apache / Nginx

1. Configure your web server document root to point at the project folder.
2. Enable PHP support.
3. Restart the server.
4. Visit the configured URL in your browser.

## Usage

- Register a new user from the login page.
- Update profile preferences and your game library.
- Use the Discover page to find players and follow them.
- Use Matchmaking to get suggestions and create or join matches.
- View the Feed page to see followed players.

## Notes

- Session management is handled through `config/session.php`.
- API responses are JSON-based and used by the front-end JavaScript.
- Adjust any local database credentials, base URL settings, or PHP server config as required.

## API Endpoints

All endpoints return JSON. Authentication uses sessions set during login.

### Authentication
- `POST /api/auth/register.php` - Register a new user
- `POST /api/auth/login.php` - Login and create session
- `POST /api/auth/logout.php` - Logout and destroy session

### Users
- `GET /api/users/get.php` - Get current user profile
- `POST /api/users/update.php` - Update user profile and preferences
- `GET /api/users/search.php` - Search for users by username

### Games
- `GET /api/games/catalog.php` - Get all available games
- `GET /api/games/library.php` - Get current user's game library
- `POST /api/games/add.php` - Add a game to user library
- `POST /api/games/remove.php` - Remove a game from user library

### Matchmaking
- `GET /api/matchmaking/suggestions.php` - Get player suggestions based on compatibility (prioritizes followed players)

### Matches
- `GET /api/matches/list.php` - Get all matches
- `POST /api/matches/create.php` - Create a new match
- `POST /api/matches/join.php` - Join an existing match

### Social
- `POST /api/social/follow.php` - Follow a user
- `POST /api/social/like.php` - Like a user
- `GET /api/social/followers.php` - Get followers of current user
- `GET /api/social/following.php` - Get users followed by current user

## Troubleshooting

- If pages do not load, verify PHP is running and the correct document root is set.
- If database connections fail, verify MySQL credentials in `config/config.php`.
- If using a subfolder, make sure `APP_BASE_URL` matches the folder path.
