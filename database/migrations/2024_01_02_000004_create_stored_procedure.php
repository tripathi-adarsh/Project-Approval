<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');

        DB::unprepared("
            CREATE PROCEDURE sp_approve_project(
                IN  p_project_id  BIGINT UNSIGNED,
                IN  p_admin_id    BIGINT UNSIGNED,
                OUT p_result      TINYINT
            )
            BEGIN
                DECLARE v_count INT DEFAULT 0;

                SELECT COUNT(*) INTO v_count
                FROM projects
                WHERE id = p_project_id AND status = 'pending';

                IF v_count = 0 THEN
                    SET p_result = 0;
                ELSE
                    UPDATE projects
                       SET status = 'approved', updated_at = NOW()
                     WHERE id = p_project_id;

                    INSERT INTO approvals (project_id, admin_id, status, created_at, updated_at)
                    VALUES (p_project_id, p_admin_id, 'approved', NOW(), NOW());

                    INSERT INTO audit_logs (action, user_id, project_id, meta, created_at)
                    VALUES ('project_approved', p_admin_id, p_project_id,
                            JSON_OBJECT('via', 'stored_procedure'), NOW());

                    SET p_result = 1;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');
    }
};
