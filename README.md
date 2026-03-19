## SampleLab LIMS

Laboratory Information Management System for quality control and sample tracking.

# Application Screenshots

<p align="center">
  <img src="https://github.com/user-attachments/assets/520fb862-d218-46aa-98a2-c70fd6134085" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/143e5038-6d19-48c0-9950-cf2ad1134d29" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/11461b8f-9914-4d41-bd85-cb7e20a58480" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/73e4a7e9-09d5-495a-875b-ac0aa3ac5d69" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/2be1e6de-ea5f-4cc6-aa7d-fdee27b272fa" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/52f3ad0b-3650-4224-9f71-2f2ba111f3b8" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/55664aff-877d-4ca6-bfdd-f9dd1c0853c0" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/f2ba1c82-ee86-480c-a320-8589f792674b" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/dff11791-ed65-4268-9a21-6f064c42de4c" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/f6726b7a-5b16-41b7-b5f9-67090f8985c6" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/18ed3953-372e-4967-9d17-c30e19b4072e" width="700"/>
  <br><br>
  <img src="https://github.com/user-attachments/assets/9be3cdcb-cc76-43b9-9c44-1623c2eebfb8" width="700"/>
</p>

## Features

### Sample Management
- **Sample Registration**: Create and register new samples with unique codes
- **Barcode Integration**: Generate barcodes and QR codes for sample tracking
- **Status Tracking**: Monitor sample lifecycle (Registered → In Progress → Completed → Archived)
- **Metadata Support**: Store additional sample information in JSON format
- **Client Management**: Assign samples to clients and projects

### Measurement Workflow
- **Method Assignment**: Link samples to specific analytical methods
- **Measurement Planning**: Schedule and assign measurements to laboratory staff
- **Progress Tracking**: Monitor measurement status in real-time
- **Quality Control**: Built-in QC checkpoints and validation

### Results Management
- **Dynamic Forms**: Auto-generated forms based on method schema
- **Data Validation**: Type checking and range validation
- **Multi-step Approval**: Draft → Submit → Review → Approve/Reject
- **Audit Trail**: Complete change tracking with JSON diffs
- **Result Locking**: Prevent unauthorized modifications

### User Roles & Permissions
- **Admin**: Full system access and user management
- **Manager**: Sample and measurement planning, report generation
- **Laborant**: Sample processing and result entry
- **QC/Reviewer**: Result review and approval
- **Client**: View reports and sample status

### Reporting
- **Real-time Reports**: Generate COA, certificates, analysis reports
- **Export Options**: PDF, Excel, CSV formats
- **Custom Templates**: Configurable report layouts
- **Audit Logs**: Complete activity tracking

## Technical Stack

- **Backend**: Laravel 12.53.0, PHP 8.2.30
- **Frontend**: Blade templates, Bootstrap 5, JavaScript
- **Database**: MySQL with optimized indexes
- **Authentication**: Laravel Sanctum with Spatie Permissions
- **Security**: CSRF protection, input validation, role-based access

## Sample Statuses

| Status | Description | Color |
|---------|-------------|--------|
| REGISTERED | Sample registered in system | Primary |
| IN_PROGRESS | Sample being processed | Warning |
| COMPLETED | Analysis completed | Success |
| ARCHIVED | Sample archived | Secondary |
| DISPOSED | Sample disposed | Dark |

## Measurement Workflow

1. **Sample Registration** → Generate unique sample code
2. **Method Assignment** → Link to analytical method
3. **Measurement Planning** → Assign to laboratory staff
4. **Sample Processing** → Perform analysis
5. **Result Entry** → Input measurement results
6. **Quality Review** → QC approval process
7. **Report Generation** → Create final report

## User Roles

### Admin (Administrator)
- Full system access
- User management
- Method configuration
- System settings
- All reports and exports

### Manager
- Create and manage samples
- Plan measurements
- Assign staff
- Generate reports
- View audit logs

### Laborant (Laboratory Technician)
- Process assigned samples
- Enter measurement results
- Update sample status
- View assigned work

### QC/Reviewer (Quality Control)
- Review measurement results
- Approve or reject results
- Lock approved results
- Generate quality reports

### Client
- View sample status
- Download reports
- Track sample progress
- View basic analytics

## User Guide

### Quick Start for Laboratory Staff

#### 1. Login to System
- Navigate to `http://localhost:8000`
- Enter your credentials:
  - **Laborant**: `lab@test.com`
  - **QC/Reviewer**: `qc@test.com`
  - **Manager**: `manager@test.com`
  - **Admin**: `admin@test.com`

#### 2. Register New Sample
1. Click **Samples** in navigation menu
2. Click **Create Sample** button
3. Fill sample information:
   - Sample name and type
   - Client and project
   - Quantity and unit
   - Methods
4. Click **Submit**
5. System generates unique sample code (e.g., `S-2026-0001`)

#### 3. Plan Measurements
1. Click **Create Measurements** button
2. Select created sample from list
3. Choose analytical methods from dropdown
4. Set planned date and priority    
5. Click **Save**

#### 4. Generate Reports
1. Go to **Reports** section
2. Select completed samples
3. Select completed projects
4. Click **Generate** and download

#### 5. Method Configuration
1. Navigate to **Methods** section
2. Create analytical methods with field definitions
3. Set validation rules and requirements
4. Publish methods for use

### Common Workflows

#### Sample Lifecycle
```
Sample Registration → Method Assignment → Measurement Planning → 
Sample Processing → Result Entry → Quality Review → Report Generation
```

#### Result Approval Process
```
Draft → Submit → Review → Approve/Reject → Lock → Report
```

## Data Model

### Core Entities
- **Samples**: Primary entities with unique codes
- **Measurements**: Analysis tasks linked to samples
- **Results**: Measurement data with validation
- **Methods**: Analytical procedures with schema definitions
- **Users**: System users with role-based permissions
- **Clients**: Organizations that submit samples
- **Projects**: Groupings of related samples

### Relationships
- Client → Projects → Samples → Measurements → Results
- User ↔ Measurements (assignments)
- Method ↔ Results (schema validation)

## Security Features

- **Role-Based Access Control**: Granular permissions system
- **Audit Trail**: Complete activity logging
- **Data Validation**: Input sanitization and type checking
- **Session Management**: Secure authentication
- **CSRF Protection**: Form security tokens

## Getting Started

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js (for assets)

### Installation
```bash
git clone <repository>
cd samplelab
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### Configuration
1. Configure database in `.env`
2. Set application timezone
3. Configure mail settings
4. Run migrations and seeders

### Development Server
```bash
php artisan serve
```

Access at: `http://localhost:8000`

## API Documentation

### Authentication
- Login: `POST /api/login`
- Logout: `POST /api/logout`
- User: `GET /api/user`

### Samples
- List: `GET /api/samples`
- Create: `POST /api/samples`
- Update: `PUT /api/samples/{id}`
- Delete: `DELETE /api/samples/{id}`

### Measurements
- List: `GET /api/measurements`
- Create: `POST /api/measurements`
- Update: `PUT /api/measurements/{id}`

### Results
- List: `GET /api/results`
- Update: `PUT /api/results/{id}`
- Submit: `POST /api/results/{id}/submit`

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter ResultWorkflowTest

# Generate coverage report
php artisan test --coverage
```
