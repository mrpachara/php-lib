--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.0
-- Dumped by pg_dump version 9.5.0

-- Started on 2016-02-12 05:31:10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 6 (class 2615 OID 49162)
-- Name: auth; Type: SCHEMA; Schema: -; Owner: startup
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO startup;

--
-- TOC entry 7 (class 2615 OID 49163)
-- Name: sys; Type: SCHEMA; Schema: -; Owner: startup
--

CREATE SCHEMA sys;


ALTER SCHEMA sys OWNER TO startup;

--
-- TOC entry 210 (class 3079 OID 12355)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2299 (class 0 OID 0)
-- Dependencies: 210
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = auth, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 182 (class 1259 OID 49164)
-- Name: accesstoken; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE accesstoken (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL
);


ALTER TABLE accesstoken OWNER TO startup;

--
-- TOC entry 183 (class 1259 OID 49167)
-- Name: accesstoken_id_seq; Type: SEQUENCE; Schema: auth; Owner: startup
--

CREATE SEQUENCE accesstoken_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE accesstoken_id_seq OWNER TO startup;

--
-- TOC entry 2300 (class 0 OID 0)
-- Dependencies: 183
-- Name: accesstoken_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE accesstoken_id_seq OWNED BY accesstoken.id;


--
-- TOC entry 184 (class 1259 OID 49169)
-- Name: account; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE account (
    id bigint NOT NULL,
    code character varying(128) NOT NULL,
    client_id character varying(64) NOT NULL,
    user_id character varying(64),
    expires timestamp with time zone NOT NULL
);


ALTER TABLE account OWNER TO startup;

--
-- TOC entry 185 (class 1259 OID 49172)
-- Name: account_id_seq; Type: SEQUENCE; Schema: auth; Owner: startup
--

CREATE SEQUENCE account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE account_id_seq OWNER TO startup;

--
-- TOC entry 2301 (class 0 OID 0)
-- Dependencies: 185
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


--
-- TOC entry 186 (class 1259 OID 49174)
-- Name: accountscope; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE accountscope (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL,
    scope character varying(64) NOT NULL
);


ALTER TABLE accountscope OWNER TO startup;

--
-- TOC entry 187 (class 1259 OID 49177)
-- Name: accountscope_id_seq; Type: SEQUENCE; Schema: auth; Owner: startup
--

CREATE SEQUENCE accountscope_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE accountscope_id_seq OWNER TO startup;

--
-- TOC entry 2302 (class 0 OID 0)
-- Dependencies: 187
-- Name: accountscope_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE accountscope_id_seq OWNED BY accountscope.id;


--
-- TOC entry 188 (class 1259 OID 49179)
-- Name: authzcode; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE authzcode (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL,
    redirect_uri character varying(256)
);


ALTER TABLE authzcode OWNER TO startup;

--
-- TOC entry 189 (class 1259 OID 49182)
-- Name: authzcode_id_seq; Type: SEQUENCE; Schema: auth; Owner: startup
--

CREATE SEQUENCE authzcode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE authzcode_id_seq OWNER TO startup;

--
-- TOC entry 2303 (class 0 OID 0)
-- Dependencies: 189
-- Name: authzcode_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE authzcode_id_seq OWNED BY authzcode.id;


--
-- TOC entry 190 (class 1259 OID 49184)
-- Name: refreshtoken; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE refreshtoken (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL
);


ALTER TABLE refreshtoken OWNER TO startup;

--
-- TOC entry 191 (class 1259 OID 49187)
-- Name: refreshtoken_id_seq; Type: SEQUENCE; Schema: auth; Owner: startup
--

CREATE SEQUENCE refreshtoken_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE refreshtoken_id_seq OWNER TO startup;

--
-- TOC entry 2304 (class 0 OID 0)
-- Dependencies: 191
-- Name: refreshtoken_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE refreshtoken_id_seq OWNED BY refreshtoken.id;


SET search_path = public, pg_catalog;

--
-- TOC entry 192 (class 1259 OID 49189)
-- Name: account; Type: TABLE; Schema: public; Owner: startup
--

