DROP DATABASE IF EXISTS langner;
CREATE DATABASE langner;

CREATE OR REPLACE USER 'langner_admin'@'localhost' IDENTIFIED BY 'qwerty123';
GRANT ALL PRIVILEGES ON langner.* TO langner_admin;
FLUSH PRIVILEGES;

USE langner;

CREATE OR REPLACE TABLE Invoices (
    invoiceId int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dateOfIssue date NOT NULL,
    dateOfDelivery date NOT NULL,
    clientId int NOT NULL,
    repicientId int NOT NULL,
    paymentType varchar(255) NOT NULL,
    dateOfPayment date NOT NULL,
    sumOfPayment float NOT NULL,
    isOriginal boolean,
    status varchar(255) NOT NULL
);

CREATE OR REPLACE TABLE Products (
    productId int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    productName varchar(255) NOT NULL,
    amount double NOT NULL,
    unitOfMeasure varchar(10) NOT NULL,
    netPrice float NOT NULL,
    tax int NOT NULL,
    photo text
);

CREATE OR REPLACE TABLE Users (
    userName varchar(255) NOT NULL,
    userSurname varchar(255) NOT NULL,
    login varchar(255) NOT NULL PRIMARY KEY,
    password varchar(255) NOT NULL,
    role varchar(255) NOT NULL,
    dateOfAdd date NOT NULL,
    activePassword boolean NOT NULL
);

CREATE OR REPLACE TABLE Corrections (
    invoiceId int NOT NULL UNIQUE,
    correctionId int NOT NULL UNIQUE,
    CONSTRAINT `CorrectionsConstraint`
        FOREIGN KEY (invoiceId) REFERENCES Invoices (invoiceId),
        FOREIGN KEY (correctionId) REFERENCES Invoices (invoiceId)
);

CREATE OR REPLACE TABLE Customers (
    customerId int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customerName varchar(255) NOT NULL,
    street varchar(255),
    postCode varchar(10) NOT NULL,
    city varchar(255) NOT NULL
);

CREATE OR REPLACE TABLE Sold (
    invoiceId int NOT NULL,
    productId int NOT NULL,
    amount double NOT NULL,
    netPrice float NOT NULL,
    tax int NOT NULL,
    PRIMARY KEY (invoiceId, productId),
    CONSTRAINT SoldConstraint
        FOREIGN KEY (invoiceId) REFERENCES Invoices (invoiceId),
        FOREIGN KEY (productId) REFERENCES Products (productId)
);

CREATE OR REPLACE TABLE Logs (
  login varchar(255) NOT NULL,
    dateOfLastLogin datetime NOT NULL,
    CONSTRAINT LogsConstraint
        FOREIGN KEY (login) REFERENCES Users (login) ON DELETE CASCADE
);

INSERT INTO `users` (`userName`, `userSurname`, `login`, `password`, `role`, `dateOfAdd`, `activePassword`) VALUES ('Piotr', 'Rzepka', 'Pivtrek', '$2y$10$Ip5fcfEStO1J9yLzF8Kpp.cEmyWe3wY6qpNo7B3ML65Nvtlvs545a', 'Administrator', '2021-01-13', '1');
INSERT INTO `users` (`userName`, `userSurname`, `login`, `password`, `role`, `dateOfAdd`, `activePassword`) VALUES ('Witold', 'Karas', 'JaWitold', '$2y$10$Ip5fcfEStO1J9yLzF8Kpp.cEmyWe3wY6qpNo7B3ML65Nvtlvs545a', 'Administrator', '2021-01-13', '1');
--password = QWERTY123