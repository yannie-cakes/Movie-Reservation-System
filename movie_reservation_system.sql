CREATE DATABASE movie_reservation_system;
USE movie_reservation_system;

CREATE TABLE EMPLOYEE(EMP_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
EMP_FNAME VARCHAR(50) NOT NULL,
EMP_MNAME VARCHAR(50),
EMP_LNAME VARCHAR(50) NOT NULL,
EMP_ROLE VARCHAR(50) NOT NULL);

CREATE TABLE CINEMA(CIN_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
CIN_CAPACITY INT NOT NULL DEFAULT 110);

CREATE TABLE MOVIE(MOV_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
MOV_NAME VARCHAR(50) NOT NULL,
MOV_LENGTH TIME NOT NULL,
MOV_GENRE VARCHAR(20) NOT NULL,
MOV_RATING VARCHAR(5) NOT NULL,
MOV_SHOWING DATE NOT NULL,
CIN_ID INT NOT NULL,
FOREIGN KEY (CIN_ID) REFERENCES CINEMA(CIN_ID));

CREATE TABLE CUSTOMER(CUS_NUMBER INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
TRANS_NUMBER INT NOT NULL,
FOREIGN KEY (TRANS_NUMBER) REFERENCES TRANSACTION(TRANS_NUMBER));

CREATE TABLE TRANSACTIONS(TRANS_NUMBER INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
TRANS_DUE INT NOT NULL,
TRANS_PAYMENT INT NOT NULL,
TRANS_CHANGE INT NOT NULL,
TRANS_DATE DATE NOT NULL,
EMP_ID INT NOT NULL,
FOREIGN KEY (EMP_ID) REFERENCES EMPLOYEE(EMP_ID));

CREATE TABLE SEAT(SEAT_ID VARCHAR(9) NOT NULL PRIMARY KEY,
SEAT_NUMBER VARCHAR(4) NOT NULL,
CIN_ID INT NOT NULL,
FOREIGN KEY(CIN_ID) REFERENCES CINEMA(CIN_ID));

CREATE TABLE TICKET (
TICKET_ID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
TICKET_PRICE INT NOT NULL,
TICKET_QUANTITY INT NOT NULL,
TRANS_NUMBER INT NOT NULL,
MOV_ID INT NOT NULL,
CIN_ID INT NOT NULL,
SEAT_ID VARCHAR(9) NOT NULL,
FOREIGN KEY (TRANS_NUMBER) REFERENCES TRANSACTIONS(TRANS_NUMBER),
FOREIGN KEY (MOV_ID) REFERENCES MOVIE(MOV_ID),
FOREIGN KEY (CIN_ID) REFERENCES CINEMA(CIN_ID),
FOREIGN KEY (SEAT_ID) REFERENCES SEAT(SEAT_ID)
);

-- Fixed for CINEMA table
INSERT INTO CINEMA(CIN_ID, CIN_CAPACITY)
VALUES (1, 110), (2, 110), (3, 110), (4, 110);

SELECT * FROM SEAT;
