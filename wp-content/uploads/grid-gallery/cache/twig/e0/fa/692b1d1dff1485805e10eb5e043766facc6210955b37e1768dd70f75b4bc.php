<?php

/* @galleries/shortcode/style.twig */
class __TwigTemplate_e0fa692b1d1dff1485805e10eb5e043766facc6210955b37e1768dd70f75b4bc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
    }

    // line 1
    public function getprop($_prop = null, $_value = null)
    {
        $context = $this->env->mergeGlobals(array(
            "prop" => $_prop,
            "value" => $_value,
        ));

        $blocks = array();

        ob_start();
        try {
            echo twig_escape_filter($this->env, (isset($context["prop"]) ? $context["prop"] : null), "html", null, true);
            echo ":";
            echo twig_escape_filter($this->env, (isset($context["value"]) ? $context["value"] : null), "html", null, true);
            echo ";";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 2
    public function getobject($_values = null)
    {
        $context = $this->env->mergeGlobals(array(
            "values" => $_values,
        ));

        $blocks = array();

        ob_start();
        try {
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["values"]) ? $context["values"] : null));
            foreach ($context['_seq'] as $context["prop"] => $context["value"]) {
                echo twig_escape_filter($this->env, (isset($context["prop"]) ? $context["prop"] : null), "html", null, true);
                echo ":";
                echo twig_escape_filter($this->env, (isset($context["value"]) ? $context["value"] : null), "html", null, true);
                echo ";";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['prop'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "@galleries/shortcode/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 2,  21 => 1,  683 => 114,  680 => 113,  677 => 111,  674 => 110,  671 => 109,  668 => 108,  665 => 107,  652 => 106,  648 => 237,  645 => 236,  641 => 228,  635 => 227,  632 => 226,  629 => 225,  624 => 224,  621 => 223,  618 => 222,  610 => 218,  607 => 217,  603 => 206,  600 => 205,  589 => 181,  584 => 178,  581 => 177,  577 => 175,  574 => 174,  570 => 172,  568 => 171,  562 => 170,  558 => 169,  554 => 168,  550 => 166,  546 => 164,  544 => 163,  541 => 162,  537 => 160,  535 => 159,  532 => 158,  528 => 156,  526 => 155,  523 => 154,  519 => 152,  517 => 151,  514 => 150,  510 => 148,  506 => 146,  504 => 145,  497 => 142,  495 => 117,  492 => 116,  489 => 105,  486 => 104,  483 => 102,  480 => 101,  477 => 100,  474 => 99,  471 => 98,  469 => 97,  466 => 96,  460 => 94,  457 => 93,  451 => 91,  448 => 90,  443 => 88,  439 => 87,  434 => 86,  431 => 85,  428 => 84,  424 => 83,  419 => 82,  415 => 81,  411 => 80,  407 => 79,  403 => 78,  399 => 77,  395 => 76,  391 => 75,  387 => 74,  383 => 73,  379 => 72,  373 => 70,  371 => 69,  368 => 68,  363 => 66,  360 => 65,  354 => 63,  351 => 62,  346 => 60,  343 => 59,  337 => 57,  332 => 55,  329 => 54,  325 => 52,  322 => 51,  316 => 49,  313 => 48,  309 => 46,  306 => 45,  302 => 43,  299 => 42,  294 => 40,  291 => 39,  288 => 38,  282 => 36,  278 => 34,  276 => 33,  268 => 32,  260 => 31,  256 => 30,  248 => 29,  239 => 27,  235 => 25,  233 => 24,  229 => 23,  225 => 22,  221 => 21,  217 => 20,  213 => 19,  209 => 18,  204 => 17,  201 => 16,  197 => 13,  187 => 10,  179 => 9,  169 => 8,  159 => 7,  156 => 6,  153 => 5,  150 => 4,  138 => 240,  134 => 238,  132 => 236,  123 => 229,  121 => 222,  117 => 220,  115 => 217,  110 => 214,  106 => 212,  102 => 210,  99 => 209,  97 => 208,  94 => 207,  92 => 205,  89 => 204,  86 => 203,  81 => 200,  74 => 198,  70 => 197,  64 => 196,  61 => 195,  50 => 186,  47 => 185,  45 => 184,  41 => 182,  39 => 16,  35 => 14,  32 => 4,  30 => 3,  27 => 2,  25 => 1,);
    }
}
