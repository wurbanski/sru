ALTER TABLE switches_port ADD COLUMN penalty_id bigint;
ALTER TABLE switches_port ADD CONSTRAINT switches_port_penalty_id_fkey FOREIGN KEY (penalty_id) REFERENCES penalties (id) ON UPDATE CASCADE ON DELETE SET NULL;