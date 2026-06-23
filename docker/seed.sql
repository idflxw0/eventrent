--
-- PostgreSQL database dump
--

\restrict 0BLnZ0ZlPNCCy4SMMcI28cffNPPOC3bCApRWquCPYvTVB8Ah7URrd0N7Z8P3zLf

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: accessory; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.accessory VALUES (1, 'Pied d''enceinte télescopique', 'Pied réglable de 1,20 m à 2,10 m, charge max 50 kg');
INSERT INTO public.accessory VALUES (2, 'Câble XLR 10 mètres', 'Câble symétrique XLR mâle/femelle, blindage haute densité');
INSERT INTO public.accessory VALUES (3, 'Câble HDMI 5 mètres', 'Câble HDMI 2.1 haute vitesse, compatible 4K/60 Hz');
INSERT INTO public.accessory VALUES (4, 'Écran de projection 2×2 m', 'Écran sur trépied, toile mate blanche, format carré');
INSERT INTO public.accessory VALUES (5, 'Support plafond universel', 'Support orientable pour vidéoprojecteur, charge max 15 kg');
INSERT INTO public.accessory VALUES (6, 'Télécommande sans fil', 'Télécommande infrarouge universelle pour vidéoprojecteurs');


--
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.category VALUES (1, 'Sonorisation', 'Enceintes, caissons de basses et systèmes de diffusion audio');
INSERT INTO public.category VALUES (2, 'Vidéoprojection', 'Vidéoprojecteurs et systèmes de projection sur grand écran');
INSERT INTO public.category VALUES (3, 'Microphones', 'Micros filaires, sans fil, cravates et systèmes HF');
INSERT INTO public.category VALUES (4, 'Amplification', 'Amplificateurs de puissance pour sonorisation');
INSERT INTO public.category VALUES (5, 'Tables de mixage', 'Consoles et tables de mixage analogiques et numériques');


--
-- Data for Name: supplier; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.supplier VALUES (1, 'AudioPro France', 'contact@audiopro.fr', '0140506070', '12 rue du Faubourg Saint-Antoine, 75012 Paris');
INSERT INTO public.supplier VALUES (2, 'VisionTech Distribution', 'info@visiontech.fr', '0472753344', '45 avenue Jean Jaurès, 69007 Lyon');
INSERT INTO public.supplier VALUES (3, 'EventPlus Équipement', 'contact@eventplus.fr', '0556789012', '8 place de la Bourse, 33000 Bordeaux');


