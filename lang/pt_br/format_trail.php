<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Trail Format - A topics based format that uses a trail of user selectable images to popup a light box of the section.
 *
 * @package    format_trail
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['display_summary'] = 'Saia da trilha';
$string['display_summary_alt'] = 'Mova esta seção para fora da trilha';
$string['editimage'] = 'Alterar imagem';
$string['editimage_alt'] = 'Definir ou alterar a imagem';
$string['formattrail'] = 'Formato de trilha'; // Name to display for format.
$string['general_information'] = 'Informação geral';  // No longer used kept for legacy versions.
$string['hidden_topic'] = 'Esta seção foi ocultada';
$string['hide_summary'] = 'Mover seção para trilha';
$string['hide_summary_alt'] = 'Mova esta seção para a trilha';
$string['nametrail'] = 'Vista de trilha';
$string['title'] = 'Título da seção';
$string['topic'] = 'Seção';
$string['topic0'] = 'Geral';
$string['topicoutline'] = 'Seção';  // No longer used kept for legacy versions.

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages.
$string['sectionname'] = 'Nome da Seção';
$string['pluginname'] = 'Formato de trilha';
$string['section0name'] = 'Geral';

// WAI-ARIA - http://www.w3.org/TR/wai-aria/roles.
$string['trailimagecontainer'] = 'Imagens de trilha';
$string['closeshadebox'] = 'Caixa de sombra perto';
$string['previoussection'] = 'Seção anterior';
$string['nextsection'] = 'Próxima seção';
$string['shadeboxcontent'] = 'Conteúdo da caixa de sombra';

// MDL-26105.
$string['page-course-view-trail'] = 'Qualquer página principal do curso no formato de trilha';
$string['page-course-view-trail-x'] = 'Qualquer página de curso no formato de trilha';

$string['addsection'] = 'Adicionar seção';
$string['hidefromothers'] = 'Ocultar seção'; // No longer used kept for legacy versions.
$string['showfromothers'] = 'Mostrar seção'; // No longer used kept for legacy versions.
$string['currentsection'] = 'Esta seção'; // No longer used kept for legacy versions.
$string['markedthissection'] = 'Esta seção é destacada como a seção atual';
$string['markthissection'] = 'Realce esta seção como a seção atual';

// Moodle 3.0 Enhancement.
$string['editsection'] = 'Editar seção';
$string['deletesection'] = 'Excluir seção';

// MDL-51802.
$string['editsectionname'] = 'Editar nome da seção';
$string['newsectionname'] = 'Novo nome para a seção {$a}';

// Moodle 2.4 Course format refactoring - MDL-35218.
$string['numbersections'] = 'Número de seções';

// Exception messages.
$string['imagecannotbeused'] = 'A imagem não pode ser usada, deve ser PNG, JPG ou GIF e a extensão GD PHP deve ser instalada.';
$string['cannotfinduploadedimage'] = 'Não é possível encontrar a imagem original carregada. Por favor, informe detalhes do erro e as informações contidas no arquivo php.log para o desenvolvedor. Atualize a página e faça o upload de uma nova cópia da imagem.';
$string['cannotconvertuploadedimagetodisplayedimage'] = 'Não é possível converter a imagem enviada para a imagem exibida. Por favor, informe detalhes do erro e as informações contidas no arquivo php.log para o desenvolvedor.';
$string['cannotgetimagesforcourse'] = 'Não é possível obter imagens por curso. Por favor, informe os detalhes do erro ao desenvolvedor.';

