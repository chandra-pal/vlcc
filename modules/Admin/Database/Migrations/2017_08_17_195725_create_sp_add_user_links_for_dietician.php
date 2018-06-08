<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpAddUserLinksForDietician extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS addUserLinksForDietician;
            CREATE PROCEDURE addUserLinksForDietician(IN userId INT, IN userTypeId INT)
            addUserLinksForDietician: BEGIN
                DECLARE linkId, notFound INTEGER DEFAULT 0;
                DECLARE user_links_cursor CURSOR FOR
                SELECT link_id FROM user_type_links WHERE user_type_id = userTypeId;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;

                OPEN user_links_cursor;
                    getUserTypeLinks: LOOP
                        FETCH user_links_cursor INTO linkId;
                            IF notFound = 1 THEN
                                LEAVE getUserTypeLinks;
                            END IF;

                        IF NOT EXISTS (SELECT * FROM user_links WHERE user_id = userId AND link_id = linkId) THEN
                            IF (userTypeId = 9 OR userTypeId = 7) THEN
                                INSERT INTO user_links (user_id, link_id, is_add, is_edit, is_delete, own_view, own_edit, own_delete)
                                VALUES(userId, linkId, 0, 0, 0, 0, 0, 0);
                            ELSEIF (linkId = 23 AND (userTypeId = 4 OR userTypeId = 8)) THEN
                                INSERT INTO user_links (user_id, link_id, is_add, is_edit, is_delete, own_view, own_edit, own_delete)
                                VALUES(userId, linkId, 1, 0, 0, 0, 1, 1);
                            ELSE
                                INSERT INTO user_links (user_id, link_id, is_add, is_edit, is_delete, own_view, own_edit, own_delete)
                                VALUES(userId, linkId, 1, 1, 1, 1, 1, 1);
                            END IF;
                        ELSE
                        IF (userTypeId = 9 OR userTypeId = 7) THEN
                                UPDATE user_links SET is_add=0, is_edit=0, is_delete=0, own_view=0, own_edit=0, own_delete=0 WHERE user_id = userId AND link_id = linkId;
                        ELSEIF (linkId = 23 AND (userTypeId = 4 OR userTypeId = 8)) THEN
                                UPDATE user_links SET is_add=1, is_edit=0, is_delete=0, own_view=0, own_edit=1, own_delete=1 WHERE user_id = userId AND link_id = linkId;
                            ELSE
                                UPDATE user_links SET is_add=1, is_edit=1, is_delete=1, own_view=1, own_edit=1, own_delete=1 WHERE user_id = userId AND link_id = linkId;
                            END IF;
                        END IF;
                    END LOOP getUserTypeLinks;
                CLOSE user_links_cursor;

                SELECT 'SUCCESS' AS response;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS addUserLinksForDietician;');
    }

}
