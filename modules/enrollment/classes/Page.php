<?php
class Page {
    private $default = 'dashboard-overview';
    private $pagesDir;
    private $allowed = [];

    /*Sidebar Content*/ 
    private $labels = [
        'dashboard-overview'          => 'Dashboard Overview',
        'content-1'             => 'Content 1',
        'content-2'  => 'Content 2',
        'content-3'   => 'Content 3',
        'content-4' => 'Content 4',
        'content-5'=> 'Content 5',
        'content-6'  => 'Content 6',
        'content-7'     => 'Content 7',
        'report-submission' => 'Report Submission',
        'concern-submission' => 'Concern Submission',
        'approval-submission' => 'Approval Submission'
    ];

    /*Content Sections*/ 
    private $sections = [
        'top'             => ['dashboard-overview'],
        'section-1'  => ['content-1', 'content-2'],
        'section-2'      => ['content-3', 'content-4'],
        'section-3'       => ['content-5'],
        'section-4' => ['content-6', 'content-7'],
        'requests-and-reports' => ['report-submission', 'concern-submission', 'approval-submission']
    ];

    public function __construct($pagesDir = null) {
        $this->pagesDir = $pagesDir ?? dirname(__DIR__) . '/pages';
        $this->discoverPages();
    }

    private function discoverPages() {
        if (!is_dir($this->pagesDir)) return;
        foreach (glob($this->pagesDir . '/*.php') as $file) {
            $this->allowed[] = basename($file, '.php');
        }
    }

    public function getPage() {
        if (!empty($_GET['page']) && in_array($_GET['page'], $this->allowed)) {
            return $_GET['page'];
        }
        return $this->default;
    }

    public function render() {
        $page = $this->getPage();
        $file = $this->pagesDir . '/' . $page . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            include $this->pagesDir . '/' . $this->default . '.php';
        }
    }

    public function isActive($page) {
        return $this->getPage() === $page;
    }

    public function getAllowedPages() {
        return $this->allowed;
    }

    public function renderNav() {
        // Top section (no heading/separator)
        foreach ($this->sections['top'] as $p) {
            $this->renderLink($p);
        }

        // Grouped sections
        $sectionOrder = ['section-1', 'section-2', 'section-3', 'section-4', 'requests-and-reports'];
        foreach ($sectionOrder as $section) {
            echo '<div class="separator"></div>';
            echo '<h3>' . ucwords(str_replace('-', ' ', $section)) . '</h3>';
            foreach ($this->sections[$section] as $p) {
                $this->renderLink($p);
            }
        }
    }

    private function renderLink($p) {
        $label = $this->labels[$p] ?? ucwords(str_replace('-', ' ', $p));
        if (in_array($p, $this->allowed)) {
            $class = $this->isActive($p) ? 'active-menu-link' : 'menu-link';
            echo "<li><a href=\"?page={$p}\" data-page=\"{$p}\" class=\"{$class}\">{$label}</a></li>";
        } else {
            echo "<li><a href=\"#\" class=\"menu-link\">{$label}</a></li>";
        }
    }
}