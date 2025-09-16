# Credit Sales Implementation

## Overview
This document outlines the complete implementation of the Credit Sales feature for the Laravel stock management application. The implementation follows the existing project structure and styling, maintaining consistency with other sales modules (Nozzle Sales and Lubricant Sales).

## Files Created/Modified

### 1. Model
- **File**: `app/Models/CreditSales.php`
- **Description**: Eloquent model for credit sales with relationships to Product, Tank, TankLari (Vehicle), and Customer models
- **Features**: 
  - Type casting for decimal fields
  - Vendor type resolution method
  - Proper relationships with related models

### 2. Controller
- **File**: `app/Http/Controllers/CreditSalesController.php`
- **Description**: Main controller handling all credit sales operations
- **Features**:
  - CRUD operations for credit sales
  - AJAX endpoints for dynamic form data
  - Proper validation and error handling
  - Database transactions for data integrity
  - Ledger entry creation (double-entry bookkeeping)
  - Activity logging

### 3. Routes
- **File**: `routes/web.php` (modified)
- **Routes Added**:
  - `GET /sales/credit` - Main credit sales page
  - `POST /sales/credit/store` - Create new credit sale
  - `POST /sales/credit/tanks` - Get tanks by product
  - `POST /sales/credit/product-rate` - Get product rate
  - `POST /sales/credit/customer-vehicles` - Get customer vehicles
  - `POST /sales/credit/delete` - Delete credit sale

### 4. View Template
- **File**: `resources/views/admin/pages/Sales/credit.blade.php`
- **Description**: Main credit sales interface
- **Features**:
  - Professional UI consistent with existing sales pages
  - Responsive design with Bootstrap components
  - Form with proper validation
  - DataTables integration for records display
  - Sales summary section
  - Print functionality

### 5. JavaScript
- **File**: `public/js/credit-sales-ajax.js`
- **Description**: Frontend functionality for credit sales
- **Features**:
  - Form validation and submission
  - Dynamic dropdowns (products → tanks, customers → vehicles)
  - Automatic amount calculation
  - Delete confirmation with SweetAlert2
  - DataTables initialization
  - Error handling and user feedback

### 6. CSS
- **File**: `public/css/credit-sales.css`
- **Description**: Custom styling for credit sales
- **Features**:
  - Consistent styling with project theme
  - Responsive design utilities
  - Form styling enhancements
  - Table styling
  - Print media queries
  - Animation effects

### 7. Sidebar Navigation
- **File**: `resources/views/admin/layout/sidebar.blade.php` (modified)
- **Changes**: Added Credit Sales menu item that appears only when `software_type = 2`

### 8. Permissions
- **File**: `database/seeders/RolePermissionSeeder.php` (modified)
- **Permissions Added**:
  - `sales.credit.view`
  - `sales.credit.create`
  - `sales.credit.delete`
- **Assigned to**: SuperAdmin (all), Admin, Employee

## Database Structure

The implementation uses the existing `credit_sales` table with the following structure:
```sql
CREATE TABLE `credit_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `transaction_type` int DEFAULT NULL COMMENT '1=receiving,2=payment',
  `payment_type` int DEFAULT NULL COMMENT '1=cash,2=bank payment',
  `product_id` int NOT NULL,
  `tank_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `vendor_type` int NOT NULL COMMENT ' 1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee ',
  `vehicle_id` int NOT NULL,
  `quantity` decimal(20,2) NOT NULL,
  `rate` decimal(20,2) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `transasction_date` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Functionality

### 1. Credit Sales Creation
- Select product (loads available tanks and current rate)
- Select customer (loads customer's vehicles)
- Enter quantity (automatically calculates amount)
- Add description
- Submit creates:
  - Credit sales record
  - Ledger entries (debit customer, credit cash)
  - Activity log

### 2. Credit Sales Display
- DataTables with sorting, searching, pagination
- Shows all relevant information
- Delete functionality (with confirmation)
- Sales summary by product

### 3. Ledger Integration
- Purchase type: 12 (credit sales)
- Vendor types: 2 (customer), 7 (cash)
- Transaction types: 1 (credit), 2 (debit)
- Maintains double-entry bookkeeping

### 4. Permissions & Security
- Role-based access control
- CSRF protection
- Input validation and sanitization
- SQL injection prevention

## Features Matching Original PHP Implementation

1. **Form Structure**: Identical layout and field arrangement
2. **AJAX Functionality**: Same dynamic loading behavior
3. **Database Operations**: Exact same ledger entries and transaction handling
4. **Validation**: Same validation rules and error messages
5. **Delete Functionality**: Identical confirmation and deletion process
6. **UI/UX**: Consistent with existing Laravel pages while maintaining original functionality

## Usage

1. **Access**: Navigate to Credit Sales from sidebar (only visible when software_type = 2)
2. **Create**: Fill form and submit to create credit sale
3. **View**: All credit sales displayed in table with search/filter capabilities
4. **Delete**: Click delete button on latest entries (with confirmation)
5. **Print**: Use print button to generate reports

## Security Considerations

- All routes protected with appropriate permissions
- CSRF tokens on all forms
- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- User activity logging for audit trails

## Maintenance

- Follow Laravel conventions for future modifications
- Update permissions when adding new features
- Maintain consistency with existing sales modules
- Keep JavaScript and CSS modular for easy maintenance

## Testing

To test the implementation:
1. Ensure `software_type` is set to 2 in config
2. Assign credit sales permissions to user role
3. Navigate to Credit Sales from sidebar
4. Test form submission with valid data
5. Verify ledger entries are created correctly
6. Test delete functionality
7. Verify responsive design on different screen sizes
