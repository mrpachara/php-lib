--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.0
-- Dumped by pg_dump version 9.5.0

-- Started on 2016-02-24 16:33:17

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
-- TOC entry 9 (class 2615 OID 57353)
-- Name: stub; Type: SCHEMA; Schema: -; Owner: startup
--

CREATE SCHEMA stub;


ALTER SCHEMA stub OWNER TO startup;

--
-- TOC entry 7 (class 2615 OID 49163)
-- Name: sys; Type: SCHEMA; Schema: -; Owner: startup
--

CREATE SCHEMA sys;


ALTER SCHEMA sys OWNER TO startup;

--
-- TOC entry 213 (class 3079 OID 12355)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 213
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = auth, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 183 (class 1259 OID 49164)
-- Name: accesstoken; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE accesstoken (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL
);


ALTER TABLE accesstoken OWNER TO startup;

--
-- TOC entry 184 (class 1259 OID 49167)
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
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 184
-- Name: accesstoken_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE accesstoken_id_seq OWNED BY accesstoken.id;


--
-- TOC entry 185 (class 1259 OID 49169)
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
-- TOC entry 186 (class 1259 OID 49172)
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
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 186
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


--
-- TOC entry 187 (class 1259 OID 49174)
-- Name: accountscope; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE accountscope (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL,
    scope character varying(64) NOT NULL
);


ALTER TABLE accountscope OWNER TO startup;

--
-- TOC entry 188 (class 1259 OID 49177)
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
-- TOC entry 2285 (class 0 OID 0)
-- Dependencies: 188
-- Name: accountscope_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE accountscope_id_seq OWNED BY accountscope.id;


--
-- TOC entry 189 (class 1259 OID 49179)
-- Name: authzcode; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE authzcode (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL,
    redirect_uri character varying(256)
);


ALTER TABLE authzcode OWNER TO startup;

--
-- TOC entry 190 (class 1259 OID 49182)
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
-- TOC entry 2286 (class 0 OID 0)
-- Dependencies: 190
-- Name: authzcode_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE authzcode_id_seq OWNED BY authzcode.id;


--
-- TOC entry 191 (class 1259 OID 49184)
-- Name: refreshtoken; Type: TABLE; Schema: auth; Owner: startup
--

CREATE TABLE refreshtoken (
    id bigint NOT NULL,
    id_auth_account bigint NOT NULL
);


ALTER TABLE refreshtoken OWNER TO startup;

--
-- TOC entry 192 (class 1259 OID 49187)
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
-- TOC entry 2287 (class 0 OID 0)
-- Dependencies: 192
-- Name: refreshtoken_id_seq; Type: SEQUENCE OWNED BY; Schema: auth; Owner: startup
--

ALTER SEQUENCE refreshtoken_id_seq OWNED BY refreshtoken.id;


SET search_path = public, pg_catalog;

--
-- TOC entry 193 (class 1259 OID 49189)
-- Name: account; Type: TABLE; Schema: public; Owner: startup
--

CREATE TABLE account (
    id bigint NOT NULL,
    code character varying(64) NOT NULL,
    name character varying(256)
);


ALTER TABLE account OWNER TO startup;

--
-- TOC entry 194 (class 1259 OID 49192)
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
-- TOC entry 2288 (class 0 OID 0)
-- Dependencies: 194
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


SET search_path = stub, pg_catalog;

--
-- TOC entry 212 (class 1259 OID 57356)
-- Name: data01; Type: TABLE; Schema: stub; Owner: startup
--

CREATE TABLE data01 (
    id bigint NOT NULL,
    data jsonb
);


ALTER TABLE data01 OWNER TO startup;

--
-- TOC entry 211 (class 1259 OID 57354)
-- Name: data01_id_seq; Type: SEQUENCE; Schema: stub; Owner: startup
--

CREATE SEQUENCE data01_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE data01_id_seq OWNER TO startup;

--
-- TOC entry 2289 (class 0 OID 0)
-- Dependencies: 211
-- Name: data01_id_seq; Type: SEQUENCE OWNED BY; Schema: stub; Owner: startup
--

ALTER SEQUENCE data01_id_seq OWNED BY data01.id;


SET search_path = sys, pg_catalog;

--
-- TOC entry 195 (class 1259 OID 49194)
-- Name: account; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE account (
    id bigint NOT NULL,
    id_account bigint NOT NULL
);


ALTER TABLE account OWNER TO startup;

--
-- TOC entry 196 (class 1259 OID 49197)
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
-- TOC entry 2290 (class 0 OID 0)
-- Dependencies: 196
-- Name: account_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE account_id_seq OWNED BY account.id;


