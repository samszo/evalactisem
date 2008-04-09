-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mercredi 09 Avril 2008 à 14:26
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.1
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Contenu de la table `ieml_acti`
-- 

INSERT INTO `ieml_acti` (`acti_id`, `uti_id`, `acti_code`, `acti_desc`) VALUES 
(1, 0, 'GetP', 'Recupperation de  Posts'),
(2, 0, 'GetP', 'Recupperation de  Posts'),
(3, 0, 'GetP', 'Recupperation de  Posts');

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
(4, 11),
(5, 0),
(6, 0),
(7, 11),
(7, 12),
(8, 0),
(9, 0),
(10, 12),
(11, 0),
(12, 0),
(13, -1),
(14, -1),
(15, -1),
(16, -1),
(17, -1),
(18, -1),
(19, -1),
(20, -1),
(21, -1),
(22, -1),
(23, -1),
(24, -1),
(25, -1),
(26, -1),
(27, -1),
(28, -1),
(29, -1),
(30, -1),
(31, -1),
(32, -1),
(33, -1),
(34, -1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cette table est utilisée pour stocker l’ontologie IEML.' AUTO_INCREMENT=293 ;

-- 
-- Contenu de la table `ieml_onto`
-- 

INSERT INTO `ieml_onto` (`ieml_id`, `ieml_code`, `ieml_lib`, `ieml_niveau`, `ieml_parent`, `ieml_date`) VALUES 
(1, 0x757765, 's’entr’aider', 1, 'ieml_10eF', '0000-00-00'),
(2, 0x6561, 'offrir', 2, 'uwe', '0000-00-00'),
(3, 0x6165, 'demande', 2, 'uwe', '0000-00-00'),
(4, 0x6161, 'contracter', 2, 'uwe', '0000-00-00'),
(5, 0x69, ' faire', 3, 'ea  ae  aa', '0000-00-00'),
(6, 0x61, ' s’engager dans', 3, 'ea  ae  aa', '0000-00-00'),
(7, 0x6965, ' prêter', 3, 'ea  ae  aa', '0000-00-00'),
(8, 0x6569, ' emprunter', 3, 'ea  ae  aa', '0000-00-00'),
(9, 0x6969, ' produire, construire', 3, 'ea  ae  aa', '0000-00-00'),
(10, 0x7965, ' enseigner', 3, 'ea  ae  aa', '0000-00-00'),
(11, 0x6579, ' apprendre', 3, 'ea  ae  aa', '0000-00-00'),
(12, 0x6179, ' chercher', 3, 'ea  ae  aa', '0000-00-00'),
(13, 0x696f, ' conseiller', 3, 'ea  ae  aa', '0000-00-00'),
(14, 0x776562, ' cultiver système d’information', 3, 'ea  ae  aa', '0000-00-00'),
(15, 0x776f6b, ' semer graine chgt social', 3, 'ea  ae  aa', '0000-00-00'),
(16, 0x77616b, ' agir social, communautaire', 3, 'ea  ae  aa', '0000-00-00'),
(17, 0x77616b62696c692a, ' accompagner', 3, 'ea  ae  aa', '0000-00-00'),
(18, 0x77756b, ' être sensible aux rapports humains', 3, 'ea  ae  aa', '0000-00-00'),
(19, 0x77656b, ' constituer liens sociaux', 3, 'ea  ae  aa', '0000-00-00'),
(20, 0x776f7775, ' jouer', 3, 'ea  ae  aa', '0000-00-00'),
(21, 0x77757775, ' découvrir, visiter', 3, 'ea  ae  aa', '0000-00-00'),
(22, 0x77617775, ' faire la fête, célébrer', 3, 'ea  ae  aa', '0000-00-00'),
(23, 0x6f776f, ' agir dans le cadre du droit de propriété intellectuelle', 3, 'ea  ae  aa', '0000-00-00'),
(24, 0x6f7761, ' agir dans le cadre du droit du droit administratif ou commercial', 3, 'ea  ae  aa', '0000-00-00'),
(25, 0x6f7775, ' agir dans le cadre du droit pénal', 3, 'ea  ae  aa', '0000-00-00'),
(26, 0x6f7765, ' agir dans le cadre du droit de la famille', 3, 'ea  ae  aa', '0000-00-00'),
(27, 0x657765, ' contrôler la reproduction', 3, 'ea  ae  aa', '0000-00-00'),
(28, 0x617761, ' créer une entreprise', 3, 'ea  ae  aa', '0000-00-00'),
(29, 0x617775, ' mener une action politique ou communautaire', 3, 'ea  ae  aa', '0000-00-00'),
(30, 0x697765, ' équiper un ménage', 3, 'ea  ae  aa', '0000-00-00'),
(31, 0x697761, ' équiper une activité économique, une entreprise', 3, 'e.a.-a.e.-''a.a.-', '0000-00-00'),
(32, 0x7561776568, ' cultiver mémoire collective', 3, 'ea  ae  aa', '0000-00-00'),
(33, 0x6f65776568, ' éduquer, aide à la parentalité', 3, 'ea  ae  aa', '0000-00-00'),
(34, 0x796f776568, ' définir, poser problème', 3, 'ea  ae  aa', '0000-00-00'),
(35, 0x7965776568, ' proposer des solutions', 3, 'ea  ae  aa', '0000-00-00'),
(36, 0x6565776568, ' résoudre problèmes', 3, 'ea  ae  aa', '0000-00-00'),
(37, 0x7969776568, ' transférer des connaissances dans la pratique', 3, 'ea  ae  aa', '0000-00-00'),
(38, 0x706b20206c6f656165, 'Recherche d''emploi', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(39, 0x6f6b, 'Désocialisation', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(40, 0x6f6620206f65776568, 'Difficultés éducatives', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(41, 0x707320206b696c69, 'Problèmes de logement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(42, 0x67747574, 'Surendettement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(43, 0x696620207964, 'Maladie / handicap', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(44, 0x6f6620206b617461, 'Problèmes administratifs', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(45, 0x676b20206f6620206e6f7878, 'Problèmes légaux', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(46, 0x7073202064696b69, 'Difficultés financières', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(47, 0x7862, 'Isolement', 4, 'yoweh yeweh eeweh', '0000-00-00'),
(48, 0x732e792e2d, 'les savoirs', 2, '77', '2008-01-12'),
(49, 0x776165, 'les savoir-faire et compétences', 2, '77', '0000-00-00'),
(50, 0x666f, 'Les acteurs', 1, 'ieml_10eF', '0000-00-00'),
(51, 0x284d45442d522d666f292d492d49, 'Le public', 2, 'fo', '0000-00-00'),
(52, 0x6e616d61666f, ' Adultes', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(53, 0x6e6f6e6f666f2a206c6f656561652a, ' Demandeurs d''emploi', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(54, 0x64616d61666f, ' Enfants', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(55, 0x6c6f746f666f2a, ' Étrangers', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(56, 0x6d616d61666f, ' Familles', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(57, 0x6e6f6e6f666f2a2066616d61, ' Femmes', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(58, 0x64616d61666f207761776120796f776568, ' Jeunes en difficulté', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(59, 0x6e6f6e6f666f2a206e6f78786f6e2a, ' Milieu carcéral', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(60, 0x74616d61666f, ' Personnes âgées', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(61, 0x6e6f6e6f666f2a207964, ' Personnes handicapées', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(62, 0x6e6f6e6f666f2a207073, ' Précaires', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(63, 0x6e6f6e6f666f2a206e7573756475, 'Tous', 3, '(MED-R-fo)-I-I', '0000-00-00'),
(64, 0x66736e66666f2a, 'animaux domestiques', 4, 'mamafo', '0000-00-00'),
(65, 0x66616d61666f, 'mère', 4, 'mamafo', '0000-00-00'),
(66, 0x64616d61666f, 'enfant', 4, 'mamafo', '0000-00-00'),
(67, 0x6e616d61666f, 'père', 4, 'mamafo', '0000-00-00'),
(68, 0x6b616d61666f, 'frère/sœur', 4, 'mamafo', '0000-00-00'),
(69, 0x74616d61666f, 'ancêtre', 4, 'mamafo', '0000-00-00'),
(70, 0x6c616d61666f, 'ami', 4, 'mamafo', '0000-00-00'),
(71, 0x646f65652d52, 'niveaux de compétence', 1, 'ieml_10eF', '0000-00-00'),
(72, 0x646f65656f6f2a, 'Débutant', 2, '71', '0000-00-00'),
(73, 0x646f656565652a, 'initié', 2, '71', '0000-00-00'),
(74, 0x646f656579792a, 'expert', 2, '71', '0000-00-00'),
(75, 0x646f656579652a, 'enseigner', 2, '71', '0000-00-00'),
(76, 0x646f6565777577652a, 'amateur', 2, '71', '0000-00-00'),
(77, 0x732e792e2d77612e652e2d, 'Objet', 1, 'ieml_10eF', '2008-01-12'),
(82, 0x6d616261, 'jeu', 3, '-1', '0000-00-00'),
(83, 0x66616b61207761776f, 'accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(84, 0x77616b, 'action sociale / communautaire', 3, '-1', '0000-00-00'),
(85, 0x66656c69, 'activité de bâtiment / travaux publics', 3, '-1', '0000-00-00'),
(86, 0x667761, 'activité physique', 3, '-1', '0000-00-00'),
(87, 0x6b617461, 'administration / bureau', 3, '-1', '0000-00-00'),
(88, 0x6d616661206c616461666f, 'aide + entrepreneur', 3, '-1', '0000-00-00'),
(89, 0x6d6166612065656c6f7379, 'aide + marché du travail', 3, '-1', '0000-00-00'),
(90, 0x6d616661206b656661776165, 'aide + prendre soin de soi-même', 3, '-1', '0000-00-00'),
(91, 0x667320776f7765, 'animal + manger / boire', 3, '-1', '0000-00-00'),
(92, 0x6c6f6e6f, 'architecture', 4, 'yyto', '0000-00-00'),
(93, 0x66617461, 'assurance', 3, '-1', '0000-00-00'),
(95, 0x6b696d697475, 'bourse', 3, '-1', '0000-00-00'),
(96, 0x74656675776165, 'composer de la musique', 4, 'uufo', '0000-00-00'),
(97, 0x62617461666f, 'comptable', 3, '-1', '0000-00-00'),
(98, 0x7379, 'connaissance organisée', 3, '-1', '0000-00-00'),
(99, 0x66616b61207761776f, 'accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(100, 0x77616b, 'action sociale / communautaire', 3, '-1', '0000-00-00'),
(101, 0x66656c69, 'activité de bâtiment / travaux publics', 3, '-1', '0000-00-00'),
(102, 0x667761, 'activité physique', 3, '-1', '0000-00-00'),
(103, 0x6b617461, 'administration / bureau', 3, '-1', '0000-00-00'),
(104, 0x6d616661206c616461666f, 'aide + entrepreneur', 3, '-1', '0000-00-00'),
(105, 0x6d6166612065656c6f7379, 'aide + marché du travail', 3, '-1', '0000-00-00'),
(106, 0x6d616661206b656661776165, 'aide + prendre soin de soi-même', 3, '-1', '0000-00-00'),
(107, 0x667320776f7765, 'animal + manger / boire', 3, '-1', '0000-00-00'),
(108, 0x6c6f6e6f, 'architecture', 4, 'yyto', '0000-00-00'),
(109, 0x66617461, 'assurance', 3, '-1', '0000-00-00'),
(111, 0x6b696d697475, 'bourse', 3, '-1', '0000-00-00'),
(112, 0x74656675776165, 'composer de la musique', 4, 'uufo', '0000-00-00'),
(113, 0x62617461666f, 'comptable', 3, '-1', '0000-00-00'),
(114, 0x7379, 'connaissance organisée', 3, '-1', '0000-00-00'),
(115, 0x74657375776165, 'création d''images', 3, 'wae', '0000-00-00'),
(116, 0x66616661666f, 'cultivateur / éleveur', 3, '-1', '0000-00-00'),
(117, 0x62656d75776165, 'culture des arts de la scène', 3, '-1', '0000-00-00'),
(118, 0x62656675776165, 'culture musicale', 4, 'uufo', '0000-00-00'),
(119, 0x62657375, 'culture visuelle', 3, 'wae', '0000-00-00'),
(120, 0x626573752061736965, 'culture visuelle', 4, 'besu', '0000-00-00'),
(121, 0x62657375206672, 'culture visuelle', 4, 'besu', '0000-00-00'),
(122, 0x6265737520696e64, 'culture visuelle', 4, 'besu', '0000-00-00'),
(123, 0x62657375206974, 'culture visuelle', 4, 'besu', '0000-00-00'),
(124, 0x62657375207370, 'culture visuelle', 4, 'besu', '0000-00-00'),
(125, 0x6265737520756b, 'culture visuelle', 4, 'besu', '0000-00-00'),
(126, 0x6265737520757361, 'culture visuelle', 4, 'besu', '0000-00-00'),
(127, 0x666f6e6f, 'danse', 3, '-1', '0000-00-00'),
(128, 0x77757775207878746f, 'découvrir + patrimoine collectif', 3, '-1', '0000-00-00'),
(129, 0x7979746f, 'discipline artistique / sci.', 3, '-1', '0000-00-00'),
(130, 0x6767666f7379, 'écologie / agronomie', 3, '-1', '0000-00-00'),
(131, 0x74656e75776165, 'écriture littéraire', 4, 'basa', '0000-00-00'),
(132, 0x6f657765682064616d61666f, 'eduquer + enfant', 3, '-1', '0000-00-00'),
(133, 0x646973697475, 'électronique / photonique', 3, '-1', '0000-00-00'),
(134, 0x7965206e617361666f, 'enseigner + adulte', 3, '-1', '0000-00-00'),
(135, 0x7965207379, 'enseigner + connaissances organisées', 3, '-1', '0000-00-00'),
(136, 0x79652070706b6f7379, 'enseigner + économie', 3, '-1', '0000-00-00'),
(137, 0x796520207964, 'enseigner + handicap', 3, '-1', '0000-00-00'),
(138, 0x796520686973746f6972652063636c6f7379, 'enseigner + histoire + géographie', 3, '-1', '0000-00-00'),
(139, 0x7965207070746f7379, 'enseigner + histoire des relations internationales', 3, '-1', '0000-00-00'),
(140, 0x7965206767746f7379, 'enseigner + langue ancienne', 3, '-1', '0000-00-00'),
(141, 0x7965207575626f, 'enseigner + langue naturelle', 3, '-1', '0000-00-00'),
(142, 0x73656b75776165, 'enseigner + lire / écrire', 3, '-1', '0000-00-00'),
(143, 0x7965206a6a626f7379, 'enseigner + mathématiques', 3, '-1', '0000-00-00'),
(144, 0x79652073656275776165, 'enseigner + parler une seconde langue', 3, '-1', '0000-00-00'),
(145, 0x7965206a6a736f7379, 'enseigner + philosophie', 3, '-1', '0000-00-00'),
(146, 0x79652069696c6f73792069696e6f7379, 'enseigner + physique + chimie', 3, '-1', '0000-00-00'),
(147, 0x79652061616d6f7379, 'enseigner + psychologie', 3, '-1', '0000-00-00'),
(148, 0x796520646f6e6f, 'enseigner + science', 3, '-1', '0000-00-00'),
(149, 0x7965206a6a6c6f7379, 'enseigner + sciences de la terre', 3, '-1', '0000-00-00'),
(150, 0x7965206565626f7379, 'enseigner + sciences de l''éducation et de la formation', 3, '-1', '0000-00-00'),
(151, 0x7965206f6f6b6f7379, 'enseigner + sciences politiques', 3, '-1', '0000-00-00'),
(152, 0x79652069696b6f, 'enseigner + technologie / procédé technique', 3, '-1', '0000-00-00'),
(153, 0x7965206969736f7379, 'enseigner + théorie scientifique de la nature', 3, '-1', '0000-00-00'),
(154, 0x796520206161736f7379, 'enseigner+ sociologie', 3, '-1', '0000-00-00'),
(155, 0x6c616461666f206b696e697475, 'entrepreneur + assemblé / manufacturé', 3, '-1', '0000-00-00'),
(156, 0x646464696b69206d616d61, 'équilibre + monnaie + famille', 3, '-1', '0000-00-00'),
(157, 0x66656d69776165, 'esthétique / mode', 3, '-1', '0000-00-00'),
(158, 0x776175207379, 'étudier la documentation + connaissances organisées', 3, '-1', '0000-00-00'),
(159, 0x7761752062656e75776165, 'étudier la documentation + érudition littéraire', 4, 'basa', '0000-00-00'),
(160, 0x776175207979746f7379, 'étudier la documentation + histoire des sciences / histoire des arts', 3, '-1', '0000-00-00'),
(161, 0x77617520667765, 'étudier la documentation + loisir', 3, '-1', '0000-00-00'),
(162, 0x77617520776f6e, 'étudier la documentation + représentation du monde', 3, '-1', '0000-00-00'),
(163, 0x7070746f7379, 'histoire des relations internationales', 3, '-1', '0000-00-00'),
(164, 0x7979746f7379, 'histoire des sciences / histoire des arts', 3, '-1', '0000-00-00'),
(166, 0x7979746f7379207575666f, 'histoire des sciences / histoires des arts + musique', 3, '-1', '0000-00-00'),
(167, 0x6c656269776165, 'ingénierie informatique', 3, '-1', '0000-00-00'),
(168, 0x73656675776165, 'jouer de la musique / chanter', 4, 'uufo', '0000-00-00'),
(169, 0x62617361, 'lecture', 3, '-1', '0000-00-00'),
(170, 0x62617361206e6d, 'lecture + divertissement', 4, 'basa', '0000-00-00'),
(171, 0x73656b757761652066757475, 'lire/ ecrire + note de musique', 4, 'uufo', '0000-00-00'),
(172, 0x62616261, 'littérature', 3, '-1', '0000-00-00'),
(173, 0x626162612064616d61666f, 'littérature + enfant', 4, 'basa', '0000-00-00'),
(174, 0x667765, 'loisir', 3, '-1', '0000-00-00'),
(175, 0x776165204d4d4d4d, 'maîtriser les compétences + cycle naturel', 3, '-1', '0000-00-00'),
(176, 0x776f7765, 'manger / boire', 3, '-1', '0000-00-00'),
(177, 0x73696d697475, 'mécanique', 3, '-1', '0000-00-00'),
(178, 0x74656d75776165, 'mise en scène / réalisation', 3, 'wae', '0000-00-00'),
(179, 0x7575666f, 'musique', 3, '-1', '0000-00-00'),
(180, 0x65656d6f7379, 'orientation professionnelle', 3, '-1', '0000-00-00'),
(181, 0x69696c6f7379, 'physique', 3, '-1', '0000-00-00'),
(182, 0x61616d6f7379, 'psychologie', 3, '-1', '0000-00-00'),
(183, 0x776579, 'reconstruire les savoirs', 3, '-1', '0000-00-00'),
(184, 0x7070666f7379, 'sciences de la santé', 3, '-1', '0000-00-00'),
(185, 0x6a6a6c6f7379, 'sciences de la terre', 3, '-1', '0000-00-00'),
(186, 0x6565626f7379, 'sciences de l''éducation et de la formation', 3, '-1', '0000-00-00'),
(187, 0x6f6f6e6f7379, 'sciences juridiques', 3, '-1', '0000-00-00'),
(188, 0x6f6f6e6f73792066616b61207761776f, 'sciences juridiques + accueil / hospitalité + dormir', 3, '-1', '0000-00-00'),
(189, 0x6161736f7379, 'sociologie', 3, '-1', '0000-00-00'),
(190, 0x64656b69776165, 'sport d''équipe', 3, '-1', '0000-00-00'),
(191, 0x6868736f7379, 'théologie', 3, '-1', '0000-00-00'),
(192, 0x62616c61666f, 'transporteur', 3, '-1', '0000-00-00'),
(193, 0x6b616461, 'vente', 3, '-1', '0000-00-00'),
(194, 0x6c6f746f, 'voyage', 3, '-1', '0000-00-00'),
(196, 0x732e792e2d77612e652e2d7c646f65652d52, 'Choix d un savoir pour un membre', 1, '198', '2008-01-13'),
(197, 0x732e792e2d77612e652e2d7c646f65652d52, 'TEST', 1, '198', '2008-01-12'),
(287, 0x75756c6f7379, 'semantique', 1, '-1', '2008-04-08'),
(288, 0x626574757761652c, 'programmation', 1, '-1', '2008-04-08'),
(289, 0x73656e75, 'semantique', 1, '-1', '2008-04-08'),
(290, 0x75756c6f, 'semantique', 1, '-1', '2008-04-08'),
(291, 0x69656d6c576f7264, 'ieml', 1, '-1', '2008-04-08'),
(292, 0x776578, 'system', 1, '-1', '2008-04-09');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `ieml_onto_flux`
-- 


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
(287, 11, '2008-04-08'),
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Contenu de la table `ieml_uti`
-- 

INSERT INTO `ieml_uti` (`uti_id`, `uti_login`, `maj`) VALUES 
(1, 'amelmaster', '2008-04-08 17:47:31'),
(2, 'samszo', '2008-04-09 14:01:38');

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
(2, 13),
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
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34);
