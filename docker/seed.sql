--
-- PostgreSQL database dump
--

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

COPY public.accessory (id, name, description) FROM stdin;
7	Pied d'enceinte télescopique	Pied réglable de 1,20 m à 2,10 m, charge max 50 kg
8	Câble XLR 10 mètres	Câble symétrique XLR mâle/femelle, blindage haute densité
9	Câble HDMI 5 mètres	Câble HDMI 2.1 haute vitesse, compatible 4K/60 Hz
10	Écran de projection 2×2 m	Écran sur trépied, toile mate blanche, format carré
11	Support plafond universel	Support orientable pour vidéoprojecteur, charge max 15 kg
12	Télécommande sans fil	Télécommande infrarouge universelle pour vidéoprojecteurs
\.


--
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.category (id, name, description) FROM stdin;
6	Sonorisation	Enceintes, caissons de basses et systèmes de diffusion audio
7	Vidéoprojection	Vidéoprojecteurs et systèmes de projection sur grand écran
8	Microphones	Micros filaires, sans fil, cravates et systèmes HF
9	Amplification	Amplificateurs de puissance pour sonorisation
10	Tables de mixage	Consoles et tables de mixage analogiques et numériques
\.


--
-- Data for Name: supplier; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.supplier (id, name, email, phone, address) FROM stdin;
4	AudioPro France	contact@audiopro.fr	0140506070	12 rue du Faubourg Saint-Antoine, 75012 Paris
5	VisionTech Distribution	info@visiontech.fr	0472753344	45 avenue Jean Jaurès, 69007 Lyon
6	EventPlus Équipement	contact@eventplus.fr	0556789012	8 place de la Bourse, 33000 Bordeaux
\.


--
-- Data for Name: equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.equipment (id, reference, name, description, daily_price, availability_status, photo, added_at, category_id, supplier_id, type) FROM stdin;
11	AUDIO-001	Enceinte active Mackie SRM450	Enceinte amplifiée 2 voies, idéale pour sonorisation de soirées et concerts	150.00	available	https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	6	4	audio
12	AUDIO-002	Micro sans fil Shure BLX24/SM58	Système micro HF avec récepteur, capsule dynamique SM58, portée 100 m	25.00	available	https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	8	4	audio
13	AUDIO-003	Table de mixage Yamaha MG16XU	Console 16 canaux avec effets SPX intégrés, compresseurs, USB audio	80.00	available	https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	10	4	audio
14	AUDIO-004	Amplificateur Crown XLS2500	Ampli de puissance classe D, 2×775 W sous 4Ω, DSP intégré	90.00	maintenance	https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	9	6	audio
15	AUDIO-005	Enceinte passive JBL EON715	Enceinte passive 15", 650 W RMS, robuste et polyvalente	60.00	available	https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	6	6	audio
16	AUDIO-006	Caisson de basses JBL PRX818XLFW	Subwoofer 18" amplifié 1500 W, réponse en fréquence 30-103 Hz	120.00	available	https://images.unsplash.com/photo-1618609378039-b572f64c5b42?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	6	6	audio
17	VIDEO-001	Vidéoprojecteur Epson EH-TW9400	Projecteur home cinéma 4K UHD, HDR10, 2600 lumens, LCD 3 puces	200.00	available	https://images.unsplash.com/photo-1535016120720-40c646be5580?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	7	5	video
18	VIDEO-002	Vidéoprojecteur Optoma UHD38	Projecteur 4K gaming/cinéma, 4000 lumens, DLP, faible latence	150.00	available	https://images.unsplash.com/photo-1601933973783-43cf8a7d4c5f?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	7	5	video
19	VIDEO-003	Vidéoprojecteur BenQ TK850	Projecteur 4K HDR, 3000 lumens, compensation HDR-Pro	120.00	available	https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	7	5	video
20	VIDEO-004	Vidéoprojecteur Sony VPL-PHZ60	Projecteur laser pro WUXGA, 6000 lumens, durée de vie laser 20000 h	350.00	available	https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=800&q=80	2026-06-21 19:24:27	7	5	video
\.


--
-- Data for Name: audio_equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.audio_equipment (power_watts, connector_type, channel_count, id) FROM stdin;
1000	XLR	2	11
0	XLR	1	12
0	XLR	16	13
1500	Speakon	2	14
650	Speakon	1	15
1500	XLR	1	16
\.


