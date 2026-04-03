-- This initialisation script creates each of the tables in the database needed for the analyses to run
-- Each record in a table can be traced back to the initial search via the search_id key which is a unique key
-- In the website code this id is made up by base64 encoding the list of resturned ids from NCBI, so every search
-- Recorded in the db is truly unique (e.g. if 2 searches brought back the same list of ids, this would result in the 
-- same search id so a user would just be taken to the existing results)
create table if not exists searches(
    search_id varchar(200) primary key,
    protein varchar(100) not null,
    taxonomic_group varchar(100) not null
);

create table if not exists ncbi(
    ncbi_id varchar(100) primary key,
    p_sequence text not null,
    p_length int as (char_length(p_sequence)) stored,
    p_description text not null,
    organism varchar(200),
    protein text,
    search_id varchar(200) not null,
    foreign key (search_id) references searches(search_id) on delete cascade
);

create table if not exists motif(
    id serial primary key,
    ncbi_id varchar(100) not null,
    seq_name varchar(100),
    s_start bigint,
    s_end bigint,
    score bigint,
    strand varchar(1),
    motif varchar(100),
    search_id varchar(200) not null,
    foreign key (ncbi_id) references ncbi(ncbi_id),
    foreign key (search_id) references searches(search_id) on delete cascade
);

create table if not exists conservation(
    id serial primary key,
    plotcon_file varchar(500) not null,
    clustalo_file varchar(500) not null,
    search_id varchar(200) not null,
    foreign key (search_id) references searches(search_id) on delete cascade
);

create table if not exists pepstats(
    id serial primary key,
    ncbi_id varchar(100) not null,
    report_content text not null,
    search_id varchar(200) not null,
    foreign key (ncbi_id) references ncbi(ncbi_id),
    foreign key (search_id) references searches(search_id) on delete cascade
);