// CONTRIB-4099 Image container size change improvement.
$string['off'] = 'Desligado';
$string['on'] = 'Ligado';
$string['scale'] = 'Escala';
$string['crop'] = 'Corte';
$string['imagefile'] = 'Carregar uma imagem';
$string['imagefile_help'] = 'Carregue uma imagem do tipo PNG, JPG ou GIF.';
$string['deleteimage'] = 'Excluir imagem';
$string['deleteimage_help'] = "Exclua a imagem da seção que está sendo editada. Se você enviou uma imagem, ela não substituirá a imagem excluída.";
$string['gfreset'] = 'Opções de redefinição de trilha';
$string['gfreset_help'] = 'Redefinir para os padrões da trilha.';
$string['defaultimagecontaineralignment'] = 'Alinhamento padrão dos containers de imagem';
$string['defaultimagecontaineralignment_desc'] = 'O alinhamento padrão dos containers de imagens.';
$string['defaultimagecontainerwidth'] = 'Largura padrão do container da imagem';
$string['defaultimagecontainerwidth_desc'] = 'A largura padrão do container de imagens.';
$string['defaultimagecontainerratio'] = 'Proporção padrão do container da imagem em relação à largura';
$string['defaultimagecontainerratio_desc'] = 'A proporção padrão do container da imagem em relação à largura.';
$string['defaultimageresizemethod'] = 'Método padrão de redimensionamento da imagem';
$string['defaultimageresizemethod_desc'] = 'O método padrão de redimensionar a imagem para caber no container.';
$string['defaultbordercolour'] = 'Cor da borda do container de imagem padrão';
$string['defaultbordercolour_desc'] = 'A cor da borda do container de imagem padrão';
$string['defaultborderradius'] = 'Raio da borda padrão';
$string['defaultborderradius_desc'] = 'O raio da borda padrão liga / desliga.';
$string['defaultborderwidth'] = 'Largura da borda padrão';
$string['defaultborderwidth_desc'] = 'A largura da borda padrão';
$string['defaultimagecontainerbackgroundcolour'] = 'Cor de fundo do recipiente de imagem padrão';
$string['defaultimagecontainerbackgroundcolour_desc'] = 'A cor de fundo do recipiente de imagens padrão.';
$string['defaultcurrentselectedsectioncolour'] = 'Cor da seção atual selecionada padrão';
$string['defaultcurrentselectedsectioncolour_desc'] = 'A cor da seção selecionada atualmente.';
$string['defaultcurrentselectedimagecontainertextcolour'] = 'Cor do texto do contentor da imagem atualmente selecionado.';
$string['defaultcurrentselectedimagecontainertextcolour_desc'] = 'A cor do texto do recipiente de imagens atualmente selecionado.';
$string['defaultcurrentselectedimagecontainercolour'] = 'Cor atual do container de imagem selecionada';
$string['defaultcurrentselectedimagecontainercolour_desc'] = 'A cor atual do container de imagem selecionada.';

$string['defaultcoursedisplay'] = 'Exibição do curso padrão';
$string['defaultcoursedisplay_desc'] = "Mostra todas as seções em uma única página ou seção zero e a seção escolhida na página.";

$string['defaultfitsectioncontainertowindow'] = 'Ajustar o container de seção à janela por padrão';
$string['defaultfitsectioncontainertowindow_desc'] = 'A configuração padrão para "Ajustar container de seção à janela".';

$string['defaultnewactivity'] = 'Mostrar novo padrão de imagem de notificação de atividade';
$string['defaultnewactivity_desc'] = "Mostrar a nova imagem de notificação de atividade quando uma nova atividade ou recurso é adicionado ao padrão de uma seção.";

$string['setimagecontaineralignment'] = 'Defina o alinhamento do container de imagem';
$string['setimagecontaineralignment_help'] = 'Defina a largura do recipiente de imagem para um dos seguintes: Esquerda, Centro ou Direita';
$string['setimagecontainerwidth'] = 'Defina a largura do container de imagem';
$string['setimagecontainerwidth_help'] = 'Defina a largura do container de imagem para um dos seguintes: 128, 192 ou 210';
$string['setimagecontainerratio'] = 'Defina a proporção do container de imagens em relação à largura';
$string['setimagecontainerratio_help'] = 'Defina a proporção do recipiente de imagem para um dos seguintes: 3-2 ou 3-1.';
$string['setimageresizemethod'] = 'Defina o método de redimensionamento da imagem';
$string['setimageresizemethod_help'] = "Defina o método de redimensionamento da imagem para: 'Escala' ou 'Recortar' ao redimensionar a imagem para caber no recipiente.";
$string['setbordercolour'] = 'Definir a cor da borda';
$string['setbordercolour_help'] = 'Defina a cor da borda em RGB hexadecimal.';
$string['setborderradius'] = 'Ativar / desativar o raio da borda';
$string['setborderradius_help'] = 'Ativar ou desativar o raio da borda.';
$string['setborderwidth'] = 'Definir a largura da borda';
$string['setborderwidth_help'] = 'Defina a largura da borda entre 1 e 10.';
$string['setimagecontainerbackgroundcolour'] = 'Defina a cor de fundo do container de imagem (999999 para transparente)';
$string['setimagecontainerbackgroundcolour_help'] = 'Defina a cor de fundo do recipiente de imagem em RGB hexadecimal. (999999 para transparente)';
$string['setcurrentselectedsectioncolour'] = 'Defina a cor atual da seção selecionada';
$string['setcurrentselectedsectioncolour_help'] = 'Defina a cor atual da seção selecionada em RGB hexadecimal.';
$string['setcurrentselectedimagecontainertextcolour'] = 'Defina a cor do texto do container de imagens atual selecionado';
$string['setcurrentselectedimagecontainertextcolour_help'] = 'Defina a cor do texto do container de imagem atual em RGB hexadecimal.';
$string['setcurrentselectedimagecontainercolour'] = 'Defina a cor atual do container da imagem selecionada. (999999 para transparente)';
$string['setcurrentselectedimagecontainercolour_help'] = 'Defina a cor atual do container de imagem selecionado em RGB hexadecimal.';

