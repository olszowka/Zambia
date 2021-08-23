## This script adds new tables for a room grid in color.
##   Also adds some new fields to improve scheduling and reports.
##
##  Created by Leane Verhulst on August 22, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

CREATE TABLE `RoomColors` (
  `roomcolorid` int(11) NOT NULL AUTO_INCREMENT,
  `roomcolorname` varchar(100) DEFAULT NULL,
  `roomcolorcode` varchar(10) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`roomcolorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `RoomColors` (`roomcolorid`, `roomcolorname`, `roomcolorcode`, `display_order`) VALUES
(1, 'aliceblue', 'F0F8FF', 1),
(2, 'antiquewhite', 'FAEBD7', 2),
(3, 'aqua', '00FFFF', 3),
(4, 'aquamarine', '7FFFD4', 4),
(5, 'azure', 'F0FFFF', 5),
(6, 'beige', 'F5F5DC', 6),
(7, 'bisque', 'FFE4C4', 7),
(8, 'black', '000000', 8),
(9, 'blanchedalmond', 'FFEBCD', 9),
(10, 'blue', '0000FF', 10),
(11, 'blueviolet', '8A2BE2', 11),
(12, 'brown', 'A52A2A', 12),
(13, 'burlywood', 'DEB887', 13),
(14, 'cadetblue', '5F9EA0', 14),
(15, 'chartreuse', '7FFF00', 15),
(16, 'chocolate', 'D2691E', 16),
(17, 'coral', 'FF7F50', 17),
(18, 'cornflowerblue', '6495ED', 18),
(19, 'cornsilk', 'FFF8DC', 19),
(20, 'crimson', 'DC143C', 20),
(21, 'cyan', '00FFFF', 21),
(22, 'darkblue', '00008B', 22),
(23, 'darkcyan', '008B8B', 23),
(24, 'darkgoldenrod', 'B8860B', 24),
(25, 'darkgray', 'A9A9A9', 25),
(26, 'darkgreen', '006400', 26),
(27, 'darkkhaki', 'BDB76B', 27),
(28, 'darkmagenta', '8B008B', 28),
(29, 'darkolivegreen', '556B2F', 29),
(30, 'darkorange', 'FF8C00', 30),
(31, 'darkorchid', '9932CC', 31),
(32, 'darkred', '8B0000', 32),
(33, 'darksalmon', 'E9967A', 33),
(34, 'darkseagreen', '8FBC8F', 34),
(35, 'darkslateblue', '483D8B', 35),
(36, 'darkslategray', '2F4F4F', 36),
(37, 'darkturquoise', '00CED1', 37),
(38, 'darkviolet', '9400D3', 38),
(39, 'deeppink', 'FF1493', 39),
(40, 'deepskyblue', '00BFFF', 40),
(41, 'dimgray', '696969', 41),
(42, 'dodgerblue', '1E90FF', 42),
(43, 'firebrick', 'B22222', 43),
(44, 'floralwhite', 'FFFAF0', 44),
(45, 'forestgreen', '228B22', 45),
(46, 'fuchsia', 'FF00FF', 46),
(47, 'gainsboro', 'DCDCDC', 47),
(48, 'ghostwhite', 'F8F8FF', 48),
(49, 'gold', 'FFD700', 49),
(50, 'goldenrod', 'DAA520', 50),
(51, 'gray', '808080', 51),
(52, 'green', '008000', 52),
(53, 'greenyellow', 'ADFF2F', 53),
(54, 'honeydew', 'F0FFF0', 54),
(55, 'hotpink', 'FF69B4', 55),
(56, 'indianred', 'CD5C5C', 56),
(57, 'indigo', '4B0082', 57),
(58, 'ivory', 'FFFFF0', 58),
(59, 'khaki', 'F0E68C', 59),
(60, 'lavender', 'E6E6FA', 60),
(61, 'lavenderblush', 'FFF0F5', 61),
(62, 'lawngreen', '7CFC00', 62),
(63, 'lemonchiffon', 'FFFACD', 63),
(64, 'lightblue', 'ADD8E6', 64),
(65, 'lightcoral', 'F08080', 65),
(66, 'lightcyan', 'E0FFFF', 66),
(67, 'lightgoldenrodyellow', 'FAFAD2', 67),
(68, 'lightgreen', '90EE90', 68),
(69, 'lightgrey', 'D3D3D3', 69),
(70, 'lightpink', 'FFB6C1', 70),
(71, 'lightsalmon', 'FFA07A', 71),
(72, 'lightseagreen', '20B2AA', 72),
(73, 'lightskyblue', '87CEFA', 73),
(74, 'lightslategray', '778899', 74),
(75, 'lightsteelblue', 'B0C4DE', 75),
(76, 'lightyellow', 'FFFFE0', 76),
(77, 'lime', '00FF00', 77),
(78, 'limegreen', '32CD32', 78),
(79, 'linen', 'FAF0E6', 79),
(80, 'magenta', 'FF00FF', 80),
(81, 'maroon', '800000', 81),
(82, 'mediumaquamarine', '66CDAA', 82),
(83, 'mediumblue', '0000CD', 83),
(84, 'mediumorchid', 'BA55D3', 84),
(85, 'mediumpurple', '9370DB', 85),
(86, 'mediumseagreen', '3CB371', 86),
(87, 'mediumslateblue', '7B68EE', 87),
(88, 'mediumspringgreen', '00FA9A', 88),
(89, 'mediumturquoise', '48D1CC', 89),
(90, 'mediumvioletred', 'C71585', 90),
(91, 'midnightblue', '191970', 91),
(92, 'mintcream', 'F5FFFA', 92),
(93, 'mistyrose', 'FFE4E1', 93),
(94, 'moccasin', 'FFE4B5', 94),
(95, 'navajowhite', 'FFDEAD', 95),
(96, 'navy', '000080', 96),
(97, 'oldlace', 'FDF5E6', 97),
(98, 'olive', '808000', 98),
(99, 'olivedrab', '6B8E23', 99),
(100, 'orange', 'FFA500', 100),
(101, 'orangered', 'FF4500', 101),
(102, 'orchid', 'DA70D6', 102),
(103, 'palegoldenrod', 'EEE8AA', 103),
(104, 'palegreen', '98FB98', 104),
(105, 'paleturquoise', 'AFEEEE', 105),
(106, 'palevioletred', 'DB7093', 106),
(107, 'papayawhip', 'FFEFD5', 107),
(108, 'peachpuff', 'FFDAB9', 108),
(109, 'peru', 'CD853F', 109),
(110, 'pink', 'FFC0CB', 110),
(111, 'plum', 'DDA0DD', 111),
(112, 'powderblue', 'B0E0E6', 112),
(113, 'purple', '800080', 113),
(114, 'red', 'FF0000', 114),
(115, 'rosybrown', 'BC8F8F', 115),
(116, 'royalblue', '4169E1', 116),
(117, 'saddlebrown', '8B4513', 117),
(118, 'salmon', 'FA8072', 118),
(119, 'sandybrown', 'F4A460', 119),
(120, 'seagreen', '2E8B57', 120),
(121, 'seashell', 'FFF5EE', 121),
(122, 'sienna', 'A0522D', 122),
(123, 'silver', 'C0C0C0', 123),
(124, 'skyblue', '87CEEB', 124),
(125, 'slateblue', '6A5ACD', 125),
(126, 'slategray', '708090', 126),
(127, 'snow', 'FFFAFA', 127),
(128, 'springgreen', '00FF7F', 128),
(129, 'steelblue', '4682B4', 129),
(130, 'tan', 'D2B48C', 130),
(131, 'teal', '008080', 131),
(132, 'thistle', 'D8BFD8', 132),
(133, 'tomato', 'FD6347', 133),
(134, 'turquoise', '40E0D0', 134),
(135, 'violet', 'EE82EE', 135),
(136, 'wheat', 'F5DEB3', 136),
(137, 'white', 'FFFFFF', 137),
(138, 'whitesmoke', 'F5F5F5', 138),
(139, 'yellow', 'FFFF00', 139),
(140, 'yellowgreen', '9ACD32', 140),
(999, 'none', '0', 0);


## Add new field for description of room set
ALTER TABLE `RoomSets` ADD COLUMN `description` varchar(255) DEFAULT NULL AFTER `roomsetname`;


## Add additional fields to the room table for the color grid code
## Also add day 4 open and close times
ALTER TABLE `Rooms` ADD COLUMN `opentime4` time DEFAULT NULL AFTER `closetime3`;
ALTER TABLE `Rooms` ADD COLUMN `closetime4` time DEFAULT NULL AFTER `opentime4`;
ALTER TABLE `Rooms` ADD COLUMN `is_gamingtable` tinyint(4) NOT NULL DEFAULT 0 AFTER `is_scheduled`;
ALTER TABLE `Rooms` ADD COLUMN `roomcolorid` int(11) NOT NULL DEFAULT 0 AFTER `is_gamingtable`;
ALTER TABLE `Rooms` ADD COLUMN `on_public_grid` tinyint(4) NOT NULL DEFAULT 1 AFTER `roomcolorid`;
ALTER TABLE `Rooms` ADD COLUMN `grid_column_span` int(2) NOT NULL DEFAULT 1 AFTER `on_public_grid`;


## Set up permissions. Check that permissionatom 2026 does not already exist.
INSERT INTO `PermissionAtoms` (`permatomid`, `permatomtag`, `page`, `notes`) VALUES ('2026', 'ce_RoomColors', 'Edit Configuration Tables', 'enables edit');
INSERT INTO `Permissions` (`permissionid`, `permatomid`, `phaseid`, `permroleid`, `badgeid`) VALUES (NULL, '2026', NULL, '1', NULL);


INSERT INTO PatchLog (patchname) VALUES ('68CAP_color_room_grid.sql');
