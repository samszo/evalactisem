-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 14 Avril 2008 à 15:16
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `ieml_evalactisem`
--

-- --------------------------------------------------------

--
-- Structure de la table `ieml_acti`
--

DROP TABLE IF EXISTS `ieml_acti`;
CREATE TABLE `ieml_acti` (
  `acti_id` int(11) NOT NULL auto_increment,
  `uti_id` int(11) NOT NULL,
  `acti_code` text NOT NULL,
  `acti_desc` varchar(255) NOT NULL,
  PRIMARY KEY  (`acti_id`),
  KEY `uti_id` (`uti_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `ieml_acti`
--

INSERT INTO `ieml_acti` (`acti_id`, `uti_id`, `acti_code`, `acti_desc`) VALUES
(1, 0, 'GetP', 'Recupperation de  Posts'),
(2, 0, 'GetP', 'Recupperation de  Posts'),
(3, 0, 'GetP', 'Recupperation de  Posts'),
(4, 0, 'GetAP', 'Recuperation de tous les Posts');

-- --------------------------------------------------------

--
-- Structure de la table `ieml_foret`
--

DROP TABLE IF EXISTS `ieml_foret`;
CREATE TABLE `ieml_foret` (
  `ieml_id` int(11) NOT NULL,
  `ieml_parent` int(11) NOT NULL,
  UNIQUE KEY `ieml_id` (`ieml_id`,`ieml_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ieml_foret`
--

INSERT INTO `ieml_foret` (`ieml_id`, `ieml_parent`) VALUES
(1, 0),
(2, 1),
(3, 1),
(4, 1),
(5, 4),
(6, 4),
(7, 4),
(8, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(35, 4),
(36, 4),
(37, 4),
(38, 34),
(39, 34),
(40, 34),
(41, 34),
(42, 34),
(43, 34),
(44, 34),
(45, 34),
(46, 34),
(47, 34),
(48, 77),
(49, 77),
(50, 0),
(51, 50),
(52, 51),
(53, 51),
(54, 51),
(55, 51),
(56, 51),
(57, 51),
(58, 51),
(59, 51),
(60, 51),
(61, 51),
(62, 51),
(63, 51),
(64, 56),
(65, 56),
(66, 56),
(67, 56),
(68, 56),
(69, 56),
(70, 56),
(71, 56),
(72, 71),
(73, 71),
(74, 71),
(75, 71),
(76, 71),
(77, 0),
(82, -1),
(83, -1),
(84, -1),
(85, -1),
(86, -1),
(87, -1),
(88, -1),
(89, -1),
(90, -1),
(91, -1),
(92, 129),
(93, -1),
(95, -1),
(96, -1),
(97, -1),
(98, -1),
(99, -1),
(100, -1),
(101, -1),
(102, -1),
(103, -1),
(104, -1),
(105, -1),
(106, -1),
(107, -1),
(108, 129),
(109, -1),
(111, -1),
(112, -1),
(113, -1),
(114, -1),
(115, 49),
(116, -1),
(117, -1),
(118, -1),
(119, -1),
(120, 119),
(121, 119),
(122, 119),
(123, 119),
(124, 119),
(125, 119),
(126, 119),
(127, -1),
(128, -1),
(129, -1),
(130, -1),
(131, -1),
(132, -1),
(133, -1),
(134, -1),
(135, -1),
(136, -1),
(137, -1),
(138, -1),
(139, -1),
(140, -1),
(141, -1),
(142, -1),
(143, -1),
(144, -1),
(145, -1),
(146, -1),
(147, -1),
(148, -1),
(149, -1),
(150, -1),
(151, -1),
(152, -1),
(153, -1),
(154, -1),
(155, -1),
(156, -1),
(157, -1),
(158, -1),
(159, -1),
(160, -1),
(161, -1),
(162, -1),
(163, -1),
(164, -1),
(165, -1),
(166, -1),
(167, -1),
(168, 179),
(169, -1),
(170, 169),
(171, 179),
(172, -1),
(173, 169),
(174, -1),
(175, -1),
(176, -1),
(177, -1),
(178, -1),
(179, -1),
(180, -1),
(181, -1),
(182, -1),
(183, -1),
(184, -1),
(185, -1),
(186, -1),
(187, -1),
(188, -1),
(189, -1),
(190, -1),
(191, -1),
(192, -1),
(193, -1),
(194, -1),
(196, 198),
(197, 198),
(198, 0),
(199, 0);

-- --------------------------------------------------------

--
-- Structure de la table `ieml_foret_flux`
--

DROP TABLE IF EXISTS `ieml_foret_flux`;
CREATE TABLE `ieml_foret_flux` (
  `onto_flux_id` int(11) NOT NULL,
  `onto_flux_parents` int(11) NOT NULL,
  PRIMARY KEY  (`onto_flux_id`,`onto_flux_parents`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ieml_foret_flux`
--

INSERT INTO `ieml_foret_flux` (`onto_flux_id`, `onto_flux_parents`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 11),
(6, 0),
(7, 0),
(8, 11),
(8, 12),
(9, 0),
(10, 12),
(11, 0),
(12, 0),
(13, 0),
(14, 25),
(15, 25),
(16, 0),
(17, 0),
(18, 25),
(19, 25),
(20, 27),
(21, 26),
(22, 26),
(23, 25),
(24, 25),
(25, 0),
(26, 0),
(27, 0);

-- --------------------------------------------------------

--
-- Structure de la table `ieml_onto`
--

DROP TABLE IF EXISTS `ieml_onto`;
CREATE TABLE `ieml_onto` (
  `ieml_id` int(11) NOT NULL auto_increment,
  `ieml_code` varchar(1000) character set utf8 collate utf8_bin NOT NULL,
  `ieml_lib` varchar(255) NOT NULL,
  `ieml_niveau` int(11) NOT NULL,
  `ieml_parent` text NOT NULL,
  `ieml_date` date NOT NULL,
  PRIMARY KEY  (`ieml_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Cette table est utilisée pour stocker l’ontologie IEML.' AUTO_INCREMENT=298 ;

--
-- Contenu de la table `ieml_onto`
--

-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 24 Avril 2008 à 19:03
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `ieml_evalactisem`
--

-- --------------------------------------------------------

--
-- Structure de la table `ieml_onto`
--

DROP TABLE IF EXISTS `ieml_onto`;
CREATE TABLE `ieml_onto` (
  `ieml_id` int(11) NOT NULL auto_increment,
  `ieml_code` varchar(1000) character set utf8 collate utf8_bin NOT NULL,
  `ieml_lib` varchar(255) NOT NULL,
  `ieml_niveau` int(11) NOT NULL,
  `ieml_parent` text NOT NULL,
  `ieml_date` date NOT NULL,
  PRIMARY KEY  (`ieml_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Cette table est utilisée pour stocker l’ontologie IEML.' AUTO_INCREMENT=293 ;

--
-- Contenu de la table `ieml_onto`
--

INSERT INTO `ieml_onto` (`ieml_id`, `ieml_code`, `ieml_lib`, `ieml_niveau`, `ieml_parent`, `ieml_date`) VALUES
(1, 'e.a.-', 's’entr’aider', 1, 'ieml_10eF', '0000-00-00'),
(2, 'a.a.-', 'offrir', 2, 'uwe', '0000-00-00'),
(3, 'a.', 'demande', 2, 'uwe', '0000-00-00'),
(4, 'e.i.-', 'contracter', 2, 'uwe', '0000-00-00'),
(5, 'y.e.-', ' faire', 3, 'ea  ae  aa', '0000-00-00'),
(6, 'a.y.-', ' s’engager dans', 3, 'ea  ae  aa', '0000-00-00'),
(7, 'w.e.-b.', ' prêter', 3, 'ea  ae  aa', '0000-00-00'),
(8, 'w.a.-k.', ' emprunter', 3, 'ea  ae  aa', '0000-00-00'),
(9, 'w.u.-k.', ' produire, construire', 3, 'ea  ae  aa', '0000-00-00'),
(10, 'w.o.-w.u.-''', ' enseigner', 3, 'ea  ae  aa', '0000-00-00'),
(11, 'w.a.-w.u.-''', ' apprendre', 3, 'ea  ae  aa', '0000-00-00'),
(12, 'o.w.-a.', ' chercher', 3, 'ea  ae  aa', '0000-00-00'),
(13, 'o.w.-e.', ' conseiller', 3, 'ea  ae  aa', '0000-00-00'),
(14, 'a.w.-a.', ' cultiver système d’information', 3, 'ea  ae  aa', '0000-00-00'),
(15, 'i.w.-e.', ' semer graine chgt social', 3, 'ea  ae  aa', '0000-00-00'),
(16, 'u.a.-w.e.-''h.''', ' agir social, communautaire', 3, 'ea  ae  aa', '0000-00-00'),
(17, 'y.o.-w.e.-''h.''', ' accompagner', 3, 'ea  ae  aa', '0000-00-00'),
(18, 'e.e.-w.e.-''h.''', ' être sensible aux rapports humains', 3, 'ea  ae  aa', '0000-00-00'),
(19, 'p.k.-''l.o.-''e.a.-e.', ' constituer liens sociaux', 3, 'ea  ae  aa', '0000-00-00'),
(20, 'o.f.-o.e.-''w.e.-h.', ' jouer', 3, 'ea  ae  aa', '0000-00-00'),
(21, 'g.t.-u.t.-''', ' découvrir, visiter', 3, 'ea  ae  aa', '0000-00-00'),
(22, 'o.f.-''k.a.-''t.a.-''', ' faire la fête, célébrer', 3, 'ea  ae  aa', '0000-00-00'),
(23, 'p.s.- d.i.-''k.i.-', ' agir dans le cadre du droit de propriété intellectuelle', 3, 'ea  ae  aa', '0000-00-00'),
(24, 's.y-', ' agir dans le cadre du droit du droit administratif ou commercial', 3, 'ea  ae  aa', '0000-00-00'),
(25, 'f.o.-', ' agir dans le cadre du droit pénal', 3, 'ea  ae  aa', '0000-00-00'),
(26, 'n.a.-m.a.-''f.o.-''', ' agir dans le cadre du droit de la famille', 3, 'ea  ae  aa', '0000-00-00'),
(27, 'd.a.-m.a.-''f.o.-''', ' contrôler la reproduction', 3, 'ea  ae  aa', '0000-00-00'),
(28, 'm.a.-m.a.-''f.o.-''', ' créer une entreprise', 3, 'ea  ae  aa', '0000-00-00'),
(29, 'd.a.-m.a.-''f.o.-'' w.a.-w.a-''y.o.-w.e.-''h.', ' mener une action politique ou communautaire', 3, 'ea  ae  aa', '0000-00-00'),
(30, 't.a.-m.a.-''f.o.-''', ' équiper un ménage', 3, 'ea  ae  aa', '0000-00-00'),
(31, 'n.o.-n.o.-''f.o.-''' ,  ' équiper une activité économique, une entreprise', 3, 'e.a.-a.e.-''a.a.-', '0000-00-00'),
(32, 'f.s.-n.f.-''f.o.-''', ' cultiver mémoire collective', 3, 'ea  ae  aa', '0000-00-00'),
(33, 'd.a.-m.a.-''f.o.-''', ' éduquer, aide à la parentalité', 3, 'ea  ae  aa', '0000-00-00'),
(34, 'k.a.-m.a.-''f.o.-''', ' définir, poser problème', 3, 'ea  ae  aa', '0000-00-00'),
(35, 'l.a.-m.a.-''f.o.-''', ' proposer des solutions', 3, 'ea  ae  aa', '0000-00-00'),
(36, 'd.o.-e.e.-''o.o.-', ' résoudre problèmes', 3, 'ea  ae  aa', '0000-00-00'),
(37, 'd.o.-e.e.-''y.y.-', ' transférer des connaissances dans la pratique', 3, 'ea  ae  aa', '0000-00-00'),
(38, 'd.o.-e.e.-''w.u.-''w.e.-', 'Recherche d''emploi', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(39, 'm.a.-b.a.-''', 'Désocialisation', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(40, 'w.a.-k.', 'Difficultés éducatives', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(41, 'f.w.-a.', 'Problèmes de logement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(42, 'm.a.-f.a.-''l.a.-d.a.-''f.o.', 'Surendettement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(43, 'm.a.-f.a.-''k.e-.f.a.-w.a.-''.e.', 'Maladie / handicap', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(44, 'l.o.-n.o.-''', 'Problèmes administratifs', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(45, 'k.i.-m.i.-''t.u.-', 'Problèmes légaux', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(46, 'b.a.-t.a.-''f.o.-''' , 'Difficultés financières', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(47, 'f.a.-k.a.-''w.a.-''w.o.-', 'Isolement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(48, 'f.e.-l.i.-''', 'les savoirs', 2, '77', '2008-01-12'),
(49, 'k.a.-t.a.-''', 'les savoir-faire et compétences', 2, '77', '0000-00-00'),
(50, 'm.a.-f.a.-''e.e.-l.o.-s.y.-''', 'Les acteurs', 1, 'ieml_10eF', '0000-00-00'),
(51, 'f.s.- .w.-''o.w.-''e.', 'Le public', 2, 'fo', '0000-00-00'),
(52, 'f.a.-t.a.-''', ' Adultes', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(53, 't.e.-f.u.-''w.a.-e.', ' Demandeurs d''emploi', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(54, 's.y.-', ' Enfants', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(55, 'f.a.-f.a.-''f.o.-', ' Étrangers', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(56, 'b.e.-f.u.-''w.a.-e.', ' Familles', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(57, 'b.e.-s.u.-''a.s.-''i.e.-"', ' Femmes', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(58, 'b.e.-s.u.-'' i.n.-d.', ' Jeunes en difficulté', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(59, 'b.e.-s.u.-'' .s.p.-', ' Milieu carcéral', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(60, 'b.e.-s.u.-'' .u.s.-a.', ' Personnes âgées', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(61, 'w.u.-w.u.-'' .x.x.-''t.o.-', ' Personnes handicapées', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(62, 'g.g.-f.o.-''s.y.-', ' Précaires', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(63, 'o.e.-w.e.-''h.d.-a.m.-a.f.-''.o', 'Tous', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(64, 'y.e.- .n.-''a.s.-a.f.-''o.', 'animaux domestiques', 4, 'mamafo', '0000-00-00'),
(65, 'y.e.- .p.-''p.''k.o.-s.y-', 'mère', 4, 'mamafo', '0000-00-00'),
(66, 's.y.-', 'enfant', 4, 'mamafo', '0000-00-00'),
(67, 'f.a.-t.a.-''', 'père', 4, 'mamafo', '0000-00-00'),
(68, 's.e.-k.u.-''w.a.-e.', 'frère/sœur', 4, 'mamafo', '0000-00-00'),
(69, 'b.e.-s.u.-''u.s.-a', 'ancêtre', 4, 'mamafo', '0000-00-00'),
(70, 'y.e.- .i.-''i.l.-o.s.-''y.i.i.-n.o.-''s.y.-', 'ami', 4, 'mamafo', '0000-00-00'),
(71, 'y.e.- .d.-''o.n.-o', 'niveaux de compétence', 1, 'ieml_10eF', '0000-00-00'),
(72, 'y.e.- .e.-''e.b.-o.s.-''y.', 'Débutant', 2, '71', '0000-00-00'),
(73, 'y.e.- .i.-''i.k.-o', 'initié', 2, '71', '0000-00-00'),
(74, 'y.e.- a.a.-''s.o.-''s.y.-', 'expert', 2, '71', '0000-00-00'),
(75, 'd.d.-d.i.-''k.i.-m.a.-''m.a.', 'enseigner', 2, '71', '0000-00-00'),
(76, 'w.a.-u.s-''y.-', 'amateur', 2, '71', '0000-00-00'),
(77, 'w.a.-u.y.-''y.t.-o.s.-''y', 'Objet', 1, 'ieml_10eF', '2008-01-12'),
(82, 'w.a.-u.w.-''o.n.-' , 'jeu', 3, '-1', '0000-00-00'),
(83, 'y.y.-t.o.-''s.y.-', 'accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(84, 'u.a.-w.e.-''h', 'action sociale / communautaire', 3, '-1', '0000-00-00'),
(85, 'b.a.-s.a.-''', 'activité de bâtiment / travaux publics', 3, '-1', '0000-00-00'),
(86, 's.e.-k.u.-''w.a.-e.f.-u.t.-''u', 'activité physique', 3, '-1', '0000-00-00'),
(87, 'b.a.-b.a.-''d.a.-m.a.-''f.o.-', 'administration / bureau', 3, '-1', '0000-00-00'),
(88, 'w.a.-eM.-''M.M.-M.', 'aide + entrepreneur', 3, '-1', '0000-00-00'),
(89, 's.i.-m.i.-''t.u.-', 'aide + marché du travail', 3, '-1', '0000-00-00'),
(90, 'u.u.-f.o.-''', 'aide + prendre soin de soi-même', 3, '-1', '0000-00-00'),
(91, 'i.i.-l.o.-''s.y.-', 'animal + manger / boire', 3, '-1', '0000-00-00'),
(92, 'w.e.-y.', 'architecture', 4, 'yyto', '0000-00-00'),
(93, 'j.j.-l.o.-''s.y.-', 'assurance', 3, '-1', '0000-00-00'),
(95, 'o.o.-n.o.-''s.y.-', 'bourse', 3, '-1', '0000-00-00'),
(96, 'a.a.-s.o.-''s.y.-', 'composer de la musique', 4, 'uufo', '0000-00-00'),
(97, 'h.h.-s.o.-''s.y.-', 'comptable', 3, '-1', '0000-00-00'),
(98, 'k.a.-d.a.-''', 'connaissance organisée', 3, '-1', '0000-00-00'),
(99, 'y.y.-t.o.-''s.y.-', 'accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(100, 'u.a.-w.e.-''h.', 'action sociale / communautaire', 3, '-1', '0000-00-00'),
(101, 'b.a.-s.a.-''', 'activité de bâtiment / travaux publics', 3, '-1', '0000-00-00'),
(102, 's.e.-k.u.-''w.a.-e.f.-''u.t.-u.', 'activité physique', 3, '-1', '0000-00-00'),
(103, 'b.a.-b.a.-'' d.a.-m.a.-f.o', 'administration / bureau', 3, '-1', '0000-00-00'),
(104, 'w.a.-e.M-''M.M.-M.', 'aide + entrepreneur', 3, '-1', '0000-00-00'),
(105, 's.i.-m.i.-''t.''u.''-''', 'aide + marché du travail', 3, '-1', '0000-00-00'),
(106, 'u.u.-f.o.-''', 'aide + prendre soin de soi-même', 3, '-1', '0000-00-00'),
(107, 'i.i.-l.o.-''s.y.-', 'animal + manger / boire', 3, '-1', '0000-00-00'),
(108, 'w.e.-y.', 'architecture', 4, 'yyto', '0000-00-00'),
(109, 'j.j.-l.o.-''s.y.-', 'assurance', 3, '-1', '0000-00-00'),
(111, 'o.o.-n.o.-''s.y.-', 'bourse', 3, '-1', '0000-00-00'),
(112, 'a.a.-s.o.-''s.y.-', 'composer de la musique', 4, 'uufo', '0000-00-00'),
(113, 'h.h.-s.o.-''s.y.-', 'comptable', 3, '-1', '0000-00-00'),
(114, 's.y.-', 'connaissance organisée', 3, '-1', '0000-00-00'),
(115, 't.e.-s.u.-''w.a.-e.', 'création d''images', 3, 'wae', '0000-00-00'),
(116, 'f.a.-f.a.-''f.o-', 'cultivateur / éleveur', 3, '-1', '0000-00-00'),
(117, 'b.e.-m.u.-''w.a.-e', 'culture des arts de la scène', 3, '-1', '0000-00-00'),
(118, 'b.e.-f.u.-''w.a-.e', 'culture musicale', 4, 'uufo', '0000-00-00'),
(119, 'b.e.-s.u-''', 'culture visuelle', 3, 'wae', '0000-00-00'),
(120, 'b.e.-s.u.-''a.s.-i.e-''', 'culture visuelle', 4, 'besu', '0000-00-00'),
(121, 'b.e.-s.u.-''f.r-', 'culture visuelle', 4, 'besu', '0000-00-00'),
(122, 'b.e.-s.u.-''i.n.-d', 'culture visuelle', 4, 'besu', '0000-00-00'),
(123, 'b.e.-s.u.-''i.t-', 'culture visuelle', 4, 'besu', '0000-00-00'),
(124, 'b.e.-s.u.-''s.p-', 'culture visuelle', 4, 'besu', '0000-00-00'),
(125, 'b.e.-s.u.-''u.k-', 'culture visuelle', 4, 'besu', '0000-00-00'),
(126, 'b.e.-s.u.-''u.s.-a', 'culture visuelle', 4, 'besu', '0000-00-00'),
(127, 'f.o.-n.o-''', 'danse', 3, '-1', '0000-00-00'),
(128, 'w.u.-w.u-''.x.x-.t.o-''', 'découvrir + patrimoine collectif', 3, '-1', '0000-00-00'),
(129, 'y.y.-t.o-''', 'discipline artistique / sci.', 3, '-1', '0000-00-00'),
(130, 'g.g.-''f.o-.s.y-''', 'écologie / agronomie', 3, '-1', '0000-00-00'),
(131, 't.e.-n.u.-''w.a.-e', 'écriture littéraire', 4, 'basa', '0000-00-00'),
(132, 'o.e.-w.e.-''h.d.-a.m.-''a.f.-o', 'eduquer + enfant', 3, '-1', '0000-00-00'),
(133, 'd.i.-s.i.-''t.u-', 'électronique / photonique', 3, '-1', '0000-00-00'),
(134, 'y.e.-n.a.-''s.a.-f.o.-', 'enseigner + adulte', 3, '-1', '0000-00-00'),
(135, 'y.e.-s.y.-''', 'enseigner + connaissances organisées', 3, '-1', '0000-00-00'),
(136, 'y.e.-p.p.-''k.o.-s.y.-''', 'enseigner + économie', 3, '-1', '0000-00-00'),
(137, 'y.e.-y.d.-''', 'enseigner + handicap', 3, '-1', '0000-00-00'),
(138, 'y.e.-h.i.-''s.t.-o.i.-''r.e.-c.c.-''l.o.-s.y.-''', 'enseigner + histoire + géographie', 3, '-1', '0000-00-00'),
(139, 'y.e.-p.p.-''t.o.-s.y.-''', 'enseigner + histoire des relations internationales', 3, '-1', '0000-00-00'),
(140, 'y.e.-g.g.-''t.o.-s.y-''', 'enseigner + langue ancienne', 3, '-1', '0000-00-00'),
(141, 'y.e.-u.u.-''b.o.-', 'enseigner + langue naturelle', 3, '-1', '0000-00-00'),
(142, 's.e.-k.u.-''w.a.-e', 'enseigner + lire / écrire', 3, '-1', '0000-00-00'),
(143, 'y.e.-j.j.-''b.o.-s.y.-''', 'enseigner + mathématiques', 3, '-1', '0000-00-00'),
(144, 'y.e.-s.e.-''b.u.-w.a.-''e.', 'enseigner + parler une seconde langue', 3, '-1', '0000-00-00'),
(145, 'y.e.-j.j.-''s.o.-s.y.-''', 'enseigner + philosophie', 3, '-1', '0000-00-00'),
(146, 'y.e.-i.i.-''l.o.-s.y.-''i.i.-n.o.-''s.y.-', 'enseigner + physique + chimie', 3, '-1', '0000-00-00'),
(147, 'y.e.-a.a.-''m.o.-s.y.-''', 'enseigner + psychologie', 3, '-1', '0000-00-00'),
(148, 'y.e.-d.o.-''n.o.-', 'enseigner + science', 3, '-1', '0000-00-00'),
(149, 'y.e.-j.j.-''l.o.-s.y.-''', 'enseigner + sciences de la terre', 3, '-1', '0000-00-00'),
(150, 'y.e.-e.e.-''b.o.-s.y.-''', 'enseigner + sciences de l''éducation et de la formation', 3, '-1', '0000-00-00'),
(151, 'y.e.-o.o.-''k.o.-s.y.-''', 'enseigner + sciences politiques', 3, '-1', '0000-00-00'),
(152, 'y.e.-i.i.-''k.o.-', 'enseigner + technologie / procédé technique', 3, '-1', '0000-00-00'),
(153, 'y.e.-i.i.-''s.o.-s.y.-''', 'enseigner + théorie scientifique de la nature', 3, '-1', '0000-00-00'),
(154, 'y.e.-a.a.-''s.o.-s.y.-''', 'enseigner+ sociologie', 3, '-1', '0000-00-00'),
(155, 'l.a.-d.a.-''f.o.-k.i.-''n.i.-t.u.-''', 'entrepreneur + assemblé / manufacturé', 3, '-1', '0000-00-00'),
(156, 'dddiki mama', 'équilibre + monnaie + famille', 3, '-1', '0000-00-00'),
(157, 'f.e.-m.i.-''w.a.-e.', 'esthétique / mode', 3, '-1', '0000-00-00'),
(158, 'w.a.-u.s.-''y', 'étudier la documentation + connaissances organisées', 3, '-1', '0000-00-00'),
(159, 'w.a.-u.b.-''e.n.-u.w.-a.e.-''', 'étudier la documentation + érudition littéraire', 4, 'basa', '0000-00-00'),
(160, 'w.a.-u.y.-''y.t.-o.s.-y', 'étudier la documentation + histoire des sciences / histoire des arts', 3, '-1', '0000-00-00'),
(161, 'w.a.-u.f.-''w.e.-', 'étudier la documentation + loisir', 3, '-1', '0000-00-00'),
(162, 'w.a.-u.w.-''o.n.-', 'étudier la documentation + représentation du monde', 3, '-1', '0000-00-00'),
(163, 'p.p.-t.o.-''s.y.-', 'histoire des relations internationales', 3, '-1', '0000-00-00'),
(164, 'y.y.-t.o.-''s.y.-', 'histoire des sciences / histoire des arts', 3, '-1', '0000-00-00'),
(166, 'y.y.-t.o.-''s.y.-u.u.-''f.o.-', 'histoire des sciences / histoires des arts + musique', 3, '-1', '0000-00-00'),
(167, 'l.e.-b.i.-''w.a.-e', 'ingénierie informatique', 3, '-1', '0000-00-00'),
(168, 's.e.-f.u.-''w.a.-e', 'jouer de la musique / chanter', 4, 'uufo', '0000-00-00'),
(169, 'b.a.-s.a.-''', 'lecture', 3, '-1', '0000-00-00'),
(170, 'b.a.-s.a.-''n.m.-', 'lecture + divertissement', 4, 'basa', '0000-00-00'),
(171, 's.e.-k.u.-''w.a.-e.f.-''u.t.-u', 'lire/ ecrire + note de musique', 4, 'uufo', '0000-00-00'),
(172, 'b.a.-b.a.-''', 'littérature', 3, '-1', '0000-00-00'),
(173, 'b.a.-b.a.-''d.a.-m.a.-''f.o.-', 'littérature + enfant', 4, 'basa', '0000-00-00'),
(174, 'f.w.-e', 'loisir', 3, '-1', '0000-00-00'),
(175, 'w.a.-e.M.-''M.M.-M.', 'maîtriser les compétences + cycle naturel', 3, '-1', '0000-00-00'),
(176, 'w.o.-w.e.-''', 'manger / boire', 3, '-1', '0000-00-00'),
(177, 's.i.-''m.i.-''t.u.-', 'mécanique', 3, '-1', '0000-00-00'),
(178, 't.e.-m.u.-''w.a.-e.', 'mise en scène / réalisation', 3, 'wae', '0000-00-00'),
(179, 'u.u.-f.o.-''', 'musique', 3, '-1', '0000-00-00'),
(180, 'e.e.-m.o.-''s.y.-', 'orientation professionnelle', 3, '-1', '0000-00-00'),
(181, 'i.i.-l.o.-''s.y.-', 'physique', 3, '-1', '0000-00-00'),
(182, 'a.a.-m.o.-''s.y.-', 'psychologie', 3, '-1', '0000-00-00'),
(183, 'w.e.-''y.', 'reconstruire les savoirs', 3, '-1', '0000-00-00'),
(184, 'p.p.-''f.o-.s.y.-''', 'sciences de la santé', 3, '-1', '0000-00-00'),
(185, 'j.j.-l.o.-''s.y.-', 'sciences de la terre', 3, '-1', '0000-00-00'),
(186, 'e.e.-b.o.-''s.y.-', 'sciences de l''éducation et de la formation', 3, '-1', '0000-00-00'),
(187, 'o.o.-n.o.-''s.y.-', 'sciences juridiques', 3, '-1', '0000-00-00'),
(188, 'o.o.-n.o.-''s.y.-f.a.-''k.a.-w.a.-''w.o.-', 'sciences juridiques + accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(189, 'a.a.-s.o.-''s.y.-', 'sociologie', 3, '-1', '0000-00-00'),
(190, 'd.e.-k.i.-''w.a.-e.', 'sport d''équipe', 3, '-1', '0000-00-00'),
(191, 'h.h.-s.o.-''s.y.-', 'théologie', 3, '-1', '0000-00-00'),
(192, 'b.a.-l.a.-''f.o.-', 'transporteur', 3, '-1', '0000-00-00'),
(193, 'k.a.-d.a.-''', 'vente', 3, '-1', '0000-00-00'),
(194, 'l.o.-t.o.-''', 'voyage', 3, '-1', '0000-00-00'),
(196, 's.y.-wa.e.-|doee-R', 'Choix d un savoir pour un membre', 1, '198', '2008-01-13'),
(197, 's.y.-wa.e.-|doee-R', 'TEST', 1, '198', '2008-01-12');


-- --------------------------------------------------------

--
-- Structure de la table `ieml_onto_flux`
--

DROP TABLE IF EXISTS `ieml_onto_flux`;
CREATE TABLE `ieml_onto_flux` (
  `onto_flux_id` int(11) NOT NULL auto_increment,
  `onto_flux_code` varchar(225) NOT NULL,
  `onto_flux_desc` varchar(255) NOT NULL,
  `onto_flux_niveau` int(11) NOT NULL,
  `onto_flux_parents` char(255) NOT NULL,
  PRIMARY KEY  (`onto_flux_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Contenu de la table `ieml_onto_flux`
--

INSERT INTO `ieml_onto_flux` (`onto_flux_id`, `onto_flux_code`, `onto_flux_desc`, `onto_flux_niveau`, `onto_flux_parents`) VALUES
(1, 'acceleo', 'tag ', 1, ''),
(2, 'd''utilisation', 'tag ', 1, ''),
(3, 'delete', 'tag ', 1, ''),
(4, 'demo', 'tag ', 1, ''),
(5, 'ieml', 'tag ', 1, 'semantique;'),
(6, 'mail', 'tag ', 1, ''),
(7, 'manuel', 'tag ', 1, ''),
(8, 'ontologie', 'tag ', 1, 'semantique;programmation;'),
(9, 'system', 'tag ', 1, ''),
(10, 'xul', 'tag ', 1, 'programmation;'),
(11, 'semantique', 'bundels ', 0, ''),
(12, 'programmation', 'bundels ', 0, ''),
(13, 'utilisation', 'tag ', 1, ''),
(14, '-projet', 'tag ', 1, 'planifications;'),
(15, 'MS-Projet', 'tag ', 1, 'planifications;'),
(16, 'Tv-algerienne', 'tag ', 1, ''),
(17, 'arabe-tv', 'tag ', 1, ''),
(18, 'budgetisation-planification', 'tag ', 1, 'planifications;'),
(19, 'etapes_plnification', 'tag ', 1, 'planifications;'),
(20, 'filme', 'tag ', 1, 'cinema;'),
(21, 'kamel', 'tag ', 1, 'musique;'),
(22, 'musique-chaabi', 'tag ', 1, 'musique;'),
(23, 'planification', 'tag ', 1, 'planifications;'),
(24, 'planification-suivi-projet', 'tag ', 1, 'planifications;'),
(25, 'planifications', 'bundels ', 0, ''),
(26, 'musique', 'bundels ', 0, ''),
(27, 'cinema', 'bundels ', 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `ieml_trad`
--

DROP TABLE IF EXISTS `ieml_trad`;
CREATE TABLE `ieml_trad` (
  `ieml_id` int(11) NOT NULL,
  `onto_flux_id` int(11) NOT NULL,
  `trad_date` date NOT NULL,
  PRIMARY KEY  (`ieml_id`,`onto_flux_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ieml_trad`
--

INSERT INTO `ieml_trad` (`ieml_id`, `onto_flux_id`, `trad_date`) VALUES
(288, 12, '2008-04-08'),
(292, 8, '2008-04-09');

-- --------------------------------------------------------

--
-- Structure de la table `ieml_uti`
--

DROP TABLE IF EXISTS `ieml_uti`;
CREATE TABLE `ieml_uti` (
  `uti_id` int(11) NOT NULL auto_increment,
  `uti_login` varchar(225) NOT NULL,
  `maj` varchar(225) NOT NULL,
  PRIMARY KEY  (`uti_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ieml_uti`
--

INSERT INTO `ieml_uti` (`uti_id`, `uti_login`, `maj`) VALUES
(1, 'amelmaster', '2008-04-12 20:35:20'),
(2, 'menerville', '2008-04-13 19:06:20');

-- --------------------------------------------------------

--
-- Structure de la table `ieml_uti_onto`
--

DROP TABLE IF EXISTS `ieml_uti_onto`;
CREATE TABLE `ieml_uti_onto` (
  `uti_id` int(11) NOT NULL,
  `ieml_id` int(11) NOT NULL,
  PRIMARY KEY  (`uti_id`,`ieml_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ieml_uti_onto`
--


-- --------------------------------------------------------

--
-- Structure de la table `ieml_uti_onto_flux`
--

DROP TABLE IF EXISTS `ieml_uti_onto_flux`;
CREATE TABLE `ieml_uti_onto_flux` (
  `uti_id` int(11) NOT NULL,
  `onto_flux_id` int(11) NOT NULL,
  PRIMARY KEY  (`uti_id`,`onto_flux_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `ieml_uti_onto_flux`
--

INSERT INTO `ieml_uti_onto_flux` (`uti_id`, `onto_flux_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27);
