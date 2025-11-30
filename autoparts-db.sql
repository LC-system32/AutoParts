--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: addresses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addresses (
    id bigint NOT NULL,
    user_id bigint,
    full_name character varying(200),
    phone character varying(30),
    country character varying(100),
    region character varying(100),
    city character varying(100),
    postal_code character varying(20),
    street_address character varying(255),
    comment character varying(255)
);


ALTER TABLE public.addresses OWNER TO postgres;

--
-- Name: addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.addresses_id_seq OWNER TO postgres;

--
-- Name: addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.addresses_id_seq OWNED BY public.addresses.id;


--
-- Name: attribute_options; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attribute_options (
    id bigint NOT NULL,
    attribute_id bigint NOT NULL,
    value character varying(150) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.attribute_options OWNER TO postgres;

--
-- Name: attribute_options_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attribute_options_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attribute_options_id_seq OWNER TO postgres;

--
-- Name: attribute_options_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attribute_options_id_seq OWNED BY public.attribute_options.id;


--
-- Name: attributes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attributes (
    id bigint NOT NULL,
    code character varying(100) NOT NULL,
    name character varying(150) NOT NULL,
    unit character varying(50),
    data_type character varying(20) DEFAULT 'string'::character varying NOT NULL
);


ALTER TABLE public.attributes OWNER TO postgres;

--
-- Name: attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attributes_id_seq OWNER TO postgres;

--
-- Name: attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attributes_id_seq OWNED BY public.attributes.id;


--
-- Name: brands; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.brands (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL,
    logo character varying(255),
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.brands OWNER TO postgres;

--
-- Name: brands_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.brands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.brands_id_seq OWNER TO postgres;

--
-- Name: brands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.brands_id_seq OWNED BY public.brands.id;


--
-- Name: car_generations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.car_generations (
    id bigint NOT NULL,
    model_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    year_from smallint,
    year_to smallint
);


ALTER TABLE public.car_generations OWNER TO postgres;

--
-- Name: car_generations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.car_generations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.car_generations_id_seq OWNER TO postgres;

--
-- Name: car_generations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.car_generations_id_seq OWNED BY public.car_generations.id;


--
-- Name: car_makes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.car_makes (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.car_makes OWNER TO postgres;

--
-- Name: car_makes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.car_makes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.car_makes_id_seq OWNER TO postgres;

--
-- Name: car_makes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.car_makes_id_seq OWNED BY public.car_makes.id;


--
-- Name: car_models; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.car_models (
    id bigint NOT NULL,
    make_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.car_models OWNER TO postgres;

--
-- Name: car_models_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.car_models_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.car_models_id_seq OWNER TO postgres;

--
-- Name: car_models_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.car_models_id_seq OWNED BY public.car_models.id;


--
-- Name: car_modifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.car_modifications (
    id bigint NOT NULL,
    generation_id bigint NOT NULL,
    engine_code character varying(50),
    engine_volume numeric(4,1),
    power_hp smallint,
    fuel_type character varying(50),
    drive_type character varying(50),
    transmission character varying(50),
    year_from smallint,
    year_to smallint
);


ALTER TABLE public.car_modifications OWNER TO postgres;

--
-- Name: car_modifications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.car_modifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.car_modifications_id_seq OWNER TO postgres;

--
-- Name: car_modifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.car_modifications_id_seq OWNED BY public.car_modifications.id;


--
-- Name: cart_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cart_items (
    id bigint NOT NULL,
    cart_id bigint NOT NULL,
    product_id bigint NOT NULL,
    quantity numeric(10,2) NOT NULL,
    price numeric(12,2) NOT NULL,
    currency character(3) DEFAULT 'UAH'::bpchar NOT NULL
);


ALTER TABLE public.cart_items OWNER TO postgres;

--
-- Name: cart_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cart_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cart_items_id_seq OWNER TO postgres;

--
-- Name: cart_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cart_items_id_seq OWNED BY public.cart_items.id;


--
-- Name: carts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.carts (
    id bigint NOT NULL,
    user_id bigint,
    session_token character varying(255),
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.carts OWNER TO postgres;

--
-- Name: carts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.carts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.carts_id_seq OWNER TO postgres;

--
-- Name: carts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.carts_id_seq OWNED BY public.carts.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    parent_id bigint,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL,
    description text,
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: delivery_methods; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.delivery_methods (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    code character varying(50) NOT NULL,
    description text,
    base_price numeric(12,2),
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.delivery_methods OWNER TO postgres;

--
-- Name: delivery_methods_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.delivery_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.delivery_methods_id_seq OWNER TO postgres;

--
-- Name: delivery_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.delivery_methods_id_seq OWNED BY public.delivery_methods.id;


--
-- Name: discount_categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discount_categories (
    discount_id bigint NOT NULL,
    category_id bigint NOT NULL
);


ALTER TABLE public.discount_categories OWNER TO postgres;

--
-- Name: discount_products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discount_products (
    discount_id bigint NOT NULL,
    product_id bigint NOT NULL
);


ALTER TABLE public.discount_products OWNER TO postgres;

--
-- Name: discounts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discounts (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    discount_type character varying(20) NOT NULL,
    value numeric(10,2) NOT NULL,
    date_from timestamp with time zone,
    date_to timestamp with time zone,
    active boolean DEFAULT true NOT NULL,
    min_order_sum numeric(12,2),
    code text
);


ALTER TABLE public.discounts OWNER TO postgres;

--
-- Name: discounts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.discounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.discounts_id_seq OWNER TO postgres;

--
-- Name: discounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.discounts_id_seq OWNED BY public.discounts.id;


--
-- Name: order_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_items (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    product_id bigint,
    product_name character varying(255) NOT NULL,
    sku character varying(100),
    quantity numeric(10,2) NOT NULL,
    price numeric(12,2) NOT NULL,
    discount numeric(12,2) DEFAULT 0 NOT NULL,
    total numeric(12,2) NOT NULL,
    currency character(3) DEFAULT 'UAH'::bpchar NOT NULL
);


ALTER TABLE public.order_items OWNER TO postgres;

--
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_items_id_seq OWNER TO postgres;

--
-- Name: order_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.order_items_id_seq OWNED BY public.order_items.id;


--
-- Name: order_status_history; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_status_history (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    old_status character varying(50),
    new_status character varying(50) NOT NULL,
    changed_by bigint,
    comment character varying(255),
    changed_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.order_status_history OWNER TO postgres;

--
-- Name: order_status_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_status_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_status_history_id_seq OWNER TO postgres;

--
-- Name: order_status_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.order_status_history_id_seq OWNED BY public.order_status_history.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    order_number character varying(50) NOT NULL,
    user_id bigint,
    status_code character varying(50) NOT NULL,
    delivery_method_id bigint,
    payment_method_id bigint,
    shipping_address_id bigint,
    billing_address_id bigint,
    total_products numeric(12,2) NOT NULL,
    total_discount numeric(12,2) DEFAULT 0 NOT NULL,
    total_delivery numeric(12,2) DEFAULT 0 NOT NULL,
    total_amount numeric(12,2) NOT NULL,
    currency character(3) DEFAULT 'UAH'::bpchar NOT NULL,
    customer_comment text,
    manager_comment text,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_seq OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: payment_methods; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_methods (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    code character varying(50) NOT NULL,
    description text,
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.payment_methods OWNER TO postgres;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payment_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payment_methods_id_seq OWNER TO postgres;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payment_methods_id_seq OWNED BY public.payment_methods.id;


--
-- Name: payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payments (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    amount numeric(12,2) NOT NULL,
    currency character(3) DEFAULT 'UAH'::bpchar NOT NULL,
    provider character varying(100),
    provider_ref character varying(255),
    status character varying(50) NOT NULL,
    raw_response jsonb,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.payments OWNER TO postgres;

--
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payments_id_seq OWNER TO postgres;

--
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- Name: product_attribute_values; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_attribute_values (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    attribute_id bigint NOT NULL,
    value_string text,
    value_int bigint,
    value_decimal numeric(18,6),
    value_bool boolean,
    option_id bigint
);


ALTER TABLE public.product_attribute_values OWNER TO postgres;

--
-- Name: product_attribute_values_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_attribute_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_attribute_values_id_seq OWNER TO postgres;

--
-- Name: product_attribute_values_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_attribute_values_id_seq OWNED BY public.product_attribute_values.id;


--
-- Name: product_fitments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_fitments (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    modification_id bigint NOT NULL,
    note character varying(255)
);


ALTER TABLE public.product_fitments OWNER TO postgres;

--
-- Name: product_fitments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_fitments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_fitments_id_seq OWNER TO postgres;

--
-- Name: product_fitments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_fitments_id_seq OWNED BY public.product_fitments.id;


--
-- Name: product_images; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_images (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    image_url character varying(255) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    is_main boolean DEFAULT false NOT NULL
);


ALTER TABLE public.product_images OWNER TO postgres;

--
-- Name: product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_images_id_seq OWNER TO postgres;

--
-- Name: product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_images_id_seq OWNED BY public.product_images.id;


--
-- Name: product_offers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_offers (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    warehouse_id bigint NOT NULL,
    supplier_sku character varying(100),
    quantity numeric(10,2) DEFAULT 0 NOT NULL,
    available_from timestamp with time zone,
    delivery_days integer,
    base_price numeric(12,2) NOT NULL,
    sale_price numeric(12,2) NOT NULL,
    currency character(3) DEFAULT 'UAH'::bpchar NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.product_offers OWNER TO postgres;

--
-- Name: product_offers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_offers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_offers_id_seq OWNER TO postgres;

--
-- Name: product_offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_offers_id_seq OWNED BY public.product_offers.id;


--
-- Name: product_oil_specs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_oil_specs (
    product_id bigint NOT NULL,
    viscosity character varying(20),
    volume_l numeric(5,2),
    oil_type character varying(50)
);


ALTER TABLE public.product_oil_specs OWNER TO postgres;

--
-- Name: product_reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_reviews (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    user_id bigint,
    rating smallint NOT NULL,
    title character varying(255),
    body text,
    is_approved boolean DEFAULT false NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT product_reviews_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.product_reviews OWNER TO postgres;

--
-- Name: product_reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_reviews_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_reviews_id_seq OWNER TO postgres;

--
-- Name: product_reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_reviews_id_seq OWNED BY public.product_reviews.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    sku character varying(100) NOT NULL,
    oem_number character varying(100),
    brand_id bigint,
    category_id bigint,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    short_desc character varying(500),
    description text,
    weight_kg numeric(10,3),
    is_active boolean DEFAULT true NOT NULL,
    is_popular boolean DEFAULT false NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.products OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    name character varying(100) NOT NULL,
    description text
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: stock_movements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stock_movements (
    id bigint NOT NULL,
    product_offer_id bigint NOT NULL,
    quantity_delta numeric(10,2) NOT NULL,
    reason character varying(50) NOT NULL,
    related_order_id bigint,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    created_by bigint
);


ALTER TABLE public.stock_movements OWNER TO postgres;

--
-- Name: stock_movements_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stock_movements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.stock_movements_id_seq OWNER TO postgres;

--
-- Name: stock_movements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stock_movements_id_seq OWNED BY public.stock_movements.id;


--
-- Name: suppliers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.suppliers (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    code character varying(50) NOT NULL,
    api_url character varying(255),
    phone character varying(50),
    email character varying(100),
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.suppliers OWNER TO postgres;

--
-- Name: suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.suppliers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.suppliers_id_seq OWNER TO postgres;

--
-- Name: suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.suppliers_id_seq OWNED BY public.suppliers.id;


--
-- Name: support_messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.support_messages (
    id bigint NOT NULL,
    ticket_id bigint NOT NULL,
    author_id bigint,
    is_staff boolean DEFAULT false NOT NULL,
    body text NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.support_messages OWNER TO postgres;

--
-- Name: support_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.support_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.support_messages_id_seq OWNER TO postgres;

--
-- Name: support_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.support_messages_id_seq OWNED BY public.support_messages.id;


--
-- Name: support_tickets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.support_tickets (
    id bigint NOT NULL,
    user_id bigint,
    email character varying(255),
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    status character varying(50) DEFAULT 'open'::character varying NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.support_tickets OWNER TO postgres;

--
-- Name: support_tickets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.support_tickets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.support_tickets_id_seq OWNER TO postgres;

--
-- Name: support_tickets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.support_tickets_id_seq OWNED BY public.support_tickets.id;


--
-- Name: user_roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_roles (
    user_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.user_roles OWNER TO postgres;

--
-- Name: user_sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_sessions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    token character varying(255) NOT NULL,
    ip_address inet,
    user_agent text,
    expires_at timestamp with time zone NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.user_sessions OWNER TO postgres;

--
-- Name: user_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_sessions_id_seq OWNER TO postgres;

--
-- Name: user_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_sessions_id_seq OWNED BY public.user_sessions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    login character varying(100) NOT NULL,
    password_hash character varying(255) NOT NULL,
    first_name character varying(100),
    last_name character varying(100),
    phone character varying(30),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    updated_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: warehouses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.warehouses (
    id bigint NOT NULL,
    supplier_id bigint,
    name character varying(150) NOT NULL,
    code character varying(50) NOT NULL,
    address character varying(255),
    city character varying(100),
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.warehouses OWNER TO postgres;

--
-- Name: warehouses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.warehouses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.warehouses_id_seq OWNER TO postgres;

--
-- Name: warehouses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.warehouses_id_seq OWNED BY public.warehouses.id;


--
-- Name: wishlist_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wishlist_items (
    wishlist_id bigint NOT NULL,
    product_id bigint NOT NULL,
    added_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.wishlist_items OWNER TO postgres;

--
-- Name: wishlists; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wishlists (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.wishlists OWNER TO postgres;

--
-- Name: wishlists_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.wishlists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wishlists_id_seq OWNER TO postgres;

--
-- Name: wishlists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.wishlists_id_seq OWNED BY public.wishlists.id;


--
-- Name: addresses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addresses ALTER COLUMN id SET DEFAULT nextval('public.addresses_id_seq'::regclass);


--
-- Name: attribute_options id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_options ALTER COLUMN id SET DEFAULT nextval('public.attribute_options_id_seq'::regclass);


--
-- Name: attributes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes ALTER COLUMN id SET DEFAULT nextval('public.attributes_id_seq'::regclass);


--
-- Name: brands id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands ALTER COLUMN id SET DEFAULT nextval('public.brands_id_seq'::regclass);


--
-- Name: car_generations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_generations ALTER COLUMN id SET DEFAULT nextval('public.car_generations_id_seq'::regclass);


--
-- Name: car_makes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_makes ALTER COLUMN id SET DEFAULT nextval('public.car_makes_id_seq'::regclass);


--
-- Name: car_models id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_models ALTER COLUMN id SET DEFAULT nextval('public.car_models_id_seq'::regclass);


--
-- Name: car_modifications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_modifications ALTER COLUMN id SET DEFAULT nextval('public.car_modifications_id_seq'::regclass);


--
-- Name: cart_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items ALTER COLUMN id SET DEFAULT nextval('public.cart_items_id_seq'::regclass);


--
-- Name: carts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts ALTER COLUMN id SET DEFAULT nextval('public.carts_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: delivery_methods id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery_methods ALTER COLUMN id SET DEFAULT nextval('public.delivery_methods_id_seq'::regclass);


--
-- Name: discounts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discounts ALTER COLUMN id SET DEFAULT nextval('public.discounts_id_seq'::regclass);


--
-- Name: order_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items ALTER COLUMN id SET DEFAULT nextval('public.order_items_id_seq'::regclass);


--
-- Name: order_status_history id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_status_history ALTER COLUMN id SET DEFAULT nextval('public.order_status_history_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: payment_methods id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_methods ALTER COLUMN id SET DEFAULT nextval('public.payment_methods_id_seq'::regclass);


--
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- Name: product_attribute_values id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values ALTER COLUMN id SET DEFAULT nextval('public.product_attribute_values_id_seq'::regclass);


--
-- Name: product_fitments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_fitments ALTER COLUMN id SET DEFAULT nextval('public.product_fitments_id_seq'::regclass);


--
-- Name: product_images id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images ALTER COLUMN id SET DEFAULT nextval('public.product_images_id_seq'::regclass);


--
-- Name: product_offers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_offers ALTER COLUMN id SET DEFAULT nextval('public.product_offers_id_seq'::regclass);


--
-- Name: product_reviews id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews ALTER COLUMN id SET DEFAULT nextval('public.product_reviews_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: stock_movements id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_movements ALTER COLUMN id SET DEFAULT nextval('public.stock_movements_id_seq'::regclass);


--
-- Name: suppliers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers ALTER COLUMN id SET DEFAULT nextval('public.suppliers_id_seq'::regclass);


--
-- Name: support_messages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_messages ALTER COLUMN id SET DEFAULT nextval('public.support_messages_id_seq'::regclass);


--
-- Name: support_tickets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_tickets ALTER COLUMN id SET DEFAULT nextval('public.support_tickets_id_seq'::regclass);


--
-- Name: user_sessions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_sessions ALTER COLUMN id SET DEFAULT nextval('public.user_sessions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: warehouses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouses ALTER COLUMN id SET DEFAULT nextval('public.warehouses_id_seq'::regclass);


--
-- Name: wishlists id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists ALTER COLUMN id SET DEFAULT nextval('public.wishlists_id_seq'::regclass);


--
-- Data for Name: addresses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addresses (id, user_id, full_name, phone, country, region, city, postal_code, street_address, comment) FROM stdin;
1	3	Дмитро Клієнт	+380503333333	Україна	Київська	Київ	02090	пр-т Перемоги, 10, кв. 25	\N
2	4	Марина Автовласенко	+380504444444	Україна	Львівська	Львів	79000	вул. Зеленська, 15, кв. 12	Домофон 25
3	3	Дмитро Клієнт	+380503333333	Україна	Київська	Київ	02090	вул. Сервісна, 5	Окремий сервісний адрес
6	\N	\N	\N	\N	\N	\N	\N	іфввфівфівфві	\N
7	6	Л Л Л	123123123	Украина	Черкаська	Сміла	022254	іфввфівфівфві	
8	6	\N	\N	\N	\N	\N	\N	іфввфівфівфві	\N
9	6	\N	\N	\N	\N	\N	\N	Сміла, іфввфівфівфві, 022254, Черкаська, Украина	\N
14	\N	\N	\N	\N	\N	\N	\N	іфввфівфівфві	\N
\.


--
-- Data for Name: attribute_options; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attribute_options (id, attribute_id, value, sort_order) FROM stdin;
1	1	5W-30	10
2	1	5W-40	20
3	4	Передня вісь	10
4	4	Задня вісь	20
\.


--
-- Data for Name: attributes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attributes (id, code, name, unit, data_type) FROM stdin;
1	viscosity	В'язкість	\N	string
2	volume_l	Об'єм, л	л	decimal
3	engine_code	Код двигуна	\N	string
4	side	Сторона встановлення	\N	string
\.


--
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.brands (id, name, slug, logo, is_active) FROM stdin;
38	KYB	kyb	https://kyb-europe.com/rus/wp-content/uploads/sites/41/2025/03/KYB-LOGO_NoStrapline-1.png	t
39	Gates	gates	https://logos-world.net/wp-content/uploads/2022/07/Gates-Logo.png	t
40	Valeo	valeo	https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Valeo_Logo.svg/2560px-Valeo_Logo.svg.png	t
1	Bosch	bosch	https://amper.kh.ua/wp-content/uploads/2023/08/logo-bosch.jpg	t
9	ATE	ate	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTTVY5gSjAuGMITg3Fur3opT6fNOlZw8wxEg&s	t
3	Castrol	castrol	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTsH6NnKcaLSvGpMW_kVDfHyqQWL0lTyJ2RA&s	t
2	Mahle	mahle	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGSwIbfHNwwbmVA-ukBifnayMbFt38L2RBmQ&s	t
4	MANN-FILTER	mann-filter	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBGZH1jlQwYeNMdtyyWUHZy-xQy4V2RHW2ug&s	t
8	NGK	ngk	https://cdn.worldvectorlogo.com/logos/ngk-3.svg	t
41	SKF	skf	https://t4.ftcdn.net/jpg/05/18/90/25/360_F_518902500_AxH2VzhFceK37VMHNvwQlO3vLd2ox0Lo.jpg	t
7	Sachs	sachs	https://logos-world.net/wp-content/uploads/2023/03/Sachs-Logo.png	t
5	Liqui Moly	liqui-moly	https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Liqui-moly.svg/2560px-Liqui-moly.svg.png	t
6	Febi Bilstein	febi-bilstein	https://cdn.worldvectorlogo.com/logos/febi-bilstein.svg	t
86	TRW	trw	https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/TRW_Auto.svg/2560px-TRW_Auto.svg.png	t
87	Monroe	monroe	https://vectorseek.com/wp-content/uploads/2023/08/Monroe-Logo-Vector.svg-.png	t
88	Hella	hella	https://logos-world.net/wp-content/uploads/2023/03/Hella-Logo-2013.png	t
89	Denso	denso	https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Denso_logo.svg/1200px-Denso_logo.svg.png	t
90	Brembo	brembo	https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Brembo_logo_%282022%29.svg/2560px-Brembo_logo_%282022%29.svg.png	t
91	Continental	continental	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTc7EXn8tZy00XYji0UC8R924drB_S2d2Iarg&s	t
\.


--
-- Data for Name: car_generations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.car_generations (id, model_id, name, year_from, year_to) FROM stdin;
1	1	B8	2008	2015
2	2	C7	2011	2018
3	4	E180	2013	2019
4	5	Mk7	2012	2019
\.


--
-- Data for Name: car_makes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.car_makes (id, name, slug) FROM stdin;
1	Audi	audi
2	BMW	bmw
3	Toyota	toyota
4	Volkswagen	volkswagen
\.


--
-- Data for Name: car_models; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.car_models (id, make_id, name, slug) FROM stdin;
1	1	A4	audi-a4
2	1	A6	audi-a6
3	2	3 Series	bmw-3-series
4	3	Corolla	toyota-corolla
5	4	Golf	vw-golf
\.


--
-- Data for Name: car_modifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.car_modifications (id, generation_id, engine_code, engine_volume, power_hp, fuel_type, drive_type, transmission, year_from, year_to) FROM stdin;
1	1	CDNC	2.0	211	Бензин	Передній	АКПП	2008	2012
2	2	CGLC	2.0	177	Дизель	Повний	АКПП	2012	2015
3	3	1ZR-FE	1.6	132	Бензин	Передній	МКПП	2013	2019
4	4	CXSA	1.4	122	Бензин	Передній	МКПП	2013	2017
\.


--
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cart_items (id, cart_id, product_id, quantity, price, currency) FROM stdin;
1	1	1	1.00	1350.00	UAH
2	1	2	1.00	299.00	UAH
36	21	7	1.00	2999.00	UAH
21	14	3	2.00	1999.00	UAH
22	15	1	1.00	1350.00	UAH
39	23	7	1.00	2999.00	UAH
40	24	9	1.00	2699.00	UAH
\.


--
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.carts (id, user_id, session_token, created_at, updated_at) FROM stdin;
1	3	sess-user1-xyz	2025-11-21 13:21:58.037792+02	2025-11-23 13:21:58.037792+02
2	4	sess-user2-abc	2025-11-23 13:21:58.037792+02	2025-11-24 11:21:58.037792+02
11	\N	\N	2025-11-25 15:41:54.023885+02	2025-11-25 15:46:51.086225+02
12	6	\N	2025-11-25 15:48:53.398207+02	2025-11-26 00:17:56.13847+02
13	6	\N	2025-11-26 00:18:14.989993+02	2025-11-27 12:45:12.198857+02
14	6	\N	2025-11-27 19:15:45.809281+02	2025-11-27 19:15:45.809281+02
15	\N	\N	2025-11-27 19:41:39.062769+02	2025-11-27 19:41:39.062769+02
16	\N	\N	2025-11-27 19:42:21.017724+02	2025-11-27 20:18:20.264205+02
17	6	\N	2025-11-27 20:19:03.285387+02	2025-11-27 20:19:07.640908+02
18	6	\N	2025-11-27 20:26:36.864791+02	2025-11-27 20:26:57.70792+02
19	6	\N	2025-11-27 20:27:10.19912+02	2025-11-27 21:16:08.729832+02
20	6	\N	2025-11-27 21:27:45.945603+02	2025-11-27 21:27:57.50563+02
21	6	\N	2025-11-27 21:42:29.264015+02	2025-11-27 21:42:29.264015+02
22	6	\N	2025-11-28 12:32:17.435828+02	2025-11-28 13:02:46.193518+02
23	6	\N	2025-11-28 13:30:43.549194+02	2025-11-28 13:30:43.549194+02
24	\N	\N	2025-11-28 19:42:57.991721+02	2025-11-28 19:42:57.991721+02
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, parent_id, name, slug, description, sort_order, is_active) FROM stdin;
1	\N	Двигун	dvigun	Запчастини для двигуна	10	t
5	4	Масляні фільтри	maslyani-filtry	Масляні фільтри для ДВЗ	50	t
6	2	Гальмівні колодки	galmivni-kolodky	Передні та задні гальмівні колодки	60	t
10	2	Гальмівні диски	brake-discs	Передні та задні гальмівні диски	100	t
11	4	Салонні фільтри	cabin-filters	Фільтри салону / кліматичної системи	110	t
3	1	Масла та рідини	masla-ta-ridini	Моторні масла, трансмісійні, антифриз	30	t
4	1	Фільтри	filtry	Масляні, повітряні, паливні фільтри	40	t
7	1	Охолодження двигуна	cooling-system	Радіатори, термостати, помпи охолодження	70	t
9	1	Свічки запалювання	spark-plugs	Свічки запалювання, котушки, дроти	90	t
2	\N	Гальмівна система	galmivna-systema	Гальмівні диски, колодки, супорти	20	t
8	1	Акумулятори та живлення	batteries	Стартерні акумулятори та аксесуари	80	t
48	\N	Підвіска та рульове керування	suspension-steering	Амортизатори, пружини, важелі, рульові тяги та наконечники	30	t
49	\N	Трансмісія	transmission	Зчеплення, КПП, привідні вали та інші елементи трансмісії	40	t
50	4	Паливні фільтри	fuel-filters	Паливні фільтри для бензинових та дизельних двигунів	55	t
51	1	Комплекти ГРМ	timing-belt-kits	Комплекти приводу ГРМ (ремінь, ролики, іноді помпа)	75	t
52	48	Ступичні підшипники	wheel-bearings	Ступиці та підшипники колеса в зборі	80	t
53	49	Комплекти зчеплення	clutch-kits	Комплекти зчеплення у зборі (корзина, диск, вижимний)	10	t
84	1	Освітлення та лампи	lighting	Галогенні, ксенонові та LED-лампи для фар та габаритів.	130	t
85	8	Щітки склоочисника	wiper-blades	Щітки склоочисника для лобового та заднього скла.	120	t
86	1	Датчики та сенсори	sensors	Датчики ABS, кисню (лямбда-зонд), температури та інші.	140	t
\.


--
-- Data for Name: delivery_methods; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.delivery_methods (id, name, code, description, base_price, active) FROM stdin;
1	Нова Пошта (відділення)	nova_poshta_branch	Доставка на відділення Нової Пошти по Україні	120.00	t
2	Кур'єр по Києву	kyiv_courier	Адресна доставка кур'єром в межах Києва	150.00	t
3	Самовивіз зі складу	pickup_warehouse	Самовивіз зі складу в Києві	0.00	t
\.


--
-- Data for Name: discount_categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discount_categories (discount_id, category_id) FROM stdin;
1	3
\.


--
-- Data for Name: discount_products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discount_products (discount_id, product_id) FROM stdin;
1	1
1	5
\.


--
-- Data for Name: discounts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discounts (id, name, description, discount_type, value, date_from, date_to, active, min_order_sum, code) FROM stdin;
1	Знижка на масла 10%	Акція на моторні масла Castrol та Liqui Moly	percent	10.00	2025-09-22 00:00:00+03	2025-11-29 23:59:59.999999+02	t	1000.00	1
4	1	3	fixed	100.00	2025-11-14 00:00:00+02	2026-01-03 23:59:59.999999+02	t	1000.00	2
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_items (id, order_id, product_id, product_name, sku, quantity, price, discount, total, currency) FROM stdin;
1	1	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	1.00	1350.00	0.00	1350.00	UAH
2	1	2	MANN-FILTER W 712/95	FILTER-MANN-W71295	2.00	299.00	50.00	548.00	UAH
3	1	3	Bosch гальмівні колодки передні	PADS-BOSCH-0986AB1234	1.00	1999.00	100.00	1899.00	UAH
4	2	5	Liqui Moly Top Tec 4200 5W-30 5L	LMOIL-TT4200-5W30-5L	1.00	1599.00	0.00	1599.00	UAH
5	2	6	Febi Bilstein передні гальмівні диски 280mm	FEBI-BRKDISC-101234	1.00	2499.00	0.00	2499.00	UAH
6	2	8	NGK Iridium IX BKR6EIX (комплект 4 шт.)	NGK-BKR6EIX-4SET	1.00	1299.00	0.00	1299.00	UAH
7	5	9	ATE передні гальмівні колодки Ceramic	ATE-PADS-13.0460-7117.2	2.00	2699.00	0.00	5398.00	UAH
8	6	9	ATE передні гальмівні колодки Ceramic	ATE-PADS-13.0460-7117.2	1.00	2699.00	0.00	2699.00	UAH
9	6	7	Sachs передній амортизатор газомасляний	SACHS-SHOCK-315123	10019.00	2999.00	0.00	30046981.00	UAH
10	7	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	1.00	1350.00	135.00	1215.00	UAH
11	7	8	NGK Iridium IX BKR6EIX (комплект 4 шт.)	NGK-BKR6EIX-4SET	3.00	1299.00	389.70	3507.30	UAH
12	8	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	2.00	1350.00	0.00	2700.00	UAH
13	9	9	ATE передні гальмівні колодки Ceramic	ATE-PADS-13.0460-7117.2	1.00	2699.00	0.00	2699.00	UAH
14	10	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	1.00	1215.00	0.00	1215.00	UAH
15	11	5	Liqui Moly Top Tec 4200 5W-30 5L	LMOIL-TT4200-5W30-5L	1.00	1299.00	0.00	1299.00	UAH
16	12	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	1.00	1250.00	0.00	1250.00	UAH
17	13	1	Castrol EDGE 5W-30 4L	OIL-CASTROL-EDGE-5W30-4L	1.00	1350.00	0.00	1350.00	UAH
\.


--
-- Data for Name: order_status_history; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_status_history (id, order_id, old_status, new_status, changed_by, comment, changed_at) FROM stdin;
1	1	\N	new	2	Створено замовлення через сайт	2025-11-23 13:21:58.037792+02
2	1	new	processing	2	Замовлення прийнято в обробку	2025-11-23 17:21:58.037792+02
3	2	\N	new	2	Замовлення оформлене клієнтом	2025-11-24 10:21:58.037792+02
4	2	new	processing	2	Підтверджено менеджером	2025-11-24 11:21:58.037792+02
5	6	pending	pending	6	Status updated	2025-11-26 01:46:58.26604+02
6	6	pending	pending	6	Status updated & paid	2025-11-26 01:47:17.188381+02
7	6	pending	cancelled	6	Status updated	2025-11-26 01:47:20.845783+02
8	6	cancelled	completed	6	Status updated	2025-11-26 01:47:24.420929+02
9	6	completed	completed	6	Status updated & paid	2025-11-26 15:11:54.868131+02
10	6	completed	paid	6	Status updated	2025-11-26 15:12:01.567353+02
11	6	paid	paid	6	Status updated & paid	2025-11-26 15:12:03.416992+02
12	5	pending	completed	6	Status updated	2025-11-26 15:13:35.705393+02
13	2	processing	cancelled	6	Status updated	2025-11-26 15:13:43.696527+02
14	1	new	pending	6	Status updated	2025-11-26 15:13:48.990596+02
15	5	completed	shipped	6	Status updated	2025-11-26 15:14:18.954975+02
16	13	pending	completed	6	Status updated	2025-11-28 18:26:04.208774+02
17	12	pending	completed	6	Status updated	2025-11-28 18:26:12.691887+02
18	11	pending	completed	6	Status updated	2025-11-28 18:26:17.032317+02
19	10	pending	completed	6	Status updated	2025-11-28 18:26:21.337786+02
20	9	pending	completed	6	Status updated	2025-11-28 18:26:28.638866+02
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, order_number, user_id, status_code, delivery_method_id, payment_method_id, shipping_address_id, billing_address_id, total_products, total_discount, total_delivery, total_amount, currency, customer_comment, manager_comment, created_at, updated_at) FROM stdin;
6	AP-20251125-3209	6	paid	1	1	7	7	30049680.00	0.00	0.00	30049680.00	UAH	\N	\N	2025-11-26 00:17:56.13847+02	2025-11-26 15:12:03.416992+02
2	AP-2025-0002	4	cancelled	2	1	2	2	4698.00	0.00	150.00	4848.00	UAH	Передзвоніть перед відправкою.	Клієнт підтвердив замовлення телефоном.	2025-11-24 11:21:58.037792+02	2025-11-26 15:13:43.696527+02
1	AP-2025-0001	3	pending	1	2	1	3	3797.00	150.00	120.00	3767.00	UAH	Прошу відправити якнайшвидше.	\N	2025-11-23 13:21:58.037792+02	2025-11-26 15:13:48.990596+02
5	AP-20251125-75790	\N	shipped	1	1	6	6	5398.00	0.00	0.00	5398.00	UAH	\N	\N	2025-11-25 15:46:51.086225+02	2025-11-26 15:14:18.954975+02
7	AP-20251127-22704	6	pending	1	1	8	8	5247.00	524.70	0.00	4722.30	UAH	\N	\N	2025-11-27 12:45:12.198857+02	2025-11-27 12:45:12.198857+02
8	AP-20251127-76625	6	pending	1	1	9	9	2700.00	0.00	0.00	2700.00	UAH	\N	\N	2025-11-27 20:18:20.264205+02	2025-11-27 20:18:20.264205+02
13	AP-20251128-22503	\N	completed	1	1	14	14	1350.00	0.00	0.00	1350.00	UAH	\N	\N	2025-11-28 13:02:46.193518+02	2025-11-28 18:26:04.208774+02
12	AP-20251127-14735	6	completed	1	1	\N	\N	1250.00	0.00	0.00	1250.00	UAH	\N	\N	2025-11-27 21:27:57.50563+02	2025-11-28 18:26:12.691887+02
11	AP-20251127-58336	6	completed	1	1	\N	\N	1299.00	0.00	0.00	1299.00	UAH	\N	\N	2025-11-27 21:16:08.729832+02	2025-11-28 18:26:17.032317+02
10	AP-20251127-67687	6	completed	1	1	\N	\N	1215.00	0.00	0.00	1215.00	UAH	\N	\N	2025-11-27 20:26:57.70792+02	2025-11-28 18:26:21.337786+02
9	AP-20251127-19835	6	completed	1	1	\N	\N	2699.00	0.00	0.00	2699.00	UAH	\N	\N	2025-11-27 20:19:07.640908+02	2025-11-28 18:26:28.638866+02
\.


--
-- Data for Name: payment_methods; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payment_methods (id, name, code, description, active) FROM stdin;
1	Готівка при отриманні	cod	Оплата замовлення при отриманні на відділенні або кур'єру	t
2	Оплата карткою онлайн	card_online	Онлайн-оплата банківською карткою	t
3	Безготівковий розрахунок для ФОП/ТОВ	bank_transfer	Рахунок-фактура та безготівковий розрахунок	f
\.


--
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payments (id, order_id, amount, currency, provider, provider_ref, status, raw_response, created_at) FROM stdin;
1	1	3767.00	UAH	LiqPay	LP-TEST-123456	paid	{"status": "success", "transaction": "test"}	2025-11-23 17:21:58.037792+02
2	2	0.00	UAH	COD	COD-AP-2025-0002	pending	{"type": "cod", "status": "pending"}	2025-11-24 12:21:58.037792+02
\.


--
-- Data for Name: product_attribute_values; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_attribute_values (id, product_id, attribute_id, value_string, value_int, value_decimal, value_bool, option_id) FROM stdin;
1	1	1	5W-30	\N	\N	\N	1
2	1	2	\N	\N	4.000000	\N	\N
3	5	1	5W-30	\N	\N	\N	1
4	5	2	\N	\N	5.000000	\N	\N
5	3	4	Передня вісь	\N	\N	\N	3
6	9	4	Передня вісь	\N	\N	\N	3
7	103	4	Задня вісь	\N	\N	\N	4
8	102	4	Передня вісь	\N	\N	\N	3
9	101	4	Передня вісь	\N	\N	\N	3
\.


--
-- Data for Name: product_fitments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_fitments (id, product_id, modification_id, note) FROM stdin;
1	2	1	Підходить для двигуна CDNC (Audi A4 B8)
2	3	1	Передні колодки для Audi A4 B8
3	4	3	Підходить для Toyota Corolla 1.6 (1ZR-FE)
4	6	4	Передні диски для VW Golf 7
5	9	1	Передні керамічні колодки для Audi A4 B8
6	47	4	Комплект ГРМ для VW Golf VII 1.4 TSI (CXSA)
7	48	4	Задній амортизатор для VW Golf VII (CXSA), задня вісь
8	49	4	Ступиця переднього колеса для VW Golf VII (CXSA)
9	50	1	Комплект зчеплення для Audi A4 B8 2.0 TFSI (CDNC)
10	51	3	Паливний фільтр для Toyota Corolla 1.6 (1ZR-FE)
11	52	1	Іридієві свічки для Audi A4 B8 2.0 TFSI (CDNC)
54	103	1	Задні амортизатори для Audi A4 B8 2.0 TFSI (CDNC)
55	106	3	Комплект ГРМ для двигуна Toyota 1ZR-FE (Corolla)
56	104	3	Іридієві свічки запалювання для Toyota Corolla 1.6 (1ZR-FE)
57	102	4	Передні вентильовані гальмівні диски 288 мм для Golf 7 (CXSA)
58	101	4	Передні гальмівні колодки для VW Golf 7 1.4 TSI (CXSA)
\.


--
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_images (id, product_id, image_url, sort_order, is_main) FROM stdin;
1	1	https://midaoil.rs/wp-content/uploads/2022/10/Castrol-EDGE-5W-30-C3_4-L_SKU15F7ED_EAN4008177188633-600x840.jpg	1	t
2	2	https://s7g10.scene7.com/is/image/mannhummel/W_712.95-filter-with-box?dpr=off&qlt=82&ts=1760882134500	1	t
3	3	https://down-my.img.susercontent.com/file/sg-11134201-7rbk0-ll1zdhetu6u035	1	t
5	5	https://midaoil.rs/wp-content/uploads/2022/10/Castrol-EDGE-5W-30-C3_4-L_SKU15F7ED_EAN4008177188633-600x840.jpg	1	t
6	6	https://down-my.img.susercontent.com/file/sg-11134201-7rbk0-ll1zdhetu6u035	1	t
7	7	https://down-my.img.susercontent.com/file/sg-11134201-7rbk0-ll1zdhetu6u035	1	t
8	8	https://s7g10.scene7.com/is/image/mannhummel/W_712.95-filter-with-box?dpr=off&qlt=82&ts=1760882134500	1	t
9	9	https://down-my.img.susercontent.com/file/sg-11134201-7rbk0-ll1zdhetu6u035	1	t
96	103	https://images.prom.ua/6573150912_w760_h860_amortizator-zadnij-monroe.jpg	1	t
92	106	https://kmd.parts/imgs/contitech/ct1168k3_460x330.webp	1	t
97	101	https://images2.exist.ua/media/images/products/2020/11/45__PKharZd.jpg	1	t
95	102	https://images2.exist.ua/media/images/products/2020/03/7644450_24128214.jpg	1	t
93	105	https://media.avtozvuk.ua/media/cache/medium/uploads/10/590980/21.jpg	1	t
94	104	https://svechi.kiev.ua/image/catalog/denso/denso_iridium_tt.jpg	1	t
55	52	https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcTIZJmh_IFcy9L3YJFm_JztecmOBja1mIIdn4L-NsI4iRFz2i0r6X3ImNelkSgLZY7ca9vFMuClTdf5DIer35Ls5XXuDAlx9bDu9adZyFx1xAOWzjlIK37i6m66wXw&usqp=CAc	1	t
4	4	https://agrosoyuz.ua/12636-large_default/filtr-vozdushnyj-mahle-original-lx2024.jpg	1	t
53	50	https://atmo.com.ua/imgs/valeo/837019_460x330.jpg	1	t
54	51	https://images2.exist.ua/media/images/products/2020/11/45__MNnF342.jpg	1	t
52	49	https://images.prom.ua/6572426286_w640_h640_podshipnik-stupitsy-perednej.jpg	1	t
50	47	https://images.prom.ua/6817694364_w640_h640_komplekt-grm-.jpg	1	t
51	48	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSf1OqFcApayuOKk9fT_AHU76Nc-N-Mst7bEg&s	1	t
\.


--
-- Data for Name: product_offers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_offers (id, product_id, warehouse_id, supplier_sku, quantity, available_from, delivery_days, base_price, sale_price, currency, is_active, updated_at) FROM stdin;
1	1	1	CAST-EDGE-5W30-4L-KYIV	15.00	2025-11-24 13:21:58.037792+02	1	1450.00	1350.00	UAH	t	2025-11-24 13:21:58.037792+02
2	2	1	MANN-W71295-KYIV	30.00	2025-11-24 13:21:58.037792+02	1	320.00	299.00	UAH	t	2025-11-24 13:21:58.037792+02
3	3	2	BOSCH-PADS-FRONT-LVIV	10.00	2025-11-24 13:21:58.037792+02	2	2100.00	1999.00	UAH	t	2025-11-24 13:21:58.037792+02
4	4	2	MAHLE-LX1234-LVIV	20.00	2025-11-24 13:21:58.037792+02	2	650.00	620.00	UAH	t	2025-11-24 13:21:58.037792+02
5	5	3	LM-4200-5W30-5L-UA	20.00	2025-11-24 13:21:58.037792+02	1	1650.00	1599.00	UAH	t	2025-11-24 13:21:58.037792+02
6	6	3	FEBI-101234-UA	10.00	2025-11-24 13:21:58.037792+02	2	2600.00	2499.00	UAH	t	2025-11-24 13:21:58.037792+02
7	7	2	SACHS-315123-UA	8.00	2025-11-24 13:21:58.037792+02	3	3100.00	2999.00	UAH	t	2025-11-24 13:21:58.037792+02
8	8	4	NGK-BKR6EIX-SET-DE	40.00	2025-11-24 13:21:58.037792+02	5	1350.00	1299.00	UAH	t	2025-11-24 13:21:58.037792+02
9	9	3	ATE-13.0460-7117.2-UA	15.00	2025-11-24 13:21:58.037792+02	2	2800.00	2699.00	UAH	t	2025-11-24 13:21:58.037792+02
40	47	3	GATES-K015579XS-UA	12.00	2025-11-28 22:14:03.704395+02	2	4200.00	3999.00	UAH	t	2025-11-28 22:14:03.704395+02
41	48	1	KYB-341851-UA	20.00	2025-11-28 22:14:03.704395+02	1	3100.00	2999.00	UAH	t	2025-11-28 22:14:03.704395+02
42	49	4	SKF-VKBA-6556-DE	8.00	2025-11-28 22:14:03.704395+02	5	5200.00	4999.00	UAH	t	2025-11-28 22:14:03.704395+02
43	50	3	VALEO-826811-UA	5.00	2025-11-28 22:14:03.704395+02	3	7800.00	7499.00	UAH	t	2025-11-28 22:14:03.704395+02
44	51	2	MAHLE-KL228-UA	25.00	2025-11-28 22:14:03.704395+02	2	850.00	799.00	UAH	t	2025-11-28 22:14:03.704395+02
45	52	4	NGK-ILZKR7B8G-SET-DE	30.00	2025-11-28 22:14:03.704395+02	5	2200.00	2099.00	UAH	t	2025-11-28 22:14:03.704395+02
94	105	1	HEL-8GD355370261	100.00	2025-11-28 22:21:49.29528+02	1	260.00	249.00	UAH	t	2025-11-28 22:21:49.29528+02
95	101	1	BR-P85006-UA	12.00	2025-11-28 22:21:49.29528+02	1	2300.00	2199.00	UAH	t	2025-11-28 22:21:49.29528+02
96	102	2	TRW-DF4346-UA	8.00	2025-11-28 22:21:49.29528+02	2	3050.00	2899.00	UAH	t	2025-11-28 22:21:49.29528+02
97	106	3	CON-CT1168K3-UA	10.00	2025-11-28 22:21:49.29528+02	2	4200.00	3999.00	UAH	t	2025-11-28 22:21:49.29528+02
98	103	3	MON-G16464-UA	6.00	2025-11-28 22:21:49.29528+02	1	3400.00	3299.00	UAH	t	2025-11-28 22:21:49.29528+02
99	104	4	DEN-IK20TT-DE	30.00	2025-11-28 22:21:49.29528+02	5	1650.00	1599.00	UAH	t	2025-11-28 22:21:49.29528+02
\.


--
-- Data for Name: product_oil_specs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_oil_specs (product_id, viscosity, volume_l, oil_type) FROM stdin;
1	5W-30	4.00	synthetic
5	5W-30	5.00	synthetic
\.


--
-- Data for Name: product_reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_reviews (id, product_id, user_id, rating, title, body, is_approved, created_at) FROM stdin;
1	1	3	5	Чудова олива	Користуюсь цією оливою вже 3 роки, двигун працює тихо.	t	2025-11-14 13:21:58.037792+02
2	3	4	4	Нормальні колодки	Гальмують добре, трохи є пил, але в межах норми.	t	2025-11-19 13:21:58.037792+02
3	1	6	5	\N	А	t	2025-11-27 22:46:51.333653+02
4	1	6	5	\N	А	t	2025-11-27 22:47:22.60159+02
5	1	6	5	\N	Ф	t	2025-11-27 22:49:50.042905+02
6	6	6	5	\N	А	t	2025-11-27 22:56:23.171402+02
7	6	6	5	\N	А	t	2025-11-27 22:57:15.248897+02
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, sku, oem_number, brand_id, category_id, name, slug, short_desc, description, weight_kg, is_active, is_popular, created_at, updated_at) FROM stdin;
1	OIL-CASTROL-EDGE-5W30-4L	\N	3	3	Castrol EDGE 5W-30 4L	castrol-edge-5w30-4l	Синтетична моторна олива 5W-30, 4 л	Сучасна повністю синтетична олива Castrol EDGE 5W-30 для бензинових та дизельних двигунів.	4.200	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
2	FILTER-MANN-W71295	W712/95	4	5	MANN-FILTER W 712/95 масляний фільтр	mann-filter-w71295	Масляний фільтр для широкого ряду бензинових та дизельних двигунів.	Оригінальний масляний фільтр MANN-FILTER із високим ступенем очищення.	0.350	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
3	PADS-BOSCH-0986AB1234	\N	1	6	Bosch гальмівні колодки передні	bosch-pads-front	Комплект передніх гальмівних колодок Bosch.	Якісні гальмівні колодки Bosch для впевненого гальмування.	2.100	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
4	FILTER-MAHLE-LX1234	\N	2	4	Mahle повітряний фільтр	mahle-air-filter-lx1234	Повітряний фільтр Mahle.	Повітряний фільтр для надійного очищення повітря, що подається в двигун.	0.450	t	f	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
5	LMOIL-TT4200-5W30-5L	LM-4200-5W30-5L	5	3	Liqui Moly Top Tec 4200 5W-30 5L	liqui-moly-top-tec-4200-5w30-5l	Синтетична моторна олива для сучасних бензинових та дизельних двигунів.	Синтетична моторна олива Liqui Moly Top Tec 4200 5W-30 для двигунів з сажовими фільтрами та турбіною.	4.700	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
6	FEBI-BRKDISC-101234	101234	6	10	Febi Bilstein передні гальмівні диски 280mm	febi-bilstein-front-brake-discs-280mm	Комплект передніх вентильованих гальмівних дисків 280mm.	Гальмівні диски Febi Bilstein з антикорозійним покриттям для популярних моделей VW / Skoda / Seat.	14.200	t	f	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
8	NGK-BKR6EIX-4SET	BKR6EIX	8	9	NGK Iridium IX BKR6EIX (комплект 4 шт.)	ngk-iridium-bkr6eix-set	Іридієві свічки запалювання NGK Iridium IX, комплект 4 шт.	Покращений запуск, стабільна іскра та збільшений ресурс завдяки іридієвому електроду.	0.400	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
9	ATE-PADS-13.0460-7117.2	13.0460-7117.2	9	6	ATE передні гальмівні колодки Ceramic	ate-ceramic-brake-pads-front	Передні керамічні гальмівні колодки ATE Ceramic з низьким рівнем пилу.	Колодки ATE Ceramic забезпечують тихе гальмування та мінімальне забруднення дисків.	2.100	t	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
7	SACHS-SHOCK-315123	315123	7	1	Sachs передній амортизатор газомасляний		Передній газомасляний амортизатор Sachs.	Амортизатор Sachs забезпечує комфорт та стабільність керування, рекомендований як заміна OEM.	6.300	t	f	2025-11-24 13:21:58.037792+02	2025-11-28 20:57:29.749828+02
47	GATES-K015579XS	K015579XS	39	51	Gates комплект ГРМ K015579XS	gates-k015579xs-timing-belt-kit	Комплект ГРМ для дизельних двигунів VAG 1.6/2.0 TDI	Оригінальний комплект приводу ГРМ Gates K015579XS з ременем та роликами для популярних дизельних двигунів VAG 1.6/2.0 TDI.	3.200	t	t	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
48	KYB-341851-GOLF7	341851	38	48	KYB Excel-G амортизатор задній VW Golf VII	kyb-excel-g-rear-shock-golf7	Газомасляний задній амортизатор для VW Golf VII (MQB)	Газомасляний задній амортизатор KYB Excel-G (341851), розрахований для платформи MQB (VW Golf VII та споріднені моделі).	4.500	t	t	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
49	SKF-VKBA-6556	VKBA 6556	41	52	SKF ступиця колеса VKBA 6556	skf-vkba-6556-wheel-hub	Комплект ступичного підшипника SKF VKBA 6556 для VW Golf VII / Audi A3	Комплект ступичного підшипника SKF VKBA 6556 у зборі з маточиною для передньої осі VW Golf VII, Audi A3 8V, Seat Leon та інших моделей на платформі MQB.	5.200	t	f	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
50	VALEO-826811-A4	826811	40	53	Valeo комплект зчеплення 826811 Audi A4	valeo-826811-clutch-kit-a4	Комплект зчеплення Valeo 826811 для Audi A4 B8 2.0 TDI	Комплект зчеплення Valeo 826811 (корзина, диск, вижимний) для двигунів 2.0 TDI на Audi A4 B8 та споріднених моделей VAG.	9.000	t	f	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
51	MAHLE-KL228	KL 228	2	50	Mahle паливний фільтр KL 228	mahle-fuel-filter-kl228	Паливний фільтр Mahle KL 228 для дизельних систем	Оригінальний паливний фільтр Mahle KL 228 для сучасних дизельних систем впорскування.	0.600	t	f	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
52	NGK-ILZKR7B8G-4SET	ILZKR7B-8G	8	9	NGK Laser Iridium ILZKR7B-8G (комплект 4 шт.)	ngk-laser-iridium-ilzkr7b8g-set	Іридієві свічки NGK Laser Iridium ILZKR7B-8G, комплект 4 шт.	Іридієві свічки NGK Laser Iridium ILZKR7B-8G забезпечують стабільну іскру, легкий запуск двигуна та збільшений ресурс.	0.450	t	t	2025-11-28 22:14:03.704395+02	2025-11-28 22:14:03.704395+02
101	BREMBO-P85006-GOLF7	P 85 006	90	6	Brembo P 85 006 передні гальмівні колодки для VW Golf 7	brembo-p85006-golf7	Передні гальмівні колодки Brembo P 85 006 для VW Golf 7 1.4 TSI / 2.0 TDI.	Оригінальні передні гальмівні колодки Brembo P 85 006 для Volkswagen Golf 7. Забезпечують стабільне гальмування, знижений шум та рівномірний знос. Рекомендовані для щоденної експлуатації в міських умовах.	2.000	t	t	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
102	TRW-DF4346-GOLF7-FRONT	DF4346	86	10	TRW DF4346 передні гальмівні диски 288мм	trw-df4346-golf7-front	Передні вентильовані гальмівні диски TRW DF4346, діаметр 288 мм.	Комплект передніх гальмівних дисків TRW DF4346 з високою стійкістю до перегріву. Підійдуть до популярних модифікацій VW Golf 7 та споріднених моделей VAG.	13.500	t	t	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
103	MONROE-G16464-A4B8-REAR	G16464	87	48	Monroe G16464 задній амортизатор газомасляний Audi A4 B8	monroe-g16464-a4b8-rear	Задній газомасляний амортизатор Monroe G16464 для Audi A4 B8.	Амортизатор Monroe G16464 забезпечує комфортну їзду та надійне зчеплення коліс з дорогою. Розрахований на експлуатацію у складних умовах українських доріг.	5.800	t	f	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
104	DENSO-IK20TT-4SET	IK20TT	89	9	Denso Iridium TT IK20TT (комплект 4 свічки)	denso-ik20tt-4set	Іридієві свічки запалювання Denso IK20TT, комплект 4 шт.	Свічки запалювання Denso Iridium Tough IK20TT з тонким іридієвим електродом. Забезпечують легкий запуск двигуна, стабільне згорання та зменшення витрати пального.	0.350	t	t	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
105	HELLA-8GD-355-370-261-H7	8GD 355 370-261	88	84	Hella H7 12V 55W 8GD 355 370-261	hella-8gd-355-370-261-h7	Галогенна лампа Hella H7 12V 55W, артикул 8GD 355 370-261.	Галогенна лампа Hella H7 для ближнього/дальнього світла. Оригінальна якість, стабільний світловий потік і довгий ресурс роботи.	0.080	t	t	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
106	CONTI-CT1168K3-COROLLA	CT1168K3	91	51	Continental CT1168K3 комплект ГРМ для Toyota 1ZR-FE	continental-ct1168k3-corolla	Комплект ременя ГРМ Continental CT1168K3 (ремінь + ролики).	Комплект ГРМ Continental CT1168K3 для бензинових двигунів Toyota 1ZR-FE (Corolla, Auris). Містить ремінь, натяжний та обвідний ролики. Рекомендована профілактична заміна кожні 60–90 тис. км.	3.200	t	f	2025-11-28 22:21:49.29528+02	2025-11-28 22:21:49.29528+02
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, code, name, description) FROM stdin;
1	admin	Адміністратор	Повний доступ до системи
2	manager	Менеджер	Обробка замовлень, робота з клієнтами
3	customer	Клієнт	Звичайний покупець
\.


--
-- Data for Name: stock_movements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stock_movements (id, product_offer_id, quantity_delta, reason, related_order_id, created_at, created_by) FROM stdin;
1	1	-1.00	order_reserve	1	2025-11-23 13:21:58.037792+02	2
2	2	-2.00	order_reserve	1	2025-11-23 13:21:58.037792+02	2
3	3	-1.00	order_reserve	1	2025-11-23 13:21:58.037792+02	2
4	5	-1.00	order_reserve	2	2025-11-24 11:21:58.037792+02	2
5	6	-1.00	order_reserve	2	2025-11-24 11:21:58.037792+02	2
6	8	-1.00	order_reserve	2	2025-11-24 11:21:58.037792+02	2
\.


--
-- Data for Name: suppliers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.suppliers (id, name, code, api_url, phone, email, is_active) FROM stdin;
1	АвтоПоставка Київ	SUP1	\N	+380676000001	kiev@autopostavka.test	t
2	ЄвроЗапчастини Львів	SUP2	\N	+380676000002	lviv@eurozap.test	t
3	AutoTrade UA	AUTOTRADE_UA	\N	+380442001122	sales@autotrade.ua	t
4	EU Parts GmbH	EU_PARTS_DE	\N	+4930123456	info@eu-parts.de	t
\.


--
-- Data for Name: support_messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.support_messages (id, ticket_id, author_id, is_staff, body, created_at) FROM stdin;
1	2	6	t	dsfd	2025-11-26 00:13:19.688054+02
2	2	6	f	afsf	2025-11-26 00:16:33.498806+02
3	2	6	f	asdasd	2025-11-26 00:16:36.416956+02
4	2	6	f	asdasd	2025-11-26 00:16:38.325442+02
5	2	6	t	sadasdas	2025-11-26 00:16:46.597715+02
6	2	6	t	sdf	2025-11-26 00:16:48.429379+02
\.


--
-- Data for Name: support_tickets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.support_tickets (id, user_id, email, subject, message, status, created_at, updated_at) FROM stdin;
2	6	\N	пп	прпр	closed	2025-11-25 23:46:13.50421+02	2025-11-26 00:16:54.804673+02
3	6	\N	іваіваіваів	r	open	2025-11-28 14:48:55.783862+02	2025-11-28 20:46:58.387584+02
1	3	user1@autoparts.test	Уточнення по сумісності	Добрий день, підкажіть, чи підійде цей фільтр до Audi A4 B8 2.0 TFSI?	closed	2025-11-21 13:21:58.037792+02	2025-11-28 21:06:00.213101+02
\.


--
-- Data for Name: user_roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_roles (user_id, role_id) FROM stdin;
1	1
2	2
3	3
4	3
6	1
7	3
9	3
\.


--
-- Data for Name: user_sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_sessions (id, user_id, token, ip_address, user_agent, expires_at, created_at) FROM stdin;
1	6	cf0109ca-00cb-48dd-8bfb-9960aee0901c	\N	\N	2025-12-01 14:38:52.022+02	2025-11-24 14:38:52.028091+02
2	6	d1768aac-6780-430d-ba99-4e379c376851	\N	\N	2025-12-01 14:39:19.691+02	2025-11-24 14:39:19.695332+02
3	6	7c5582f8-324d-4c4b-af63-77ca45447dee	\N	\N	2025-12-02 13:24:13.997+02	2025-11-25 13:24:13.999911+02
4	6	4a69be9c-c886-4623-a1e0-a56bad5f9ab8	\N	\N	2025-12-02 14:13:24.192+02	2025-11-25 14:13:24.195923+02
5	7	7079ea5f-93a5-420f-93eb-c7a940929366	\N	\N	2025-12-02 15:14:49.548+02	2025-11-25 15:14:49.549935+02
6	6	c0ed9bd6-c1e4-4aa1-b575-3fd5c38e143b	\N	\N	2025-12-02 15:46:56.513+02	2025-11-25 15:46:56.514734+02
7	7	3963aeb0-2153-4e15-b1b8-e30637cb2ad1	\N	\N	2025-12-03 00:52:03.633+02	2025-11-26 00:52:03.638842+02
8	6	f63b0dff-7d3d-4924-8f70-8dc8efd9ecad	\N	\N	2025-12-03 00:52:22.642+02	2025-11-26 00:52:22.646631+02
9	8	36d0a066-60ba-4517-b9c0-3de5ea21bb79	\N	\N	2025-12-03 15:11:27.421+02	2025-11-26 15:11:27.422619+02
10	6	8e853598-5bd3-4673-a098-d4eeafc0c54f	\N	\N	2025-12-03 15:11:35.107+02	2025-11-26 15:11:35.108889+02
11	6	eb68a132-af0b-41a6-b0b7-cd36f827275e	\N	\N	2025-12-04 19:41:23.598+02	2025-11-27 19:41:23.599744+02
12	6	506ef32d-357a-4be7-bbf5-d90df5e2e088	\N	\N	2025-12-04 19:41:40.583+02	2025-11-27 19:41:40.584171+02
13	6	b1fe778c-081b-4b11-8a83-4ed29f859c35	\N	\N	2025-12-04 19:42:26.155+02	2025-11-27 19:42:26.156378+02
14	6	803d53b4-2480-482c-abf0-0cd2f754fbf4	\N	\N	2025-12-04 21:43:28.254+02	2025-11-27 21:43:28.256329+02
15	6	a9403c9b-f15c-4c4c-a791-9ea7376816d3	\N	\N	2025-12-04 21:47:55.858+02	2025-11-27 21:47:55.858929+02
16	6	1b59e5f1-1a24-4861-9a80-1b8e79b09fec	\N	\N	2025-12-05 13:28:39.66+02	2025-11-28 13:28:39.661303+02
17	6	5b894996-521e-42d4-a53f-d4424fa87a3e	\N	\N	2025-12-05 13:56:49.451+02	2025-11-28 13:56:49.452099+02
18	6	66d66167-d540-4df7-9ef4-c45404c24890	\N	\N	2025-12-05 20:35:57.414+02	2025-11-28 20:35:57.415454+02
19	6	544cb9e4-831e-42e8-b4dd-90b993cd6919	\N	\N	2025-12-05 21:23:02.044+02	2025-11-28 21:23:02.044561+02
20	6	791790ca-e344-432b-898c-096d5afeab51	\N	\N	2025-12-05 21:23:12.277+02	2025-11-28 21:23:12.27823+02
21	9	1c5b7029-eb49-41f7-806a-fda3f17eaa9a	\N	\N	2025-12-05 23:40:38.327+02	2025-11-28 23:40:38.328848+02
22	9	366e1f56-753b-45d8-8fcf-4cafa459b009	\N	\N	2025-12-05 23:43:45.014+02	2025-11-28 23:43:45.015653+02
23	9	ff018742-db23-45d1-ad0b-a4432489a180	\N	\N	2025-12-07 14:12:14.285+02	2025-11-30 14:12:14.285559+02
24	9	1328bd60-34cc-439d-b1e2-10cc13b2d599	\N	\N	2025-12-07 16:23:45.203+02	2025-11-30 16:23:45.204909+02
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, email, login, password_hash, first_name, last_name, phone, is_active, created_at, updated_at) FROM stdin;
2	manager@autoparts.test	manager	$2y$10$examplehashmanager	Ірина	Менеджер	+380502222222	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
3	user1@autoparts.test	user1	$2y$10$examplehashuser1	Дмитро	Клієнт	+380503333333	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
4	user2@autoparts.test	user2	$2y$10$examplehashuser2	Марина	Автовласенко	+380504444444	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
1	admin@autoparts.test	admin	$2y$10$examplehashadmin	Олег	Адміненко	+380501111111	t	2025-11-24 13:21:58.037792+02	2025-11-24 13:21:58.037792+02
7	nataliamartinenko4445@gmail.com	ATE	$2b$10$0Ea3fPV6TZ4TALt6DkN3I.M51nixijY4CG4HS6JyeY8McnBnXKYS.	ATE	\N	\N	f	2025-11-25 15:14:49.543259+02	2025-11-26 00:51:45.001001+02
8	alehamartinenko8@gmail.com	LC-system32	$2b$10$SglvVz5rPycoLKgnYkVlHejqrS1zvpQnBELfkO6ZHIdPPydK9bXfm	\N	\N	\N	t	2025-11-26 01:35:52.440644+02	2025-11-26 15:11:13.444667+02
6	pj_admin@gmail.com	pj_admin	$2b$10$p0La26Zm4tyc1oWuh6WD7.UjhKjRpg60UiYOvSrK8aeKSOXezUer.	pj_admin11	Л	123123123	t	2025-11-24 14:38:52.005253+02	2025-11-28 14:59:47.159545+02
9	alehamartinenko75@gmail.com	google_alehamartinenko75_878616	$2b$10$bZpDxYG0Aa5toeBiB15.We0jO1ebvT.LuyBBKPYxODQtBdOo3LyTS	тут ничего нет		\N	t	2025-11-28 23:40:38.32503+02	2025-11-28 23:40:38.32503+02
\.


--
-- Data for Name: warehouses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.warehouses (id, supplier_id, name, code, address, city, is_active) FROM stdin;
1	1	Склад Київ, центральний	WH-KYIV-1	вул. Складська, 1	Київ	t
2	2	Склад Львів, хаб	WH-LVIV-1	вул. Промислова, 5	Львів	t
3	3	Київ, AutoTrade UA	UA-KYIV-01	вул. Промислова, 10	Київ	t
4	4	Berlin Zentrallager	DE-BERLIN-01	Industriestr. 25	Berlin	t
\.


--
-- Data for Name: wishlist_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.wishlist_items (wishlist_id, product_id, added_at) FROM stdin;
1	1	2025-11-22 13:21:58.037792+02
1	2	2025-11-22 13:21:58.037792+02
1	3	2025-11-23 13:21:58.037792+02
2	5	2025-11-23 13:21:58.037792+02
2	9	2025-11-24 01:21:58.037792+02
3	6	2025-11-27 10:47:09.262049+02
3	8	2025-11-28 19:07:53.412067+02
\.


--
-- Data for Name: wishlists; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.wishlists (id, user_id, created_at) FROM stdin;
1	3	2025-11-22 13:21:58.037792+02
2	4	2025-11-23 13:21:58.037792+02
3	6	2025-11-27 10:47:00.604233+02
\.


--
-- Name: addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.addresses_id_seq', 14, true);


--
-- Name: attribute_options_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attribute_options_id_seq', 1, false);


--
-- Name: attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attributes_id_seq', 1, false);


--
-- Name: brands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.brands_id_seq', 91, true);


--
-- Name: car_generations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.car_generations_id_seq', 1, false);


--
-- Name: car_makes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.car_makes_id_seq', 1, false);


--
-- Name: car_models_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.car_models_id_seq', 1, false);


--
-- Name: car_modifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.car_modifications_id_seq', 1, false);


--
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 40, true);


--
-- Name: carts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.carts_id_seq', 24, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 86, true);


--
-- Name: delivery_methods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.delivery_methods_id_seq', 1, false);


--
-- Name: discounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.discounts_id_seq', 4, true);


--
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_items_id_seq', 17, true);


--
-- Name: order_status_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_status_history_id_seq', 20, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 13, true);


--
-- Name: payment_methods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.payment_methods_id_seq', 1, false);


--
-- Name: payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.payments_id_seq', 1, false);


--
-- Name: product_attribute_values_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_attribute_values_id_seq', 9, true);


--
-- Name: product_fitments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_fitments_id_seq', 58, true);


--
-- Name: product_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_images_id_seq', 97, true);


--
-- Name: product_offers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_offers_id_seq', 99, true);


--
-- Name: product_reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_reviews_id_seq', 9, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 106, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 1, false);


--
-- Name: stock_movements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stock_movements_id_seq', 1, false);


--
-- Name: suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.suppliers_id_seq', 1, false);


--
-- Name: support_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.support_messages_id_seq', 6, true);


--
-- Name: support_tickets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.support_tickets_id_seq', 3, true);


--
-- Name: user_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_sessions_id_seq', 24, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 9, true);


--
-- Name: warehouses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.warehouses_id_seq', 1, false);


--
-- Name: wishlists_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.wishlists_id_seq', 3, true);


--
-- Name: addresses addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_pkey PRIMARY KEY (id);


--
-- Name: attribute_options attribute_options_attribute_id_value_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_options
    ADD CONSTRAINT attribute_options_attribute_id_value_key UNIQUE (attribute_id, value);


--
-- Name: attribute_options attribute_options_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_options
    ADD CONSTRAINT attribute_options_pkey PRIMARY KEY (id);


--
-- Name: attributes attributes_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT attributes_code_key UNIQUE (code);


--
-- Name: attributes attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT attributes_pkey PRIMARY KEY (id);


--
-- Name: brands brands_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_name_key UNIQUE (name);


--
-- Name: brands brands_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (id);


--
-- Name: brands brands_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_slug_key UNIQUE (slug);


--
-- Name: car_generations car_generations_model_id_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_generations
    ADD CONSTRAINT car_generations_model_id_name_key UNIQUE (model_id, name);


--
-- Name: car_generations car_generations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_generations
    ADD CONSTRAINT car_generations_pkey PRIMARY KEY (id);


--
-- Name: car_makes car_makes_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_makes
    ADD CONSTRAINT car_makes_name_key UNIQUE (name);


--
-- Name: car_makes car_makes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_makes
    ADD CONSTRAINT car_makes_pkey PRIMARY KEY (id);


--
-- Name: car_makes car_makes_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_makes
    ADD CONSTRAINT car_makes_slug_key UNIQUE (slug);


--
-- Name: car_models car_models_make_id_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_models
    ADD CONSTRAINT car_models_make_id_name_key UNIQUE (make_id, name);


--
-- Name: car_models car_models_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_models
    ADD CONSTRAINT car_models_pkey PRIMARY KEY (id);


--
-- Name: car_modifications car_modifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_modifications
    ADD CONSTRAINT car_modifications_pkey PRIMARY KEY (id);


--
-- Name: cart_items cart_items_cart_id_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_product_id_key UNIQUE (cart_id, product_id);


--
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id);


--
-- Name: carts carts_session_token_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_session_token_key UNIQUE (session_token);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- Name: delivery_methods delivery_methods_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery_methods
    ADD CONSTRAINT delivery_methods_code_key UNIQUE (code);


--
-- Name: delivery_methods delivery_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.delivery_methods
    ADD CONSTRAINT delivery_methods_pkey PRIMARY KEY (id);


--
-- Name: discount_categories discount_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_categories
    ADD CONSTRAINT discount_categories_pkey PRIMARY KEY (discount_id, category_id);


--
-- Name: discount_products discount_products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_products
    ADD CONSTRAINT discount_products_pkey PRIMARY KEY (discount_id, product_id);


--
-- Name: discounts discounts_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_code_key UNIQUE (code);


--
-- Name: discounts discounts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: order_status_history order_status_history_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_pkey PRIMARY KEY (id);


--
-- Name: orders orders_order_number_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_order_number_key UNIQUE (order_number);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: payment_methods payment_methods_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_code_key UNIQUE (code);


--
-- Name: payment_methods payment_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_pkey PRIMARY KEY (id);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- Name: product_attribute_values product_attribute_values_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values
    ADD CONSTRAINT product_attribute_values_pkey PRIMARY KEY (id);


--
-- Name: product_attribute_values product_attribute_values_product_id_attribute_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values
    ADD CONSTRAINT product_attribute_values_product_id_attribute_id_key UNIQUE (product_id, attribute_id);


--
-- Name: product_fitments product_fitments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_fitments
    ADD CONSTRAINT product_fitments_pkey PRIMARY KEY (id);


--
-- Name: product_fitments product_fitments_product_id_modification_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_fitments
    ADD CONSTRAINT product_fitments_product_id_modification_id_key UNIQUE (product_id, modification_id);


--
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- Name: product_offers product_offers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_offers
    ADD CONSTRAINT product_offers_pkey PRIMARY KEY (id);


--
-- Name: product_offers product_offers_product_id_warehouse_id_supplier_sku_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_offers
    ADD CONSTRAINT product_offers_product_id_warehouse_id_supplier_sku_key UNIQUE (product_id, warehouse_id, supplier_sku);


--
-- Name: product_oil_specs product_oil_specs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_oil_specs
    ADD CONSTRAINT product_oil_specs_pkey PRIMARY KEY (product_id);


--
-- Name: product_reviews product_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_sku_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_sku_key UNIQUE (sku);


--
-- Name: products products_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_key UNIQUE (slug);


--
-- Name: roles roles_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_code_key UNIQUE (code);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: stock_movements stock_movements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_movements
    ADD CONSTRAINT stock_movements_pkey PRIMARY KEY (id);


--
-- Name: suppliers suppliers_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_code_key UNIQUE (code);


--
-- Name: suppliers suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.suppliers
    ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);


--
-- Name: support_messages support_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_messages
    ADD CONSTRAINT support_messages_pkey PRIMARY KEY (id);


--
-- Name: support_tickets support_tickets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_tickets
    ADD CONSTRAINT support_tickets_pkey PRIMARY KEY (id);


--
-- Name: user_roles user_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_pkey PRIMARY KEY (user_id, role_id);


--
-- Name: user_sessions user_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT user_sessions_pkey PRIMARY KEY (id);


--
-- Name: user_sessions user_sessions_token_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT user_sessions_token_key UNIQUE (token);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_login_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_login_key UNIQUE (login);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: warehouses warehouses_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouses
    ADD CONSTRAINT warehouses_code_key UNIQUE (code);


--
-- Name: warehouses warehouses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouses
    ADD CONSTRAINT warehouses_pkey PRIMARY KEY (id);


--
-- Name: wishlist_items wishlist_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist_items
    ADD CONSTRAINT wishlist_items_pkey PRIMARY KEY (wishlist_id, product_id);


--
-- Name: wishlists wishlists_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_pkey PRIMARY KEY (id);


--
-- Name: discounts_active_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX discounts_active_idx ON public.discounts USING btree (active);


--
-- Name: discounts_code_lower_uidx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX discounts_code_lower_uidx ON public.discounts USING btree (lower(code)) WHERE (code IS NOT NULL);


--
-- Name: discounts_date_from_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX discounts_date_from_idx ON public.discounts USING btree (date_from);


--
-- Name: discounts_date_to_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX discounts_date_to_idx ON public.discounts USING btree (date_to);


--
-- Name: idx_car_generations_model; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_car_generations_model ON public.car_generations USING btree (model_id);


--
-- Name: idx_car_models_make; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_car_models_make ON public.car_models USING btree (make_id);


--
-- Name: idx_car_modifications_gen; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_car_modifications_gen ON public.car_modifications USING btree (generation_id);


--
-- Name: idx_categories_parent_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_categories_parent_id ON public.categories USING btree (parent_id);


--
-- Name: idx_products_brand; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_brand ON public.products USING btree (brand_id);


--
-- Name: idx_products_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_category ON public.products USING btree (category_id);


--
-- Name: idx_support_messages_ticket_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_support_messages_ticket_id ON public.support_messages USING btree (ticket_id);


--
-- Name: addresses addresses_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: attribute_options attribute_options_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_options
    ADD CONSTRAINT attribute_options_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attributes(id) ON DELETE CASCADE;


--
-- Name: car_generations car_generations_model_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_generations
    ADD CONSTRAINT car_generations_model_id_fkey FOREIGN KEY (model_id) REFERENCES public.car_models(id) ON DELETE CASCADE;


--
-- Name: car_models car_models_make_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_models
    ADD CONSTRAINT car_models_make_id_fkey FOREIGN KEY (make_id) REFERENCES public.car_makes(id) ON DELETE CASCADE;


--
-- Name: car_modifications car_modifications_generation_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.car_modifications
    ADD CONSTRAINT car_modifications_generation_id_fkey FOREIGN KEY (generation_id) REFERENCES public.car_generations(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_cart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_fkey FOREIGN KEY (cart_id) REFERENCES public.carts(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: carts carts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: categories categories_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: discount_categories discount_categories_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_categories
    ADD CONSTRAINT discount_categories_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: discount_categories discount_categories_discount_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_categories
    ADD CONSTRAINT discount_categories_discount_id_fkey FOREIGN KEY (discount_id) REFERENCES public.discounts(id) ON DELETE CASCADE;


--
-- Name: discount_products discount_products_discount_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_products
    ADD CONSTRAINT discount_products_discount_id_fkey FOREIGN KEY (discount_id) REFERENCES public.discounts(id) ON DELETE CASCADE;


--
-- Name: discount_products discount_products_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_products
    ADD CONSTRAINT discount_products_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE SET NULL;


--
-- Name: order_status_history order_status_history_changed_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_changed_by_fkey FOREIGN KEY (changed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: order_status_history order_status_history_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: orders orders_billing_address_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_billing_address_id_fkey FOREIGN KEY (billing_address_id) REFERENCES public.addresses(id);


--
-- Name: orders orders_delivery_method_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_delivery_method_id_fkey FOREIGN KEY (delivery_method_id) REFERENCES public.delivery_methods(id);


--
-- Name: orders orders_payment_method_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_payment_method_id_fkey FOREIGN KEY (payment_method_id) REFERENCES public.payment_methods(id);


--
-- Name: orders orders_shipping_address_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_shipping_address_id_fkey FOREIGN KEY (shipping_address_id) REFERENCES public.addresses(id);


--
-- Name: orders orders_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: payments payments_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: product_attribute_values product_attribute_values_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values
    ADD CONSTRAINT product_attribute_values_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attributes(id) ON DELETE CASCADE;


--
-- Name: product_attribute_values product_attribute_values_option_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values
    ADD CONSTRAINT product_attribute_values_option_id_fkey FOREIGN KEY (option_id) REFERENCES public.attribute_options(id) ON DELETE SET NULL;


--
-- Name: product_attribute_values product_attribute_values_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_attribute_values
    ADD CONSTRAINT product_attribute_values_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_fitments product_fitments_modification_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_fitments
    ADD CONSTRAINT product_fitments_modification_id_fkey FOREIGN KEY (modification_id) REFERENCES public.car_modifications(id) ON DELETE CASCADE;


--
-- Name: product_fitments product_fitments_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_fitments
    ADD CONSTRAINT product_fitments_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_images product_images_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_offers product_offers_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_offers
    ADD CONSTRAINT product_offers_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_offers product_offers_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_offers
    ADD CONSTRAINT product_offers_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES public.warehouses(id) ON DELETE CASCADE;


--
-- Name: product_oil_specs product_oil_specs_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_oil_specs
    ADD CONSTRAINT product_oil_specs_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_reviews product_reviews_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_reviews product_reviews_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: products products_brand_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_brand_id_fkey FOREIGN KEY (brand_id) REFERENCES public.brands(id) ON DELETE SET NULL;


--
-- Name: products products_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: stock_movements stock_movements_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_movements
    ADD CONSTRAINT stock_movements_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: stock_movements stock_movements_product_offer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_movements
    ADD CONSTRAINT stock_movements_product_offer_id_fkey FOREIGN KEY (product_offer_id) REFERENCES public.product_offers(id) ON DELETE CASCADE;


--
-- Name: stock_movements stock_movements_related_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_movements
    ADD CONSTRAINT stock_movements_related_order_id_fkey FOREIGN KEY (related_order_id) REFERENCES public.orders(id) ON DELETE SET NULL;


--
-- Name: support_messages support_messages_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_messages
    ADD CONSTRAINT support_messages_author_id_fkey FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: support_messages support_messages_ticket_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_messages
    ADD CONSTRAINT support_messages_ticket_id_fkey FOREIGN KEY (ticket_id) REFERENCES public.support_tickets(id) ON DELETE CASCADE;


--
-- Name: support_tickets support_tickets_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.support_tickets
    ADD CONSTRAINT support_tickets_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_roles user_roles_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: user_roles user_roles_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_sessions user_sessions_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_sessions
    ADD CONSTRAINT user_sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: warehouses warehouses_supplier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.warehouses
    ADD CONSTRAINT warehouses_supplier_id_fkey FOREIGN KEY (supplier_id) REFERENCES public.suppliers(id) ON DELETE SET NULL;


--
-- Name: wishlist_items wishlist_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist_items
    ADD CONSTRAINT wishlist_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: wishlist_items wishlist_items_wishlist_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist_items
    ADD CONSTRAINT wishlist_items_wishlist_id_fkey FOREIGN KEY (wishlist_id) REFERENCES public.wishlists(id) ON DELETE CASCADE;


--
-- Name: wishlists wishlists_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlists
    ADD CONSTRAINT wishlists_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT USAGE ON SCHEMA public TO "user";


--
-- Name: TABLE addresses; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.addresses TO "user";


--
-- Name: SEQUENCE addresses_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.addresses_id_seq TO "user";


--
-- Name: TABLE attribute_options; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.attribute_options TO "user";


--
-- Name: SEQUENCE attribute_options_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.attribute_options_id_seq TO "user";


--
-- Name: TABLE attributes; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.attributes TO "user";


--
-- Name: SEQUENCE attributes_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.attributes_id_seq TO "user";


--
-- Name: TABLE brands; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.brands TO "user";


--
-- Name: SEQUENCE brands_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.brands_id_seq TO "user";


--
-- Name: TABLE car_generations; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.car_generations TO "user";


--
-- Name: SEQUENCE car_generations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.car_generations_id_seq TO "user";


--
-- Name: TABLE car_makes; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.car_makes TO "user";


--
-- Name: SEQUENCE car_makes_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.car_makes_id_seq TO "user";


--
-- Name: TABLE car_models; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.car_models TO "user";


--
-- Name: SEQUENCE car_models_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.car_models_id_seq TO "user";


--
-- Name: TABLE car_modifications; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.car_modifications TO "user";


--
-- Name: SEQUENCE car_modifications_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.car_modifications_id_seq TO "user";


--
-- Name: TABLE cart_items; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.cart_items TO "user";


--
-- Name: SEQUENCE cart_items_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.cart_items_id_seq TO "user";


--
-- Name: TABLE carts; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.carts TO "user";


--
-- Name: SEQUENCE carts_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.carts_id_seq TO "user";


--
-- Name: TABLE categories; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.categories TO "user";


--
-- Name: SEQUENCE categories_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.categories_id_seq TO "user";


--
-- Name: TABLE delivery_methods; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.delivery_methods TO "user";


--
-- Name: SEQUENCE delivery_methods_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.delivery_methods_id_seq TO "user";


--
-- Name: TABLE discount_categories; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.discount_categories TO "user";


--
-- Name: TABLE discount_products; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.discount_products TO "user";


--
-- Name: TABLE discounts; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.discounts TO "user";


--
-- Name: SEQUENCE discounts_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.discounts_id_seq TO "user";


--
-- Name: TABLE order_items; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.order_items TO "user";


--
-- Name: SEQUENCE order_items_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.order_items_id_seq TO "user";


--
-- Name: TABLE order_status_history; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.order_status_history TO "user";


--
-- Name: SEQUENCE order_status_history_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.order_status_history_id_seq TO "user";


--
-- Name: TABLE orders; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.orders TO "user";


--
-- Name: SEQUENCE orders_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.orders_id_seq TO "user";


--
-- Name: TABLE payment_methods; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.payment_methods TO "user";


--
-- Name: SEQUENCE payment_methods_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.payment_methods_id_seq TO "user";


--
-- Name: TABLE payments; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.payments TO "user";


--
-- Name: SEQUENCE payments_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.payments_id_seq TO "user";


--
-- Name: TABLE product_attribute_values; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_attribute_values TO "user";


--
-- Name: SEQUENCE product_attribute_values_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_attribute_values_id_seq TO "user";


--
-- Name: TABLE product_fitments; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_fitments TO "user";


--
-- Name: SEQUENCE product_fitments_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_fitments_id_seq TO "user";


--
-- Name: TABLE product_images; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_images TO "user";


--
-- Name: SEQUENCE product_images_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_images_id_seq TO "user";


--
-- Name: TABLE product_offers; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_offers TO "user";


--
-- Name: SEQUENCE product_offers_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_offers_id_seq TO "user";


--
-- Name: TABLE product_oil_specs; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_oil_specs TO "user";


--
-- Name: TABLE product_reviews; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.product_reviews TO "user";


--
-- Name: SEQUENCE product_reviews_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_reviews_id_seq TO "user";


--
-- Name: TABLE products; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.products TO "user";


--
-- Name: SEQUENCE products_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.products_id_seq TO "user";


--
-- Name: TABLE roles; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.roles TO "user";


--
-- Name: SEQUENCE roles_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.roles_id_seq TO "user";


--
-- Name: TABLE stock_movements; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.stock_movements TO "user";


--
-- Name: SEQUENCE stock_movements_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.stock_movements_id_seq TO "user";


--
-- Name: TABLE suppliers; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.suppliers TO "user";


--
-- Name: SEQUENCE suppliers_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.suppliers_id_seq TO "user";


--
-- Name: TABLE support_messages; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.support_messages TO "user";


--
-- Name: SEQUENCE support_messages_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.support_messages_id_seq TO "user";


--
-- Name: TABLE support_tickets; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.support_tickets TO "user";


--
-- Name: SEQUENCE support_tickets_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.support_tickets_id_seq TO "user";


--
-- Name: TABLE user_roles; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.user_roles TO "user";


--
-- Name: TABLE user_sessions; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.user_sessions TO "user";


--
-- Name: SEQUENCE user_sessions_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.user_sessions_id_seq TO "user";


--
-- Name: TABLE users; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.users TO "user";


--
-- Name: SEQUENCE users_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.users_id_seq TO "user";


--
-- Name: TABLE warehouses; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.warehouses TO "user";


--
-- Name: SEQUENCE warehouses_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.warehouses_id_seq TO "user";


--
-- Name: TABLE wishlist_items; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.wishlist_items TO "user";


--
-- Name: TABLE wishlists; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.wishlists TO "user";


--
-- Name: SEQUENCE wishlists_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.wishlists_id_seq TO "user";


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO "user";


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT SELECT,INSERT,DELETE,UPDATE ON TABLES TO "user";


--
-- PostgreSQL database dump complete
--

