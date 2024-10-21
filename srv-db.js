const mysql = require('mysql2');

const connection = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME || 'gp1ma_db' // Use the provided DB name or default
});

function connectToDatabase() {
    return new Promise((resolve, reject) => {
        connection.connect(err => {
            if (err) {
                console.error('Database connection failed:', err.stack);
                reject(err);
            } else {
                console.log('Connected to the database.');
                resolve();
            }
        });
    });
}

async function setupDatabase() {
    const createUsersTable = `
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            profile_pic VARCHAR(255) DEFAULT NULL,
            bio TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    `;

    // ... (other CREATE TABLE queries)

    try {
        await executeQuery(createUsersTable);
        // Add other table creation queries here
        console.log('Database tables created successfully.');
    } catch (error) {
        console.error('Error setting up database:', error);
    }
}

function executeQuery(query) {
    return new Promise((resolve, reject) => {
        connection.query(query, (err, results) => {
            if (err) {
                console.error('Query execution failed:', err.stack);
                reject(err);
            } else {
                resolve(results);
            }
        });
    });
}

async function main() {
    try {
        await connectToDatabase();
        await setupDatabase(); // Set up database and tables
    } catch (error) {
        console.error('An error occurred:', error);
    } finally {
        connection.end(err => {
            if (err) {
                console.error('Error closing connection:', err.stack);
            }
            console.log('Connection closed.');
        });
    }
}

main();
