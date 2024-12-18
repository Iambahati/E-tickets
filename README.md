# E-Ticketing System

Welcome to the E-Ticketing System! This platform allows event organizers to easily manage and sell tickets, while users can browse and purchase tickets for various events. Follow the instructions below to set up the project on your local machine.

## Getting Started

### 1. Clone the Project

Start by cloning the repository to your local machine. Run the following command in your terminal:

```bash
git clone https://github.com/Joanmboya/E-Ticketing.git
```

### 2. Set Up the Project

After cloning, place the project folder in your XAMPP `htdocs` directory. The path should look like this:

```bash
C:/xampp/htdocs/E-Ticketing/e-ticketing
```

### 3. Import the Database

1. Open **phpMyAdmin** by navigating to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Create a new database named `e-ticketing`.
3. In the database, import the SQL file located in `sql/ticketingapp.sql` to set up the necessary tables.

   To import:
   - Click on the **Import** tab in phpMyAdmin.
   - Select the `ticketingapp.sql` file from your project’s `sql` folder.
   - Click **Go**.

4. Once the import is complete, verify that all tables have been created successfully.

### 4. Access the Website

Now that the database is set up, you can navigate to the site by visiting:

[http://localhost/E-Ticketing/e-ticketing/index.php](http://localhost/E-tickets/e-ticketing/index.php)

## How to Use

1. **Create an Organizer Account**  
   First, create an organizer account so you can add and manage events. This account will have the ability to post new events that users can purchase tickets for.

2. **Create a Buyer/Client Account**  
   After creating events as an organizer, sign up for a buyer or client account to purchase tickets for the events you are interested in.

---

Happy event organizing and ticket buying!