$string['setnewactivity'] = 'Mostrar nova imagem de notificação de atividade';
$string['setnewactivity_help'] = "Mostrar a nova imagem de notificação de atividade quando uma nova atividade ou recurso for adicionado a uma seção.";

$string['setfitsectioncontainertowindow'] = 'Ajusta o popup da seção à janela';
$string['setfitsectioncontainertowindow_help'] = 'Se habilitado, a caixa pop-up com o conteúdo da seção vai caber no tamanho da janela e irá rolar para dentro se necessário. Se desativado, a página inteira será rolada.';

$string['colourrule'] = "Por favor, digite uma cor RGB válida, seis dígitos hexadecimais.";
$string['opacityrule'] = "Por favor digite uma opacidade válida, entre 0 e 1, com incrementos de 0.1.";
$string['sectiontitlefontsizerule'] = "Por favor insira um tamanho de fonte de título de seção válido, entre 12 e 24 (pixels) ou 0 para 'não definir'.";

// Section title text format options.
$string['hidesectiontitle'] = 'Ocultar opção do título da seção';
$string['hidesectiontitle_help'] = 'Ocultar o título da seção.';
$string['defaulthidesectiontitle'] = 'Ocultar opção do título da seção';
$string['defaulthidesectiontitle_desc'] = 'Ocultar o título da seção.';

$string['hidenavside'] = 'Ocultar navegação lateral';
$string['hidenavside_help'] = 'Ocultar setas de navegação lateral.';

$string['sethidesectionlock'] = 'Mostrar cadeado quando tiver restrição';
$string['sethidesectionlock_help'] = 'Mostrar imagem do cadeado quando a seção tiver restrição.';
$string['defaultsethidesectionlock'] = 'Mostrar cadeado quando tiver restrição';
$string['defaultsethidesectionlock_desc'] = 'Mostrar imagem do cadeado quando a seção tiver restrição.';

