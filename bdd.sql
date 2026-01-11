-- TABLES DE BASE
CREATE TABLE IF NOT EXISTS utilisateurs (
  nom_util VARCHAR(50) PRIMARY KEY,
  mdp VARCHAR(50) NOT NULL,
  admin BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE IF NOT EXISTS competences (
  id_comp VARCHAR(100) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS projets (
  id_proj SERIAL PRIMARY KEY,
  nom_proj VARCHAR(255) NOT NULL,
  commentaire_proj VARCHAR(4096)
);

CREATE TABLE IF NOT EXISTS projets_competences (
  id_proj INTEGER REFERENCES projets(id_proj) ON DELETE CASCADE,
  id_comp VARCHAR(100) REFERENCES competences(id_comp) ON DELETE CASCADE,
  PRIMARY KEY (id_proj, id_comp)
);


-- VUES
CREATE VIEW projects_view AS
SELECT p.id_proj, p.nom_proj, p.commentaire_proj, array_agg(c.name) AS competences
FROM projets p
LEFT JOIN projets_competences pc ON p.id_proj = pc.id_proj
LEFT JOIN competences c ON pc.id_comp = c.id_comp
GROUP BY p.id_proj, p.nom_proj, p.commentaire_proj;


-- INSERTIONS DE BASE
INSERT INTO utilisateurs (nom_util, mdp, admin) VALUES
('admin',	'17c725af38777cf3ac4bf34e39f44caa',	't');
-- adminmdp