CREATE TABLE account (
    id bigint NOT NULL,
    code character varying(64) NOT NULL,
    name character varying(256)
);


ALTER TABLE account OWNER TO startup;

--
-- TOC entry 193 (class 1259 OID 49192)
-- Name: account_id_seq; Type: SEQUENCE; Schema: public; Owner: startup
--

CREATE SEQUENCE account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE account_id_seq OWNER TO startup;

--
-- TOC entry 2305 (class 0 OID 0)
-- Dependencies: 193
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


SET search_path = sys, pg_catalog;

--
-- TOC entry 194 (class 1259 OID 49194)
-- Name: account; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE account (
    id bigint NOT NULL,
    id_account bigint NOT NULL
);


ALTER TABLE account OWNER TO startup;

--
-- TOC entry 195 (class 1259 OID 49197)
-- Name: account_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE account_id_seq OWNER TO startup;

--
-- TOC entry 2306 (class 0 OID 0)
-- Dependencies: 195
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


--
-- TOC entry 196 (class 1259 OID 49199)
-- Name: accountrole; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE accountrole (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    role character varying(64) NOT NULL
);


ALTER TABLE accountrole OWNER TO startup;

--
-- TOC entry 197 (class 1259 OID 49202)
-- Name: accountrole_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE accountrole_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE accountrole_id_seq OWNER TO startup;

--
-- TOC entry 2307 (class 0 OID 0)
-- Dependencies: 197
-- Name: accountrole_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE accountrole_id_seq OWNED BY accountrole.id;


--
-- TOC entry 198 (class 1259 OID 49204)
-- Name: client; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE client (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    secret character varying(128)
);


ALTER TABLE client OWNER TO startup;

--
-- TOC entry 199 (class 1259 OID 49207)
-- Name: client_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE client_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client_id_seq OWNER TO startup;

--
-- TOC entry 2308 (class 0 OID 0)
-- Dependencies: 199
-- Name: client_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE client_id_seq OWNED BY client.id;


--
-- TOC entry 200 (class 1259 OID 49209)
-- Name: clientgranttype; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE clientgranttype (
    id bigint NOT NULL,
    id_sys_client bigint NOT NULL,
    granttype character varying(64) NOT NULL
);


ALTER TABLE clientgranttype OWNER TO startup;

--
-- TOC entry 201 (class 1259 OID 49212)
-- Name: clientgranttype_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE clientgranttype_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE clientgranttype_id_seq OWNER TO startup;

--
-- TOC entry 2309 (class 0 OID 0)
-- Dependencies: 201
-- Name: clientgranttype_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE clientgranttype_id_seq OWNED BY clientgranttype.id;


--
-- TOC entry 202 (class 1259 OID 49214)
-- Name: clientredirecturi; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE clientredirecturi (
    id bigint NOT NULL,
    id_sys_client bigint NOT NULL,
    uri character varying(256) NOT NULL
);


ALTER TABLE clientredirecturi OWNER TO startup;

--
-- TOC entry 203 (class 1259 OID 49217)
-- Name: clientredirecturl_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE clientredirecturl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE clientredirecturl_id_seq OWNER TO startup;

--
-- TOC entry 2310 (class 0 OID 0)
-- Dependencies: 203
-- Name: clientredirecturl_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE clientredirecturl_id_seq OWNED BY clientredirecturi.id;


--
-- TOC entry 204 (class 1259 OID 49219)
-- Name: group; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE "group" (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL
);


ALTER TABLE "group" OWNER TO startup;

--
-- TOC entry 205 (class 1259 OID 49222)
-- Name: group_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE group_id_seq OWNER TO startup;

--
-- TOC entry 2311 (class 0 OID 0)
-- Dependencies: 205
-- Name: group_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE group_id_seq OWNED BY "group".id;


--
-- TOC entry 206 (class 1259 OID 49224)
-- Name: groupaccount; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE groupaccount (
    id bigint NOT NULL,
    id_sys_group bigint NOT NULL,
    id_sys_account bigint NOT NULL
);