$string['sectiontitletraillengthmaxoption'] = 'Opção de comprimento da trilha do título da seção';
$string['sectiontitletraillengthmaxoption_help'] = 'Defina o comprimento máximo do título da seção na caixa de trilha. Digite \'0\' para não truncamento.';
$string['defaultsectiontitletraillengthmaxoption'] = 'Opção de comprimento da trilha do título da seção';
$string['defaultsectiontitletraillengthmaxoption_desc'] = 'Defina o comprimento máximo padrão do título da seção na caixa de trilha. Digite \'0\' para não truncamento.';
$string['sectiontitletraillengthmaxoptionrule'] = 'O comprimento máximo do título da seção na caixa de trilha não deve ser zero. Digite \'0\' para não truncamento.';
$string['sectiontitleboxposition'] = 'Opção de posição da caixa de título da seção';
$string['sectiontitleboxposition_help'] = 'Defina a posição do título da seção dentro da caixa de trilha para um dos seguintes: \'Dentro\' ou \'Fora\'.';
$string['defaultsectiontitleboxposition'] = 'Opção de posição da caixa de título da seção';
$string['defaultsectiontitleboxposition_desc'] = 'Defina a posição do título da seção dentro da caixa de trilha para um dos seguintes: \'Dentro\' ou \'Fora\'.';
$string['sectiontitleboxpositioninside'] = 'Dentro';
$string['sectiontitleboxpositionoutside'] = 'Fora';
$string['sectiontitleboxinsideposition'] = 'Posição da caixa de título da seção quando a opção \'Dentro\'';
$string['sectiontitleboxinsideposition_help'] = 'Defina a posição do título da seção quando \'Dentro\' a caixa de trilha para um dos seguintes: \'Topo\', \'Meio\' ou \'Base\'.';
$string['defaultsectiontitleboxinsideposition'] = 'Posição da caixa de título da seção quando a opção \'Dentro\'';
$string['defaultsectiontitleboxinsideposition_desc'] = 'Defina a posição do título da seção quando \'Dentro\' a caixa de trilha para um dos seguintes: \'Topo\', \'Meio\' ou \'Base\'.';
$string['sectiontitleboxinsidepositiontop'] = 'Topo';
$string['sectiontitleboxinsidepositionmiddle'] = 'Meio';
$string['sectiontitleboxinsidepositionbottom'] = 'Base';
$string['sectiontitleboxheight'] = 'Altura da caixa do título da seção';
$string['sectiontitleboxheight_help'] = 'Altura da caixa de título da seção em pixels ou 0 para calculado. Quando a posição da caixa é \'Dentro\'.';
$string['defaultsectiontitleboxheight'] = 'Altura da caixa do título da seção';
$string['defaultsectiontitleboxheight_desc'] = 'Altura da caixa de título da seção em pixels ou 0 para calculado. Quando a posição da caixa é  \'Dentro\'.';
$string['sectiontitleboxopacity'] = 'Opacidade da caixa de título da seção';
$string['sectiontitleboxopacity_help'] = 'Opacidade da caixa de título da seção entre 0 e 1 em incrementos de 0,1. Quando a posição da caixa é \'Dentro\'.';
$string['defaultsectiontitleboxopacity'] = 'Opacidade da caixa de título da seção';
$string['defaultsectiontitleboxopacity_desc'] = 'Opacidade da caixa de título da seção entre 0 e 1 em incrementos de 0,1. Quando a posição da caixa é \'Dentro\'.';
$string['sectiontitlefontsize'] = 'Tamanho da fonte do título da seção';
$string['sectiontitlefontsize_help'] = 'Tamanho da fonte do título da seção entre 12 e 24 pixels, onde 0 representa \'não definido, mas herdado do tema ou qualquer outro CSS\'.';
$string['defaultsectiontitlefontsize'] = 'Tamanho da fonte do título da seção';
$string['defaultsectiontitlefontsize_desc'] = 'Tamanho da fonte do título da seção entre 12 e 24 pixels onde 0 representa \'não definido, mas herdado do tema ou qualquer outro CSS\'.';
$string['sectiontitlealignment'] = 'Alinhamento do título da seção';
$string['sectiontitlealignment_help'] = 'Defina o alinhamento do título da seção como \'Esquerda\', \'Centro\' ou \'Direita\'.';
$string['defaultsectiontitlealignment'] = 'Alinhamento do título da seção';
$string['defaultsectiontitlealignment_desc'] = 'Defina o alinhamento do título da seção como \'Esquerda\', \'Centro\' ou \'Direita\'.';
$string['sectiontitleinsidetitletextcolour'] = 'Cor do texto do título da seção quando a opção \'Dentro\'';
$string['sectiontitleinsidetitletextcolour_help'] = 'Defina a cor do texto do título quando estiver \'Dentro\' da caixa de trilha.';
$string['defaultsectiontitleinsidetitletextcolour'] = 'Cor do texto do título da seção quando a opção \'Dentro\'';
$string['defaultsectiontitleinsidetitletextcolour_desc'] = 'Defina a cor do texto do título quando estiver em \'Dentro\' da caixa de trilha.';
$string['sectiontitleinsidetitlebackgroundcolour'] = 'Cor do fundo do título da seção quando a opção \'Dentro\'';
$string['sectiontitleinsidetitlebackgroundcolour_help'] = 'Defina a cor de fundo do título quando está em \'Dentro\' da caixa de trilha.';
$string['defaultsectiontitleinsidetitlebackgroundcolour'] = 'Cor do fundo do título da seção quando a opção \'Dentro\'';
$string['defaultsectiontitleinsidetitlebackgroundcolour_desc'] = 'Defina a cor de fundo do título quando está em \'Dentro\' da caixa de trilha.';
$string['showsectiontitlesummary'] = 'Mostrar resumo do título da seção na opção de hover';
$string['showsectiontitlesummary_help'] = 'Mostrar o resumo do título da seção ao passar o mouse sobre a caixa da trilha.';
$string['defaultshowsectiontitlesummary'] = 'Mostra o resumo do título da seção na opção hover';
$string['defaultshowsectiontitlesummary_desc'] = 'Mostra o resumo do título da seção ao passar o mouse sobre a caixa da trilha.';
$string['setshowsectiontitlesummaryposition'] = 'Defina o resumo do título da seção na opção de posição suspensa';
$string['setshowsectiontitlesummaryposition_help'] = 'Defina a posição de resumo do título da seção ao passar o mouse sobre a caixa de trilha para um dos seguintes: \'topo\', \'base\', \'esquerda\' ou \'direita\'.';
$string['defaultsetshowsectiontitlesummaryposition'] = 'Defina o resumo do título da seção na opção de posição suspensa';
$string['defaultsetshowsectiontitlesummaryposition_desc'] = 'Defina a posição de resumo do título da seção ao passar o mouse sobre a caixa de trilha para um dos seguintes: \'topo\', \'base\', \'esquerda\' ou \'direita\'.';
$string['sectiontitlesummarymaxlength'] = 'Defina o comprimento máximo do resumo do título da seção em hover';
$string['sectiontitlesummarymaxlength_help'] = 'Defina o comprimento máximo do resumo do título da seção ao passar o mouse sobre a caixa da trilha. Digite \'0\' para não truncamento.';
$string['defaultsectiontitlesummarymaxlength'] = 'Defina o comprimento máximo do resumo do título da seção em hover';
$string['defaultsectiontitlesummarymaxlength_desc'] = 'Defina o comprimento máximo do resumo do título da seção ao passar o mouse sobre a caixa da trilha. Digite \'0\' para não truncamento. ';
$string['sectiontitlesummarytextcolour'] = 'Defina a cor do texto de resumo do título da seção em hover';
$string['sectiontitlesummarytextcolour_help'] = 'Defina a cor do texto de resumo do título da seção ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['defaultsectiontitlesummarytextcolour'] = 'Defina a cor do texto de resumo do título da seção em hover';
$string['defaultsectiontitlesummarytextcolour_desc'] = 'Defina a cor do texto de resumo do título da seção ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['sectiontitlesummarybackgroundcolour'] = 'Defina a cor de fundo do resumo do título da seção em hover';
$string['sectiontitlesummarybackgroundcolour_help'] = 'Defina a cor de fundo do resumo do título da seção ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['defaultsectiontitlesummarybackgroundcolour'] = 'Defina a cor de fundo do resumo do título da seção em hover';
$string['defaultsectiontitlesummarybackgroundcolour_desc'] = 'Defina a cor de fundo do resumo do título da seção ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['sectiontitlesummarybackgroundopacity'] = 'Defina a opacidade de fundo do resumo do título da seção em hover';
$string['sectiontitlesummarybackgroundopacity_help'] = 'Defina a opacidade de fundo do resumo do título da seção, entre 0 e 1 em incrementos de 0,1, ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['defaultsectiontitlesummarybackgroundopacity'] = 'Defina a opacidade de fundo do resumo do título da seção em hover';
$string['defaultsectiontitlesummarybackgroundopacity_desc'] = 'Defina a opacidade de fundo do resumo do título da seção, entre 0 e 1 em incrementos de 0,1, ao passar o mouse sobre o título da seção na caixa de trilha.';
$string['top'] = 'Topo';
$string['bottom'] = 'Base';
$string['centre'] = 'Centro';
$string['left'] = 'Esquerda';
$string['right'] = 'Direita';

