# Laravel SaaS Boilerplate - Blade Edition

A comprehensive, production-ready Laravel SaaS boilerplate with authentication, teams, billing, and multi-tenancy support.

## 🚀 Tech Stack

### Backend
- **Laravel 12** - Latest Laravel framework
- **PHP 8.2+** - Modern PHP features
- **MySQL/PostgreSQL** - Database support
- **Laravel Breeze** - Authentication scaffolding
- **Laravel Cashier** - Stripe subscription management
- **Spatie Laravel Permission** - Roles & permissions

### Frontend
- **Blade Templates** - Server-side rendering
- **Tailwind CSS 3** - Utility-first CSS framework
- **Alpine.js 3** - Lightweight JavaScript framework
- **Vite** - Modern build tool for assets

## 📦 Installed Packages

```bash
# Authentication
laravel/breeze

# Billing
laravel/cashier

# Permissions
spatie/laravel-permission
```

## 🗄️ Database Structure

### Tables
- `users` - User accounts
- `teams` - Organizations/Teams
- `team_user` - Team membership pivot
- `permissions` - Permission definitions (Spatie)
- `roles` - Role definitions (Spatie)
- `model_has_permissions` - User permissions (Spatie)
- `model_has_roles` - User roles (Spatie)
- `subscriptions` - Stripe subscriptions (Cashier)
- `subscription_items` - Subscription items (Cashier)

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Authentication controllers (Breeze)
│   │   └── ProfileController.php
│   └── Requests/
├── Models/
│   ├── User.php               # User model with teams & roles
│   └── Team.php               # Team model
└── Services/
    ├── TeamService.php        # Team management logic
    └── BillingService.php     # Subscription management logic

resources/
├── views/
│   ├── components/            # Blade components
│   ├── layouts/               # Layout templates
│   ├── auth/                  # Authentication views
│   └── profile/               # Profile views
├── css/
│   └── app.css               # Tailwind CSS
└── js/
    └── app.js                # Alpine.js initialization

database/
└── migrations/
    ├── create_users_table
    ├── create_teams_table
    ├── create_team_user_table
    └── create_permission_tables
```

## 🎯 Features

### ✅ Implemented
- [x] User Authentication (Login, Register, Password Reset)
- [x] Email Verification
- [x] Profile Management
- [x] Team/Organization System
- [x] Team Roles (Owner, Admin, Member)
- [x] Roles & Permissions (Spatie)
- [x] Billing Service (Stripe integration ready)
- [x] Service Layer Architecture
- [x] Vite + Tailwind + Alpine.js setup

### 🚧 Planned
- [ ] Dashboard with analytics
- [ ] Billing/Subscription pages
- [ ] Team management UI
- [ ] Settings pages
- [ ] API routes
- [ ] Multi-tenancy support
- [ ] Admin panel

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd saas
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Build assets**
   ```bash
   npm run dev    # Development
   npm run build  # Production
   ```

6. **Run the server**
   ```bash
   php artisan serve
   ```

## 📝 Usage

### Creating a Team

```php
use App\Services\TeamService;
use App\Models\User;

$teamService = new TeamService();
$user = User::find(1);

$team = $teamService->create([
    'name' => 'Acme Corp',
    'description' => 'My awesome company',
    'website' => 'https://acme.com'
], $user);
```

### Managing Subscriptions

```php
use App\Services\BillingService;

$billingService = new BillingService();

// Subscribe user to a plan
$subscription = $billingService->subscribe(
    $user,
    'price_1234567890', // Stripe price ID
    ['payment_method' => 'pm_xxxxx']
);

// Check subscription status
$hasActive = $billingService->hasActiveSubscription($user);
$status = $billingService->getSubscriptionStatus($user);
```

### Working with Permissions

```php
// Assign role to user
$user->assignRole('admin');

// Give permission
$user->givePermissionTo('manage teams');

// Check permission
if ($user->can('manage teams')) {
    // User has permission
}
```

## 🔐 Environment Variables

Add these to your `.env`:

```env
# Stripe (for billing)
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Application
APP_NAME="Your SaaS Name"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🎨 Frontend Development

### Vite Dev Server
```bash
npm run dev
```

This will start Vite's development server with hot module replacement.

### Tailwind CSS
Tailwind is configured in `tailwind.config.js`. All Blade views are scanned for classes.

### Alpine.js
Alpine is initialized in `resources/js/app.js`. Use Alpine directives in your Blade templates:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

## 📚 Next Steps

1. **Dashboard Layout** - Create the main dashboard navigation and layout
2. **Team Management UI** - Build team creation, member management pages
3. **Billing Pages** - Create subscription management interface
4. **Settings Pages** - User and team settings
5. **API Endpoints** - For potential Vue.js/React frontends
6. **Multi-tenancy** - Implement tenant isolation

## 🤝 Contributing

This is a boilerplate project. Feel free to customize it for your needs!

## 📄 License

MIT License - feel free to use this for your projects.

---

**Built with ❤️ using Laravel, Tailwind CSS, and Alpine.js**
