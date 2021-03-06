-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成時間: 2013 年 8 月 30 日 16:28
-- サーバのバージョン: 5.5.16
-- PHP のバージョン: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `pcms`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `TBCustomer`
--

CREATE TABLE IF NOT EXISTS `TBCustomer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

--
-- テーブルのデータをダンプしています `TBCustomer`
--

INSERT INTO `TBCustomer` (`id`, `Name`, `DeleteFlag`) VALUES
(46, '顧客1', 0),
(47, '顧客2', 1),
(48, '顧客3', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `TBDepartment`
--

CREATE TABLE IF NOT EXISTS `TBDepartment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SortNo` int(11) NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=81 ;

--
-- テーブルのデータをダンプしています `TBDepartment`
--

INSERT INTO `TBDepartment` (`id`, `Name`, `SortNo`, `DeleteFlag`) VALUES
(76, '部署01', 1, 0),
(77, '部署02', 2, 1),
(78, '部署03', 3, 0),
(79, '部署04', 4, 0),
(80, '部署05', 5, 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `TBProductionCost`
--

CREATE TABLE IF NOT EXISTS `TBProductionCost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectCostMasterId` int(11) NOT NULL,
  `SystemUserId` int(11) NOT NULL,
  `WorkDate` date NOT NULL,
  `Cost` int(11) NOT NULL,
  `Note` longtext COLLATE utf8_unicode_ci,
  `DeleteFlag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D08CE457D1971DCD` (`ProjectCostMasterId`),
  KEY `IDX_D08CE457A5DCE404` (`SystemUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- テーブルのデータをダンプしています `TBProductionCost`
--

INSERT INTO `TBProductionCost` (`id`, `ProjectCostMasterId`, `SystemUserId`, `WorkDate`, `Cost`, `Note`, `DeleteFlag`) VALUES
(25, 71, 151, '2013-08-01', 480, '備考1', 0),
(26, 71, 151, '2013-08-02', 120, '備考2', 0),
(27, 73, 151, '2013-08-02', 300, '備考3', 0),
(28, 73, 152, '2013-08-02', 480, '備考4', 0);

-- --------------------------------------------------------

--
-- テーブルの構造 `TBProjectCostHierarchyMaster`
--

CREATE TABLE IF NOT EXISTS `TBProjectCostHierarchyMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TBProjectMasterId` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SortNo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BBCFA335AF18B565` (`TBProjectMasterId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=284 ;

--
-- テーブルのデータをダンプしています `TBProjectCostHierarchyMaster`
--

