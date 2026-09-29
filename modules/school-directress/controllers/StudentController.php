<?php

$host    = 'localhost';
$db      = 'sms';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$students = [];
$courses  = [];
$error    = null;
$stats    = ['total' => 0, 'enrolled' => 0, 'on_leave' => 0, 'graduated' => 0, 'dropped' => 0];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->query("
        SELECT
            s.id, s.student_number, s.year_level, s.enrollment_status, s.enrolled_at,
            a.surname, a.first_name, a.middle_name, a.suffix, a.sex,
            a.email AS applicant_email, a.contact_number, a.date_of_birth,
            a.civil_status, a.address_city, a.address_province,
            a.school_last_attended, a.year_graduated, a.application_number,
            u.username, u.email AS user_email,
            c.course_code, c.course_name, c.duration_years,
            sec.section_code, sec.section_name, sec.academic_year,
            sec.semester, sec.max_students,
            (SELECT COUNT(*) FROM enr_students s2
             WHERE s2.section_id = s.section_id AND s2.enrollment_status = 'enrolled'
            ) AS section_enrolled_count
        FROM      enr_students   s
        JOIN      enr_applicants a   ON s.applicant_id = a.id
        JOIN      enr_users      u   ON s.user_id      = u.id
        JOIN      enr_courses    c   ON s.course_id    = c.id
        LEFT JOIN enr_sections   sec ON s.section_id   = sec.id
        ORDER BY s.enrolled_at DESC
    ");
    $students = $stmt->fetchAll();

    $cStmt   = $pdo->query("SELECT id, course_code, course_name FROM enr_courses WHERE is_active = 1 ORDER BY course_code");
    $courses = $cStmt->fetchAll();

    $stats['total'] = count($students);
    foreach ($students as $r) {
        $key = $r['enrollment_status'];
        if (isset($stats[$key])) $stats[$key]++;
    }

} catch (PDOException $e) {
    $error = $e->getMessage();

    // Mock data for preview
    $students = [
        ['id'=>1,'student_number'=>'2024-00001','year_level'=>1,'enrollment_status'=>'enrolled','enrolled_at'=>'2024-08-15 09:00:00','surname'=>'Santos','first_name'=>'Maria','middle_name'=>'Cruz','suffix'=>null,'sex'=>'Female','applicant_email'=>'maria@uni.edu','contact_number'=>'09171234567','date_of_birth'=>'2005-03-12','civil_status'=>'Single','address_city'=>'Quezon City','address_province'=>'Metro Manila','school_last_attended'=>'QC Science High School','year_graduated'=>'2024','application_number'=>'APP-2024-001','username'=>'maria.santos','user_email'=>'maria@uni.edu','course_code'=>'BSCS','course_name'=>'Bachelor of Science in Computer Science','duration_years'=>4,'section_code'=>'CS-1A','section_name'=>'CS 1-A','academic_year'=>'2024-2025','semester'=>'1st Semester','max_students'=>40,'section_enrolled_count'=>32],
        ['id'=>2,'student_number'=>'2024-00002','year_level'=>2,'enrollment_status'=>'enrolled','enrolled_at'=>'2024-08-15 09:30:00','surname'=>'dela Cruz','first_name'=>'Juan','middle_name'=>null,'suffix'=>'Jr.','sex'=>'Male','applicant_email'=>'juan@uni.edu','contact_number'=>'09281234567','date_of_birth'=>'2004-07-22','civil_status'=>'Single','address_city'=>'Makati','address_province'=>'Metro Manila','school_last_attended'=>'Makati Science HS','year_graduated'=>'2023','application_number'=>'APP-2024-002','username'=>'juan.delacruz','user_email'=>'juan@uni.edu','course_code'=>'BSIT','course_name'=>'Bachelor of Science in Information Technology','duration_years'=>4,'section_code'=>'IT-2B','section_name'=>'IT 2-B','academic_year'=>'2024-2025','semester'=>'1st Semester','max_students'=>35,'section_enrolled_count'=>28],
        ['id'=>3,'student_number'=>'2023-00045','year_level'=>3,'enrollment_status'=>'on_leave','enrolled_at'=>'2023-08-10 10:00:00','surname'=>'Reyes','first_name'=>'Ana','middle_name'=>'Luz','suffix'=>null,'sex'=>'Female','applicant_email'=>'ana@uni.edu','contact_number'=>'09091234567','date_of_birth'=>'2003-11-05','civil_status'=>'Single','address_city'=>'Pasig','address_province'=>'Metro Manila','school_last_attended'=>'Pasig NHS','year_graduated'=>'2022','application_number'=>'APP-2023-045','username'=>'ana.reyes','user_email'=>'ana@uni.edu','course_code'=>'BSCS','course_name'=>'Bachelor of Science in Computer Science','duration_years'=>4,'section_code'=>null,'section_name'=>null,'academic_year'=>null,'semester'=>null,'max_students'=>null,'section_enrolled_count'=>0],
        ['id'=>4,'student_number'=>'2021-00012','year_level'=>4,'enrollment_status'=>'graduated','enrolled_at'=>'2021-08-12 08:00:00','surname'=>'Villanueva','first_name'=>'Pedro','middle_name'=>'Gomez','suffix'=>null,'sex'=>'Male','applicant_email'=>'pedro@uni.edu','contact_number'=>'09151234567','date_of_birth'=>'2001-04-18','civil_status'=>'Single','address_city'=>'Taguig','address_province'=>'Metro Manila','school_last_attended'=>'BGC Senior HS','year_graduated'=>'2020','application_number'=>'APP-2021-012','username'=>'pedro.villanueva','user_email'=>'pedro@uni.edu','course_code'=>'BSIT','course_name'=>'Bachelor of Science in Information Technology','duration_years'=>4,'section_code'=>'IT-4A','section_name'=>'IT 4-A','academic_year'=>'2024-2025','semester'=>'2nd Semester','max_students'=>40,'section_enrolled_count'=>38],
        ['id'=>5,'student_number'=>'2022-00033','year_level'=>2,'enrollment_status'=>'dropped','enrolled_at'=>'2022-08-11 11:00:00','surname'=>'Mercado','first_name'=>'Liza','middle_name'=>null,'suffix'=>null,'sex'=>'Female','applicant_email'=>'liza@uni.edu','contact_number'=>'09361234567','date_of_birth'=>'2003-09-29','civil_status'=>'Single','address_city'=>'Mandaluyong','address_province'=>'Metro Manila','school_last_attended'=>'Mandaluyong NHS','year_graduated'=>'2021','application_number'=>'APP-2022-033','username'=>'liza.mercado','user_email'=>'liza@uni.edu','course_code'=>'BSCS','course_name'=>'Bachelor of Science in Computer Science','duration_years'=>4,'section_code'=>'CS-2C','section_name'=>'CS 2-C','academic_year'=>'2024-2025','semester'=>'1st Semester','max_students'=>38,'section_enrolled_count'=>25],
        ['id'=>6,'student_number'=>'2024-00006','year_level'=>1,'enrollment_status'=>'enrolled','enrolled_at'=>'2024-08-16 08:45:00','surname'=>'Bautista','first_name'=>'Carlos','middle_name'=>'Ramos','suffix'=>null,'sex'=>'Male','applicant_email'=>'carlos@uni.edu','contact_number'=>'09221234567','date_of_birth'=>'2006-01-14','civil_status'=>'Single','address_city'=>'Las Pinas','address_province'=>'Metro Manila','school_last_attended'=>'Las Pinas NHS','year_graduated'=>'2024','application_number'=>'APP-2024-006','username'=>'carlos.bautista','user_email'=>'carlos@uni.edu','course_code'=>'BSCpE','course_name'=>'Bachelor of Science in Computer Engineering','duration_years'=>5,'section_code'=>'CPE-1B','section_name'=>'CPE 1-B','academic_year'=>'2024-2025','semester'=>'1st Semester','max_students'=>30,'section_enrolled_count'=>18],
    ];
    $courses = [
        ['id'=>1,'course_code'=>'BSCS','course_name'=>'BS Computer Science'],
        ['id'=>2,'course_code'=>'BSIT','course_name'=>'BS Information Technology'],
        ['id'=>3,'course_code'=>'BSCpE','course_name'=>'BS Computer Engineering'],
    ];
    $stats['total'] = count($students);
    foreach ($students as $r) {
        $key = $r['enrollment_status'];
        if (isset($stats[$key])) $stats[$key]++;
    }
}

