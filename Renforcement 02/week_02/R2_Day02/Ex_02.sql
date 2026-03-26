SELECT * FROM books ORDER BY publication_year DESC;


SELECT * FROM books WHERE title LIKE '%The%';


SELECT COUNT(*) FROM books WHERE available = TRUE;


SELECT * FROM books ORDER BY title ASC LIMIT 3;


SELECT MAX(publication_year) AS most_recent, MIN(publication_year) AS oldest FROM books;