INSERT INTO `TBProjectCostHierarchyMaster` (`id`, `TBProjectMasterId`, `Name`, `Path`, `SortNo`) VALUES
(35, 1, 'root', '\\', 0),
(36, 1, '製造', '\\35\\', 2),
(37, 1, '管理側', '\\35\\36\\', 2),
(38, 1, '一般側', '\\35\\36\\', 1),
(39, 17, 'root', '\\', 0),
(41, 19, 'root', '\\', 0),
(42, 19, 'グループ１', '\\41\\', 1),
(43, 19, 'グループ２', '\\41\\42\\', 1),
(44, 19, 'グループ３', '\\41\\42\\43\\', 1),
(45, 19, 'グループ５', '\\41\\42\\43\\44\\', 1),
(46, 19, 'グループ４', '\\41\\42\\43\\', 2),
(47, 19, 'グループ６', '\\41\\42\\43\\46\\', 1),
(48, 19, 'グループ７', '\\41\\42\\43\\46\\47\\', 1),
(49, 19, 'グループ８', '\\41\\42\\43\\46\\47\\', 2),
(50, 20, 'root', '\\', 0),
(51, 20, 'グループ１', '\\50\\', 1),
(52, 20, 'グループ２', '\\50\\51\\', 1),
(53, 20, 'グループ３', '\\50\\51\\52\\', 1),
(54, 20, 'グループ５', '\\50\\51\\52\\53\\', 1),
(55, 20, 'グループ４', '\\50\\51\\52\\', 2),
(56, 20, 'グループ６', '\\50\\51\\52\\55\\', 1),
(57, 20, 'グループ７', '\\50\\51\\52\\55\\56\\', 1),
(58, 20, 'グループ８', '\\50\\51\\52\\55\\56\\', 2),
(59, 21, 'root', '\\', 0),
(60, 21, 'グループ１', '\\59\\', 1),
(61, 21, 'グループ２', '\\59\\60\\', 1),
(62, 21, 'グループ３', '\\59\\60\\61\\', 1),
(63, 21, 'グループ５', '\\59\\60\\61\\62\\', 1),
(64, 21, 'グループ４', '\\59\\60\\61\\', 2),
(65, 21, 'グループ６', '\\59\\60\\61\\64\\', 1),
(66, 21, 'グループ７', '\\59\\60\\61\\64\\65\\', 1),
(67, 21, 'グループ８', '\\59\\60\\61\\64\\65\\', 2),
(68, 22, 'root', '\\', 0),
(69, 22, 'グループ１', '\\68\\', 1),
(70, 22, 'グループ２', '\\68\\69\\', 1),
(71, 22, 'グループ３', '\\68\\69\\70\\', 1),
(72, 22, 'グループ５', '\\68\\69\\70\\71\\', 1),
(73, 22, 'グループ４', '\\68\\69\\70\\', 2),
(74, 22, 'グループ６', '\\68\\69\\70\\73\\', 2),
(75, 22, 'グループ７', '\\68\\69\\70\\73\\74\\', 1),
(76, 22, 'グループ８', '\\68\\69\\70\\73\\74\\', 2),
(77, 23, 'root', '\\', 0),
(78, 23, 'グループ１', '\\77\\', 1),
(79, 23, 'グループ２', '\\77\\78\\', 1),
(80, 23, 'グループ３', '\\77\\78\\79\\', 1),
(81, 23, 'グループ５', '\\77\\78\\79\\80\\', 1),
(82, 23, 'グループ４', '\\77\\78\\79\\', 2),
(83, 23, 'グループ６', '\\77\\78\\79\\82\\', 2),
(84, 23, 'グループ７', '\\77\\78\\79\\82\\83\\', 1),
(85, 23, 'グループ８', '\\77\\78\\79\\82\\83\\', 2),
(86, 24, 'root', '\\', 0),
(87, 24, 'グループ１', '\\86\\', 1),
(88, 24, 'グループ２', '\\86\\87\\', 1),
(89, 24, 'グループ３', '\\86\\87\\88\\', 1),
(90, 24, 'グループ５', '\\86\\87\\88\\89\\', 1),
(91, 24, 'グループ４', '\\86\\87\\88\\', 2),
(92, 24, 'グループ６', '\\86\\87\\88\\91\\', 2),
(93, 24, 'グループ７', '\\86\\87\\88\\91\\92\\', 1),
(94, 24, 'グループ８', '\\86\\87\\88\\91\\92\\', 2),
(95, 25, 'root', '\\', 0),
(96, 25, 'グループ１', '\\95\\', 1),
(97, 25, 'グループ２', '\\95\\96\\', 1),
(98, 25, 'グループ３', '\\95\\96\\97\\', 1),
(99, 25, 'グループ５', '\\95\\96\\97\\98\\', 1),
(100, 25, 'グループ４', '\\95\\96\\97\\', 2),
(101, 25, 'グループ６', '\\95\\96\\97\\100\\', 2),
(102, 25, 'グループ７', '\\95\\96\\97\\100\\101\\', 1),
(103, 25, 'グループ８', '\\95\\96\\97\\100\\101\\', 2),
(104, 26, 'root', '\\', 0),
(105, 26, 'グループ１', '\\104\\', 1),
(106, 26, 'グループ２', '\\104\\105\\', 1),
(107, 26, 'グループ３', '\\104\\105\\106\\', 1),
(108, 26, 'グループ５', '\\104\\105\\106\\107\\', 1),
(109, 26, 'グループ４', '\\104\\105\\106\\', 2),
(110, 26, 'グループ６', '\\104\\105\\106\\109\\', 2),
(111, 26, 'グループ７', '\\104\\105\\106\\109\\110\\', 1),
(112, 26, 'グループ８', '\\104\\105\\106\\109\\110\\', 2),
(113, 27, 'root', '\\', 0),
(114, 27, 'グループ１', '\\113\\', 1),
(115, 27, 'グループ２', '\\113\\114\\', 1),
(116, 27, 'グループ３', '\\113\\114\\115\\', 1),
(117, 27, 'グループ５', '\\113\\114\\115\\116\\', 1),
(118, 27, 'グループ４', '\\113\\114\\115\\', 2),
(119, 27, 'グループ６', '\\113\\114\\115\\118\\', 2),
(120, 27, 'グループ７', '\\113\\114\\115\\118\\119\\', 1),
(121, 27, 'グループ８', '\\113\\114\\115\\118\\119\\', 2),
(122, 28, 'root', '\\', 0),
(123, 28, 'グループ１', '\\122\\', 1),
(124, 28, 'グループ２', '\\122\\123\\', 1),
(125, 28, 'グループ３', '\\122\\123\\124\\', 1),
(126, 28, 'グループ５', '\\122\\123\\124\\125\\', 1),
(127, 28, 'グループ４', '\\122\\123\\124\\', 2),
(128, 28, 'グループ６', '\\122\\123\\124\\127\\', 2),
(129, 28, 'グループ７', '\\122\\123\\124\\127\\128\\', 1),
(130, 28, 'グループ８', '\\122\\123\\124\\127\\128\\', 2),
(131, 29, 'root', '\\', 0),
(132, 29, 'グループ１', '\\131\\', 1),
(133, 29, 'グループ２', '\\131\\132\\', 1),
(134, 29, 'グループ３', '\\131\\132\\133\\', 1),
(135, 29, 'グループ５', '\\131\\132\\133\\134\\', 1),
(136, 29, 'グループ４', '\\131\\132\\133\\', 2),
(137, 29, 'グループ６', '\\131\\132\\133\\136\\', 2),
(138, 29, 'グループ７', '\\131\\132\\133\\136\\137\\', 1),
(139, 29, 'グループ８', '\\131\\132\\133\\136\\137\\', 2),
(140, 30, 'root', '\\', 0),
(141, 30, 'グループ１', '\\140\\', 1),
(142, 30, 'グループ２', '\\140\\141\\', 1),
(143, 30, 'グループ３', '\\140\\141\\142\\', 1),
(144, 30, 'グループ５', '\\140\\141\\142\\143\\', 1),
(145, 30, 'グループ４', '\\140\\141\\142\\', 2),
(146, 30, 'グループ６', '\\140\\141\\142\\145\\', 2),
(147, 30, 'グループ７', '\\140\\141\\142\\145\\146\\', 1),
(148, 30, 'グループ８', '\\140\\141\\142\\145\\146\\', 2),
(149, 31, 'root', '\\', 0),
(150, 31, 'グループ１', '\\149\\', 1),
(151, 31, 'グループ２', '\\149\\150\\', 1),
(152, 31, 'グループ３', '\\149\\150\\151\\', 1),
(153, 31, 'グループ５', '\\149\\150\\151\\152\\', 1),
(154, 31, 'グループ４', '\\149\\150\\151\\', 2),
(155, 31, 'グループ６', '\\149\\150\\151\\154\\', 2),
(156, 31, 'グループ７', '\\149\\150\\151\\154\\155\\', 1),
(157, 31, 'グループ８', '\\149\\150\\151\\154\\155\\', 2),
(158, 32, 'root', '\\', 0),
(159, 32, 'グループ１', '\\158\\', 1),
(160, 32, 'グループ２', '\\158\\159\\', 1),
(161, 32, 'グループ３', '\\158\\159\\160\\', 1),
(162, 32, 'グループ５', '\\158\\159\\160\\161\\', 1),
(163, 32, 'グループ４', '\\158\\159\\160\\', 2),
(164, 32, 'グループ６', '\\158\\159\\160\\163\\', 2),
(165, 32, 'グループ７', '\\158\\159\\160\\163\\164\\', 1),
(166, 32, 'グループ８', '\\158\\159\\160\\163\\164\\', 2),
(167, 33, 'root', '\\', 0),
(168, 33, 'グループ１', '\\167\\', 1),
(169, 33, 'グループ２', '\\167\\168\\', 1),
(170, 33, 'グループ３', '\\167\\168\\169\\', 1),
(171, 33, 'グループ５', '\\167\\168\\169\\170\\', 1),
(172, 33, 'グループ４', '\\167\\168\\169\\', 2),
(173, 33, 'グループ６', '\\167\\168\\169\\172\\', 2),
(174, 33, 'グループ７', '\\167\\168\\169\\172\\173\\', 1),
(175, 33, 'グループ８', '\\167\\168\\169\\172\\173\\', 2),
(176, 34, 'root', '\\', 0),
(177, 34, 'グループ１', '\\176\\', 1),
(178, 34, 'グループ２', '\\176\\177\\', 1),
(179, 34, 'グループ３', '\\176\\177\\178\\', 1),
(180, 34, 'グループ５', '\\176\\177\\178\\179\\', 1),
(181, 34, 'グループ４', '\\176\\177\\178\\', 2),
(182, 34, 'グループ６', '\\176\\177\\178\\181\\', 2),
(183, 34, 'グループ７', '\\176\\177\\178\\181\\182\\', 1),
(184, 34, 'グループ８', '\\176\\177\\178\\181\\182\\', 2),
(185, 35, 'root', '\\', 0),
(186, 35, 'グループ１', '\\185\\', 1),
(187, 35, 'グループ２', '\\185\\186\\', 1),
(188, 35, 'グループ３', '\\185\\186\\187\\', 1),
(189, 35, 'グループ５', '\\185\\186\\187\\188\\', 1),
(190, 35, 'グループ４', '\\185\\186\\187\\', 2),
(191, 35, 'グループ６', '\\185\\186\\187\\190\\', 2),
(192, 35, 'グループ７', '\\185\\186\\187\\190\\191\\', 1),
(193, 35, 'グループ８', '\\185\\186\\187\\190\\191\\', 2),
(194, 36, 'root', '\\', 0),
(195, 36, 'グループ１', '\\194\\', 1),
(196, 36, 'グループ２', '\\194\\195\\', 1),
(197, 36, 'グループ３', '\\194\\195\\196\\', 1),
(198, 36, 'グループ５', '\\194\\195\\196\\197\\', 1),
(199, 36, 'グループ４', '\\194\\195\\196\\', 2),
(200, 36, 'グループ６', '\\194\\195\\196\\199\\', 2),
(201, 36, 'グループ７', '\\194\\195\\196\\199\\200\\', 1),
(202, 36, 'グループ８', '\\194\\195\\196\\199\\200\\', 2),
(203, 37, 'root', '\\', 0),
(204, 37, 'グループ１', '\\203\\', 1),
(205, 37, 'グループ２', '\\203\\204\\', 1),
(206, 37, 'グループ３', '\\203\\204\\205\\', 1),
(207, 37, 'グループ５', '\\203\\204\\205\\206\\', 1),
(208, 37, 'グループ４', '\\203\\204\\205\\', 2),
(209, 37, 'グループ６', '\\203\\204\\205\\208\\', 2),
(210, 37, 'グループ７', '\\203\\204\\205\\208\\209\\', 1),
(211, 37, 'グループ８', '\\203\\204\\205\\208\\209\\', 2),
(212, 38, 'root', '\\', 0),
(213, 38, 'グループ１', '\\212\\', 1),
(214, 38, 'グループ２', '\\212\\213\\', 1),
(215, 38, 'グループ３', '\\212\\213\\214\\', 1),
(216, 38, 'グループ５', '\\212\\213\\214\\215\\', 1),
(217, 38, 'グループ４', '\\212\\213\\214\\', 2),
(218, 38, 'グループ６', '\\212\\213\\214\\217\\', 2),
(219, 38, 'グループ７', '\\212\\213\\214\\217\\218\\', 1),
(220, 38, 'グループ８', '\\212\\213\\214\\217\\218\\', 2),
(221, 39, 'root', '\\', 0),
(222, 39, 'グループ１', '\\221\\', 1),
(223, 39, 'グループ２', '\\221\\222\\', 1),
(224, 39, 'グループ３', '\\221\\222\\223\\', 1),
(225, 39, 'グループ５', '\\221\\222\\223\\224\\', 1),
(226, 39, 'グループ４', '\\221\\222\\223\\', 2),
(227, 39, 'グループ６', '\\221\\222\\223\\226\\', 2),
(228, 39, 'グループ７', '\\221\\222\\223\\226\\227\\', 1),
(229, 39, 'グループ８', '\\221\\222\\223\\226\\227\\', 2),
(230, 40, 'root', '\\', 0),
(231, 40, 'グループ１', '\\230\\', 1),
(232, 40, 'グループ２', '\\230\\231\\', 1),
(233, 40, 'グループ３', '\\230\\231\\232\\', 1),
(234, 40, 'グループ５', '\\230\\231\\232\\233\\', 1),
(235, 40, 'グループ４', '\\230\\231\\232\\', 2),
(236, 40, 'グループ６', '\\230\\231\\232\\235\\', 2),
(237, 40, 'グループ７', '\\230\\231\\232\\235\\236\\', 1),
(238, 40, 'グループ８', '\\230\\231\\232\\235\\236\\', 2),
(239, 41, 'root', '\\', 0),
(240, 41, 'グループ１', '\\239\\', 1),
(241, 41, 'グループ２', '\\239\\240\\', 1),
(242, 41, 'グループ３', '\\239\\240\\241\\', 1),
(243, 41, 'グループ５', '\\239\\240\\241\\242\\', 1),
(244, 41, 'グループ４', '\\239\\240\\241\\', 2),
(245, 41, 'グループ６', '\\239\\240\\241\\244\\', 2),
(246, 41, 'グループ７', '\\239\\240\\241\\244\\245\\', 1),
(247, 41, 'グループ８', '\\239\\240\\241\\244\\245\\', 2),
(248, 42, 'root', '\\', 0),
(249, 42, 'グループ１', '\\248\\', 1),
(250, 42, 'グループ２', '\\248\\249\\', 1),
(251, 42, 'グループ３', '\\248\\249\\250\\', 1),
(252, 42, 'グループ５', '\\248\\249\\250\\251\\', 1),
(253, 42, 'グループ４', '\\248\\249\\250\\', 2),
(254, 42, 'グループ６', '\\248\\249\\250\\253\\', 2),
(255, 42, 'グループ７', '\\248\\249\\250\\253\\254\\', 1),
(256, 42, 'グループ８', '\\248\\249\\250\\253\\254\\', 2),
(257, 43, 'root', '\\', 0),
(258, 43, 'グループ１', '\\257\\', 1),
(259, 43, 'グループ２', '\\257\\258\\', 1),
(260, 43, 'グループ３', '\\257\\258\\259\\', 1),
(261, 43, 'グループ５', '\\257\\258\\259\\260\\', 1),
(262, 43, 'グループ４', '\\257\\258\\259\\', 2),
(263, 43, 'グループ６', '\\257\\258\\259\\262\\', 2),
(264, 43, 'グループ７', '\\257\\258\\259\\262\\263\\', 1),
(265, 43, 'グループ８', '\\257\\258\\259\\262\\263\\', 2),
(266, 44, 'root', '\\', 0),
(267, 44, 'グループ１', '\\266\\', 1),
(268, 44, 'グループ２', '\\266\\267\\', 1),
(269, 44, 'グループ３', '\\266\\267\\268\\', 1),
(270, 44, 'グループ５', '\\266\\267\\268\\269\\', 1),
(271, 44, 'グループ４', '\\266\\267\\268\\', 2),
(272, 44, 'グループ６', '\\266\\267\\268\\271\\', 2),
(273, 44, 'グループ７', '\\266\\267\\268\\271\\272\\', 1),
(274, 44, 'グループ８', '\\266\\267\\268\\271\\272\\', 2),
(275, 45, 'root', '\\', 0),
(276, 45, 'グループ１', '\\275\\', 1),
(277, 45, 'グループ２', '\\275\\276\\', 1),
(278, 45, 'グループ３', '\\275\\276\\277\\', 1),
(279, 45, 'グループ５', '\\275\\276\\277\\278\\', 1),
(280, 45, 'グループ４', '\\275\\276\\277\\', 2),
(281, 45, 'グループ６', '\\275\\276\\277\\280\\', 2),
(282, 45, 'グループ７', '\\275\\276\\277\\280\\281\\', 1),
(283, 45, 'グループ８', '\\275\\276\\277\\280\\281\\', 2);