/* ── HELPERS ─────────────────────────────────────────────────────────────── */
function ep_fullName(array $r): string {
    $n = trim($r['first_name'] . ' ' . ($r['middle_name'] ? $r['middle_name'][0].'. ' : '') . $r['surname']);
    if (!empty($r['suffix'])) $n .= ', ' . $r['suffix'];
    return $n;
}
function ep_initials(array $r): string {
    return strtoupper(substr($r['first_name'],0,1) . substr($r['surname'],0,1));
}
function ep_age(string $dob): int {
    return (int) date_diff(date_create($dob), date_create('today'))->y;
}
function ep_statusMeta(string $s): array {
    $map = [
        'enrolled'  => ['label'=>'Enrolled',  'cls'=>'s-enrolled'],
        'on_leave'  => ['label'=>'On Leave',   'cls'=>'s-leave'],
        'graduated' => ['label'=>'Graduated',  'cls'=>'s-graduated'],
        'dropped'   => ['label'=>'Dropped',    'cls'=>'s-dropped'],
    ];
    return $map[$s] ?? ['label'=>ucfirst($s),'cls'=>'s-default'];
}
function ep_yearLabel(int $y): string {
    return (['','1st','2nd','3rd','4th','5th'][$y] ?? "{$y}th");
}
function ep_sectionFill(int $enrolled, int $max): int {
    return $max > 0 ? (int)min(100, round($enrolled / $max * 100)) : 0;
}