CREATE DATABASE library;

USE library;

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publication_year INT,
    genre VARCHAR(100),
    available BOOLEAN
);

INSERT INTO books (title, author, publication_year, genre, available) VALUES
('The Hobbit', 'J.R.R. Tolkien', 1937, 'Fantasy', TRUE),
('1984', 'George Orwell', 1949, 'Dystopian', FALSE),
('Dune', 'Frank Herbert', 1965, 'Science Fiction', TRUE),
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 1997, 'Fantasy', TRUE),
('The Da Vinci Code', 'Dan Brown', 2003, 'Thriller', FALSE),
('The Hunger Games', 'Suzanne Collins', 2008, 'Young Adult', TRUE),
('Sapiens', 'Yuval Noah Harari', 2011, 'Non-Fiction', TRUE),
('Project Hail Mary', 'Andy Weir', 2021, 'Science Fiction', FALSE);