ALTER TABLE groupaccount OWNER TO startup;

--
-- TOC entry 207 (class 1259 OID 49227)
-- Name: groupaccount_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE groupaccount_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE groupaccount_id_seq OWNER TO startup;

--
-- TOC entry 2312 (class 0 OID 0)
-- Dependencies: 207
-- Name: groupaccount_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE groupaccount_id_seq OWNED BY groupaccount.id;


--
-- TOC entry 208 (class 1259 OID 49229)
-- Name: user; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE "user" (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    password character varying(128)
);


ALTER TABLE "user" OWNER TO startup;

--
-- TOC entry 209 (class 1259 OID 49232)
-- Name: user_id_seq; Type: SEQUENCE; Schema: sys; Owner: startup
--

CREATE SEQUENCE user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_id_seq OWNER TO startup;

--
-- TOC entry 2313 (class 0 OID 0)
-- Dependencies: 209
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE user_id_seq OWNED BY "user".id;


SET search_path = auth, pg_catalog;

--
-- TOC entry 2061 (class 2604 OID 49234)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken ALTER COLUMN id SET DEFAULT nextval('accesstoken_id_seq'::regclass);


--
-- TOC entry 2062 (class 2604 OID 49235)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


--
-- TOC entry 2063 (class 2604 OID 49236)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope ALTER COLUMN id SET DEFAULT nextval('accountscope_id_seq'::regclass);


--
-- TOC entry 2064 (class 2604 OID 49237)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode ALTER COLUMN id SET DEFAULT nextval('authzcode_id_seq'::regclass);