-- --------------------------------------------------------

--
-- テーブルの構造 `TBProjectCostMaster`
--

CREATE TABLE IF NOT EXISTS `TBProjectCostMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectMasterId` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Cost` int(11) DEFAULT NULL,
  `SortNo` int(11) DEFAULT NULL,
  `DeleteFlag` tinyint(1) NOT NULL,
  `HierarchyPath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TBProjectCostHierarchyMasterId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DDDDD06921E54FE0` (`ProjectMasterId`),
  KEY `IDX_DDDDD069C753C8D9` (`TBProjectCostHierarchyMasterId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=213 ;

--
-- テーブルのデータをダンプしています `TBProjectCostMaster`
--

INSERT INTO `TBProjectCostMaster` (`id`, `ProjectMasterId`, `Name`, `Cost`, `SortNo`, `DeleteFlag`, `HierarchyPath`, `TBProjectCostHierarchyMasterId`) VALUES
(71, 1, '要件定義', 1440, 4, 0, '', 35),
(72, 1, '設計', 2400, 3, 0, '', 35),
(73, 1, 'ユーザー管理', 2880, 1, 0, '', 37),
(74, 1, '顧客管理', 1920, 2, 0, '', 37),
(75, 1, 'ログイン', 960, 2, 0, '', 38),
(76, 1, '申請管理', 4320, 1, 0, '', 38),
(77, 1, '納品', 480, 1, 0, '', 35),
(78, 19, 'コスト３', 3, 0, 0, NULL, 45),
(79, 19, 'コスト４', 4, 1, 0, NULL, 45),
(80, 19, 'コスト２', 2, 0, 0, NULL, 46),
(81, 19, 'コスト５', 5, 0, 0, NULL, 48),
(82, 19, 'コスト１', 1, 1, 0, NULL, 42),
(83, 20, 'コスト３', 3, 0, 0, NULL, 54),
(84, 20, 'コスト４', 4, 1, 0, NULL, 54),
(85, 20, 'コスト２', 2, 0, 0, NULL, 55),
(86, 20, 'コスト５', 5, 0, 0, NULL, 57),
(87, 20, 'コスト１', 1, 1, 0, NULL, 51),
(88, 21, 'コスト３', 3, 1, 0, NULL, 63),
(89, 21, 'コスト４', 4, 2, 0, NULL, 63),
(90, 21, 'コスト２', 2, 2, 0, NULL, 64),
(91, 21, 'コスト５', 5, 1, 0, NULL, 66),
(92, 21, 'コスト１', 1, 2, 0, NULL, 60),
(93, 22, 'コスト３', 3, 1, 0, NULL, 72),
(94, 22, 'コスト４', 4, 2, 0, NULL, 72),
(95, 22, 'コスト２', 2, 1, 0, NULL, 73),
(96, 22, 'コスト５', 5, 1, 0, NULL, 75),
(97, 22, 'コスト１', 1, 2, 0, NULL, 69),
(98, 23, 'コスト３', 3, 1, 0, NULL, 81),
(99, 23, 'コスト４', 4, 2, 0, NULL, 81),
(100, 23, 'コスト２', 2, 1, 0, NULL, 82),
(101, 23, 'コスト５', 5, 1, 0, NULL, 84),
(102, 23, 'コスト１', 1, 2, 0, NULL, 78),
(103, 24, 'コスト３', 3, 1, 0, NULL, 90),
(104, 24, 'コスト４', 4, 2, 0, NULL, 90),
(105, 24, 'コスト２', 2, 1, 0, NULL, 91),
(106, 24, 'コスト５', 5, 1, 0, NULL, 93),
(107, 24, 'コスト１', 1, 2, 0, NULL, 87),
(108, 25, 'コスト３', 3, 1, 0, NULL, 99),
(109, 25, 'コスト４', 4, 2, 0, NULL, 99),
(110, 25, 'コスト２', 2, 1, 0, NULL, 100),
(111, 25, 'コスト５', 5, 1, 0, NULL, 102),
(112, 25, 'コスト１', 1, 2, 0, NULL, 96),
(113, 26, 'コスト３', 3, 1, 0, NULL, 108),
(114, 26, 'コスト４', 4, 2, 0, NULL, 108),
(115, 26, 'コスト２', 2, 1, 0, NULL, 109),
(116, 26, 'コスト５', 5, 1, 0, NULL, 111),
(117, 26, 'コスト１', 1, 2, 0, NULL, 105),
(118, 27, 'コスト３', 3, 1, 0, NULL, 117),
(119, 27, 'コスト４', 4, 2, 0, NULL, 117),
(120, 27, 'コスト２', 2, 1, 0, NULL, 118),
(121, 27, 'コスト５', 5, 1, 0, NULL, 120),
(122, 27, 'コスト１', 1, 2, 0, NULL, 114),
(123, 28, 'コスト３', 3, 1, 0, NULL, 126),
(124, 28, 'コスト４', 4, 2, 0, NULL, 126),
(125, 28, 'コスト２', 2, 1, 0, NULL, 127),
(126, 28, 'コスト５', 5, 1, 0, NULL, 129),
(127, 28, 'コスト１', 1, 2, 0, NULL, 123),
(128, 29, 'コスト３', 3, 1, 0, NULL, 135),
(129, 29, 'コスト４', 4, 2, 0, NULL, 135),
(130, 29, 'コスト２', 2, 1, 0, NULL, 136),
(131, 29, 'コスト５', 5, 1, 0, NULL, 138),
(132, 29, 'コスト１', 1, 2, 0, NULL, 132),
(133, 30, 'コスト３', 3, 1, 0, NULL, 144),
(134, 30, 'コスト４', 4, 2, 0, NULL, 144),
(135, 30, 'コスト２', 2, 1, 0, NULL, 145),
(136, 30, 'コスト５', 5, 1, 0, NULL, 147),
(137, 30, 'コスト１', 1, 2, 0, NULL, 141),
(138, 31, 'コスト３', 3, 1, 0, NULL, 153),
(139, 31, 'コスト４', 4, 2, 0, NULL, 153),
(140, 31, 'コスト２', 2, 1, 0, NULL, 154),
(141, 31, 'コスト５', 5, 1, 0, NULL, 156),
(142, 31, 'コスト１', 1, 2, 0, NULL, 150),
(143, 32, 'コスト３', 3, 1, 0, NULL, 162),
(144, 32, 'コスト４', 4, 2, 0, NULL, 162),
(145, 32, 'コスト２', 2, 1, 0, NULL, 163),
(146, 32, 'コスト５', 5, 1, 0, NULL, 165),
(147, 32, 'コスト１', 1, 2, 0, NULL, 159),
(148, 33, 'コスト３', 3, 1, 0, NULL, 171),
(149, 33, 'コスト４', 4, 2, 0, NULL, 171),
(150, 33, 'コスト２', 2, 1, 0, NULL, 172),
(151, 33, 'コスト５', 5, 1, 0, NULL, 174),
(152, 33, 'コスト１', 1, 2, 0, NULL, 168),
(153, 34, 'コスト３', 3, 1, 0, NULL, 180),
(154, 34, 'コスト４', 4, 2, 0, NULL, 180),
(155, 34, 'コスト２', 2, 1, 0, NULL, 181),
(156, 34, 'コスト５', 5, 1, 0, NULL, 183),
(157, 34, 'コスト１', 1, 2, 0, NULL, 177),
(158, 35, 'コスト３', 3, 1, 0, NULL, 189),
(159, 35, 'コスト４', 4, 2, 0, NULL, 189),
(160, 35, 'コスト２', 2, 1, 0, NULL, 190),
(161, 35, 'コスト５', 5, 1, 0, NULL, 192),
(162, 35, 'コスト１', 1, 2, 0, NULL, 186),
(163, 36, 'コスト３', 3, 1, 0, NULL, 198),
(164, 36, 'コスト４', 4, 2, 0, NULL, 198),
(165, 36, 'コスト２', 2, 1, 0, NULL, 199),
(166, 36, 'コスト５', 5, 1, 0, NULL, 201),
(167, 36, 'コスト１', 1, 2, 0, NULL, 195),
(168, 37, 'コスト３', 3, 1, 0, NULL, 207),
(169, 37, 'コスト４', 4, 2, 0, NULL, 207),
(170, 37, 'コスト２', 2, 1, 0, NULL, 208),
(171, 37, 'コスト５', 5, 1, 0, NULL, 210),
(172, 37, 'コスト１', 1, 2, 0, NULL, 204),
(173, 38, 'コスト３', 3, 1, 0, NULL, 216),
(174, 38, 'コスト４', 4, 2, 0, NULL, 216),
(175, 38, 'コスト２', 2, 1, 0, NULL, 217),
(176, 38, 'コスト５', 5, 1, 0, NULL, 219),
(177, 38, 'コスト１', 1, 2, 0, NULL, 213),
(178, 39, 'コスト３', 3, 1, 0, NULL, 225),
(179, 39, 'コスト４', 4, 2, 0, NULL, 225),
(180, 39, 'コスト２', 2, 1, 0, NULL, 226),
(181, 39, 'コスト５', 5, 1, 0, NULL, 228),
(182, 39, 'コスト１', 1, 2, 0, NULL, 222),
(183, 40, 'コスト３', 3, 1, 0, NULL, 234),
(184, 40, 'コスト４', 4, 2, 0, NULL, 234),
(185, 40, 'コスト２', 2, 1, 0, NULL, 235),
(186, 40, 'コスト５', 5, 1, 0, NULL, 237),
(187, 40, 'コスト１', 1, 2, 0, NULL, 231),
(188, 41, 'コスト３', 3, 1, 0, NULL, 243),
(189, 41, 'コスト４', 4, 2, 0, NULL, 243),
(190, 41, 'コスト２', 2, 1, 0, NULL, 244),
(191, 41, 'コスト５', 5, 1, 0, NULL, 246),
(192, 41, 'コスト１', 1, 2, 0, NULL, 240),
(193, 42, 'コスト３', 3, 1, 0, NULL, 252),
(194, 42, 'コスト４', 4, 2, 0, NULL, 252),
(195, 42, 'コスト２', 2, 1, 0, NULL, 253),
(196, 42, 'コスト５', 5, 1, 0, NULL, 255),
(197, 42, 'コスト１', 1, 2, 0, NULL, 249),
(198, 43, 'コスト３', 3, 1, 0, NULL, 261),
(199, 43, 'コスト４', 4, 2, 0, NULL, 261),
(200, 43, 'コスト２', 2, 1, 0, NULL, 262),
(201, 43, 'コスト５', 5, 1, 0, NULL, 264),
(202, 43, 'コスト１', 1, 2, 0, NULL, 258),
(203, 44, 'コスト３', 3, 1, 0, NULL, 270),
(204, 44, 'コスト４', 4, 2, 0, NULL, 270),
(205, 44, 'コスト２', 2, 1, 0, NULL, 271),
(206, 44, 'コスト５', 5, 1, 0, NULL, 273),
(207, 44, 'コスト１', 1, 2, 0, NULL, 267),
(208, 45, 'コスト３', 3, 1, 0, NULL, 279),
(209, 45, 'コスト４', 4, 2, 0, NULL, 279),
(210, 45, 'コスト２', 2, 1, 0, NULL, 280),
(211, 45, 'コスト５', 5, 1, 0, NULL, 282),
(212, 45, 'コスト１', 1, 2, 0, NULL, 276);

