## Running the Project with Docker

This project is containerized using Docker and Docker Compose for a consistent development environment. Below are the project-specific instructions and requirements for running the application.

### Requirements & Dependencies
- **PHP Version:** 8.2 (as specified in the Dockerfile: `php:8.2-apache`)
- **MySQL Version:** Latest (as specified in the compose file: `mysql:latest`)
- **PHP Extensions:** `pdo`, `pdo_mysql`, `gd`, `mbstring`, `zip` (installed in the Dockerfile)
- **Apache:** Used as the web server, with `mod_rewrite` enabled

### Environment Variables
- The MySQL service uses the following environment variables (set in `docker-compose.yml`):
  - `MYSQL_ROOT_PASSWORD`: rootpassword *(change this in production)*
  - `MYSQL_DATABASE`: appdb
  - `MYSQL_USER`: appuser
  - `MYSQL_PASSWORD`: apppassword *(change this in production)*
- If your application requires additional environment variables (e.g., for PHP), you can add a `.env` file in the `src/` directory and uncomment the `env_file` line in the compose file.

### Build & Run Instructions
1. **Build and start the containers:**
   ```sh
   docker-compose up --build
   ```
   This will build the PHP/Apache container from the Dockerfile in `.devcontainer/` and start both the PHP app and MySQL services.

2. **Access the application:**
   - The web application will be available at [http://localhost:8080](http://localhost:8080)
   - MySQL will be accessible on port `3306` for local development

### Special Configuration
- **Document Root:** The Apache document root is set to `/var/www/html/public` to serve the application from the `public/` directory.
- **User Permissions:** The application runs as a non-root user (`appuser`) for improved security.
- **Persistent Database Storage:** MySQL data is stored in a Docker volume (`db_data`) to persist data between container restarts.
- **Network:** Both services are connected via a custom Docker network (`appnet`).

### Ports
- **PHP/Apache:** Exposed on host port `8080` (container port `80`)
- **MySQL:** Exposed on host port `3306` (container port `3306`)

---

*Ensure you change the default MySQL passwords before deploying to production.*