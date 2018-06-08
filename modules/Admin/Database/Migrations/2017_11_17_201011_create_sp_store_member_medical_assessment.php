<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpStoreMemberMedicalAssessment extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS storeMemberMedicalAssessment;

        CREATE PROCEDURE storeMemberMedicalAssessment(IN memberId INT, IN currentMedicalProblem TEXT, IN epilipsyDate DATE, IN followupDate DATE, IN assessmentDate DATE, IN otherProblem VARCHAR(100), IN physicalFinding VARCHAR(100), IN systemicExamination VARCHAR(100), IN gyane VARCHAR(100), IN birthWeight VARCHAR(100), IN sleepingPattern TINYINT, IN medicalHistory VARCHAR(100), IN diabeteseHistory VARCHAR(100), IN detailedHistory VARCHAR(100), IN treatmenatHistory VARCHAR(100), IN investigation VARCHAR(100), IN doctorName VARCHAR(100))

        BEGIN

            DECLARE medical_assessment_id INT;

            IF EXISTS(SELECT id FROM member_medical_assessment WHERE member_id=memberId) THEN

                UPDATE member_medical_assessment SET current_associated_medical_problem=currentMedicalProblem, epilepsy=epilipsyDate, other=otherProblem, physical_finding=physicalFinding, systemic_examination=systemicExamination, gynae_obstetrics_history=gyane, clients_birth_weight=birthWeight, sleeping_pattern=sleepingPattern, past_mediacl_history=medicalHistory, family_history_of_diabetes_obesity=diabeteseHistory, detailed_history=detailedHistory, treatment_history=treatmenatHistory, suggested_investigation=investigation, followup_date=followupDate, doctors_name=doctorName, assessment_date=assessmentDate
                WHERE member_id=memberId;

                SELECT id as medical_assessment_id FROM member_medical_assessment WHERE member_id=memberId;

            ELSE

                INSERT INTO member_medical_assessment (member_id,current_associated_medical_problem,epilepsy,other,physical_finding,systemic_examination,gynae_obstetrics_history,clients_birth_weight,sleeping_pattern,past_mediacl_history,family_history_of_diabetes_obesity,detailed_history,treatment_history,suggested_investigation,followup_date,doctors_name,assessment_date)
                VALUES(memberId,currentMedicalProblem,epilipsyDate,otherProblem,physicalFinding,systemicExamination,gyane,birthWeight,sleepingPattern,medicalHistory,diabeteseHistory,detailedHistory,treatmenatHistory,investigation,followupDate,doctorName,assessmentDate);

                IF LAST_INSERT_ID() > 0 THEN
                    SELECT LAST_INSERT_ID() AS medical_assessment_id;
                END IF;

            END IF;

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS storeMemberMedicalAssessment');
    }

}
