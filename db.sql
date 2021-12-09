-- create tables
CREATE TABLE Users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  role VARCHAR(10) NOT NULL
);
CREATE TABLE Categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL
);
CREATE TABLE Posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  content VARCHAR(100) NOT NULL,
  time VARCHAR(30) NOT NULL,
  category_id INT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (category_id) REFERENCES Categories (id),
  FOREIGN KEY (user_id) REFERENCES Users (id)
);
-- sample data
INSERT INTO
  Users (name, email, password, role)
VALUES
  (
    'SiteAdmin',
    'admin@admin.hu',
    '$2y$12$zdI1eN1jzMWb19MA0Qpw0.uoAaDW2WmyJ.k5IMFJdJS/ZHViyNSyy',
    'admin'
  ),
  (
    'Teszt Elek',
    'teszt@elek.hu',
    '$2y$12$Ur/6T7B/2tXdywNa14mtGOHSUxirwTtHb0KTr76zbSqLufOtwgcsy',
    'user'
  );
INSERT INTO
  Categories (name)
VALUES
  ('Teszt Kategória'),
  ('Teszt Kategória 2'),
  ('Teszt Kategória 3');
INSERT INTO
  Posts (content, time, category_id, user_id)
VALUES
  ('Teszt hozzászólás', '1638985897', 1, 1),
  ('Teszt hozzászólás 2', '1638985897', 1, 1),
  ('Teszt hozzászólás 3', '1638985897', 1, 2),
  ('Teszt hozzászólás 4', '1638985897', 2, 1),
  ('Teszt hozzászólás 5', '1638985897', 2, 2),
  ('Teszt hozzászólás 6', '1638985897', 3, 1),
  ('Teszt hozzászólás 7', '1638985897', 3, 1);