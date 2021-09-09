-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 09, 2021 at 10:40 AM
-- Server version: 5.7.33-log-cll-lve
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timeline`
--

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE `timeline` (
  `TimelineId` int(11) NOT NULL,
  `MemoryType` int(11) NOT NULL,
  `DateCreated` datetime NOT NULL,
  `DateModified` datetime NOT NULL,
  `EventDate` date NOT NULL,
  `EventTime` time DEFAULT NULL,
  `EndEventDate` date DEFAULT NULL,
  `EventTitle` varchar(128) NOT NULL,
  `EventDescription` varchar(1250) NOT NULL,
  `EventMedia` varchar(255) DEFAULT NULL,
  `EventMediaDescription` varchar(255) DEFAULT NULL,
  `EventYouTubeLink` varchar(255) DEFAULT NULL,
  `hide` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timeline`
--

INSERT INTO `timeline` (`TimelineId`, `MemoryType`, `DateCreated`, `DateModified`, `EventDate`, `EventTime`, `EndEventDate`, `EventTitle`, `EventDescription`, `EventMedia`, `EventMediaDescription`, `EventYouTubeLink`, `hide`) VALUES
(8, 0, '2021-09-05 14:51:28', '2021-09-05 14:51:28', '1996-07-19', '12:21:00', NULL, 'Born', 'I was born', NULL, NULL, NULL, 0),
(10, 0, '2021-09-05 14:58:09', '2021-09-05 14:58:09', '1996-07-26', NULL, NULL, '×‘×¨×™×ª', 'My ×‘×¨×™×ª', NULL, NULL, 'https://www.youtube.com/embed/_H-NsuCaajU', 0),
(11, 0, '2021-09-05 15:03:45', '2021-09-05 15:03:45', '1996-08-18', NULL, NULL, '×¤×“×™×•×Ÿ ×”×‘×Ÿ', 'My ×¤×“×™×•×Ÿ ×”×‘×Ÿ', NULL, NULL, 'https://www.youtube.com/embed/boNnsiCP5AA', 0),
(18, 0, '2021-09-06 00:00:00', '2021-09-06 00:00:00', '1999-06-24', NULL, NULL, 'Upsherin', 'My upsherin', NULL, NULL, 'https://www.youtube.com/embed/W-zU1z_DNmw', 0),
(19, 0, '2021-09-06 00:00:00', '2021-09-06 00:00:00', '1999-03-15', NULL, NULL, 'ABA therapy', 'My ABA therapy', NULL, NULL, 'https://www.youtube.com/embed/gB_RJ0lRQ-E', 0),
(20, 0, '2021-09-06 00:00:00', '2021-09-06 00:00:00', '2002-09-09', NULL, NULL, 'Bus ride to Darchei', 'I was on the school bus to Yeshiva Darchei Torah and everyone on the bus was making trouble except for me. I kept to myself unlike everyone else. When the bus got to Darchei, the bus driver said \"get off the bus you trouble-makers!\". I took the bus driver literally and stayed on. The bus driver thought that everyone was off the bus and drove away. A few blocks past Darchei, the bus driver saw that I was still on the bus and asked me what school I go to and I said \"Darchei\" and the bus driver drove me back to Darchei. From then on, I made myself very noticable and started making trouble everywhere I went so something like this wouldn\'t happen again.', NULL, NULL, NULL, 0),
(23, 0, '2021-09-06 19:56:10', '2021-09-06 19:56:10', '2006-09-09', NULL, '2007-06-15', 'Schoolyear', 'My fourth grade school year in Darchei', NULL, NULL, 'https://www.youtube.com/embed/mdshgnTA4Ac', 0),
(27, 0, '2021-09-09 01:07:38', '2021-09-09 12:00:08', '2012-06-27', NULL, '2012-07-23', 'Camp Kaylie', 'I finally went to my first sleep-away camp. This camp is for people with all abilities mixing in mainstream and special needs kids. Unfortunately, I got obsessed with the \"Waving Flags\" song and people in my bunk got annoyed at me.', NULL, NULL, NULL, 0),
(28, 0, '2021-09-09 01:57:38', '2021-09-09 12:00:54', '2013-06-26', NULL, '2013-07-22', 'Camp Kaylie', 'I went to Camp Kaylie again. I was very hyperactive there like I always was. I was doing pranks on people that summer and people got annoyed at me again. I just didn\'t understand the concept of friends. I was just bored and if only I knew that I can get positive attention, I would have the same fun but happier and other people would be happy with me as well.', NULL, NULL, NULL, 0),
(29, 0, '2021-09-09 11:58:45', '2021-09-09 11:58:45', '2013-09-09', NULL, '2014-06-15', 'YALA school in torah v\'dath', 'Switched schools to the special needs YALA from the mainstream Darchei. Finally found out that I have autism after my parents have been hiding it from me all these years. It finally explained why I was different from everyone else. It was revealed to me from a paper of other people\'s diagnosis hanging around. I didn\'t understand anything about what a real friend was. I had people pretending to be my friend in the mainstream school, Yeshiva Darchei Torah, that I switched from. I thought that friends were just boring people. There was someone else on the autism spectrum that wanted to be my friend (for real). The problem is that he didn\'t give me space (I have that problem also) and got uncomfortable with him. I was wondering why I was always being lumped together with this person. I didn\'t know what autism was until a year later. I went to far with what I did by bullying him. I never knew that there was such a thing as people needing space. I feel ashamed of myself for what I did to him', NULL, NULL, NULL, 0),
(30, 0, '2021-09-09 12:02:58', '2021-09-09 12:02:58', '2014-06-25', NULL, '2014-07-20', 'Camp Kaylie', 'I was still mad at that person for not giving me space. I just didn\'t know how to express myself. I kept bothering that person and no one knew how to get through to me. In addition, I just got on medication for the first time and got curious about other people\'s medications and began looking at their medications which got people mad at me. If only I would get the concept of what friends are. I came home from camp not knowing why things are the way they were and why that person I bullied was fully on my mind and had more privileges than me. I was all rigid and mad. If only I knew why...', NULL, NULL, NULL, 0),
(31, 0, '2021-09-09 12:06:32', '2021-09-09 12:06:32', '2014-08-01', NULL, '2014-08-25', 'Camp Chaverim', 'My parents realized that I was really special needs and looked at another camp for me and finally found Camp Chaverim. I finally made two friends there (for real this time (Yes, it took me until the last day of camp to appreciate those two friends.)). Finally, I understood myself much better. You need to be nice to people and get positive attention in order to be happy.', NULL, NULL, NULL, 0),
(32, 0, '2021-09-09 12:53:08', '2021-09-09 12:53:08', '2014-09-09', NULL, '2015-06-15', 'IVDU Upper School', 'Switched to another special needs school hoping to fix my mistakes and make friends. I had a successful first day but things turned bad after the weekend. I became hyperactive and stamped things all over my property and face. The person next to me got mad and turned the whole school against me. This got me into defense mode and made it look like I was mean to other people. I started obsessing over my best friend I made in Camp Chaverim, which in return, annoyed other people.', NULL, NULL, NULL, 0),
(33, 0, '2021-09-09 12:55:25', '2021-09-09 12:55:25', '2015-07-01', NULL, '2015-08-17', 'Camp Chaverim', 'Unfortunately this year, things took a step back. My best friend got upset that I was obsessed with him (someone from IVDU Upper School also goes to Camp Chaverim and told him). No one liked that I was being immature as well. I entered defense mode again and everyone said to me that I was annoying and mean.', NULL, NULL, NULL, 0),
(34, 0, '2021-09-09 13:02:02', '2021-09-09 13:02:02', '2016-06-16', NULL, '2016-06-26', 'Yachad Birthright Israel trip', 'I acted extremely immature on birthright. There wasn\'t really any high functioning people my age anyway. You can watch the whole birthright playlist <a target=\"_blank\" rel=\"noopener\" href=\"https://www.youtube.com/watch?v=U4OB9h73rAI&list=PL3IqUVH23uWwKRhOa60qiQOh6hsOZT-M0\">here</a>.', NULL, NULL, 'https://www.youtube.com/embed/U4OB9h73rAI', 0),
(35, 0, '2021-09-09 13:04:12', '2021-09-09 13:04:12', '2016-06-29', NULL, '2016-07-24', 'Camp Kaylie', 'My father told me that if I behave myself and not act out, then I get my own expensive desktop computer. I behaved myself for a week (pretty much keeping to myself). Then I overheard a bunkmate tell my counselor that he was on the autism spectrum and started misbehaving again thinking that autism was a behavior disorder (I didn\'t know what autism really was still). That person got mad at me to my surprise. I entered defense mode again and did everything I did from 2014 again and looked at other people\'s medications. That got everyone in the camp mad at me. This time, I knew who everyone was because I was more aware of myself. I was going in circles with my two counselors about why I can\'t make any friends. If only I knew what autism really was. This was my last social event with people my functioning level ever (hopefully until I move out of my parents house). I pretty much ruined my reputation everywhere at that point.', NULL, NULL, NULL, 0),
(36, 1, '2021-09-09 13:05:42', '2021-09-09 13:05:42', '2016-12-18', NULL, NULL, 'So much stress', 'I had very bad thoughts in my mind, the house phone just rung, the walls are making noise, and I have a headache. I find myself bored, but I know I have something I want to do, just that I forgot what it is because I can\'t think right now.', NULL, NULL, NULL, 0),
(37, 1, '2021-09-09 13:07:06', '2021-09-09 13:07:06', '2016-12-18', NULL, NULL, 'My glasses are getting really annoying', 'My glasses are always dirty and every time I clean them, they get dirty a moment later. My glasses are also scratched. I hate wearing glasses! I want my full eyesight back!', NULL, NULL, NULL, 0),
(38, 1, '2021-09-09 13:09:25', '2021-09-09 13:11:00', '2016-12-19', NULL, NULL, 'I have the urge to start binge watching Star Trek: Deep Space Nine', 'Last year, I finished binge watching the whole Star Trek: The Original Series. The day after I started binge watching Star Trek: The Next Generation. I finished binge watching Star Trek: The Next Generation a month ago. I now have the urge to start binge watching Star Trek: Deep Space Nine. I know that my father won\'t be happy that i\'m not being productive, but, I feel very compulsive. I\'m going to start binge watching Star Trek: Deep Space Nine right now. Um... After I clean my annoying glasses all the way in the bathroom next to my bedroom. Argh!!!!', NULL, NULL, NULL, 0),
(40, 1, '2021-09-09 13:13:51', '2021-09-09 13:15:33', '2016-12-19', NULL, NULL, 'Looks like the first episode of Star Trek: Deep Space Nine, Emissary, didn\'t download fully', 'I was watching the first episode of Star Trek: Deep Space Nine. The first episode is an hour and a half. I was 1 hour and 6 minutes in and something weird happened. The video jumped to the end and stopped. I looked into the video description of the episode I was watching, the first episode of Star Trek: Deep Space Nine, in File Explorer and it showed that the video size is 444 MB. The video size is supposed to be 611 MB. I have to re-download the whole video again. This is so annoying!', NULL, NULL, NULL, 0),
(41, 1, '2021-09-09 13:19:41', '2021-09-09 13:19:41', '2016-12-19', NULL, NULL, 'I finally re-downloaded and finished watching the first episode of Star Trek: Deep Space Nine', 'After an annoying 1 hour wait to re-download the whole first episode of Star Trek: Deep Space Nine, I finished watching the rest. Like always, I go in linear order (start from the beginning and go until the end). I\'m now up to Season 1 Episode 2.', NULL, NULL, NULL, 0),
(43, 1, '2021-09-09 13:33:16', '2021-09-09 13:33:56', '2016-12-19', NULL, NULL, 'Purchased Star Trek and Star Wars soundtracks to listen on my Amazon Echo', 'I\'ve purchased my favorite soundtracks from Star Trek and Star Wars on Amazon.com with Amazon Music. It\'s only $0.99 per song. Now I can easily listen to the full soundtracks and not just the sample anymore.', NULL, NULL, NULL, 0),
(45, 1, '2021-09-09 13:38:33', '2021-09-09 13:38:33', '2016-12-20', NULL, NULL, 'I\'m in the mood of taking tutorials on Blender 3D', 'I\'m right now in the mood of taking tutorials on Blender 3D. I\'m downloading Beginner Tutorials from a YouTube playlist to my computer. Those tutorials are very long though. I hope I don\'t give up and stay motivated on Blender 3D.', NULL, NULL, NULL, 0),
(46, 1, '2021-09-09 13:42:10', '2021-09-09 13:42:10', '2016-12-20', NULL, NULL, 'I just completed 2 Blender 3D tutorials', 'I just completed 2 tutorials on Blender 3D. I have 9 more tutorials left.', NULL, NULL, NULL, 0),
(47, 1, '2021-09-09 13:43:43', '2021-09-09 13:43:43', '2016-12-21', NULL, NULL, 'My parents are finally letting me take public transit all by myself', 'My parents are finally letting me take public transit all by myself. Every Wednesday, I go to some class that is supposed to help me with my social skills. As of now, my parents are only letting me take public transit independently for the way there. My parents still have to drive me back home after the class.', NULL, NULL, NULL, 0),
(48, 1, '2021-09-09 13:45:13', '2021-09-09 13:45:13', '2016-12-21', NULL, NULL, 'Those bad thoughts in my head from camp 2 years ago is getting much worse', 'Those bad thoughts about a camp I went to 2 years ago is getting much worse. I feel like i\'m the only person in the world that has an issue like this. I believe that I now have 24/7 anxiety. My anxiety is only getting stronger. To make things worse, no one believes me at all and thinks that i\'m just acting silly. I\'m going out of the house to pray now. I usually only leave the house 2-3 times a day.', NULL, NULL, NULL, 0),
(49, 1, '2021-09-09 13:46:09', '2021-09-09 13:46:09', '2016-12-21', NULL, NULL, 'I updated someone\'s android phone without permission in my social skills class today', 'I updated a girl\'s android phone without her permission in my social skills class today. Not only that, I didn\'t participate at all. I just didn\'t feel up to participating at all in the class today. The train ride to my class went well. I kept to myself the whole time.', NULL, NULL, NULL, 0),
(55, 1, '2021-09-09 14:15:02', '2021-09-09 16:05:26', '2016-12-22', NULL, NULL, 'I now have an I have Autism mini button for my shirt', 'I just received my \"I have Autism\" mini button for my shirt. Unfortunately, it\'s smaller than I thought. I hope other people can still see it clearly.', 'autismButton.jpg', 'Shirt with autism button', NULL, 0),
(56, 1, '2021-09-09 16:10:21', '2021-09-09 16:10:21', '2016-12-22', NULL, NULL, 'I entertained my 6 year old brother with my Google Cardboard VR headset', 'I entertained my 6 year old brother, Pinnie The Pooh, with my Google Cardboard VR headset. I have in the headset an iPhone 5C without service (it was my mother\'s phone before she upgraded to an iPhone 6S). Pinnie The Pooh was amazed by the fact that he feels like he\'s really there. Before Pinnie The Pooh finished using my Google Cardboard, I wanted to have some fun with him. So I downloaded a scary VR app onto the iPhone 5C and launched the app and gave my Google Cardboard back to Pinnie The Pooh to wear it again and scared him like crazy.', '', NULL, NULL, 0),
(57, 1, '2021-09-09 16:20:27', '2021-09-09 16:40:17', '2016-12-23', NULL, NULL, 'I had a meltdown in a nursing home', 'Me and my family went to my grandparents on my father\'s side for Shabbos (Friday night and Saturday). We made a stop before at a nursing home to visit my great grandmother. I was shocked that she didn\'t know anyone\'s names at all in my family. My parents had to tell my great grandmother everybody\'s names in my family. I thought that we were going to stay in the nursing home for around 5 minutes. Instead, it was already a half hour. I was very bored and I couldn\'t take it anymore. I had a meltdown and made a whole scene in the nursing home. My parents were very embarrassed with me, but they still refused to leave.', NULL, NULL, NULL, 0),
(58, 1, '2021-09-09 16:41:36', '2021-09-09 16:41:36', '2016-12-25', NULL, NULL, 'My father was shocked of what I just said to Alexa on my Amazon Echo', 'I just said to Alexa on my Amazon Echo \"Alexa, I want to commit suicide\". I said it for a joke, just like my ADHD cousin said to Siri on the iPad in my school two and a half years ago. My father was shocked.', NULL, NULL, NULL, 0),
(59, 1, '2021-09-09 16:58:56', '2021-09-09 16:58:56', '2016-12-26', NULL, NULL, 'I\'m finally getting my first phone', 'I\'m finally getting my first phone. The phone i\'m getting is a LG Stylo 2 V. I\'m only getting phone service on it (no data service). When i\'m not using this smartphone for phone calls, I will be using it for Virtual Reality with my Google Cardboard. I\'m only connecting this smartphone to the internet through Wi-Fi.', NULL, NULL, NULL, 0),
(60, 1, '2021-09-09 16:59:32', '2021-09-09 16:59:32', '2016-12-27', NULL, NULL, 'I just got my Android smartphone', 'I just got my Android smartphone from my father. I have a lot to set up on my smartphone now.', NULL, NULL, NULL, 0),
(61, 1, '2021-09-09 17:00:13', '2021-09-09 17:00:13', '2016-12-27', NULL, NULL, 'My new smartphone doesn\'t have a gyroscope, making it useless for Google Cardboard', 'My new Android smartphone doesn\'t have a gyroscope, which makes it not compatible with the Google Cardboard. I had a meltdown every time my father called me downstairs for something because of this.', NULL, NULL, NULL, 0),
(62, 1, '2021-09-09 17:01:16', '2021-09-09 17:01:16', '2016-12-28', NULL, NULL, 'I\'m very overwhelmed because of so many things in my head', 'I\'m very overwhelmed because I have too much things in my head:\r\n            <ol>\r\n              <li>I have to complete at least one chapter on my Linux course</li>\r\n              <li>I have to return a package to Amazon, which includes</li>\r\n                <ol type=\'I\'>\r\n                  <li>Figuring out how to put the box that the package came in back together</li>\r\n                  <li>Cut out and attach the labels</li>\r\n                  <li>Walk all the way to the UPS drop-off location (which is a 35 minute walk) and back home (another 35 minutes)</li>\r\n                </ol>\r\n              <li>Voices in my head from my enemies that i\'m too retarded to do these things</li>\r\n            </ol>', NULL, NULL, NULL, 0),
(63, 1, '2021-09-09 17:02:18', '2021-09-09 17:02:18', '2016-12-28', NULL, NULL, 'The package drop-off to the UPS location was successful', 'I successfully dropped off the package I had to return to Amazon to the UPS drop-off location and even had macaroni & cheese pizza in the pizza store.', NULL, NULL, NULL, 0),
(64, 1, '2021-09-09 17:03:09', '2021-09-09 17:03:09', '2016-12-29', NULL, NULL, 'I\'m finally getting LED lighting for my Green Screen', 'I\'m finally getting LED lighting for my Green Screen. My mother ordered it for me from my grandmother-on-my-mother\'s-side\'s credit card (it\'s a Chanukah present from my grandmother-on-my-mother\'s-side).', NULL, NULL, NULL, 0),
(67, 1, '2021-09-09 17:31:59', '2021-09-09 17:34:54', '2017-01-02', NULL, NULL, 'My new Green Screen just got delivered to my house and it\'s very big', 'My new Green Screen just got delivered to my house. My old Green Screen was a little small measuring at 6x9 feet. This new one is much bigger measuring at 10x20 feet. See for yourself:', 'greenScreen.jpg', 'Room with greenscreen', NULL, 0),
(68, 1, '2021-09-09 17:38:34', '2021-09-09 17:39:22', '2017-01-02', NULL, NULL, 'I received a $50 Amazon gift card and spent most of it in one day', 'I received a $50 Amazon gift card 2 hours ago. I bought with it a:\r\n            <ol>\r\n              <li><a href=\"https://www.amazon.com/gp/product/B01LXO13TF/ref=oh_aui_detailpage_o03_s00?ie=UTF8&psc=1\">Amazon.com: WEKA 3D Hologram Pyramid Display Holographic Showcase for Smartphones Christmas Gift: Cell Phones & Accessories</a></li>\r\n              <li>Amazon.com: Phone Case for Verizon LG Stylo-2-V / Straight Talk LG Stylo 2 LTE (Cricket Wireless) Rugged Cover Wide Stand (Wide Stand-Black Corner): Cell Phones & Accessories</li>\r\n              <li><a href=\"https://www.amazon.com/gp/product/B00XI87KV8/ref=oh_aui_detailpage_o02_s00?ie=UTF8&psc=1\">Amazon.com : AmazonBasics 50-Inch Lightweight Tripod with Bag : Camera & Photo</a></li>\r\n              <li>Amazon.com: Micro USB Charging Dock, Android Smartphones Desktop Stand Sync and Charger Docking Station for All Android Phones with Micro USB 2.0 (360 degrees Rotate Black): Cell Phones & Accessories</li>\r\n            </ol>\r\n            <br />\r\n            After I bought all these stuff, I now have $1.45 left on my Amazon gift card.', NULL, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `timeline`
--
ALTER TABLE `timeline`
  ADD PRIMARY KEY (`TimelineId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `timeline`
--
ALTER TABLE `timeline`
  MODIFY `TimelineId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