--
-- TOC entry 2065 (class 2604 OID 49238)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken ALTER COLUMN id SET DEFAULT nextval('refreshtoken_id_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- TOC entry 2066 (class 2604 OID 49239)
-- Name: id; Type: DEFAULT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2067 (class 2604 OID 49240)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


--
-- TOC entry 2068 (class 2604 OID 49241)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole ALTER COLUMN id SET DEFAULT nextval('accountrole_id_seq'::regclass);


--
-- TOC entry 2069 (class 2604 OID 49242)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client ALTER COLUMN id SET DEFAULT nextval('client_id_seq'::regclass);


--
-- TOC entry 2070 (class 2604 OID 49243)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype ALTER COLUMN id SET DEFAULT nextval('clientgranttype_id_seq'::regclass);


--
-- TOC entry 2071 (class 2604 OID 49244)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi ALTER COLUMN id SET DEFAULT nextval('clientredirecturl_id_seq'::regclass);


--
-- TOC entry 2072 (class 2604 OID 49245)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group" ALTER COLUMN id SET DEFAULT nextval('group_id_seq'::regclass);


--
-- TOC entry 2073 (class 2604 OID 49246)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount ALTER COLUMN id SET DEFAULT nextval('groupaccount_id_seq'::regclass);


--
-- TOC entry 2074 (class 2604 OID 49247)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user" ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2264 (class 0 OID 49164)
-- Dependencies: 182
-- Data for Name: accesstoken; Type: TABLE DATA; Schema: auth; Owner: startup
--

COPY accesstoken (id, id_auth_account) FROM stdin;
24	49
25	51
\.


--
-- TOC entry 2314 (class 0 OID 0)
-- Dependencies: 183
-- Name: accesstoken_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: startup
--

SELECT pg_catalog.setval('accesstoken_id_seq', 25, true);


--
-- TOC entry 2266 (class 0 OID 49169)
-- Dependencies: 184
-- Data for Name: account; Type: TABLE DATA; Schema: auth; Owner: startup
--

COPY account (id, code, client_id, user_id, expires) FROM stdin;
49	bed6203dc0d7b126f86dc0f68effd22ad2651884	web_client	admin	2016-02-12 05:40:42+07
51	d2d36c6717bc4ce8256065009f465b3be13d2731	web_client	admin	2016-02-12 05:41:19+07
52	28ee9885706c20f33c17b80b035dc6e7cf15b163	web_client	admin	2016-02-26 04:41:19+07
\.


--
-- TOC entry 2315 (class 0 OID 0)
-- Dependencies: 185
-- Name: account_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: startup
--

SELECT pg_catalog.setval('account_id_seq', 52, true);


--
-- TOC entry 2268 (class 0 OID 49174)
-- Dependencies: 186
-- Data for Name: accountscope; Type: TABLE DATA; Schema: auth; Owner: startup
--

COPY accountscope (id, id_auth_account, scope) FROM stdin;
42	49	ADMIN
44	51	ADMIN
45	52	ADMIN
\.


--
-- TOC entry 2316 (class 0 OID 0)
-- Dependencies: 187
-- Name: accountscope_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: startup
--

SELECT pg_catalog.setval('accountscope_id_seq', 45, true);


--
-- TOC entry 2270 (class 0 OID 49179)
-- Dependencies: 188
-- Data for Name: authzcode; Type: TABLE DATA; Schema: auth; Owner: startup
--

COPY authzcode (id, id_auth_account, redirect_uri) FROM stdin;
\.


--
-- TOC entry 2317 (class 0 OID 0)
-- Dependencies: 189
-- Name: authzcode_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: startup
--

SELECT pg_catalog.setval('authzcode_id_seq', 1, false);


--
-- TOC entry 2272 (class 0 OID 49184)
-- Dependencies: 190
-- Data for Name: refreshtoken; Type: TABLE DATA; Schema: auth; Owner: startup
--

COPY refreshtoken (id, id_auth_account) FROM stdin;
21	52
\.


--
-- TOC entry 2318 (class 0 OID 0)
-- Dependencies: 191
-- Name: refreshtoken_id_seq; Type: SEQUENCE SET; Schema: auth; Owner: startup
--

SELECT pg_catalog.setval('refreshtoken_id_seq', 21, true);


SET search_path = public, pg_catalog;

--
-- TOC entry 2274 (class 0 OID 49189)
-- Dependencies: 192
-- Data for Name: account; Type: TABLE DATA; Schema: public; Owner: startup
--

COPY account (id, code, name) FROM stdin;
1	root	Root
2	client	Client
4	admin	Administrator
5	administrators	Administrators
6	users	Users
3	web_client	Web Client
\.


--
-- TOC entry 2319 (class 0 OID 0)
-- Dependencies: 193
-- Name: account_id_seq; Type: SEQUENCE SET; Schema: public; Owner: startup
--

SELECT pg_catalog.setval('account_id_seq', 6, true);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2276 (class 0 OID 49194)
-- Dependencies: 194
-- Data for Name: account; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY account (id, id_account) FROM stdin;
1	1
2	2
3	3
4	4
5	5
6	6
\.


--
-- TOC entry 2320 (class 0 OID 0)
-- Dependencies: 195
-- Name: account_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('account_id_seq', 6, true);


--
-- TOC entry 2278 (class 0 OID 49199)
-- Dependencies: 196
-- Data for Name: accountrole; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY accountrole (id, id_sys_account, role) FROM stdin;
1	4	ADMIN
2	5	ADMIN
3	6	USER
\.


--
-- TOC entry 2321 (class 0 OID 0)
-- Dependencies: 197
-- Name: accountrole_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('accountrole_id_seq', 3, true);


--
-- TOC entry 2280 (class 0 OID 49204)
-- Dependencies: 198
-- Data for Name: client; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY client (id, id_sys_account, secret) FROM stdin;
1	2	1234
2	3	\N
\.


--
-- TOC entry 2322 (class 0 OID 0)
-- Dependencies: 199
-- Name: client_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('client_id_seq', 2, true);


--
-- TOC entry 2282 (class 0 OID 49209)
-- Dependencies: 200
-- Data for Name: clientgranttype; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY clientgranttype (id, id_sys_client, granttype) FROM stdin;
\.


--
-- TOC entry 2323 (class 0 OID 0)
-- Dependencies: 201
-- Name: clientgranttype_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('clientgranttype_id_seq', 1, false);


--
-- TOC entry 2284 (class 0 OID 49214)
-- Dependencies: 202
-- Data for Name: clientredirecturi; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY clientredirecturi (id, id_sys_client, uri) FROM stdin;
\.


--
-- TOC entry 2324 (class 0 OID 0)
-- Dependencies: 203
-- Name: clientredirecturl_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('clientredirecturl_id_seq', 1, false);


--
-- TOC entry 2286 (class 0 OID 49219)
-- Dependencies: 204
-- Data for Name: group; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY "group" (id, id_sys_account) FROM stdin;
1	5
2	6
\.


--
-- TOC entry 2325 (class 0 OID 0)
-- Dependencies: 205
-- Name: group_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('group_id_seq', 2, true);


--
-- TOC entry 2288 (class 0 OID 49224)
-- Dependencies: 206
-- Data for Name: groupaccount; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY groupaccount (id, id_sys_group, id_sys_account) FROM stdin;
\.


--
-- TOC entry 2326 (class 0 OID 0)
-- Dependencies: 207
-- Name: groupaccount_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('groupaccount_id_seq', 1, false);


--
-- TOC entry 2290 (class 0 OID 49229)
-- Dependencies: 208
-- Data for Name: user; Type: TABLE DATA; Schema: sys; Owner: startup
--

COPY "user" (id, id_sys_account, password) FROM stdin;
1	1	$2y$10$MDZ9TRGTC6jgQKmFLE9hCe.udxDwfhlwffp4Ly6d5TFFf00OJiBKC
2	4	$2y$10$vJbY2bpCL36Y5EXNA9ut.eftfXtZEV7lft8dsJ4wEbjkyXt3TPdTe
\.


--
-- TOC entry 2327 (class 0 OID 0)
-- Dependencies: 209
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: sys; Owner: startup
--

SELECT pg_catalog.setval('user_id_seq', 2, true);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2076 (class 2606 OID 49249)
-- Name: accesstoken_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2078 (class 2606 OID 49251)
-- Name: accesstoken_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_pkey PRIMARY KEY (id);


--
-- TOC entry 2081 (class 2606 OID 49253)
-- Name: account_code_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_code_key UNIQUE (code);


--
-- TOC entry 2084 (class 2606 OID 49255)
-- Name: account_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


--
-- TOC entry 2087 (class 2606 OID 49257)
-- Name: accountscope_id_auth_account_scope_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_id_auth_account_scope_key UNIQUE (id_auth_account, scope);


--
-- TOC entry 2089 (class 2606 OID 49259)
-- Name: accountscope_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_pkey PRIMARY KEY (id);


--
-- TOC entry 2091 (class 2606 OID 49261)
-- Name: authzcode_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2093 (class 2606 OID 49263)
-- Name: authzcode_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_pkey PRIMARY KEY (id);


--
-- TOC entry 2095 (class 2606 OID 49265)
-- Name: refreshtoken_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2097 (class 2606 OID 49267)
-- Name: refreshtoken_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_pkey PRIMARY KEY (id);


SET search_path = public, pg_catalog;

--
-- TOC entry 2099 (class 2606 OID 49269)
-- Name: account_code_key; Type: CONSTRAINT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_code_key UNIQUE (code);


--
-- TOC entry 2101 (class 2606 OID 49271)
-- Name: account_pkey; Type: CONSTRAINT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2103 (class 2606 OID 49273)
-- Name: account_id_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_id_account_key UNIQUE (id_account);


--
-- TOC entry 2105 (class 2606 OID 49275)
-- Name: account_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


--
-- TOC entry 2107 (class 2606 OID 49277)
-- Name: accountrole_id_sys_account_role_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_id_sys_account_role_key UNIQUE (id_sys_account, role);


--
-- TOC entry 2109 (class 2606 OID 49279)
-- Name: accountrole_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_pkey PRIMARY KEY (id);


--
-- TOC entry 2112 (class 2606 OID 49281)
-- Name: client_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2114 (class 2606 OID 49283)
-- Name: client_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id);


