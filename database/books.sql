CREATE TABLE public.books (
	id uuid DEFAULT gen_random_uuid() NOT NULL,
	"name" varchar NOT NULL,
	"author_id" uuid NOT NULL,
	created_at timestamp DEFAULT now() NOT NULL,
	CONSTRAINT books_name_key UNIQUE (name),
	CONSTRAINT books_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.authors (id),
	CONSTRAINT books_pkey PRIMARY KEY (id)
);