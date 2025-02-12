CREATE TABLE public.authors (
    id uuid DEFAULT gen_random_uuid(),
    name VARCHAR unique NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id)
);