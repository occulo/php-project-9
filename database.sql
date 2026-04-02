create table if not exists urls (
    id bigint primary key generated always as identity,
    name varchar(255),
    created_at timestamp
);