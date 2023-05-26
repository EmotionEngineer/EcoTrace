CREATE TABLE customers (
    customer_id INTEGER PRIMARY KEY,
    first_name TEXT,
    last_name TEXT,
    email TEXT
);

CREATE TABLE questions (
    question_id INTEGER PRIMARY KEY,
    customer_id INTEGER,
    subject TEXT,
    message TEXT,
    created_at TIMESTAMP,
    status TEXT CHECK(status IN ('open', 'closed')),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE replies (
    reply_id INTEGER PRIMARY KEY,
    question_id INTEGER,
    message TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(question_id)
);