// Background options.
$string['tipo_pista'] = 'Pista';
$string['tipo_pista2'] = 'Pista cinza';
$string['tipo_rio'] = 'Rio';
$string['tipo_quebra1'] = 'Quebra-cabeças 1';
$string['tipo_quebra2'] = 'Quebra-cabeças 2';
$string['tipo_tesouro'] = 'Tesouro';
$string['defaultsetshowbackground'] = 'Estilo da trilha';
$string['setshowbackground'] = 'Estilo da Trilha';
$string['setshowbackground_help'] = 'Escolha o fundo da Trilha';

// Check options.
$string['none'] = 'Nenhum';
$string['check'] = 'Check';
$string['star'] = 'Estrela';
$string['like'] = 'Joínha';
$string['setshowcheckstar'] = 'Estilo do check';
$string['setshowcheckstar_help'] = 'Escolha o estilo de check que aparecerá quando o aluno concluir todas as atividades com critérios de conclusão da seção.';
$string['checked'] = 'Completada';

// Lock options.
$string['lock'] = 'Tranca';
$string['mini_lock'] = 'Mini tranca';
$string['mini_lock_tesouro'] = 'Mini tranca tesouro';
$string['locked'] = 'Trancado';
// Reset.
$string['resetgrp'] = 'Resetar:';
$string['resetallgrp'] = 'Resetar tudo:';
$string['resetimagecontaineralignment'] = 'Alinhamento de container de imagem';
$string['resetimagecontaineralignment_help'] = 'Redefine o alinhamento do container de imagem para o valor padrão, de modo que ele será o mesmo que um curso na primeira vez que ele estiver no formato de trilha.';
$string['resetallimagecontaineralignment'] = 'Alinhamentos de container de imagem';
$string['resetallimagecontaineralignment_help'] = 'Redefine os alinhamentos do container de imagens para o valor padrão de todos os cursos, de modo que será o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetimagecontainersize'] = 'Tamanho do container de imagem';
$string['resetimagecontainersize_help'] = 'Redefine o tamanho do container de imagem para o valor padrão, de modo que ele será o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetallimagecontainersize'] = 'Tamanhos de container de imagem';
$string['resetallimagecontainersize_help'] = 'Redefine os tamanhos dos containers de imagens para o valor padrão de todos os cursos, de modo que seja o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetimageresizemethod'] = 'Método de redimensionamento de imagem';
$string['resetimageresizemethod_help'] = 'Redefine o método de redimensionamento da imagem para o valor padrão, de modo que seja o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetallimageresizemethod'] = 'Métodos de redimensionamento de imagem';
$string['resetallimageresizemethod_help'] = 'Redefine os métodos de redimensionamento da imagem para o valor padrão para todos os cursos, de forma que seja o mesmo que o primeiro na formatação da trilha.';
$string['resetimagecontainerstyle'] = 'Estilo do container de imagem';
$string['resetimagecontainerstyle_help'] = 'Redefine o estilo do container de imagem para o valor padrão, de modo que ele será o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetallimagecontainerstyle'] = 'Estilos de container de imagem';
$string['resetallimagecontainerstyle_help'] = 'Redefine os estilos do container de imagem para o valor padrão de todos os cursos, de modo que seja o mesmo que o primeiro na formatação da trilha.';
$string['resetsectiontitleoptions'] = 'Opções do título da seção';
$string['resetsectiontitleoptions_help'] = 'Redefine as opções do título da seção para o valor padrão, de modo que seja o mesmo que na primeira vez que estiver no formato de trilha.';
$string['resetallsectiontitleoptions'] = 'Opções do título da seção';
$string['resetallsectiontitleoptions_help'] = 'Redefine as opções do título da seção para o valor padrão de todos os cursos, de modo que seja o mesmo que na primeira vez que estiver no formato de trilha.';
$string['resetnewactivity'] = 'Nova atividade';
$string['resetnewactivity_help'] = 'Redefine a nova imagem de notificação de atividade para o valor padrão, de modo que ela seja igual a um curso na primeira vez que estiver no formato de trilha.';
$string['resetallnewactivity'] = 'Novas atividades';
$string['resetallnewactivity_help'] = 'Redefine as novas imagens de notificação de atividade para o valor padrão de todos os cursos, de forma que ela seja igual a um curso na primeira vez que estiver no formato de trilha.';
$string['resetfitpopup'] = 'Encaixe o popup da seção na janela';
$string['resetfitpopup_help'] = 'Restaura o popup da seção \'Ajustar ​​para a janela\' para o valor padrão, então será o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetallfitpopup'] = 'Ajustar a seção pop-up à janela';
$string['resetallfitpopup_help'] = 'Restaura o popup da seção \'Ajustar ​​para a janela\' para o valor padrão para todos os cursos, então será o mesmo que um curso na primeira vez que estiver no formato de trilha.';
$string['resetgreyouthidden'] = 'Indisponível cinza por fora';
$string['resetgreyouthidden_desc'] = 'Restaura a propriedade \'A exibição da trilha mostra imagens da seção indisponíveis em cinza e desvinculadas.\'';
$string['resetgreyouthidden_help'] = 'Redefine a propriedade \'Na exibição da trilha mostre as imagens da seção indisponíveis em cinza e desvinculadas.\'';

