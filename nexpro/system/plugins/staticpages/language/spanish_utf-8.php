<?php

###############################################################################
# spanish_utf-8.php
# This is the spanish language page for the Geeklog Static Page Plug-in!
#
# Copyright (C) 2007 José R. Valverde (Terminado)
# jrvalverde@cnb.uam.es
#
# Copyright (C) 2001 Tony Bibbs
# tony@tonybibbs.com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

global $LANG32;

###############################################################################
# Array Format:
# $LANGXX[YY]:  $LANG - variable name
#               XX    - file id number
#               YY    - phrase id number
###############################################################################

$LANG_STATIC = array(
    'newpage' => 'Nueva Página',
    'adminhome' => 'Administración',
    'staticpages' => 'Páginas Estáticas',
    'staticpageeditor' => 'Editor de páginas estáticas',
    'writtenby' => 'Escrito por',
    'date' => 'Última edición',
    'title' => 'Título',
    'content' => 'Contenido',
    'hits' => 'Hits',
    'staticpagelist' => 'Lista de Páginas Estáticas',
    'url' => 'URL',
    'edit' => 'Editar',
    'lastupdated' => 'Última Edición',
    'pageformat' => 'Formato de Página',
    'leftrightblocks' => 'Cajas a Derecha e Izquierda',
    'blankpage' => 'Página en blanco',
    'noblocks' => 'Sin Cajas',
    'leftblocks' => 'Cajas a Izquierda',
    'addtomenu' => 'Añadir al menú',
    'label' => 'Etiqueta',
    'nopages' => 'Todavía no hay páginas estáticas',
    'save' => 'guardar',
    'preview' => 'vista previa',
    'delete' => 'eliminar',
    'cancel' => 'cancelar',
    'access_denied' => 'Acceso Denegado',
    'access_denied_msg' => 'Estás intentando acceder a una página de administración de Páginas Estáticas. Ten en cuenta que cualquier acceso a esta página se registra',
    'all_html_allowed' => 'Se permite cualquier etiqueta HTML',
    'results' => 'Resultado de Páginas Estáticas',
    'author' => 'Autor',
    'no_title_or_content' => 'Debes rellenar al menos los campos <b>Título</b> y <b>Contenido</b>.',
    'no_such_page_anon' => 'Por favor, regístrate..',
    'no_page_access_msg' => "Esto puede ser porque no te has registrado o no eres un miembro de {$_CONF['site_name']}. Por favor, <a href=\"{$_CONF['site_url']}/users.php?mode=new\">regístrate</a> en {$_CONF['site_name']} para obtener acceso completo",
    'php_msg' => 'PHP: ',
    'php_warn' => 'Aviso: Si activas esta opción se interpretará el código PHP en tu página. ¡¡Usar con cuidado!!',
    'exit_msg' => 'Tipo de salida: ',
    'exit_info' => 'Activar para Mensaje de Acceso Preciso. No marcar para verificaciones de seguridad  y mensaje normales.',
    'deny_msg' => 'Acceso denegado a esta página. O bien ha sido movida/renombrada o no tienes permiso suficiente.',
    'stats_headline' => '10 páginas estáticas principales',
    'stats_page_title' => 'Título de la página',
    'stats_hits' => 'Hits',
    'stats_no_hits' => 'Parece que no hay páginas estáticas o que nadie las ha visto nunca.',
    'id' => 'ID',
    'duplicate_id' => 'La ID elegida ya está en uso. Por favor, elige otra.',
    'instructions' => 'Para modificar o borrar una página estática, pulsa en el número correspondiente. Para ver una página estática pulsa en su título. Para cerar una página nueva pulsa en "Página nueva". Pulsa en [C] para crear una copia de una página existente.',
    'centerblock' => 'Bloque central: ',
    'centerblock_msg' => 'Cuando se selecciona esta opción la página estática aparecerá como un bloque central en la página principal.',
    'topic' => 'Tópico: ',
    'position' => 'Posición: ',
    'all_topics' => 'Todos',
    'no_topic' => 'Solo página principal',
    'position_top' => 'Arriba de la página',
    'position_feat' => 'Tras la noticia destacada',
    'position_bottom' => 'Abajo de la página',
    'position_entire' => 'Toda la página',
    'head_centerblock' => 'Bloque central',
    'centerblock_no' => 'No',
    'centerblock_top' => 'Arriba',
    'centerblock_feat' => 'Noticia destacada',
    'centerblock_bottom' => 'Abajo',
    'centerblock_entire' => 'Toda la página',
    'inblock_msg' => 'In a block: ',
    'inblock_info' => 'Wrap Static Page in a block.',
    'title_edit' => 'Edit page',
    'title_copy' => 'Make a copy of this page',
    'title_display' => 'Display page',
    'select_php_none' => 'no ejecutar PHP',
    'select_php_return' => 'ejecutar PHP (volver)',
    'select_php_free' => 'ejecutar PHP',
    'php_not_activated' => "Es uso de PHP en páginas estáticas no está activado. Por favor, véase la <a href=\"{$_CONF['site_url']}/docs/english/staticpages.html#php\">documentación</a> para más información.",
    'printable_format' => 'Listo para imprimir',
    'copy' => 'Copy',
    'limit_results' => 'Limit Results',
    'search' => 'Search',
    'submit' => 'Submit',
    'no_new_pages' => 'No new pages',
    'pages' => 'Pages'
);

