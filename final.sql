CREATE TABLE Users (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,                
    password VARCHAR(255),
    firstname VARCHAR(50),                    
    lastname VARCHAR(50),                                         
    address VARCHAR(255),                     
    phone VARCHAR(20),                 
    role VARCHAR(20) DEFAULT 'client'
);

CREATE TABLE Quote (
    quoteid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT,
    address VARCHAR(255),
    dimension_length INT, 
    dimension_width INT, 
    price FLOAT,
    photo1 VARCHAR(255),
    photo2 VARCHAR(255),
    photo3 VARCHAR(255),
    photo4 VARCHAR(255),
    photo5 VARCHAR(255),
    client_note VARCHAR(255),
    status ENUM('pending', 'accepted', 'refused') DEFAULT 'pending', 
    admin_note VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES Users(userid)
);

CREATE TABLE Work (
    workid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT,
    quoteid INT, 
    start_date DATE,
    end_date DATE,
    price FLOAT,
    client_status ENUM('pending', 'accepted', 'refused') DEFAULT 'pending',
    client_note VARCHAR(255),
    admin_status ENUM('pending', 'accepted', 'refused') DEFAULT 'pending',
    admin_note VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES Users(userid),
    FOREIGN KEY (quoteid) REFERENCES Quote(quoteid) 
);

CREATE TABLE WorkHistory (
    historyid INT AUTO_INCREMENT PRIMARY KEY,
    workid INT, 
    userid INT,
    quoteid INT, 
    start_date DATE,
    end_date DATE,
    price FLOAT,
    client_status ENUM('pending', 'accepted', 'refused') DEFAULT 'pending',
    client_note VARCHAR(255),
    admin_status ENUM('pending', 'accepted', 'refused') DEFAULT 'pending',
    admin_note VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workid) REFERENCES Work(workid),
    FOREIGN KEY (userid) REFERENCES Users(userid)
);

CREATE TABLE Card (
    cardid INT AUTO_INCREMENT PRIMARY KEY, -- admin to see
    userid INT,
    nickname VARCHAR(100), -- user to see
    number VARCHAR(20),
    name VARCHAR(100),
    month TINYINT,
    year TINYINT,
    cvv INT(3),
    FOREIGN KEY (userid) REFERENCES Users(userid)
);

CREATE TABLE Transactions ( -- Orders and Bills
    transactionid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT,
    quoteid INT,
    workid INT,
    cardid INT,
    price FLOAT,
    start_date DATE,
    end_date DATE,
    charge_status ENUM('pending', 'charge', 'deny') DEFAULT 'pending', 
    client_note VARCHAR(255),
    pay_status ENUM('pending', 'paid', 'declined') DEFAULT 'pending', 
    admin_note VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES Users(userid),
    FOREIGN KEY (quoteid) REFERENCES Quote(quoteid),
    FOREIGN KEY (workid) REFERENCES Work(workid),
    FOREIGN KEY (cardid) REFERENCES Card(cardid) 
);

CREATE TABLE TransactionPaid (
    historyid INT AUTO_INCREMENT PRIMARY KEY,
    transactionid INT,         
    userid INT,                
    quoteid INT,               
    workid INT,                
    cardid INT,
    created_at DATETIME,       
    paid_date DATETIME NULL,   
    FOREIGN KEY (transactionid) REFERENCES Transactions(transactionid),
    FOREIGN KEY (userid) REFERENCES Users(userid),
    FOREIGN KEY (quoteid) REFERENCES Quote(quoteid),
    FOREIGN KEY (workid) REFERENCES Work(workid),
    FOREIGN KEY (cardid) REFERENCES Card(cardid) 
);
