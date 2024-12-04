# Online Wallet

Online Wallet is a web application that allows users to securely manage their digital wallet. Users can deposit funds, request withdrawals, and provide feedback about the system. This application integrates with the Paystack API for deposit transactions and supports feedback submission with image upload functionality.

## Features

- User Registration & Login: Users can sign up and log in to access their wallets.
- Deposit Funds: Users can initiate deposits via Paystack (pending status shown until transaction is completed).
- Withdraw Requests: Users can request withdrawals based on their available balance, with a pending status for admin approval.
- Feedback System: Users can submit feedback and upload images as part of their feedback process.
- Responsive Design: Built with Bootstrap 5 for a clean, responsive interface.

## Installation

### Prerequisites

- PHP >= 7.4
- MySQL or MariaDB
- Apache or Nginx server
- Paystack API Key (for deposit integration)

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/online-wallet-.git
   cd online-wallet-
   ```

2. Create a Database:
   Create a MySQL database and import the SQL schema. You can find the schema in the `database/` folder (if available) or create the necessary tables based on the application requirements.

   Example schema for the `transactions` table:
   ```sql
   CREATE TABLE transactions (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT,
       amount DECIMAL(10, 2),
       type VARCHAR(50),
       status VARCHAR(50),
       paystack_reference VARCHAR(100),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

3. Configure Database:
   - Open the `config/database.php` file and update the database connection settings:
   ```php
   $host = 'localhost'; // Database host
   $db   = 'your_db_name'; // Your database name
   $user = 'your_db_user'; // Database username
   $pass = 'your_db_password'; // Database password
   ```

4. Set up Paystack:
   - If using Paystack for deposits, go to the [Paystack Dashboard](https://dashboard.paystack.com) and generate your Secret Key.
   - Update your Paystack Secret Key in the relevant file (`deposit.php` or `controller`).

5. Run the application:
   - Make sure your web server (Apache/Nginx) is running.
   - Navigate to your app in the browser (`http://localhost:8080/public`).

## File Structure

```
/online-wallet
├── /assets        # Static files (CSS, JS, images)
├── /config        # Configuration files
├── /controllers   # Backend logic for handling requests
├── /public        # Frontend views and public resources
├── /src           # Source files
│   ├── /models    # Database models
│   ├── /views     # HTML views and templates
│   └── /utils     # Utility functions
├── /database      # SQL schema or migration files
└── /README.md     # Project documentation
```

## Usage

- Deposit Funds: 
  - Users can deposit money into their wallet using Paystack. The transaction status is shown as "pending" until it's confirmed by Paystack.

- Withdraw Request: 
  - Users can request a withdrawal from their wallet. The request is checked to ensure they are not withdrawing more than their available balance.

- Feedback: 
  - Users can provide feedback with an option to upload an image. The feedback will be stored in the database and reviewed by admins.

## Technologies Used

- PHP: Server-side programming language.
- MySQL: Database for storing user and transaction data.
- Paystack API: For handling deposit transactions.
- Bootstrap 5: Frontend framework for responsive design.
- AJAX: For asynchronous form submission.

## Contributing

We welcome contributions! To contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes.
4. Submit a pull request detailing your changes and why they’re necessary.

## License

This project is licensed under the MIT License 