-- --------------------------------------------------------

--
-- テーブルの構造 `TBProjectMaster`
--

CREATE TABLE IF NOT EXISTS `TBProjectMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL,
  `Explanation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CustomerId` int(11) NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `ManagerId` int(11) DEFAULT NULL,
  `EstimateFilePath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ScheduleFilePath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C23FF7FBE22D475` (`CustomerId`),
  KEY `IDX_7C23FF7FD64FFAC3` (`ManagerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

--
-- テーブルのデータをダンプしています `TBProjectMaster`
--

INSERT INTO `TBProjectMaster` (`id`, `Name`, `Status`, `Explanation`, `CustomerId`, `DeleteFlag`, `PeriodStart`, `PeriodEnd`, `ManagerId`, `EstimateFilePath`, `ScheduleFilePath`) VALUES
(1, 'edit', 1, '案件3説明', 46, 0, '2013-08-01', '2013-08-05', 151, 'estimate3', 'schedule3'),
(17, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(19, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(20, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(21, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(22, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(23, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(24, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(25, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(26, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(27, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(28, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(29, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(30, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(31, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(32, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(33, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(34, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(35, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(36, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(37, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(38, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(39, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(40, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(41, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(42, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(43, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(44, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス'),
(45, '新規登録テスト１', 1, '新規登録テスト１説明\n新規登録テスト１説明', 46, 0, '2013-08-01', '2013-08-07', 151, '見積ファイルパス', 'スケジュールファイルパス');

-- --------------------------------------------------------

--
-- テーブルの構造 `TBProjectUser`
--

CREATE TABLE IF NOT EXISTS `TBProjectUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SystemUserId` int(11) NOT NULL,
  `ProjectMasterId` int(11) NOT NULL,
  `RoleNo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5B5B65FA5DCE404` (`SystemUserId`),
  KEY `IDX_5B5B65F21E54FE0` (`ProjectMasterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `TBSystemUser`
--

CREATE TABLE IF NOT EXISTS `TBSystemUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Salt` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `SystemRoleId` int(11) DEFAULT NULL,
  `DisplayName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DisplayNameKana` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `NickName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `MailAddress` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `DepartmentId` int(11) DEFAULT NULL,
  `LastLoginDatetime` datetime DEFAULT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB29A6C224EDF738` (`DepartmentId`),
  KEY `IX_TBSystemUser` (`Username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=161 ;

--
-- テーブルのデータをダンプしています `TBSystemUser`
--

INSERT INTO `TBSystemUser` (`id`, `Username`, `Salt`, `Password`, `Active`, `SystemRoleId`, `DisplayName`, `DisplayNameKana`, `NickName`, `MailAddress`, `DepartmentId`, `LastLoginDatetime`, `DeleteFlag`) VALUES
(151, 'test001', NULL, 'a', 1, 1, 'てすと001', 'テスト001', 't001', 'test001@test.com', 76, '2012-07-01 00:00:00', 0),
(152, 'test002', NULL, 'a', 1, 1, 'てすと002', 'テスト002', 't002', 'test002@test.com', 76, '2012-07-02 00:00:00', 1),
(153, 'test003', NULL, 'a', 0, 1, 'てすと003', 'テスト003', 't003', 'test003@test.com', 76, '2012-07-03 00:00:00', 0),
(154, 'test004', NULL, 'a', 1, 1, 'てすと004', 'テスト004', 't004', 'test004@test.com', 76, '2013-07-04 00:00:00', 0),
(155, 'test005', NULL, 'a', 1, 1, 'てすと005', 'テスト005', 't005', 'test005@test.com', 76, '2013-07-05 00:00:00', 0),
(156, 'test006', NULL, 'a', 1, 1, 'てすと006', 'テスト006', 't006', 'test006@test.com', 76, '2013-07-06 00:00:00', 0),
(157, 'test007', NULL, 'a', 1, 1, 'てすと007', 'テスト007', 't007', 'test007@test.com', 76, '2013-07-07 00:00:00', 0),
(158, 'test008', NULL, 'a', 1, 1, 'てすと008', 'テスト008', 't008', 'test008@test.com', 76, '2013-07-08 00:00:00', 0),
(159, 'test009', NULL, 'a', 1, 1, 'てすと009', 'テスト009', 't009', 'test009@test.com', 76, '2013-07-09 00:00:00', 0),
(160, 'test010', NULL, 'a', 1, 1, 'てすと010', 'テスト010', 't010', 'test010@test.com', 76, '2013-07-10 00:00:00', 0);

-- --------------------------------------------------------

--
-- ビュー用の代替構造 `vprojectuser`
--
CREATE TABLE IF NOT EXISTS `vprojectuser` (
`id` int(11)
,`SystemUserId` int(11)
,`RoleNo` int(11)
);
-- --------------------------------------------------------

--
-- ビュー用の代替構造 `vprojectview`
--
CREATE TABLE IF NOT EXISTS `vprojectview` (
`id` int(11)
,`Name` varchar(255)
,`Status` int(11)
,`CustomerId` int(11)
,`PeriodStart` date
,`PeriodEnd` date
,`ManagerId` int(11)
,`ProjectTotalCost` decimal(32,0)
,`ProductionTotalCost` decimal(32,0)
);
-- --------------------------------------------------------

--
-- ビュー用の構造 `vprojectuser`
--
DROP TABLE IF EXISTS `vprojectuser`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vprojectuser` AS select `pu`.`id` AS `id`,`pu`.`SystemUserId` AS `SystemUserId`,`pu`.`RoleNo` AS `RoleNo` from `tbprojectuser` `pu`;

-- --------------------------------------------------------

--
-- ビュー用の構造 `vprojectview`
--
DROP TABLE IF EXISTS `vprojectview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vprojectview` AS select `tp`.`id` AS `id`,`tp`.`Name` AS `Name`,`tp`.`Status` AS `Status`,`tp`.`CustomerId` AS `CustomerId`,`tp`.`PeriodStart` AS `PeriodStart`,`tp`.`PeriodEnd` AS `PeriodEnd`,`tp`.`ManagerId` AS `ManagerId`,(select ifnull(sum(`tpcm`.`Cost`),0) AS `cost` from `tbprojectcostmaster` `tpcm` where (`tp`.`id` = `tpcm`.`ProjectMasterId`) group by `tpcm`.`ProjectMasterId`) AS `ProjectTotalCost`,(select ifnull(sum(`tpc`.`Cost`),0) AS `cost` from (`tbprojectcostmaster` `tpcm` left join `tbproductioncost` `tpc` on((`tpcm`.`id` = `tpc`.`ProjectCostMasterId`))) where (`tp`.`id` = `tpcm`.`ProjectMasterId`) group by `tpcm`.`ProjectMasterId`) AS `ProductionTotalCost` from `tbprojectmaster` `tp`;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `TBProductionCost`
--
ALTER TABLE `TBProductionCost`
  ADD CONSTRAINT `FK_D08CE457A5DCE404` FOREIGN KEY (`SystemUserId`) REFERENCES `TBSystemUser` (`id`),
  ADD CONSTRAINT `FK_D08CE457D1971DCD` FOREIGN KEY (`ProjectCostMasterId`) REFERENCES `TBProjectCostMaster` (`id`);

--
-- テーブルの制約 `TBProjectCostHierarchyMaster`
--
ALTER TABLE `TBProjectCostHierarchyMaster`
  ADD CONSTRAINT `FK_BBCFA335AF18B565` FOREIGN KEY (`TBProjectMasterId`) REFERENCES `TBProjectMaster` (`id`);

--
-- テーブルの制約 `TBProjectCostMaster`
--
ALTER TABLE `TBProjectCostMaster`
  ADD CONSTRAINT `FK_DDDDD069C753C8D9` FOREIGN KEY (`TBProjectCostHierarchyMasterId`) REFERENCES `TBProjectCostHierarchyMaster` (`id`),
  ADD CONSTRAINT `FK_DDDDD06921E54FE0` FOREIGN KEY (`ProjectMasterId`) REFERENCES `TBProjectMaster` (`id`);

--
-- テーブルの制約 `TBProjectMaster`
--
ALTER TABLE `TBProjectMaster`
  ADD CONSTRAINT `FK_7C23FF7FD64FFAC3` FOREIGN KEY (`ManagerId`) REFERENCES `TBSystemUser` (`id`),
  ADD CONSTRAINT `FK_7C23FF7FBE22D475` FOREIGN KEY (`CustomerId`) REFERENCES `TBCustomer` (`id`);

--
-- テーブルの制約 `TBProjectUser`
--
ALTER TABLE `TBProjectUser`
  ADD CONSTRAINT `FK_5B5B65F21E54FE0` FOREIGN KEY (`ProjectMasterId`) REFERENCES `TBProjectMaster` (`id`),
  ADD CONSTRAINT `FK_5B5B65FA5DCE404` FOREIGN KEY (`SystemUserId`) REFERENCES `TBSystemUser` (`id`);

--
-- テーブルの制約 `TBSystemUser`
--
ALTER TABLE `TBSystemUser`
  ADD CONSTRAINT `FK_CB29A6C224EDF738` FOREIGN KEY (`DepartmentId`) REFERENCES `TBDepartment` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
