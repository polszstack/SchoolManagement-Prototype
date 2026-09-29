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

/* layout/base.twig */
class __TwigTemplate_0db1468dad97d1dee414cc3918818c76 extends Template
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
            'title' => [$this, 'block_title'],
            'cssScript' => [$this, 'block_cssScript'],
            'content' => [$this, 'block_content'],
            'jsScript' => [$this, 'block_jsScript'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title> ";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    ";
        // line 7
        yield from $this->unwrap()->yieldBlock('cssScript', $context, $blocks);
        // line 8
        yield "
</head>
<body>

   ";
        // line 12
        yield from $this->load("/partials/header.twig", 12)->unwrap()->yield($context);
        // line 13
        yield "   ";
        yield from $this->load("/partials/sidebar.twig", 13)->unwrap()->yield($context);
        // line 14
        yield "      
    
    ";
        // line 16
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 18
        yield "
     ";
        // line 19
        yield from $this->load("/partials/footer.twig", 19)->unwrap()->yield($context);
        // line 20
        yield "
";
        // line 21
        yield from $this->unwrap()->yieldBlock('jsScript', $context, $blocks);
        // line 22
        yield "</body>
</html>


";
        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield " ";
        yield from [];
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_cssScript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield " ";
        yield from [];
    }

    // line 16
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 17
        yield "    ";
        yield from [];
    }

    // line 21
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_jsScript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield " ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "layout/base.twig";
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
        return array (  129 => 21,  124 => 17,  117 => 16,  106 => 7,  95 => 6,  86 => 22,  84 => 21,  81 => 20,  79 => 19,  76 => 18,  74 => 16,  70 => 14,  67 => 13,  65 => 12,  59 => 8,  57 => 7,  53 => 6,  46 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "layout/base.twig", "C:\\xampp\\htdocs\\SchoolManagementSystem\\Modules\\template2\\app\\views\\layout\\base.twig");
    }
}
