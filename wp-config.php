<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'unoesc');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'rootroot');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ',ebU{>})4O@:MIu)Q[#fPb-w{te)-UMv{uiO4hM^lXev%%f0lC7RKWgZ)M]v(8z<');
define('SECURE_AUTH_KEY',  '@S-JL;Z3BpImNJw?t8=,$7WC(N{EM}ydU|7yezh<AHv*AEws#fkW43CXhU8FOSfw');
define('LOGGED_IN_KEY',    '4tNn3 I7vJi8g9)h?gB;JZAr^wN46dI(H,EeCrm(ul/maCmr|6 0[%(wz(F9SkB6');
define('NONCE_KEY',        ';-?Cwe%w5h6r!HRbgD$l1N{=^lN5U4R[gGgcjxhj)@.dL:Q[*.GVvt0$XhS:6%wP');
define('AUTH_SALT',        ';q^gOl.{!r~P}7D]j*jU^Rx/%dZ A_a^/N^41%sJKE{C5)twTr22JlJru/zLrkTO');
define('SECURE_AUTH_SALT', 'aM_@ %^uEJ4nk>(B-eAlMiluzmMZ=![,P :F1r,55&Oyjx0f1%OU0RaP[)+~k,El');
define('LOGGED_IN_SALT',   '@m$aId_]aR@X68SDU7a&yX:4c:PI1WG3vLC]8O/,pZMBqO.zQaB!ux*8PBr@f<xN');
define('NONCE_SALT',       '3.fJ65J26r(Z^SVUu s<iP7n_]odxIfcsY7L(448cUo$nfW>F]<SbJj3pRi==jFx');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
