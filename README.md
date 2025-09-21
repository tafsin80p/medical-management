# Dashboard Portal

A HIPAA-compliant client intake, portal, document delivery, and secure messaging system for WordPress. This plugin provides a secure platform for managing sensitive client data, supporting features like encrypted messaging, secure document sharing, and client intake forms.

## Features

- **Admin Dashboard:** Manage cases, clients, team members, and view analytics.
- **Client Portal:** Secure client dashboard for case management, document uploads, and notifications.
- **Secure Messaging:** Encrypted chat system for admins and clients.
- **Document Delivery:** Upload and share documents securely.
- **Multi-step Intake Form:** Collects personal, service, and consent information.
- **Notifications:** Real-time dashboard notifications for admins and clients.
- **Role-based Access:** Separates admin and client functionality.

## Installation

1. Download or clone the plugin to your WordPress `wp-content/plugins` directory.
2. Activate the plugin from the WordPress admin dashboard.

## Usage

### Admin Dashboard

- Accessible via the WordPress admin menu under "Nexus Dashboard".
- Includes custom CSS and JS for enhanced UI and chat functionality.
- Only loads scripts/styles on the plugin’s admin page for performance.

### Client Portal

- Use the `[pixelcode_client_dashboard]` shortcode to embed the client dashboard on any page.
- Loads Font Awesome icons, custom CSS, and JS for client-side features.
- Secure chat and document delivery available for clients.

## File Structure

```
medical-management/
├── dashboard-portal.php
├── includes/
│   ├── admin-dashboard.php
│   ├── client-dashboard.php
│   └── function.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── client.css
│   └── js/
│       ├── admin.js
│       ├── client.js
│       └── chat.js
```

- **dashboard-portal.php**: Main plugin file, handles script/style enqueuing and includes core modules.
- **includes/**: Contains PHP files for dashboard logic and functions.
- **assets/**: CSS and JS for admin and client dashboards.

## Scripts & Styles

- **Admin:** Loads custom CSS/JS for admin dashboard and chat (`admin.css`, `admin.js`, `chat.js`).
- **Client:** Loads custom CSS/JS for client dashboard, chat, and Font Awesome icons (`client.css`, `client.js`, `chat.js`).

## Shortcodes

- `[pixelcode_client_dashboard]` – Displays the client dashboard with case management, intake form, and chat.

## Security

- Uses WordPress nonces for AJAX requests.
- Checks user roles and permissions for admin/client separation.
- All sensitive operations require authentication.

## Development

- CSS is built with Tailwind via PostCSS (`npm run build`).
- JS uses jQuery for DOM manipulation and AJAX.

## Requirements

- WordPress 6.2.2 or higher
- PHP 8.0 or higher

## License

GPL-2.0+

---

For more details, see the code in `dashboard-portal.php` and the included files.