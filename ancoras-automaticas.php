<?php
/**
 * Plugin Name:       Âncoras Automáticas
 * Plugin URI:        https://www.borvo-sc.com
 * Description:       Adiciona um ícone de âncora a parágrafos e listas. Ao clicar, copia o URL para a área de transferência.
 * Version:           3.2
 * Author:            Alexandre Rodrigues
 * Author URI:        https://www.borvo-sc.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ancoras-elementos
 */

// Medida de segurança: evita que o ficheiro seja acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ap_adicionar_ancoras_elementos($content) {
    if (is_singular('post') && in_the_loop() && is_main_query()) {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);
        $elementos = $xpath->query('//p | //li');
        $contador = 1;

        $post_url = get_permalink();
        // ALTERAÇÃO: Usa o HTML para um ícone Tabler
        $icon_html = '<i class="ti ti-link"></i>';

        foreach ($elementos as $el) {
            if (strlen(trim($el->nodeValue)) == 0) continue;

            $id = 'p-' . $contador;
            $el->setAttribute('id', $id);

            $link_ancora = $dom->createElement('a');
            $link_ancora->setAttribute('href', $post_url . '#' . $id);
            $link_ancora->setAttribute('class', 'ancora-elemento-link');
            $link_ancora->setAttribute('aria-label', 'Copiar link para este elemento');
            $link_ancora->setAttribute('title', 'Copiar link');
            
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($icon_html);
            $link_ancora->appendChild($fragment);

            $el->insertBefore($link_ancora, $el->firstChild);
            $contador++;
        }
        
        $output = '';
        $body_node = $dom->getElementsByTagName('body')->item(0);
        if ($body_node) {
            foreach ($body_node->childNodes as $child) {
                $output .= $dom->saveHTML($child);
            }
        } else {
            $output = $dom->saveHTML();
        }

        return $output;
    }
    return $content;
}
add_filter('the_content', 'ap_adicionar_ancoras_elementos');

function ap_adicionar_estilos_e_scripts() {
    if (!is_singular('post')) return;

    // ALTERAÇÃO: Carrega a folha de estilos dos Tabler Icons.
    wp_enqueue_style('tabler-icons', 'https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css', [], null);

    $css = <<<CSS
    <style>
        html {
            scroll-behavior: auto !important;
        }
        p[id^="p-"], li[id^="p-"] {
            position: relative; 
            padding-left: 28px !important;
            margin-left: -28px;
        }
        .entry p a.ancora-elemento-link,
        .entry li a.ancora-elemento-link,
        a.ancora-elemento-link {
            position: absolute;
            left: 0;
            top: 0.1em;
            text-decoration: none !important;
            border-bottom: none !important;
            box-shadow: none !important;
            opacity: 0;
            transition: color 0.2s ease-in-out, opacity 0.2s ease-in-out;
            cursor: pointer;
            color: #a0a0a0 !important;
            background: none !important;
            line-height: 1;
            font-size: 18px; /* Ajuste para o tamanho dos Tabler Icons */
        }
        /* Estilo para os ícones Tabler dentro do link */
        .ancora-elemento-link i {
            font-style: normal;
        }
        p[id^="p-"]:hover .ancora-elemento-link,
        li[id^="p-"]:hover .ancora-elemento-link {
            opacity: 0.6;
        }
        .entry p a.ancora-elemento-link:hover,
        .entry li a.ancora-elemento-link:hover,
        a.ancora-elemento-link:hover {
            opacity: 1;
            color: #f15c22 !important;
            background: none !important;
            text-decoration: none !important;
            border-bottom: none !important;
        }
    </style>
    CSS;
    echo $css;

    // ALTERAÇÃO: Usa as classes dos Tabler Icons.
    wp_register_script('ancoras-script', plugin_dir_url(__FILE__) . 'js/ancoras.js', [], '3.2', true);
    wp_localize_script('ancoras-script', 'ancoras_vars', [
        'copied_icon'   => '<i class="ti ti-check"></i>',
        'original_icon' => '<i class="ti ti-link"></i>'
    ]);
    wp_enqueue_script('ancoras-script');
}
add_action('wp_enqueue_scripts', 'ap_adicionar_estilos_e_scripts');