--
-- TOC entry 197 (class 1259 OID 49199)
-- Name: accountrole; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE accountrole (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    role character varying(64) NOT NULL
);


ALTER TABLE accountrole OWNER TO startup;

--
-- TOC entry 198 (class 1259 OID 49202)
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
-- TOC entry 2291 (class 0 OID 0)
-- Dependencies: 198
-- Name: accountrole_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE accountrole_id_seq OWNED BY accountrole.id;


--
-- TOC entry 199 (class 1259 OID 49204)
-- Name: client; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE client (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    secret character varying(128)
);


ALTER TABLE client OWNER TO startup;

--
-- TOC entry 200 (class 1259 OID 49207)
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
-- TOC entry 2292 (class 0 OID 0)
-- Dependencies: 200
-- Name: client_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE client_id_seq OWNED BY client.id;


--
-- TOC entry 201 (class 1259 OID 49209)
-- Name: clientgranttype; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE clientgranttype (
    id bigint NOT NULL,
    id_sys_client bigint NOT NULL,
    granttype character varying(64) NOT NULL
);


ALTER TABLE clientgranttype OWNER TO startup;

--
-- TOC entry 202 (class 1259 OID 49212)
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
-- TOC entry 2293 (class 0 OID 0)
-- Dependencies: 202
-- Name: clientgranttype_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE clientgranttype_id_seq OWNED BY clientgranttype.id;


--
-- TOC entry 203 (class 1259 OID 49214)
-- Name: clientredirecturi; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE clientredirecturi (
    id bigint NOT NULL,
    id_sys_client bigint NOT NULL,
    uri character varying(256) NOT NULL
);


ALTER TABLE clientredirecturi OWNER TO startup;

--
-- TOC entry 204 (class 1259 OID 49217)
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
-- TOC entry 2294 (class 0 OID 0)
-- Dependencies: 204
-- Name: clientredirecturl_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE clientredirecturl_id_seq OWNED BY clientredirecturi.id;


--
-- TOC entry 205 (class 1259 OID 49219)
-- Name: group; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE "group" (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL
);


ALTER TABLE "group" OWNER TO startup;

--
-- TOC entry 206 (class 1259 OID 49222)
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
-- TOC entry 2295 (class 0 OID 0)
-- Dependencies: 206
-- Name: group_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE group_id_seq OWNED BY "group".id;


--
-- TOC entry 207 (class 1259 OID 49224)
-- Name: groupaccount; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE groupaccount (
    id bigint NOT NULL,
    id_sys_group bigint NOT NULL,
    id_sys_account bigint NOT NULL
);


ALTER TABLE groupaccount OWNER TO startup;

--
-- TOC entry 208 (class 1259 OID 49227)
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
-- TOC entry 2296 (class 0 OID 0)
-- Dependencies: 208
-- Name: groupaccount_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE groupaccount_id_seq OWNED BY groupaccount.id;


--
-- TOC entry 209 (class 1259 OID 49229)
-- Name: user; Type: TABLE; Schema: sys; Owner: startup
--

CREATE TABLE "user" (
    id bigint NOT NULL,
    id_sys_account bigint NOT NULL,
    password character varying(128)
);


ALTER TABLE "user" OWNER TO startup;

--
-- TOC entry 210 (class 1259 OID 49232)
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
-- TOC entry 2297 (class 0 OID 0)
-- Dependencies: 210
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: sys; Owner: startup
--

ALTER SEQUENCE user_id_seq OWNED BY "user".id;


SET search_path = auth, pg_catalog;

--
-- TOC entry 2069 (class 2604 OID 49234)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken ALTER COLUMN id SET DEFAULT nextval('accesstoken_id_seq'::regclass);


--
-- TOC entry 2070 (class 2604 OID 49235)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


--
-- TOC entry 2071 (class 2604 OID 49236)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope ALTER COLUMN id SET DEFAULT nextval('accountscope_id_seq'::regclass);


--
-- TOC entry 2072 (class 2604 OID 49237)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode ALTER COLUMN id SET DEFAULT nextval('authzcode_id_seq'::regclass);


