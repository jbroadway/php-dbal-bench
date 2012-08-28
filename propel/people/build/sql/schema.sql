
-----------------------------------------------------------------------
-- people
-----------------------------------------------------------------------

DROP TABLE IF EXISTS people;

CREATE TABLE people
(
    id INTEGER NOT NULL PRIMARY KEY,
    name CHAR(32) NOT NULL,
    email CHAR(48) NOT NULL,
    score INTEGER NOT NULL
);

CREATE INDEX people_name ON people (name);

CREATE INDEX people_score ON people (score);
