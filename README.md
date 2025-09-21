# Dashboard Portal

A HIPAA-compliant client intake, portal, document delivery, and secure messaging system for WordPress. This plugin provides a secure platform for managing sensitive client data, supporting features like encrypted messaging, secure document sharing, and client intake forms.

Plugin Overview :
<br/>

Admin Dashboard  <br/>

Case-Management <br/>
<img width="1278" height="703" alt="image" src="https://github.com/user-attachments/assets/4f948750-c46f-4a16-b01e-5055f4c3b3a8" />

Client-Section <br/>

<img width="1424" height="572" alt="image" src="https://github.com/user-attachments/assets/f9fc051c-566c-4f4a-8184-9be768bdfd73" />

Analytics-Section <br/>

<img width="1005" height="649" alt="image" src="https://github.com/user-attachments/assets/e0807beb-c37d-4b38-9709-9f223618d11d" />

Team-Section <br/>

<img width="1423" height="644" alt="image" src="https://github.com/user-attachments/assets/99c7b6af-a8e8-433e-a9c4-5a3176203f35" />

Case Details View <br/>

<img width="660" height="623" alt="image" src="https://github.com/user-attachments/assets/4ff1c2ad-2862-4964-bfce-713934bc7f5e" />



Client Portal Dashboard <br/>

Case-Section <br/>

<img width="1204" height="645" alt="image" src="https://github.com/user-attachments/assets/15280f36-633c-44fe-95cc-dbacafeedb84" />

Case-Details <br/>

<img width="461" height="647" alt="image" src="https://github.com/user-attachments/assets/112567af-4ff6-4804-be05-85f755374adf" />

Intake-Form <br/>

<img width="637" height="557" alt="image" src="https://github.com/user-attachments/assets/a518f69e-09b8-4089-9114-dc906429c2a3" />

Chat-System-Between Client And Admin <br/>

<img width="1406" height="644" alt="image" src="https://github.com/user-attachments/assets/97452378-726c-41ee-a7af-3bdf2c7a008e" />



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
