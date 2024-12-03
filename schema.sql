CREATE DATABASE dolphin_crm;
USE dolphin_crm;

--Creating the Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(35) UNIQUE NOT NULL,
    lastname VARCHAR(35) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(60),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
);

--Creating the Contacts Table
CREATE TABLE Contacts(
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(20), 
    firstname VARCHAR(35) UNIQUE NOT NULL,
    lastname VARCHAR(35) UNIQUE NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(15),
    company VARCHAR(100),
    type_role VARCHAR(11) NOT NULL,
    assigned_to INT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES Users(user_id),
    FOREIGN KEY (created_by) REFERENCES Users(user_id)
);

--Creating the Notes Table
CREATE TABLE Notes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT,
    comment TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES Contacts(contact_id),
    FOREIGN KEY (created_by) REFERENCES Users(user_id)
);
