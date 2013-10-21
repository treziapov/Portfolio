HTML and CSS Template belongs to :
	BLUEWEBTEMPLATES.COM
	DREAMTEMPLATE.COM/TEMPLATEACCESS.COM

Porfolio URL:
	http://web.engr.illinois.edu/~reziapo1/

MySQL Databse Schema:

	CREATE DATABASE reziapo1_cs242;

	CREATE TABLE Comments (
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		author VARCHAR(25) DEFAULT 'Anonymous',
		date TIMESTAMP,
		content TEXT NOT NULL,
		parent_project VARCHAR(25) NOT NULL,
		parent_comment_id INT DEFAULT NULL,
	);


	/* Filtering Part */

	// SQL query to setup the table for filtering red flag words
	CREATE TABLE RedFlags (
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		actual VARCHAR(50) NOT NULL UNIQUE,
		filter VARCHAR(50) NOT NULL DEFAULT("")
	);

	// SQL queries to add the red flags to the database
	INSERT INTO RedFlags (actual, filter) VALUES ("hello", "hi");
	INSERT INTO RedFlags (actual, filter) VALUES ("hell", "oops");
	INSERT INTO RedFlags (actual, filter) VALUES ("you", "you, sir,");