--
-- TOC entry 2073 (class 2604 OID 49238)
-- Name: id; Type: DEFAULT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken ALTER COLUMN id SET DEFAULT nextval('refreshtoken_id_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- TOC entry 2074 (class 2604 OID 49239)
-- Name: id; Type: DEFAULT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


SET search_path = stub, pg_catalog;

--
-- TOC entry 2083 (class 2604 OID 57359)
-- Name: id; Type: DEFAULT; Schema: stub; Owner: startup
--

ALTER TABLE ONLY data01 ALTER COLUMN id SET DEFAULT nextval('data01_id_seq'::regclass);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2075 (class 2604 OID 49240)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account ALTER COLUMN id SET DEFAULT nextval('account_id_seq'::regclass);


--
-- TOC entry 2076 (class 2604 OID 49241)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole ALTER COLUMN id SET DEFAULT nextval('accountrole_id_seq'::regclass);


--
-- TOC entry 2077 (class 2604 OID 49242)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client ALTER COLUMN id SET DEFAULT nextval('client_id_seq'::regclass);


--
-- TOC entry 2078 (class 2604 OID 49243)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype ALTER COLUMN id SET DEFAULT nextval('clientgranttype_id_seq'::regclass);


--
-- TOC entry 2079 (class 2604 OID 49244)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi ALTER COLUMN id SET DEFAULT nextval('clientredirecturl_id_seq'::regclass);


--
-- TOC entry 2080 (class 2604 OID 49245)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group" ALTER COLUMN id SET DEFAULT nextval('group_id_seq'::regclass);


--
-- TOC entry 2081 (class 2604 OID 49246)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount ALTER COLUMN id SET DEFAULT nextval('groupaccount_id_seq'::regclass);


--
-- TOC entry 2082 (class 2604 OID 49247)
-- Name: id; Type: DEFAULT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user" ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2085 (class 2606 OID 49249)
-- Name: accesstoken_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2087 (class 2606 OID 49251)
-- Name: accesstoken_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_pkey PRIMARY KEY (id);


--
-- TOC entry 2090 (class 2606 OID 49253)
-- Name: account_code_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_code_key UNIQUE (code);


--
-- TOC entry 2093 (class 2606 OID 49255)
-- Name: account_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


--
-- TOC entry 2096 (class 2606 OID 49257)
-- Name: accountscope_id_auth_account_scope_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_id_auth_account_scope_key UNIQUE (id_auth_account, scope);


--
-- TOC entry 2098 (class 2606 OID 49259)
-- Name: accountscope_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_pkey PRIMARY KEY (id);


--
-- TOC entry 2100 (class 2606 OID 49261)
-- Name: authzcode_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2102 (class 2606 OID 49263)
-- Name: authzcode_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_pkey PRIMARY KEY (id);


--
-- TOC entry 2104 (class 2606 OID 49265)
-- Name: refreshtoken_id_auth_account_key; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_id_auth_account_key UNIQUE (id_auth_account);


--
-- TOC entry 2106 (class 2606 OID 49267)
-- Name: refreshtoken_pkey; Type: CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_pkey PRIMARY KEY (id);


SET search_path = public, pg_catalog;

--
-- TOC entry 2108 (class 2606 OID 49269)
-- Name: account_code_key; Type: CONSTRAINT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_code_key UNIQUE (code);


--
-- TOC entry 2110 (class 2606 OID 49271)
-- Name: account_pkey; Type: CONSTRAINT; Schema: public; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


SET search_path = stub, pg_catalog;

--
-- TOC entry 2147 (class 2606 OID 57364)
-- Name: data01_pkey; Type: CONSTRAINT; Schema: stub; Owner: startup
--

ALTER TABLE ONLY data01
    ADD CONSTRAINT data01_pkey PRIMARY KEY (id);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2112 (class 2606 OID 49273)
-- Name: account_id_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_id_account_key UNIQUE (id_account);


--
-- TOC entry 2114 (class 2606 OID 49275)
-- Name: account_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_pkey PRIMARY KEY (id);


--
-- TOC entry 2116 (class 2606 OID 49277)
-- Name: accountrole_id_sys_account_role_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_id_sys_account_role_key UNIQUE (id_sys_account, role);


--
-- TOC entry 2118 (class 2606 OID 49279)
-- Name: accountrole_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_pkey PRIMARY KEY (id);


--
-- TOC entry 2121 (class 2606 OID 49281)
-- Name: client_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2123 (class 2606 OID 49283)
-- Name: client_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_pkey PRIMARY KEY (id);


--
-- TOC entry 2126 (class 2606 OID 49285)
-- Name: clientgranttype_id_sys_client_granttype_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_id_sys_client_granttype_key UNIQUE (id_sys_client, granttype);


--
-- TOC entry 2128 (class 2606 OID 49287)
-- Name: clientgranttype_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_pkey PRIMARY KEY (id);


--
-- TOC entry 2130 (class 2606 OID 49289)
-- Name: clientredirecturi_id_sys_client_uri_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturi_id_sys_client_uri_key UNIQUE (id_sys_client, uri);


--
-- TOC entry 2132 (class 2606 OID 49291)
-- Name: clientredirecturl_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturl_pkey PRIMARY KEY (id);


--
-- TOC entry 2134 (class 2606 OID 49293)
-- Name: group_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2136 (class 2606 OID 49295)
-- Name: group_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_pkey PRIMARY KEY (id);


--
-- TOC entry 2139 (class 2606 OID 49297)
-- Name: groupaccount_id_sys_group_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_group_id_sys_account_key UNIQUE (id_sys_group, id_sys_account);


--
-- TOC entry 2141 (class 2606 OID 49299)
-- Name: groupaccount_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_pkey PRIMARY KEY (id);


--
-- TOC entry 2143 (class 2606 OID 49301)
-- Name: user_id_sys_account_key; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_id_sys_account_key UNIQUE (id_sys_account);


--
-- TOC entry 2145 (class 2606 OID 49303)
-- Name: user_pkey; Type: CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2088 (class 1259 OID 49305)
-- Name: account_client_id_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_client_id_idx ON account USING btree (client_id);


--
-- TOC entry 2091 (class 1259 OID 49306)
-- Name: account_expires_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_expires_idx ON account USING btree (expires);


--
-- TOC entry 2094 (class 1259 OID 49307)
-- Name: account_user_id_idx; Type: INDEX; Schema: auth; Owner: startup
--

CREATE INDEX account_user_id_idx ON account USING btree (user_id);


SET search_path = sys, pg_catalog;

--
-- TOC entry 2119 (class 1259 OID 49308)
-- Name: accountrole_role_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX accountrole_role_idx ON accountrole USING btree (role);


--
-- TOC entry 2124 (class 1259 OID 49309)
-- Name: clientgranttype_granttype_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX clientgranttype_granttype_idx ON clientgranttype USING btree (granttype);


--
-- TOC entry 2137 (class 1259 OID 49310)
-- Name: groupaccount_id_sys_account_idx; Type: INDEX; Schema: sys; Owner: startup
--

CREATE INDEX groupaccount_id_sys_account_idx ON groupaccount USING btree (id_sys_account);


SET search_path = auth, pg_catalog;

--
-- TOC entry 2148 (class 2606 OID 49311)
-- Name: accesstoken_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accesstoken
    ADD CONSTRAINT accesstoken_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2149 (class 2606 OID 49316)
-- Name: accountscope_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY accountscope
    ADD CONSTRAINT accountscope_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2150 (class 2606 OID 49321)
-- Name: authzcode_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY authzcode
    ADD CONSTRAINT authzcode_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2151 (class 2606 OID 49326)
-- Name: refreshtoken_id_auth_account_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: startup
--

ALTER TABLE ONLY refreshtoken
    ADD CONSTRAINT refreshtoken_id_auth_account_fkey FOREIGN KEY (id_auth_account) REFERENCES account(id) ON DELETE CASCADE;


SET search_path = sys, pg_catalog;

--
-- TOC entry 2152 (class 2606 OID 49331)
-- Name: account_id_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY account
    ADD CONSTRAINT account_id_account_fkey FOREIGN KEY (id_account) REFERENCES public.account(id) ON DELETE CASCADE;


--
-- TOC entry 2153 (class 2606 OID 49336)
-- Name: accountrole_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY accountrole
    ADD CONSTRAINT accountrole_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2154 (class 2606 OID 49341)
-- Name: client_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY client
    ADD CONSTRAINT client_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2155 (class 2606 OID 49346)
-- Name: clientgranttype_id_sys_client_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientgranttype
    ADD CONSTRAINT clientgranttype_id_sys_client_fkey FOREIGN KEY (id_sys_client) REFERENCES client(id_sys_account) ON DELETE CASCADE;


--
-- TOC entry 2156 (class 2606 OID 49351)
-- Name: clientredirecturl_id_sys_client_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY clientredirecturi
    ADD CONSTRAINT clientredirecturl_id_sys_client_fkey FOREIGN KEY (id_sys_client) REFERENCES client(id_sys_account) ON DELETE CASCADE;


--
-- TOC entry 2157 (class 2606 OID 49356)
-- Name: group_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2158 (class 2606 OID 49361)
-- Name: groupaccount_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2159 (class 2606 OID 49366)
-- Name: groupaccount_id_sys_group_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY groupaccount
    ADD CONSTRAINT groupaccount_id_sys_group_fkey FOREIGN KEY (id_sys_group) REFERENCES "group"(id) ON DELETE CASCADE;


--
-- TOC entry 2160 (class 2606 OID 49371)
-- Name: user_id_sys_account_fkey; Type: FK CONSTRAINT; Schema: sys; Owner: startup
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_id_sys_account_fkey FOREIGN KEY (id_sys_account) REFERENCES account(id) ON DELETE CASCADE;


--
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 8
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2016-02-24 16:33:18

--
-- PostgreSQL database dump complete
--

