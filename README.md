# DDS - Digital Distribution System

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive digital distribution platform built with Laravel that connects Digital Advertisers (DAs), Digital Content Distributors (DCDs), and Clients through an innovative QR code-based marketing and rewards system.

## 🌟 Overview

DDS revolutionizes digital marketing by creating a three-tier ecosystem:
- **Digital Advertisers (DAs)**: Content creators and influencers who promote products
- **Digital Content Distributors (DCDs)**: Business owners who display QR codes for customer scanning
- **Clients**: Brands and companies running marketing campaigns

The platform enables seamless campaign management, real-time scan tracking, automated earnings calculation, and comprehensive analytics through a robust API-first architecture.

## ✨ Key Features

### 🔄 Multi-Tier User Management
- **Digital Advertisers (DAs)**: Register with referral codes, manage profiles, track earnings
- **Digital Content Distributors (DCDs)**: Business registration with QR code generation and referral capabilities
- **Clients**: Brand accounts with campaign management and billing integration
- **Admin System**: Comprehensive oversight with email notifications and action controls

### 📱 QR Code Ecosystem
- Dynamic QR code generation for DCDs
- Campaign-specific QR codes with unique identifiers
- Real-time scan validation and tracking
- Automatic earnings calculation per scan

### 💰 Advanced Earnings & Rewards
- Multi-level referral system (DA → DA, DCD → DA, DCD → DCD)
- Venture shares allocation system
- Automated payout processing
- Comprehensive earnings analytics

### 📊 Campaign Management
- Campaign creation and approval workflow
- Real-time performance tracking
- Geographic targeting capabilities
- Content safety and compliance features

### 🔐 Security & Compliance
- Secure referral code system
- Admin authentication for sensitive operations
- Data privacy compliance (GDPR-ready)
- Comprehensive audit logging

### 📧 Communication System
- Automated admin notifications for all user registrations
- Email-based admin actions (campaign approvals, rejections)
- Professional HTML email templates
- Configurable notification preferences

## 🏗️ System Architecture

### Backend Stack
- **Framework**: Laravel 11
- **Database**: SQLite (dev) / PostgreSQL (prod)
- **Cache/Queue**: Redis
- **Mail**: Laravel Mail (SMTP/Mailgun/SendGrid)
- **Testing**: Pest PHP
- **Documentation**: API-first design

### Database Schema
- **Users**: Core user management with role-based access
- **DAs**: Digital advertisers with referral codes and earnings tracking
- **DCDs**: Content distributors with QR codes and business profiles
- **Clients**: Brand accounts with billing and campaign management
- **Campaigns**: Marketing campaigns with targeting and analytics
- **Scans**: Real-time scan tracking with earnings calculation
- **Referrals**: Multi-level referral relationship management
- **Earnings**: Comprehensive earnings and payout tracking

## 🚀 API Documentation

### Core Endpoints

#### User Registration & Management
```http
POST /api/da/create          # Register Digital Advertiser
POST /api/dcd/create         # Register Digital Content Distributor
POST /api/client/create      # Register Client Account
```

#### Campaign Management
```http
POST /api/campaign/create                # Create new campaign
POST /api/campaign/{id}/approve          # Admin campaign approval
POST /api/campaign/{id}/reject           # Admin campaign rejection
POST /api/campaign/{id}/mark-paid        # Mark campaign as paid
```

#### QR Code & Scan System
```http
POST /api/qr/generate-dcd               # Generate QR for DCD
POST /api/qr/regenerate/{dcdId}         # Regenerate DCD QR
GET  /api/scan/validate                 # Validate QR scan
POST /api/scan/track                    # Track scan event
```

#### Analytics & Reporting
```http
GET  /api/scans/analytics/{campaignId}  # Campaign scan analytics
POST /api/earnings/calculate            # Calculate earnings
POST /api/payouts/generate-report       # Generate payout reports
```

#### Referral & Earnings
```http
POST /api/referral/track                # Track referral events
POST /api/ventureshares/allocate        # Allocate venture shares
POST /api/ventureshares/batch-allocate  # Batch venture share allocation
```

#### Admin Operations
```http
GET  /api/admin/digest                  # Admin system digest
POST /api/alerts/send                   # Send admin alerts
POST /api/admin/action/authenticate     # Authenticate admin actions
```

### Example API Usage

#### Register a Digital Advertiser
```bash
curl -X POST http://localhost:8000/api/da/create \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "password": "securepassword123",
    "referrer_code": "DA_ABC123",
    "national_id": "12345678",
    "dob": "1990-01-01",
    "gender": "male",
    "country": "Kenya",
    "consent_terms": true,
    "consent_data": true,
    "consent_ethics": true
  }'
```

#### Create a Campaign
```bash
curl -X POST http://localhost:8000/api/campaign/create \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "title": "Summer Sale Campaign",
    "description": "Promote summer collection",
    "product_url": "https://example.com/summer-sale",
    "target_counties": ["Nairobi", "Mombasa"],
    "rate_per_scan": 15.00,
    "budget": 5000.00
  }'
```

#### Track a Scan
```bash
curl -X POST http://localhost:8000/api/scan/track \
  -H "Content-Type: application/json" \
  -d '{
    "qr_code": "DCD_ABC123_CAMP_XYZ789"
  }'
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js & npm
- Redis server
- SQLite (dev) or PostgreSQL (prod)

### Quick Start
1. **Clone and Install Dependencies**
   ```bash
   git clone <repository-url>
   cd dds-platform
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   # For development (SQLite)
   touch database/database.sqlite

   # Run migrations
   php artisan migrate

   # Seed admin user
   php artisan db:seed
   ```

4. **Redis Configuration**
   ```bash
   # Install Redis (macOS)
   brew install redis
   brew services start redis

   # Verify Redis
   redis-cli ping  # Should return PONG
   ```

5. **Mail Configuration**
   Update `.env` with your mail settings:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email@domain.com
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   ```

### Development Server
```bash
# Start Laravel server
php artisan serve

# Start asset compilation (in another terminal)
npm run dev

# Start queue worker for earnings processing
php artisan queue:work --queue=earnings
```

## 🧪 Testing

Run the comprehensive test suite:
```bash
php artisan test
```

The test suite includes:
- ✅ Unit tests for core functionality
- ✅ Feature tests for API endpoints
- ✅ Integration tests for user workflows
- ✅ Scan tracking and earnings calculation
- ✅ Admin notification system

**Expected Results**: All 56 assertions should pass

## 📈 Usage Examples

### Complete User Registration Flow
1. **Register DA** → Receives referral code (e.g., `DA_ABC123`)
2. **Register DCD** → Receives referral code (e.g., `DCD_XYZ789`)
3. **Create Campaign** → Client sets up marketing campaign
4. **Generate QR** → DCD gets unique QR code for campaign
5. **Customer Scans** → Earnings automatically calculated
6. **Admin Notifications** → Email alerts for all registrations

### Referral System
- DAs can refer other DAs and DCDs
- DCDs can refer other DAs and DCDs
- Multi-level earnings tracking
- Automated venture share allocation

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for API changes
- Ensure all tests pass before submitting PR

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- Testing powered by [Pest PHP](https://pestphp.com)
- QR Code generation using [SimpleQRCode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- PDF generation with [DomPDF](https://github.com/dompdf/dompdf)

## 📞 Support

For support and questions:
- Create an issue in this repository
- Contact the development team
- Check the documentation in `docs/` directory

---

**DDS - Connecting Digital Creators, Distributors, and Brands** 🚀