$PLG_staticpages_MESSAGE15 = 'Your comment has been submitted for review and will be published when approved by a moderator.';
$PLG_staticpages_MESSAGE19 = 'Your page has been successfully saved.';
$PLG_staticpages_MESSAGE20 = 'Your page has been successfully deleted.';

// Messages for the plugin upgrade
$PLG_staticpages_MESSAGE3001 = 'Plugin upgrade not supported.';
$PLG_staticpages_MESSAGE3002 = $LANG32[9];

// Localization of the Admin Configuration UI
$LANG_configsections['staticpages'] = array(
    'label' => 'Static Pages',
    'title' => 'Static Pages Configuration'
);

$LANG_confignames['staticpages'] = array(
    'allow_php' => 'Allow PHP?',
    'sort_by' => 'Sort Centerblocks by',
    'sort_menu_by' => 'Sort Menu Entries by',
    'delete_pages' => 'Delete Pages with Owner?',
    'in_block' => 'Wrap Pages in Block?',
    'show_hits' => 'Show Hits?',
    'show_date' => 'Show Date?',
    'filter_html' => 'Filter HTML?',
    'censor' => 'Censor Content?',
    'default_permissions' => 'Page Default Permissions',
    'aftersave' => 'After Saving Page',
    'atom_max_items' => 'Max. Pages in Webservices Feed',
    'meta_tags' => 'Enable Meta Tags',
    'newstaticpagesinterval' => 'New Static Page Interval',
    'hidenewstaticpages' => 'Hide New Static Pages',
    'title_trim_length' => 'Title Trim Length',
    'includecenterblocks' => 'Include Center Block Static Pages',
    'includephp' => 'Include Static Pages with PHP',
    'includesearch' => 'Enable Static Pages in Search',
    'includesearchcenterblocks' => 'Include Center Block Static Pages',
    'includesearchphp' => 'Include Static Pages with PHP'
);

$LANG_configsubgroups['staticpages'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['staticpages'] = array(
    'fs_main' => 'Static Pages Main Settings',
    'fs_whatsnew' => 'What\'s New Block',
    'fs_search' => 'Search Results',
    'fs_permissions' => 'Default Permissions'
);

// Note: entries 0, 1, 9, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['staticpages'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => true, 'False' => false),
    2 => array('Date' => 'date', 'Page ID' => 'id', 'Title' => 'title'),
    3 => array('Date' => 'date', 'Page ID' => 'id', 'Title' => 'title', 'Label' => 'label'),
    9 => array('Forward to page' => 'item', 'Display List' => 'list', 'Display Home' => 'home', 'Display Admin' => 'admin'),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3)
);

?>