// Section 0 on own page when out of the trail and course layout is 'Show one section per page'.
$string['setsection0ownpagenotrailonesection'] = 'Seção 0 em sua própria página quando fora da trilha e em uma única página de seção';
$string['setsection0ownpagenotrailonesection_help'] = 'Ter seção 0 em sua própria página quando estiver fora da trilha e a configuração \'Layout do curso\' por \'Uma seção por página\'.';
$string['defaultsection0ownpagenotrailonesection'] = 'Seção 0 em sua própria página quando fora da trilha e em uma única página de seção';
$string['defaultsection0ownpagenotrailonesection_desc'] = 'Ter seção 0 em sua própria página quando estiver fora da trilha e a configuração \'Layout do curso\' por \' Uma seção por página\'.';
$string['resetimagecontainernavigation'] = 'Navegação por container de imagem';
$string['resetimagecontainernavigation_help'] = 'Redefine a navegação do container de imagens para o valor padrão, de modo que seja o mesmo de um curso na primeira vez que estiver no formato de trilha.';
$string['resetallimagecontainernavigation'] = 'Navegação por container de imagens';
$string['resetallimagecontainernavigation_help'] = 'Redefine a navegação do container de imagens para o valor padrão de todos os cursos, para que seja o mesmo de um curso na primeira vez que estiver no formato de trilha.';

