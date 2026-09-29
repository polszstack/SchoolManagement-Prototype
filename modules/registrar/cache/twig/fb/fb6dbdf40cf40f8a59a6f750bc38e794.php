<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* /partials/sidebar.twig */
class __TwigTemplate_7e9265d5e147c7dad38f5dbd5a642f52 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<aside class=\"sidebar\">
        <div class=\"school-logo\">
            <img src=\"";
        // line 3
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base"] ?? null), "html", null, true);
        yield "/assets/images/bcp-logo.png\" alt=\"School Logo\">
            <div class=\"sidebar-icons\">
                <i class=\"fa-regular fa-bell\"></i>
                <i class=\"fa-regular fa-circle-user\"></i>
            </div>
        </div>
        <div class=\"sidebar-header\">
            <div class=\"user-avatar\">UA</div>
            <h1>Username</h1>
            <p class=\"user-id\">SAMPLEID123456789</p>
        </div>
        <h2>Dashboard</h2>
        <ul>
            <li><a href=\"\" class=\"menu-link active\">Dashboard</a></li>
            <li><a href=\"/schoolmanagementsystem/modules/registrar/students/index.php\" class=\"menu-link\">Student List</a></li>
            <li><a href=\"\" class=\"menu-link\">Module 2</a></li>
            <li><a href=\"\" class=\"menu-link\">Module 3</a></li>
            <li><a href=\"\" class=\"menu-link\">Module 4</a></li>
            <li><a href=\"\" class=\"menu-link\">Module 5</a></li>
        </ul>
      
</aside>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "/partials/sidebar.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  46 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "/partials/sidebar.twig", "C:\\xampp\\htdocs\\SchoolManagementSystem\\Modules\\template2\\app\\views\\partials\\sidebar.twig");
    }
}