--
-- Data for Name: category_supplier; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.category_supplier (category_id, supplier_id) FROM stdin;
6	4
6	6
7	5
7	6
8	4
8	6
9	4
10	4
\.


--
-- Data for Name: equipment_accessory; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.equipment_accessory (equipment_id, accessory_id) FROM stdin;
11	7
11	8
12	8
13	8
14	8
15	7
15	8
16	8
17	9
17	11
17	12
18	9
18	10
18	12
19	9
19	12
20	9
20	11
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public."user" (id, email, roles, password, last_name, first_name, phone, registered_at, active) FROM stdin;
8	admin@eventrent.com	["ROLE_ADMIN"]	$2y$13$GT35Vi1Hh4KZyIqgNsvQ5eHZPsf5T7lxLxAUV5780xSqD6WHdxctm	Durand	Thomas	0601020304	2026-06-21 19:24:25	t
9	tech@eventrent.com	["ROLE_TECHNICIEN"]	$2y$13$v2TSYQtWb.sqm//Rb/Lshu80Ag6SFzG2eCJK8FQQClOMc8QeLj0eW	Laurent	Marie	0605060708	2026-06-21 19:24:25	t
10	user@eventrent.com	["ROLE_USER"]	$2y$13$pD6LDHnowAuTuThWsFFL4Od9Evclk0Nn8M8xRrcSyMoTzicK6VV3S	Dupont	Jean	0609101112	2026-06-21 19:24:25	t
11	tristan.pruvost@example.org	["ROLE_USER"]	$2y$13$Rip8YqJRsNRIi8hwt0ecdeetoBqCbquzYoxDRPnH6gnK0hpk4fd76	Lacroix	Véronique	07 51 97 80 49	2026-06-21 19:24:26	t
12	francois87@example.com	["ROLE_USER"]	$2y$13$xGZFshGdToGftRzOk2lQ9.xOwRlGuo4yrb96PY8f8B9/uHG3qDwNy	Royer	Anouk	+33 (0)7 76 80 66 46	2026-06-21 19:24:26	t
13	oweber@example.net	["ROLE_USER"]	$2y$13$PdtcmCAKktdLm6CtxeeP5u19ZeZG/2lXvgXgiDlpDB0rWyWr7HGJC	Bonneau	Yves	+33 (0)2 40 31 15 40	2026-06-21 19:24:26	t
\.


--
-- Data for Name: reservation; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.reservation (id, start_date, end_date, event_city, venue_type, status, total_amount, weather_forecast, created_at, user_id) FROM stdin;
7	2026-05-10	2026-05-12	Lyon	indoor	completed	1290.00	\N	2026-06-21 19:24:27	10
8	2026-06-01	2026-06-03	Marseille	outdoor	confirmed	1170.00	Ensoleillé, 28°C, vent léger 10 km/h — Pas de risque de pluie	2026-06-21 19:24:27	10
9	2026-08-15	2026-08-16	Paris	indoor	confirmed	640.00	\N	2026-06-21 19:24:27	10
10	2026-07-20	2026-07-22	Bordeaux	indoor	cancelled	1500.00	\N	2026-06-21 19:24:27	10
11	2026-04-05	2026-04-06	Nantes	indoor	completed	200.00	\N	2026-06-21 19:24:27	11
\.


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.invoice (id, number, amount, payment_status, issued_at, due_date, reservation_id) FROM stdin;
4	INV-2026-000123	1290.00	paid	2026-05-10 14:30:00	2026-06-09	7
5	INV-2026-000124	1170.00	pending	2026-06-01 09:15:00	2026-07-01	8
\.


--
-- Data for Name: maintenance; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.maintenance (id, intervention_type, description, intervention_date, status_after_intervention, equipment_id, technician_id) FROM stdin;
4	repair	Remplacement du haut-parleur grillé sur la voie gauche. Vérification ampli interne OK.	2026-04-15 10:00:00	available	11	9
5	inspection	Contrôle périodique : lampe à 1200 h (max 5000 h), filtre à air nettoyé, focus vérifié.	2026-05-20 14:00:00	available	17	9
6	breakdown	Court-circuit canal droit. Pièces commandées, en attente de livraison.	2026-06-10 08:30:00	maintenance	14	9
\.


