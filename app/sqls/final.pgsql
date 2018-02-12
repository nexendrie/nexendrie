-- Foreign keys --
ALTER TABLE adventure_npcs ADD CONSTRAINT adventure_npcs_ibfk_1 FOREIGN KEY (adventure) REFERENCES adventures (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON adventure_npcs (adventure);
ALTER TABLE adventures ADD CONSTRAINT adventures_ibfk_1 FOREIGN KEY (event) REFERENCES events (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON adventures (event);
ALTER TABLE articles ADD CONSTRAINT articles_ibfk_1 FOREIGN KEY (author) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON articles (author);
ALTER TABLE beer_production ADD CONSTRAINT beer_production_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON beer_production ("user");
ALTER TABLE beer_production ADD CONSTRAINT beer_production_ibfk_2 FOREIGN KEY (house) REFERENCES houses (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON beer_production (house);
ALTER TABLE castles ADD CONSTRAINT castles_ibfk_1 FOREIGN KEY (owner) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON castles (owner);
ALTER TABLE comments ADD CONSTRAINT comments_ibfk_2 FOREIGN KEY (author) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON comments (author);
ALTER TABLE comments ADD CONSTRAINT comments_ibfk_3 FOREIGN KEY (article) REFERENCES articles (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON comments (article);
ALTER TABLE deposits ADD CONSTRAINT deposits FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON deposits ("user");
ALTER TABLE election_results ADD CONSTRAINT election_results_ibfk_1 FOREIGN KEY (candidate) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON election_results (candidate);
ALTER TABLE election_results ADD CONSTRAINT election_results_ibfk_2 FOREIGN KEY (town) REFERENCES towns (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON election_results (town);
ALTER TABLE elections ADD CONSTRAINT elections_ibfk_1 FOREIGN KEY (candidate) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON elections (candidate);
ALTER TABLE elections ADD CONSTRAINT elections_ibfk_2 FOREIGN KEY (voter) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON elections (voter);
ALTER TABLE elections ADD CONSTRAINT elections_ibfk_3 FOREIGN KEY (town) REFERENCES towns (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON elections (town);
ALTER TABLE guilds ADD CONSTRAINT guilds_ibfk_1 FOREIGN KEY (town) REFERENCES towns (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON guilds (town);
ALTER TABLE guilds ADD CONSTRAINT guilds_ibfk_2 FOREIGN KEY (skill) REFERENCES skills (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON guilds (skill);
ALTER TABLE houses ADD CONSTRAINT houses_ibfk_1 FOREIGN KEY (owner) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON houses (owner);
ALTER TABLE item_sets ADD CONSTRAINT item_sets_ibfk_1 FOREIGN KEY (weapon) REFERENCES items (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON item_sets (weapon);
ALTER TABLE item_sets ADD CONSTRAINT item_sets_ibfk_2 FOREIGN KEY (armor) REFERENCES items (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON item_sets (armor);
ALTER TABLE item_sets ADD CONSTRAINT item_sets_ibfk_3 FOREIGN KEY (helmet) REFERENCES items (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON item_sets (helmet);
ALTER TABLE items ADD CONSTRAINT items_ibfk_2 FOREIGN KEY (shop) REFERENCES shops (id) ON DELETE SET NULL ON UPDATE CASCADE DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON items (shop);
ALTER TABLE job_messages ADD CONSTRAINT job_messages_ibfk_1 FOREIGN KEY (job) REFERENCES jobs (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON job_messages (job);
ALTER TABLE jobs ADD CONSTRAINT jobs_ibfk_1 FOREIGN KEY (needed_skill) REFERENCES skills (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON jobs (needed_skill);
ALTER TABLE loans ADD CONSTRAINT loans_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON loans ("user");
ALTER TABLE marriages ADD CONSTRAINT marriages_ibfk_1 FOREIGN KEY (user1) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON marriages (user1);
ALTER TABLE marriages ADD CONSTRAINT marriages_ibfk_2 FOREIGN KEY (user2) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON marriages (user2);
ALTER TABLE messages ADD CONSTRAINT messages_ibfk_1 FOREIGN KEY ("from") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON messages ("from");
ALTER TABLE messages ADD CONSTRAINT messages_ibfk_2 FOREIGN KEY ("to") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON messages ("to");
ALTER TABLE monasteries ADD CONSTRAINT monasteries_ibfk_1 FOREIGN KEY (leader) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON monasteries (leader);
ALTER TABLE monasteries ADD CONSTRAINT monasteries_ibfk_2 FOREIGN KEY (town) REFERENCES towns (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON monasteries (town);
ALTER TABLE monastery_donations ADD CONSTRAINT monastery_donations_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON monastery_donations ("user");
ALTER TABLE monastery_donations ADD CONSTRAINT monastery_donations_ibfk_2 FOREIGN KEY (monastery) REFERENCES monasteries (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON monastery_donations (monastery);
ALTER TABLE mounts ADD CONSTRAINT mounts_ibfk_1 FOREIGN KEY (owner) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON mounts (owner);
ALTER TABLE mounts ADD CONSTRAINT mounts_ibfk_2 FOREIGN KEY (type) REFERENCES mount_types (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON mounts (type);
ALTER TABLE permissions ADD CONSTRAINT permissions_ibfk_2 FOREIGN KEY ("group") REFERENCES groups (id) ON UPDATE CASCADE DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON permissions ("group");
ALTER TABLE poll_votes ADD CONSTRAINT poll_votes_ibfk_1 FOREIGN KEY (poll) REFERENCES polls (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON poll_votes (poll);
ALTER TABLE poll_votes ADD CONSTRAINT poll_votes_ibfk_2 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON poll_votes ("user");
ALTER TABLE polls ADD CONSTRAINT polls_ibfk_1 FOREIGN KEY (author) REFERENCES users (id) ON UPDATE CASCADE DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON polls (author);
ALTER TABLE punishments ADD CONSTRAINT punishments_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON punishments ("user");
ALTER TABLE towns ADD CONSTRAINT towns_ibfk_1 FOREIGN KEY (owner) REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON towns (owner);
ALTER TABLE user_adventures ADD CONSTRAINT user_adventures_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_adventures ("user");
ALTER TABLE user_adventures ADD CONSTRAINT user_adventures_ibfk_2 FOREIGN KEY (adventure) REFERENCES adventures (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_adventures (adventure);
ALTER TABLE user_adventures ADD CONSTRAINT user_adventures_ibfk_3 FOREIGN KEY (mount) REFERENCES mounts (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_adventures (mount);
ALTER TABLE user_items ADD CONSTRAINT user_items_ibfk_1 FOREIGN KEY (item) REFERENCES items (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_items (item);
ALTER TABLE user_items ADD CONSTRAINT user_items_ibfk_2 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_items ("user");
ALTER TABLE user_jobs ADD CONSTRAINT user_jobs_ibfk_1 FOREIGN KEY (job) REFERENCES jobs (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_jobs (job);
ALTER TABLE user_jobs ADD CONSTRAINT user_jobs_ibfk_2 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_jobs ("user");
ALTER TABLE user_skills ADD CONSTRAINT user_skills_ibfk_1 FOREIGN KEY ("user") REFERENCES users (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_skills ("user");
ALTER TABLE user_skills ADD CONSTRAINT user_skills_ibfk_2 FOREIGN KEY (skill) REFERENCES skills (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON user_skills (skill);
ALTER TABLE users ADD CONSTRAINT users_ibfk_1 FOREIGN KEY ("group") REFERENCES groups (id) ON UPDATE CASCADE DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users ("group");
ALTER TABLE users ADD CONSTRAINT users_ibfk_2 FOREIGN KEY (town) REFERENCES towns (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (town);
ALTER TABLE users ADD CONSTRAINT users_ibfk_3 FOREIGN KEY (monastery) REFERENCES monasteries (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (monastery);
ALTER TABLE users ADD CONSTRAINT users_ibfk_4 FOREIGN KEY (castle) REFERENCES castles (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (castle);
ALTER TABLE users ADD CONSTRAINT users_ibfk_5 FOREIGN KEY (house) REFERENCES houses (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (house);
ALTER TABLE users ADD CONSTRAINT users_ibfk_6 FOREIGN KEY (guild) REFERENCES guilds (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (guild);
ALTER TABLE users ADD CONSTRAINT users_ibfk_7 FOREIGN KEY (guild_rank) REFERENCES guild_ranks (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (guild_rank);
ALTER TABLE users ADD CONSTRAINT users_ibfk_8 FOREIGN KEY ("order") REFERENCES orders (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users ("order");
ALTER TABLE users ADD CONSTRAINT users_ibfk_9 FOREIGN KEY (order_rank) REFERENCES order_ranks (id) DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON users (order_rank);
