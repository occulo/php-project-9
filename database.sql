create table if not exists urls (
    id bigint primary key generated always as identity,
    name varchar(255),
    created_at timestamp
);

create table if not exists url_checks (
    id bigint primary key generated always as identity,
    url_id bigint references urls(id) not null,
    status_code int,
    h1 varchar(255),
    title text,
    description text,
    created_at timestamp
);