// Capabilities.
$string['trail:changeimagecontaineralignment'] = 'Alterar ou repor o alinhamento do container de imagem';
$string['trail:changeimagecontainernavigation'] = 'Alterar ou repor a navegação do container de imagem';
$string['trail:changeimagecontainersize'] = 'Alterar ou redefinir o tamanho do container da imagem';
$string['trail:changeimageresizemethod'] = 'Alterar ou redefinir o método de redimensionamento da imagem';
$string['trail:changeimagecontainerstyle'] = 'Alterar ou redefinir o estilo do container da imagem';
$string['trail:changesectiontitleoptions'] = 'Alterar ou redefinir as opções do título da seção';

// Other.
$string['greyouthidden'] = 'Indisponível cinza por fora';
$string['greyouthidden_desc'] = 'Na exibição da trilha, mostre imagens da seção indisponíveis em cinza e desvinculadas.';
$string['greyouthidden_help'] = 'Na exibição da trilha, mostre imagens da seção indisponíveis em cinza e desvinculadas.';

$string['custommousepointers'] = 'Use ponteiros de mouse personalizados';
$string['custommousepointers_desc'] = 'Na trilha use ponteiros de mouse personalizados.';

// Privacy.
$string['privacy:nop'] = 'O formato Trail armazena muitas configurações que pertencem à sua configuração. Nenhuma das configurações está relacionada a um usuário específico. É sua responsabilidade garantir que nenhum dado do usuário seja inserido em nenhum dos campos de texto livre. Definir uma configuração fará com que a ação seja registrada no núcleo do sistema de registro do Moodle em relação ao usuário que a alterou, isso está fora do controle de formatos. Consulte o sistema principal de registro para obter conformidade com a privacidade para isso. Ao carregar imagens, você deve evitar o upload de imagens com dados de localização incorporados (EXIF GPS) incluídos ou outros dados pessoais. Seria possível extrair qualquer localização / dados pessoais das imagens. Por favor, examine o código cuidadosamente para ter certeza de que ele está de acordo com sua interpretação de suas leis de privacidade. Eu não sou advogado e minha análise é baseada na minha interpretação. Se você tiver alguma dúvida, remova o formato imediatamente.';