--
-- TOC entry 2117 (class 2606 OID 49285)
-- Name: clientgranttype_id_sys_client_granttype_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_id_sys_client_granttype_key UNIQUE (id_sys_client, granttype);


--
-- TOC entry 2119 (class 2606 OID 49287)
-- Name: clientgranttype_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_pkey PRIMARY KEY (id);


--
-- TOC entry 2121 (class 2606 OID 49289)
-- Name: clientredirecturi_id_sys_client_uri_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturi_id_sys_client_uri_key UNIQUE (id_sys_client, uri);


--
-- TOC entry 2123 (class 2606 OID 49291)
-- Name: clientredirecturl_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturl_pkey PRIMARY KEY (id);


--
-- TOC entry 2125 (class 2606 OID 49293)
-- Name: group_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2127 (class 2606 OID 49295)
-- Name: group_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_pkey PRIMARY KEY (id);


--
-- TOC entry 2130 (class 2606 OID 49297)
-- Name: groupaccount_id_sys_group_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_group_id_sys_account_key UNIQUE (id_sys_group, id_sys_account);


--
-- TOC entry 2132 (class 2606 OID 49299)
-- Name: groupaccount_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_pkey PRIMARY KEY (id);


--
-- TOC entry 2134 (class 2606 OID 49301)
-- Name: user_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2136 (class 2606 OID 49303)
-- Name: user_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2079 (class 1259 OID 49305)
-- Name: account_client_id_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_client_id_idx ON account USING btree (client_id);