--
-- Data for Name: notification; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.notification (id, message, type, read, created_at, user_id) FROM stdin;
16	Votre réservation à Lyon du 10 au 12 mai 2026 est confirmée.	reservation_confirmed	t	2026-05-09 16:30:00	10
17	Votre devis pour Nice (septembre 2026) a bien été reçu.	quote_received	f	2026-06-21 19:24:27	10
18	Votre devis pour Lille (octobre 2026) a été approuvé ! Transformez-le en réservation.	quote_approved	f	2026-06-14 11:00:00	10
19	Facture INV-2026-000123 disponible dans votre espace client.	invoice_available	t	2026-05-10 14:35:00	10
20	Maintenance assignée : Amplificateur Crown XLS2500 — court-circuit canal droit.	maintenance_assigned	f	2026-06-10 08:35:00	9
21	Nouvelle réservation de Jean Dupont à Paris (15-16 août 2026) en attente.	new_reservation	f	2026-06-21 19:24:27	8
22	Rappel : votre réservation à Marseille est dans 2 jours.	reservation_reminder	t	2026-05-30 08:00:00	10
\.


--
-- Data for Name: quote; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.quote (id, requested_start_date, requested_end_date, event_city, estimated_amount, status, created_at, valid_until, user_id) FROM stdin;
11	2026-09-01	2026-09-03	Nice	2040.00	pending	2026-06-21 19:24:27	2026-07-06	10
12	2026-10-05	2026-10-06	Lille	1800.00	approved	2026-06-21 19:24:27	2026-07-06	10
13	2026-04-10	2026-04-12	Toulouse	225.00	expired	2026-03-20 00:00:00	2026-04-04	10
\.


--
-- Data for Name: quote_line; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.quote_line (id, quantity, unit_price_per_day, quote_id, equipment_id) FROM stdin;
13	4	150.00	11	11
14	1	80.00	11	13
15	2	350.00	12	20
16	1	200.00	12	17
17	3	25.00	13	12
\.


--
-- Data for Name: reservation_line; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.reservation_line (id, quantity, unit_price_per_day, reservation_id, equipment_id) FROM stdin;
13	2	150.00	7	11
14	2	25.00	7	12
15	1	80.00	7	13
16	1	90.00	8	14
17	3	60.00	8	15
18	1	120.00	8	16
19	1	200.00	9	17
20	1	120.00	9	19
21	1	150.00	10	18
22	1	350.00	10	20
23	1	200.00	11	17
\.


--
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.review (id, rating, comment, created_at, user_id, equipment_id) FROM stdin;
5	5	Excellent son, très fiable. Utilisé pour un mariage, la sono était parfaite.	2026-06-21 19:24:27	10	11
6	4	Bonne qualité audio, un léger souffle en fin de soirée mais rien de gênant.	2026-06-21 19:24:27	10	12
7	5	Console très intuitive, les compresseurs intégrés font la différence sur les voix.	2026-06-21 19:24:27	10	13
8	4	Très bon projecteur, image nette même en salle éclairée. Un peu lourd à installer.	2026-06-21 19:24:27	11	17
\.


--
-- Data for Name: video_equipment; Type: TABLE DATA; Schema: public; Owner: eventrent
--

COPY public.video_equipment (resolution, brightness_lumens, projection_type, id) FROM stdin;
3840x2160	2600	LCD	17
3840x2160	4000	DLP	18
3840x2160	3000	DLP	19
1920x1200	6000	Laser	20
\.


--
-- Name: accessory_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.accessory_id_seq', 12, true);


--
-- Name: category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.category_id_seq', 10, true);


--
-- Name: equipment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.equipment_id_seq', 20, true);


--
-- Name: invoice_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.invoice_id_seq', 5, true);


--
-- Name: maintenance_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.maintenance_id_seq', 6, true);


--
-- Name: notification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.notification_id_seq', 22, true);


--
-- Name: quote_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.quote_id_seq', 13, true);


--
-- Name: quote_line_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.quote_line_id_seq', 17, true);


--
-- Name: reservation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.reservation_id_seq', 11, true);


--
-- Name: reservation_line_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.reservation_line_id_seq', 23, true);


--
-- Name: review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.review_id_seq', 8, true);


--
-- Name: supplier_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.supplier_id_seq', 6, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: eventrent
--

SELECT pg_catalog.setval('public.user_id_seq', 13, true);


--
-- PostgreSQL database dump complete
--

\unrestrict BxuXqkBYWnp6ZQt7jGU8Q28CFdjWAFKmnBKl56MZKTcMz2kHjA84BnHjhcpPbiZ

