-- TABLES DE BASE
CREATE TABLE IF NOT EXISTS utilisateurs (
  nom_util VARCHAR(50) PRIMARY KEY,
  mdp VARCHAR(50) NOT NULL,
  admin BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE IF NOT EXISTS competences (
  id_comp VARCHAR(15) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(1000)
);

CREATE TABLE IF NOT EXISTS projets (
  id_proj SERIAL PRIMARY KEY,
  nom_proj VARCHAR(255) NOT NULL,
  desc_proj VARCHAR(4096),
  commentaire_proj VARCHAR(4096),
  lien_proj VARCHAR(4096),
  visible BOOLEAN DEFAULT FALSE NOT NULL
);

CREATE TABLE IF NOT EXISTS projets_competences (
  id_proj INTEGER REFERENCES projets(id_proj) ON DELETE CASCADE,
  id_comp VARCHAR(15) REFERENCES competences(id_comp) ON DELETE CASCADE,
  PRIMARY KEY (id_proj, id_comp)
);

CREATE TABLE IF NOT EXISTS images (
  id_img SERIAL PRIMARY KEY,
  id_proj INTEGER REFERENCES projets(id_proj) ON DELETE CASCADE,
  url_img VARCHAR(2048) NOT NULL
);


-- VUES
CREATE OR REPLACE VIEW projects_view AS
SELECT p.id_proj as id_proj, p.nom_proj as nom_proj, p.desc_proj as desc_proj, p.commentaire_proj as commentaire_proj, 
       array_agg(DISTINCT c.name) AS competences,
       array_agg(DISTINCT i.url_img) AS images,
       p.visible AS visible
FROM projets p
LEFT JOIN projets_competences pc ON p.id_proj = pc.id_proj
LEFT JOIN competences c ON pc.id_comp = c.id_comp
LEFT JOIN images i ON p.id_proj = i.id_proj
GROUP BY p.id_proj, p.nom_proj, p.desc_proj, p.commentaire_proj;


-- INSERTIONS DE BASE
INSERT INTO utilisateurs (nom_util, mdp, admin) VALUES
('admin',	'17c725af38777cf3ac4bf34e39f44caa',	't');

INSERT INTO competences (id_comp, name, description) VALUES
('GIT', 'Git', 'Gestion de versions décentralisée.'),
-- Web
('HTML', 'HTML', 'Langage de balisage pour le Web.'),
('CSS', 'CSS', 'Feuilles de style en cascade pour la mise en forme de pages Web.'),
('JAVASCRIPT', 'JavaScript', 'Langage de programmation pour l''interactivité côté client.'),
('PHP', 'PHP', 'Langage de script côté serveur populaire pour le Web.'),
('NODEJS', 'Node.js', 'Environnement d''exécution JavaScript côté serveur.'),
('VUEJS', 'Vue.js', 'Framework JavaScript pour interfaces utilisateur.'),
('NUXTJS', 'Nuxt.js', 'Framework web basé sur Vue.js.'),
('SYMFONY', 'Symfony', 'Framework PHP pour applications web.'),
-- Applications
('PYTHON', 'Python', 'Langage de script polyvalent pour le backend et l''analyse de données.'),
('JAVA', 'Java', 'Langage de programmation orienté objet.'),
('CPP', 'C++', 'Langage de programmation système et performant.'),
('C', 'C', 'Langage de programmation système bas niveau.'),
-- Database
('SQL', 'SQL', 'Langage de requête pour les bases de données relationnelles.'),
('NOSQL', 'NoSQL', 'Bases de données non relationnelles.'),
-- Systèmes
('LINUX', 'Linux', 'Administration de systèmes d''exploitation Linux/Unix.'),
('DOCKER', 'Docker', 'Technologie de conteneurisation d''applications.')
