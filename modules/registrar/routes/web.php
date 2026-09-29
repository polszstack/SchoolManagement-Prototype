<?php

use App\Controllers\CalendarController;
use App\Controllers\CourseController;
use App\Controllers\HomeController;
use App\Controllers\IssueTrackingController;
use App\Controllers\OcrController;
use App\Controllers\ReportApprovalController;
use App\Controllers\ReportSubmissionController;
use App\Controllers\StrandController;
use App\Controllers\StudentController;
use App\Controllers\ActivityController;
use App\Controllers\SchoolYearController;
use App\Controllers\SemesterController;
use App\Controllers\SubjectController;
use App\Controllers\EnroleeController;
use App\Helper\AiHelper;


// home page

    $r->get('/',[HomeController::class,'index']);
    $r->get('/students/count',[HomeController::class,'countNumber']);


// students 

    $r->get('/students',[StudentController::class,'index']);
    $r->get('/students/all',[StudentController::class,'studentData']);
    $r->get('/students/pdf',[StudentController::class,'studentPDF']);
    $r->get('/students/excel',[StudentController::class,'studentExcel']);
    $r->get('/students/csv',[StudentController::class,'studentCSV']);


// settings 

    // activity 
    $r->get('/settings/activity',[ActivityController::class,'index']);
    $r->get('/activity/pdf',[ActivityController::class,'activityPDF']);
    $r->get('/activity/excel',[ActivityController::class,'activityExcel']);
    $r->get('/activivty/csv',[ActivityController::class,'activityCSV']);
    $r->get('/activity/all',[ActivityController::class,'allActivity']);
    $r->get('/activity/{id:\d+}',[ActivityController::class,'show']);
    $r->post('/activity/delete',[ActivityController::class,'destroy']);
    
    $r->get('/activity/api',[ActivityController::class,'allActivityWithoutPagination']);


    // concerns 
 
    $r->get('/settings/concerns',[IssueTrackingController::class,'index']);
    $r->get('/concern/all',[IssueTrackingController::class,'allIssue']);
     $r->post('/concern/store',[IssueTrackingController::class,'store']);
     $r->post('/concern/delete',[IssueTrackingController::class,'destroy']);
     


// course 

    $r->get('/course',[CourseController::class,'index']);
    $r->get('/course/all',[CourseController::class,'allCourse']);
    $r->get('/course/excel',[CourseController::class,'courseExcel']);
    $r->get('/course/csv',[CourseController::class,'courseCSV']);
    $r->get('/course/pdf',[CourseController::class,'coursePDF']);
    $r->get('/course/{id:\d+}',[CourseController::class,'show']);
    $r->get('/course/{id:\d+}/edit',[CourseController::class,'edit']);
    $r->post('/course/delete',[CourseController::class,'destroy']);
    $r->post('/course/store',[CourseController::class,'store']);
    $r->post('/course/{id:\d+}/update',[CourseController::class,'update']);

// school year

    $r->get('/school-year',[SchoolYearController::class,'index']);
    $r->get('/school-year/all',[SchoolYearController::class,'allSchoolYear']);
    $r->get('/school-year/pdf',[SchoolYearController::class,'schoolYearPdf']);
    $r->get('/school-year/excel',[SchoolYearController::class,'schoolYearExcel']);
    $r->get('/school-year/csv',[SchoolYearController::class,'schoolYearCsv']);
    $r->post('/school-year/delete',[SchoolYearController::class,'destroy']);
    $r->post('/school-year/store',[SchoolYearController::class,'store']);
    $r->post('/school-year/{id:\d+}/update',[SchoolYearController::class,'update']);


// semester 

    $r->get('/semester',[SemesterController::class,'index']);
    $r->get('/semester/all',[SemesterController::class,'allSemester']);
    $r->post('/semester/delete',[SemesterController::class,'destroy']);
    $r->get('/semester/pdf',[SemesterController::class,'semesterPdf']);
    $r->get('/semester/excel',[SemesterController::class,'semesterExcel']);
    $r->get('/semester/csv',[SemesterController::class,'semesterCsv']);
    $r->post('/semester/store',[SemesterController::class,'store']);
    $r->post('/semester/{id:\d+}/update',[SemesterController::class,'update']);

// strand 

    $r->get('/strand',[StrandController::class,'index']);
    $r->get('/strand/all',[StrandController::class,'allStrands']);
    $r->get('/strand/{id:\d+}',[StrandController::class,'show']);
    $r->post('/strand/store',[StrandController::class,'store']);
    $r->get('/strand/{id:\d+}/edit',[StrandController::class,'edit']);
    $r->post('/strand/{id:\d+}/update',[StrandController::class,'update']);
    $r->post('/strand/delete',[StrandController::class,'destroy']);
    $r->get('/strand/pdf',[StrandController::class,'strandPdf']);
    $r->get('/strand/excel',[StrandController::class,'strandExcel']);
    $r->get('/strand/csv',[StrandController::class,'strandCsv']);
    

//tools

    // ocr 
    $r->get('/tools/recog', [OcrController::class, 'index']); 
    $r->post('/scan',[OcrController::class,'gets']);

    //calendar
    $r->get('/calendar',[CalendarController::class,'index']);
    $r->get('/calendar/events',[CalendarController::class,'allEvents']);


// subjects 

    $r->get('/subject',[SubjectController::class,'index']);
    $r->get('/subject/all',[SubjectController::class,'allSubjects']);
    $r->post('/subject/store',[SubjectController::class,'store']);
    $r->post('/subject/delete',[SubjectController::class,'destroy']);



// reports (approval)

   $r->get('/reports-approval',[ReportApprovalController::class,'index']);
   $r->get('/reports-approval/all',[ReportApprovalController::class,'allApproval']);
   $r->post('/reports-approval/store',[ReportApprovalController::class,'store']);
   $r->post('/reports-approval/delete',[ReportApprovalController::class,'destroy']);
   $r->post('/reports-approval/{id:\d+}/approved',[ReportApprovalController::class,'approved']);
    $r->post('/reports-approval/{id:\d+}/reject',[ReportApprovalController::class,'reject']);

// report (submission)

    $r->get('/reports-submit',[ReportSubmissionController::class,'index']);
     $r->get('/reports-submit/all',[ReportSubmissionController::class,'allReports']);
     $r->post('/reports-submit/store',[ReportSubmissionController::class,'store']);
     $r->post('/reports-submit/delete',[ReportSubmissionController::class,'destroy']);


     // enrollee 

     $r->get('/enrollee',[EnroleeController::class,'index']);
     $r->get('/enrollee/all',[EnroleeController::class,'allEnrollees']);
     $r->get('/enrollee/{id:\d+}',[EnroleeController::class,'find']);

// for all 

   
    $r->post('/cleaner', function() {

            header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);
            $text = $input['text'] ?? '';

            if ($text) {

                $cleaned = AiHelper::cleanTextGemini($text);
                
                echo json_encode([
                    "cleaned" => $cleaned
                ]);
            }
        
    });






