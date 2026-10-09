# LibroSchool

School library management system for cataloging books, managing members, and handling borrowing, returns, and fines.

![Project Screenshot](https://github.com/user-attachments/assets/ef580246-7a2b-45cb-8d21-6bce9a1b20df)

## Key Features

- **Book catalog** with categories and rack locations
- **Member management** for students and staff
- **Borrowing workflow** with borrowing details tracking
- **Book returns** handling
- **Fine management** for late or damaged returns
- **Admin dashboard** powered by Filament
- **Application settings** management
- **Authentication and user management**

## Tech Stack

| Layer       | Technology           |
| ----------- | -------------------- |
| Backend     | Laravel 12, PHP 8.2+ |
| Admin panel | Filament 5           |
| Database    | PostgreSQL           |

## Installation & Setup

1. Clone the repository:

    ```bash
    git clone <repository-url> libroschool-app
    cd libroschool-app
    ```

2. Install PHP dependencies:

    ```bash
    composer install
    ```

3. Set up environment variables:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Configure database in `.env`:

    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=db_libroschool
    DB_USERNAME=postgres
    DB_PASSWORD=
    ```

5. Run migrations:

    ```bash
    php artisan migrate
    ```

    Or use bundled setup:

    ```bash
    composer setup
    ```

6. Install frontend dependencies and build assets:

    ```bash
    npm install
    npm run build
    ```

7. Start the development server:

    ```bash
    php artisan serve
    ```

    Or run full development stack (server, queue, Vite):

    ```bash
    composer run dev
    ```

## License

MIT License. See Laravel framework license at [MIT license](https://opensource.org/licenses/MIT).