--
-- TOC entry 2082 (class 1259 OID 49306)
-- Name: account_expires_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_expires_idx ON account USING btree (expires);


--
-- TOC entry 2085 (class 1259 OID 49307)
-- Name: account_user_id_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_user_id_idx ON account USING btree (user_id);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2110 (class 1259 OID 49308)
-- Name: accountrole_role_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX accountrole_role_idx ON accountrole USING btree (role);


--
-- TOC entry 2115 (class 1259 OID 49309)
-- Name: clientgranttype_granttype_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX clientgranttype_granttype_idx ON clientgranttype USING btree (granttype);


--
-- TOC entry 2128 (class 1259 OID 49310)
-- Name: groupaccount_id_sys_account_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX groupaccount_id_sys_account_idx ON groupaccount USING btree (id_sys_account);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2137 (class 2606 OID 49311)
-- Name: accesstoken_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2138 (class 2606 OID 49316)
-- Name: accountscope_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2139 (class 2606 OID 49321)
-- Name: authzcode_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2140 (class 2606 OID 49326)
-- Name: refreshtoken_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


SET search_path = sys, pg_catalog;

--
-- TOC entry 2141 (class 2606 OID 49331)
-- Name: account_id_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_id_account_fkey FOREIGN KEY (id_account) REFERENCES public.account(id) ON DELETE CASCADE;


--
-- TOC entry 2142 (class 2606 OID 49336)
-- Name: accountrole_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2143 (class 2606 OID 49341)
-- Name: client_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2144 (class 2606 OID 49346)
-- Name: clientgranttype_id_sys_client_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_id_sys_client_fkey FOREIGN KEY (id_sys_client) REFERENCES client(id_sys_account) ON DELETE CASCADE;


--
-- TOC entry 2145 (class 2606 OID 49351)
-- Name: clientredirecturl_id_sys_client_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturl_id_sys_client_fkey FOREIGN KEY (id_sys_client) REFERENCES client(id_sys_account) ON DELETE CASCADE;


--
-- TOC entry 2146 (class 2606 OID 49356)
-- Name: group_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2147 (class 2606 OID 49361)
-- Name: groupaccount_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2148 (class 2606 OID 49366)
-- Name: groupaccount_id_sys_group_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_group_fkey FOREIGN KEY (id_sys_group) REFERENCES "group"(id) ON DELETE CASCADE;


--
-- TOC entry 2149 (class 2606 OID 49371)
-- Name: user_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2298 (class 0 OID 0)
-- Dependencies: 8
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2016-02-12 05:31:10

--
-- PostgreSQL database dump complete
--