--
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.equipment VALUES (1, 'AUDIO-001', 'Enceinte active Mackie SRM450', 'Enceinte amplifiée 2 voies, idéale pour sonorisation de soirées et concerts', 150.00, 'available', 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 1, 1, 'audio');
INSERT INTO public.equipment VALUES (2, 'AUDIO-002', 'Micro sans fil Shure BLX24/SM58', 'Système micro HF avec récepteur, capsule dynamique SM58, portée 100 m', 25.00, 'available', 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 3, 1, 'audio');
INSERT INTO public.equipment VALUES (3, 'AUDIO-003', 'Table de mixage Yamaha MG16XU', 'Console 16 canaux avec effets SPX intégrés, compresseurs, USB audio', 80.00, 'available', 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 5, 1, 'audio');
INSERT INTO public.equipment VALUES (4, 'AUDIO-004', 'Amplificateur Crown XLS2500', 'Ampli de puissance classe D, 2×775 W sous 4Ω, DSP intégré', 90.00, 'maintenance', 'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 4, 3, 'audio');
INSERT INTO public.equipment VALUES (5, 'AUDIO-005', 'Enceinte passive JBL EON715', 'Enceinte passive 15", 650 W RMS, robuste et polyvalente', 60.00, 'available', 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 1, 3, 'audio');
INSERT INTO public.equipment VALUES (6, 'AUDIO-006', 'Caisson de basses JBL PRX818XLFW', 'Subwoofer 18" amplifié 1500 W, réponse en fréquence 30-103 Hz', 120.00, 'available', 'https://images.unsplash.com/photo-1618609378039-b572f64c5b42?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 1, 3, 'audio');
INSERT INTO public.equipment VALUES (7, 'VIDEO-001', 'Vidéoprojecteur Epson EH-TW9400', 'Projecteur home cinéma 4K UHD, HDR10, 2600 lumens, LCD 3 puces', 200.00, 'available', 'https://images.unsplash.com/photo-1535016120720-40c646be5580?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 2, 2, 'video');
INSERT INTO public.equipment VALUES (8, 'VIDEO-002', 'Vidéoprojecteur Optoma UHD38', 'Projecteur 4K gaming/cinéma, 4000 lumens, DLP, faible latence', 150.00, 'available', 'https://images.unsplash.com/photo-1601933973783-43cf8a7d4c5f?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 2, 2, 'video');
INSERT INTO public.equipment VALUES (9, 'VIDEO-003', 'Vidéoprojecteur BenQ TK850', 'Projecteur 4K HDR, 3000 lumens, compensation HDR-Pro', 120.00, 'available', 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 2, 2, 'video');
INSERT INTO public.equipment VALUES (10, 'VIDEO-004', 'Vidéoprojecteur Sony VPL-PHZ60', 'Projecteur laser pro WUXGA, 6000 lumens, durée de vie laser 20000 h', 350.00, 'available', 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=800&q=80', '2026-06-23 18:21:34', 2, 2, 'video');


--
-- Data for Name: audio_equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.audio_equipment VALUES (1000, 'XLR', 2, 1);
INSERT INTO public.audio_equipment VALUES (0, 'XLR', 1, 2);
INSERT INTO public.audio_equipment VALUES (0, 'XLR', 16, 3);
INSERT INTO public.audio_equipment VALUES (1500, 'Speakon', 2, 4);
INSERT INTO public.audio_equipment VALUES (650, 'Speakon', 1, 5);
INSERT INTO public.audio_equipment VALUES (1500, 'XLR', 1, 6);


--
-- Data for Name: category_supplier; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.category_supplier VALUES (1, 1);
INSERT INTO public.category_supplier VALUES (1, 3);
INSERT INTO public.category_supplier VALUES (2, 2);
INSERT INTO public.category_supplier VALUES (2, 3);
INSERT INTO public.category_supplier VALUES (3, 1);
INSERT INTO public.category_supplier VALUES (3, 3);
INSERT INTO public.category_supplier VALUES (4, 1);
INSERT INTO public.category_supplier VALUES (5, 1);


--
-- Data for Name: equipment_accessory; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.equipment_accessory VALUES (1, 1);
INSERT INTO public.equipment_accessory VALUES (1, 2);
INSERT INTO public.equipment_accessory VALUES (2, 2);
INSERT INTO public.equipment_accessory VALUES (3, 2);
INSERT INTO public.equipment_accessory VALUES (4, 2);
INSERT INTO public.equipment_accessory VALUES (5, 1);
INSERT INTO public.equipment_accessory VALUES (5, 2);
INSERT INTO public.equipment_accessory VALUES (6, 2);
INSERT INTO public.equipment_accessory VALUES (7, 3);
INSERT INTO public.equipment_accessory VALUES (7, 5);
INSERT INTO public.equipment_accessory VALUES (7, 6);
INSERT INTO public.equipment_accessory VALUES (8, 3);
INSERT INTO public.equipment_accessory VALUES (8, 4);
INSERT INTO public.equipment_accessory VALUES (8, 6);
INSERT INTO public.equipment_accessory VALUES (9, 3);
INSERT INTO public.equipment_accessory VALUES (9, 6);
INSERT INTO public.equipment_accessory VALUES (10, 3);
INSERT INTO public.equipment_accessory VALUES (10, 5);


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public."user" VALUES (1, 'admin@eventrent.com', '["ROLE_ADMIN"]', '$2y$13$xXj1X.OicoT8J10Qe4eO0u45mbPC3PH1oiYBZo7ak3j681AJxj.0W', 'Durand', 'Thomas', '0601020304', '2026-06-23 18:21:33', true);
INSERT INTO public."user" VALUES (2, 'tech@eventrent.com', '["ROLE_TECHNICIEN"]', '$2y$13$iG5ij2GC7A1uGi3ub7n7ZOe3Shr56DDWDNWVz.OaDQAb8ITswTkh6', 'Laurent', 'Marie', '0605060708', '2026-06-23 18:21:33', true);
INSERT INTO public."user" VALUES (3, 'user@eventrent.com', '["ROLE_USER"]', '$2y$13$SVAkiN2alGIzLvS8mKEZI.iiG6UUuYsOjQpD9EmQ4ByZyVOpYrr7e', 'Dupont', 'Jean', '0609101112', '2026-06-23 18:21:33', true);
INSERT INTO public."user" VALUES (4, 'edouard.fischer@example.org', '["ROLE_USER"]', '$2y$13$Y6/X2Lu2mAqxHQEnvkztP.6RUuCq4gg3iS9cARwYCjH1e79CGYRw6', 'Tanguy', 'Augustin', '0132095960', '2026-06-23 18:21:33', true);
INSERT INTO public."user" VALUES (5, 'irousset@example.net', '["ROLE_USER"]', '$2y$13$XbRmnNGaOgP64EBnjg0U.eQ0c/s.7HTmoDvQaoDHyCZhBpUjfOerq', 'Gilbert', 'Richard', '+33 2 83 92 17 02', '2026-06-23 18:21:34', true);
INSERT INTO public."user" VALUES (6, 'daniel.andre@example.com', '["ROLE_USER"]', '$2y$13$OzvTiJrW/EOgyWyxlbyx.OYkPrP7bYe4KHCOjn5HGrkeD8qPCfP2W', 'Blanc', 'Gabriel', '09 95 72 33 08', '2026-06-23 18:21:34', true);


--
-- Data for Name: reservation; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.reservation VALUES (1, '2026-05-10', '2026-05-12', 'Lyon', 'indoor', 'completed', 1290.00, NULL, '2026-06-23 18:21:34', 3);
INSERT INTO public.reservation VALUES (3, '2026-08-15', '2026-08-16', 'Paris', 'indoor', 'confirmed', 640.00, NULL, '2026-06-23 18:21:34', 3);
INSERT INTO public.reservation VALUES (4, '2026-07-20', '2026-07-22', 'Bordeaux', 'indoor', 'cancelled', 1500.00, NULL, '2026-06-23 18:21:34', 3);
INSERT INTO public.reservation VALUES (5, '2026-04-05', '2026-04-06', 'Nantes', 'indoor', 'completed', 200.00, NULL, '2026-06-23 18:21:34', 4);
INSERT INTO public.reservation VALUES (2, '2026-06-01', '2026-06-03', 'Marseille', 'outdoor', 'completed', 1170.00, 'Ensoleillé, 28°C, vent léger 10 km/h — Pas de risque de pluie', '2026-06-23 18:21:34', 3);
INSERT INTO public.reservation VALUES (6, '2026-06-13', '2026-06-20', 'Rennes', 'indoor', 'completed', 450.00, NULL, '2026-06-23 18:21:34', 3);
INSERT INTO public.reservation VALUES (7, '2026-06-23', '2026-06-24', 'paris', 'outdoor', 'confirmed', 120.00, NULL, '2026-06-23 19:00:48', 2);
INSERT INTO public.reservation VALUES (8, '2026-06-24', '2026-06-26', 'paris', 'outdoor', 'confirmed', 300.00, 'Météo : service temporairement indisponible.', '2026-06-23 19:07:14', 2);
INSERT INTO public.reservation VALUES (9, '2026-06-24', '2026-06-29', 'paris', 'outdoor', 'confirmed', 300.00, 'Météo : service temporairement indisponible.', '2026-06-23 19:12:07', 2);


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.invoice VALUES (1, 'INV-2026-000123', 1290.00, 'paid', '2026-05-10 14:30:00', '2026-06-09', 1);
INSERT INTO public.invoice VALUES (2, 'INV-2026-000124', 1170.00, 'pending', '2026-06-01 09:15:00', '2026-07-01', 2);
INSERT INTO public.invoice VALUES (3, 'INV-2026-393676', 120.00, 'pending', '2026-06-23 19:00:48', '2026-07-23', 7);
INSERT INTO public.invoice VALUES (4, 'INV-2026-273276', 300.00, 'pending', '2026-06-23 19:07:14', '2026-07-23', 8);
INSERT INTO public.invoice VALUES (5, 'INV-2026-357299', 300.00, 'pending', '2026-06-23 19:12:07', '2026-07-23', 9);


--
-- Data for Name: maintenance; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.maintenance VALUES (1, 'repair', 'Remplacement du haut-parleur grillé sur la voie gauche. Vérification ampli interne OK.', '2026-04-15 10:00:00', 'available', 1, 2);
INSERT INTO public.maintenance VALUES (2, 'inspection', 'Contrôle périodique : lampe à 1200 h (max 5000 h), filtre à air nettoyé, focus vérifié.', '2026-05-20 14:00:00', 'available', 7, 2);
INSERT INTO public.maintenance VALUES (3, 'breakdown', 'Court-circuit canal droit. Pièces commandées, en attente de livraison.', '2026-06-10 08:30:00', 'maintenance', 4, 2);


--
-- Data for Name: notification; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.notification VALUES (1, 'Votre réservation à Lyon du 10 au 12 mai 2026 est confirmée.', 'reservation_confirmed', true, '2026-05-09 16:30:00', 3);
INSERT INTO public.notification VALUES (2, 'Votre devis pour Nice (septembre 2026) a bien été reçu.', 'quote_received', false, '2026-06-23 18:21:34', 3);
INSERT INTO public.notification VALUES (3, 'Votre devis pour Lille (octobre 2026) a été approuvé ! Transformez-le en réservation.', 'quote_approved', false, '2026-06-14 11:00:00', 3);
INSERT INTO public.notification VALUES (4, 'Facture INV-2026-000123 disponible dans votre espace client.', 'invoice_available', true, '2026-05-10 14:35:00', 3);
INSERT INTO public.notification VALUES (5, 'Maintenance assignée : Amplificateur Crown XLS2500 — court-circuit canal droit.', 'maintenance_assigned', false, '2026-06-10 08:35:00', 2);
INSERT INTO public.notification VALUES (6, 'Nouvelle réservation de Jean Dupont à Paris (15-16 août 2026) en attente.', 'new_reservation', false, '2026-06-23 18:21:34', 1);
INSERT INTO public.notification VALUES (7, 'Rappel : votre réservation à Marseille est dans 2 jours.', 'reservation_reminder', true, '2026-05-30 08:00:00', 3);
INSERT INTO public.notification VALUES (8, 'Votre réservation à paris (23/06/2026 – 24/06/2026) est confirmée.', 'reservation_confirmed', false, '2026-06-23 19:00:48', 2);
INSERT INTO public.notification VALUES (9, 'Votre réservation à paris (24/06/2026 – 26/06/2026) est confirmée.', 'reservation_confirmed', false, '2026-06-23 19:07:15', 2);
INSERT INTO public.notification VALUES (10, 'Votre réservation à paris (24/06/2026 – 29/06/2026) est confirmée.', 'reservation_confirmed', false, '2026-06-23 19:12:07', 2);


--
-- Data for Name: quote; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.quote VALUES (1, '2026-09-01', '2026-09-03', 'Nice', 2040.00, 'pending', '2026-06-23 18:21:34', '2026-07-08', 3);
INSERT INTO public.quote VALUES (2, '2026-10-05', '2026-10-06', 'Lille', 1800.00, 'approved', '2026-06-23 18:21:34', '2026-07-08', 3);
INSERT INTO public.quote VALUES (3, '2026-04-10', '2026-04-12', 'Toulouse', 225.00, 'expired', '2026-03-20 00:00:00', '2026-04-04', 3);
INSERT INTO public.quote VALUES (4, '2026-07-15', '2026-07-16', 'Strasbourg', 300.00, 'expired', '2026-06-03 18:21:34', '2026-07-08', 3);


--
-- Data for Name: quote_line; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.quote_line VALUES (1, 4, 150.00, 1, 1);
INSERT INTO public.quote_line VALUES (2, 1, 80.00, 1, 3);
INSERT INTO public.quote_line VALUES (3, 2, 350.00, 2, 10);
INSERT INTO public.quote_line VALUES (4, 1, 200.00, 2, 7);
INSERT INTO public.quote_line VALUES (5, 3, 25.00, 3, 2);
INSERT INTO public.quote_line VALUES (6, 2, 25.00, 4, 2);


--
-- Data for Name: reservation_line; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.reservation_line VALUES (1, 2, 150.00, 1, 1);
INSERT INTO public.reservation_line VALUES (2, 2, 25.00, 1, 2);
INSERT INTO public.reservation_line VALUES (3, 1, 80.00, 1, 3);
INSERT INTO public.reservation_line VALUES (4, 1, 90.00, 2, 4);
INSERT INTO public.reservation_line VALUES (5, 3, 60.00, 2, 5);
INSERT INTO public.reservation_line VALUES (6, 1, 120.00, 2, 6);
INSERT INTO public.reservation_line VALUES (7, 1, 200.00, 3, 7);
INSERT INTO public.reservation_line VALUES (8, 1, 120.00, 3, 9);
INSERT INTO public.reservation_line VALUES (9, 1, 150.00, 4, 8);
INSERT INTO public.reservation_line VALUES (10, 1, 350.00, 4, 10);
INSERT INTO public.reservation_line VALUES (11, 1, 200.00, 5, 7);
INSERT INTO public.reservation_line VALUES (12, 1, 150.00, 6, 1);
INSERT INTO public.reservation_line VALUES (13, 1, 120.00, 7, 6);
INSERT INTO public.reservation_line VALUES (14, 1, 150.00, 8, 1);
INSERT INTO public.reservation_line VALUES (15, 1, 60.00, 9, 5);


--
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.review VALUES (1, 5, 'Excellent son, très fiable. Utilisé pour un mariage, la sono était parfaite.', '2026-06-23 18:21:34', 3, 1);
INSERT INTO public.review VALUES (2, 4, 'Bonne qualité audio, un léger souffle en fin de soirée mais rien de gênant.', '2026-06-23 18:21:34', 3, 2);
INSERT INTO public.review VALUES (3, 5, 'Console très intuitive, les compresseurs intégrés font la différence sur les voix.', '2026-06-23 18:21:34', 3, 3);
INSERT INTO public.review VALUES (4, 4, 'Très bon projecteur, image nette même en salle éclairée. Un peu lourd à installer.', '2026-06-23 18:21:34', 4, 7);


--
-- Data for Name: video_equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

INSERT INTO public.video_equipment VALUES ('3840x2160', 2600, 'LCD', 7);
INSERT INTO public.video_equipment VALUES ('3840x2160', 4000, 'DLP', 8);
INSERT INTO public.video_equipment VALUES ('3840x2160', 3000, 'DLP', 9);
INSERT INTO public.video_equipment VALUES ('1920x1200', 6000, 'Laser', 10);


--
-- Name: accessory_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.accessory_id_seq', 6, true);


--
-- Name: category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.category_id_seq', 5, true);


--
-- Name: equipment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.equipment_id_seq', 10, true);


--
-- Name: invoice_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.invoice_id_seq', 5, true);


--
-- Name: maintenance_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.maintenance_id_seq', 3, true);


--
-- Name: notification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.notification_id_seq', 10, true);


--
-- Name: quote_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.quote_id_seq', 4, true);


--
-- Name: quote_line_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.quote_line_id_seq', 6, true);


--
-- Name: reservation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.reservation_id_seq', 9, true);


--
-- Name: reservation_line_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.reservation_line_id_seq', 15, true);


--
-- Name: review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.review_id_seq', 4, true);


--
-- Name: supplier_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.supplier_id_seq', 3, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.user_id_seq', 6, true);


--
-- PostgreSQL database dump complete
--

\unrestrict 0BLnZ0ZlPNCCy4SMMcI28cffNPPOC3bCApRWquCPYvTVB8Ah7URrd0N7Z8P3zLf

