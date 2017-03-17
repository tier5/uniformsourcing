CREATE TABLE public.tbl_container
(
  "containerId" bigint NOT NULL DEFAULT nextval(('tbl_container_containerId_seq'::text)::regclass),
  "styleId" bigint NOT NULL,
  "scaleId" bigint,
  "sizeScaleId" bigint,
  "colorId" bigint,
  "opt1ScaleId" bigint,
  "opt2ScaleId" bigint,
  quantity integer,
  price character varying(50),
  "locationId" character varying(200),
  notes character varying,
  "styleNumber" character varying(20),
  "mainSize" character varying(150),
  "rowSize" character varying(150),
  "isStorage" smallint DEFAULT 0,
  "newQty" integer DEFAULT 0,
  "isActive" smallint NOT NULL DEFAULT 1,
  "createdBy" bigint DEFAULT 0,
  "createdDate" bigint DEFAULT 0,
  "updatedBy" bigint DEFAULT 0,
  "updatedDate" bigint DEFAULT 0,
  "columnSize" character varying(150),
  CONSTRAINT tbl_container_pkey PRIMARY KEY ("containerId")
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.tbl_container
  OWNER TO globaluniformuser