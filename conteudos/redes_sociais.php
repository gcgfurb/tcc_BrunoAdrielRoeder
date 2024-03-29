<?php
  class RedeSocial {
    
    public function __construct () {
    }
    
    public function controleExibicao($secao, $subsecao, $item) {
      if (isset($_GET['acao']))  {      $acao = addslashes($_GET['acao']);          } else {      $acao = '';               }
      if (isset($_GET['at']))    {      $ativas = addslashes($_GET['at']);          } else {      $ativas = 1;              }
      if (isset($_GET['cd']))    {      $codigo = addslashes($_GET['cd']);          } else {      $codigo = '';             }

      switch ($acao) {
        case "":
          $this->listarAcoes($secao, $subsecao, $item, $ativas);
          $this->listarItens($secao, $subsecao, $item, $ativas);
        break;

        case "cadastrar":
          $link = "index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=".$ativas;
          $this->montarFormularioCadastro($link);
        break;

        case "editar":
          $link = "index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=".$ativas;
          $this->montarFormularioEdicao($link, $codigo);
        break;
        
        case "salvar":
          if (isset($_SESSION['life_edicao'])) {
            $this->salvarCadastroAlteracao();
            unset($_SESSION['life_edicao']);
          } 
          $this->listarAcoes($secao, $subsecao, $item, $ativas);
          $this->listarItens($secao, $subsecao, $item, $ativas);
        break;        
               
        case "status":
          $this->alterarSituacaoAtivoRedeSocial($codigo);
          $this->listarAcoes($secao, $subsecao, $item, $ativas);
          $this->listarItens($secao, $subsecao, $item, $ativas);
        break;
      }
    }
   
    public function listarAcoes($secao, $subsecao, $item, $ativas) {
      require_once 'includes/utilitarios.php';                                  $util = new Utilitario();
      $opcoes_1= array();
      $opcao= array();      $opcao['indice']= "1";      $opcao['link']= "index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=1";           if($ativas == '1') { $opcao['selecionado'] = ' selected=\"selected\" '; } else { $opcao['selecionado'] = ''; }      $opcao['descricao']= "Ativas";                                            $opcoes_1[]= $opcao;
      $opcao= array();      $opcao['indice']= "2";      $opcao['link']= "index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=0";           if($ativas == '0') { $opcao['selecionado'] = ' selected=\"selected\" '; } else { $opcao['selecionado'] = ''; }      $opcao['descricao']= "Inativas";                                          $opcoes_1[]= $opcao;
      $opcao= array();      $opcao['indice']= "3";      $opcao['link']= "index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=2";           if($ativas == '2') { $opcao['selecionado'] = ' selected=\"selected\" '; } else { $opcao['selecionado'] = ''; }      $opcao['descricao']= "Ativas/Inativas";                                   $opcoes_1[]= $opcao;
      foreach ($opcoes_1 as $op) {        $nome = 'comandos_filtros_1_'.$op['indice'];        $util->campoHidden($nome, $op['link']);      }
      
      include 'js/js_navegacao.js';
      echo "<p class=\"fontComandosFiltros\">\n";
      echo "  <a href=\"index.php?secao=".$secao."\"><img src=\"icones/voltar.png\" alt=\"Voltar\" title=\"Voltar\" border=\"0\"></a> \n";
      echo "  <a href=\"index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=".$ativas."&acao=cadastrar\"><img src=\"icones/novo.png\" alt=\"Nova rede social\" title=\"Nova rede social\" border=\"0\"></a> \n";
      echo "  <select name=\"comandos_filtros_1\" id=\"comandos_filtros_1\" class=\"fontComandosFiltros\" onChange=\"navegar(1);\" alt=\"Filtro de status\" title=\"Filtro de status\">\n";
      foreach ($opcoes_1 as $op) {
        echo "    <option value=\"".$op['indice']."\" ".$op['selecionado'].">".$op['descricao']."&nbsp;</option>\n";
      }
      echo "  </select>\n";
      echo "</p>\n";
    }

    private function listarItens($secao, $subsecao, $item, $ativas) {
      require_once 'includes/utilitarios.php';                                  $util = new Utilitario();

      $mensagem = "Redes sociais ";

      $itens = $this->selectRedesSociais($ativas);
      
      echo "<h2>".$mensagem."</h2>\n";      
      echo "  <table class=\"tabConteudo\">\n";
      $style = "linhaOn"; 
      echo "    <tr class=\"".$style."\">\n";
      echo "      <td class=\"celConteudo\">Rede social</td>\n";
      echo "      <td class=\"celConteudo\">A��es</td>\n";
      echo "    </tr>\n";      
      foreach ($itens as $it) {
        $style = ($style!="linhaOf")?('linhaOf'):('linhaOn');
        echo "    <tr class=\"".$style."\">\n";
        echo "      <td class=\"celConteudo\">".$it['nm_rede_social']."</td>\n";
        echo "      <td class=\"celConteudo\">\n";
        echo "        <a href=\"#\" class=\"dcontexto\">\n";
        echo "          <img src=\"icones/informacoes.png\" border=\"0\">\n";
        echo "          <span class=\"fontdDetalhar\">\n";
        echo $this->detalharRedeSocial($it['cd_rede_social']);
        echo "        </span></a>\n";
        echo "        <a href=\"index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=".$ativas."&cd=".$it['cd_rede_social']."&acao=editar\"><img src=\"icones/editar.png\" alt=\"Editar\" title=\"Editar\" border=\"0\"></a>\n";
        if ($it['eh_editavel'] == '1') {
          echo "        <a href=\"index.php?secao=".$secao."&sub=".$subsecao."&it=".$item."&at=".$ativas."&cd=".$it['cd_rede_social']."&acao=status\">";
          if ($it['eh_ativo'] == 1) {
            echo "          <img src=\"icones/inativar.png\" alt=\"Inativar\" title=\"Inativar\" border=\"0\"></a>\n";
          } else {
            echo "          <img src=\"icones/reativar.png\" alt=\"Reativar\" title=\"Reativar\" border=\"0\"></a>\n";
          }
        } else {
          echo "          <img src=\"icones/vazio.png\" border=\"0\">\n";
        }
        echo "      </td>\n";
        echo "    </tr>\n";
      }
      echo "  </table>\n";       
    }
    
    public function detalharRedeSocial($cd_rede_social) {
      require_once 'usuarios/usuarios.php';                                     $usu = new Usuario();
      require_once 'includes/data_hora.php';                                    $dh = new DataHora();
      
      $dados = $this->selectDadosRedeSocial($cd_rede_social);
      
      $retorno = "";
      $dados_usuario = $usu->selectDadosUsuario($dados['cd_usuario_cadastro']);
      $retorno.= "Cadastrado por ".$dados_usuario['nm_usuario']."<br />\n";
      $retorno.= "Data do cadastro ".$dh->imprimirData($dados['dt_cadastro'])."<br />\n";
      if ($dados['cd_usuario_ultima_atualizacao'] != '') {
        $dados_usuario = $usu->selectDadosUsuario($dados['cd_usuario_ultima_atualizacao']);
        $retorno.= "�ltima atualiza��o por ".$dados_usuario['nm_usuario']."<br />\n";
        $retorno.= "Data do �ltima atualiza��o ".$dh->imprimirData($dados['dt_ultima_atualizacao'])."\n";
      }
      return $retorno;
    }
     
    private function montarFormularioCadastro($link) {
      $cd_rede_social = "";
      $nm_rede_social = "";
      $ds_arquivo_logo_compartilhamento = "";
      $lk_rede_social = "";
      $eh_ativo = "1";
      $eh_editavel = '1';
      
      $_SESSION['life_edicao']= 1;
      echo "  <h2>Cadastro de redes sociais</h2>\n";
      $this->imprimeFormularioCadastro($link, $cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $eh_editavel);
    }
    
    private function montarFormularioEdicao($link, $cd_rede_social) {
      $dados = $this->selectDadosRedeSocial($cd_rede_social);

      $nm_rede_social = $dados['nm_rede_social'];
      $ds_arquivo_logo_compartilhamento = $dados['ds_arquivo_logo_compartilhamento'];
      $lk_rede_social = $dados['lk_rede_social'];
      $eh_ativo = $dados['eh_ativo'];
      $eh_editavel = $dados['eh_editavel'];
      
      $_SESSION['life_edicao']= 1;
      echo "  <h2>Edi��o de rede social</h2>\n";
      $this->imprimeFormularioCadastro($link, $cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $eh_editavel);
    }
    
    public function imprimeFormularioCadastro($link, $cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $eh_editavel) {
      require_once 'includes/utilitarios.php';                                  $util = new Utilitario();

      echo "<p class=\"fontComandosFiltros\">\n";
      echo "  <a href=\"".$link."\"><img src=\"icones/voltar.png\" alt=\"Voltar\" title=\"Voltar\" border=\"0\"></a> \n";
      echo "</p>\n";

      include "js/js_cadastro_rede_social.js";
      echo "  <form method=\"POST\" name=\"cadastro\" id=\"cadastro\" enctype=\"multipart/form-data\" action=\"".$link."&acao=salvar\" onSubmit=\"return valida(this);\">\n";
      echo "    <table class=\"tabConteudo\">\n";
      $util->campoHidden('eh_form', '1');
      $util->campoHidden('cd_rede_social', $cd_rede_social);
      $util->campoHidden('ds_arquivo_logo_compartilhamento_antigo', $ds_arquivo_logo_compartilhamento);

      if ($eh_editavel == '1') {
        $util->linhaUmCampoText(1, 'Rede social ', 'nm_rede_social', '100', '100', $nm_rede_social);
      } else {
        $util->campoHidden('nm_rede_social', $nm_rede_social);
        $util->linhaDuasColunasComentario('Rede social ', $nm_rede_social);
      }
      $util->linhaComentario('');
      $util->linhaUmCampoText(1, 'Link para compartilhamento ', 'lk_rede_social', '250', '100', $lk_rede_social);
      $explicacao = "Utilize<br />".
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#URL# - ser� substitu�do pela URL da p�gina;<br />".
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#TITULO# - ser� substitu�do pelo t�tulo da p�gina, mat�ria ou material;<br />";
      $util->linhaComentario($explicacao);
      $util->linhaComentario('');
      if ($cd_rede_social > 0) {
        $util->linhaUmCampoArquivo('0', 'Logo de compartilhamento ', 'ds_arquivo_logo_compartilhamento', 100, 100, '');
        echo "    <tr>\n";
        echo "      <td class=\"celConteudo\" colspan=\"2\" style=\"text-align:center;\">\n";
        echo "        <img src=\"".$_SESSION['life_link_completo'].$ds_arquivo_logo_compartilhamento."\" border=\"0\" width=\"100px;\">\n";
        echo "      </td>\n";
        echo "    </tr>\n";
        $util->linhaComentario('Para alterar o arquivo atual, selecione um novo arquivo.O arquivo com a imagem deve estar no formato .png ou .jpg. A imagem para logo de compartilhamento deve possuir 24px de largura por 24px de altura.');
      } else {
        $util->linhaUmCampoArquivo('1', 'Logo de compartilhamento ', 'ds_arquivo_logo_compartilhamento', 100, 100, '');
        $util->linhaComentario('O arquivo com a imagem deve estar no formato .png ou .jpg. A imagem para logo de compartilhamento deve possuir 24px de largura por 24px de altura.');
      }
      $util->linhaComentario('');
      
      $opcoes= array();
      $opcao= array();      $opcao[]= '1';      $opcao[]= 'Sim';      $opcoes[]= $opcao;
      $opcao= array();      $opcao[]= '0';      $opcao[]= 'N�o';      $opcoes[]= $opcao;
      $util->linhaSeletor('Ativa a rede para compartilhamento ', 'eh_ativo', $eh_ativo, $opcoes, '100');

      $util->linhaBotao('Salvar', "valida(cadastro);");
      echo "    </table>\n";
      echo "    <p class=\"fontConteudoAlerta\">(*) Campos obrigat�rios</p>\n";
      echo "  </form>\n";
      $util->posicionarCursor('cadastro', 'nm_rede_social'); 
    }
    
    public function salvarCadastroAlteracao() {
      require_once 'includes/utilitarios.php';                                  $util = new Utilitario();
      require_once 'conteudos/fotos.php';                                       $fot = new Fotos();

      $cd_rede_social = addslashes($_POST['cd_rede_social']);
      $nm_rede_social = $util->limparVariavel($_POST['nm_rede_social']);

      $ds_arquivo_logo_compartilhamento_antigo = $util->limparVariavel($_POST['ds_arquivo_logo_compartilhamento_antigo']);

      $lk_rede_social = $util->limparVariavel($_POST['lk_rede_social']);

      $eh_ativo = addslashes($_POST['eh_ativo']);

      $tp_associacao = 'RS';
      $dados_pasta = $fot->selectDadosTiposAssociacoesFotos($tp_associacao);
      $ds_pasta = $dados_pasta['ds_pasta_tipo_associacao_foto'];

      $lk_seo = $util->retornaLinkSEO($nm_rede_social, 'life_redes_sociais', 'lk_seo', '100', $cd_rede_social);
      
      $arquivo = $_FILES['ds_arquivo_logo_compartilhamento'];
      if ($arquivo['name'] != '') {
        $foto = $fot->enviarFoto('ds_arquivo_logo_compartilhamento', $ds_pasta, '', $tp_associacao, '60');
        if ($foto[0] != '') {
          echo "<p class=\"fontConteudoAlerta\">ERRO - ".$foto[0]."</p>\n";
          $ds_arquivo_logo_compartilhamento = '';
        } else {
          $ds_arquivo_logo_compartilhamento = $ds_pasta."/".$foto[1];
        }
      } else {
        if ($cd_rede_social > 0) {
          $ds_arquivo_logo_compartilhamento = $ds_arquivo_logo_compartilhamento_antigo;
        } else {
          echo "<p class=\"fontConteudoAlerta\">Erro - N�o foi selecionado aquivo com Logo de compartilhamento!</p>\n";
          $ds_arquivo_logo_compartilhamento = '';
        }
      }

      if ($cd_rede_social > 0) {
        if ($this->alteraRedeSocial($cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $lk_seo)) {
          echo "<p class=\"fontConteudoSucesso\">Rede social editada com sucesso!</p>\n";
        } else {
          echo "<p class=\"fontConteudoAlerta\">Problemas na edi��o da rede social, ou nenhuma informa��o alterada!</p>\n";
        }
      } else {
        if ($this->insereRedeSocial($nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $lk_seo)) {
          echo "<p class=\"fontConteudoSucesso\">Rede social cadastrada com sucesso!</p>\n";
        } else {
          echo "<p class=\"fontConteudoAlerta\">Problemas no cadastro da rede social!</p>\n";
        }
      }
    } 


    private function alterarSituacaoAtivoRedeSocial($cd_rede_social) {
      $dados = $this->selectDadosRedeSocial($cd_rede_social);

      $nm_rede_social = $dados['nm_rede_social'];
      $ds_arquivo_logo_compartilhamento = $dados['ds_arquivo_logo_compartilhamento'];
      $lk_rede_social = $dados['lk_rede_social'];
      $eh_ativo = $dados['eh_ativo'];
      $lk_seo = $dados['lk_seo'];

      if ($eh_ativo == 1) {        $eh_ativo = 0;      } else {        $eh_ativo = 1;      }      

      if ($this->alteraRedeSocial($cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $lk_seo)) {
        echo "<p class=\"fontConteudoSucesso\">Status da rede social alterado com sucesso!</p>\n";
      } else {
        echo "<p class=\"fontConteudoAlerta\">Problemas ao alterar status da rede social!</p>\n";
      }
    }

    public function retornaOpcaoCompartilhamento($url, $titulo) {
      $titulo = str_replace('"',"",$titulo);
      $titulo = str_replace("'","",$titulo);

      $redes = $this->selectRedesSociais('1');
      if (count($redes) > 0) {
        echo "      Compartilhe ";
        foreach ($redes as $r) {
          $link = $r['lk_rede_social'];
          $link = str_replace("#URL#",urlencode($url),$link);
          $link = str_replace("#TITULO#",urlencode($titulo),$link);
          echo "<a href=\"javascript: void(0);\" onclick=\"window.open('".$link."/','compartilhe', 'toolbar=0, status=0, width=650, height=450');\"><img src=\"".$_SESSION['life_link_completo'].$r['ds_arquivo_logo_compartilhamento']."\" alt=\"Compartilhe no ".$r['nm_rede_social']."\" title=\"Compartilhe no ".$r['nm_rede_social']."\" border=\"0\" height=\"24px\"></a>&nbsp;\n";
        }
      }
    }

//**************BANCO DE DADOS**************************************************    
    public function selectRedesSociais($eh_ativo) {
      $sql  = "SELECT * ".
              "FROM life_redes_sociais ".
              "WHERE cd_rede_social > '0' ";
      if ($eh_ativo != 2) {
        $sql.= "AND eh_ativo = '$eh_ativo' ";
      }
      $sql.= "ORDER BY nm_rede_social";
      $result_id = @mysql_query($sql) or die ("Erro no banco de dados - TABELA REDES SOCIAIS!");
      $dados = array();
      while ($linha = mysql_fetch_assoc($result_id) ) {
          $dados[] = $linha;
      }
      return $dados;        
    }    
    
    public function selectDadosRedeSocial($cd_rede_social) {
      $sql  = "SELECT * ".
              "FROM life_redes_sociais ".
              "WHERE cd_rede_social = '$cd_rede_social' ";
      $result_id = @mysql_query($sql) or die ("Erro no banco de dados - TABELA REDES SOCIAIS!");
      $dados= mysql_fetch_assoc($result_id);
      return $dados;        
    }
    
    public function insereRedeSocial($nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $lk_seo) {
      $cd_usuario_cadastro = $_SESSION['life_codigo'];
      $dt_cadastro = date('Y-m-d');
      $sql = "INSERT INTO life_redes_sociais ".
             "(nm_rede_social, ds_arquivo_logo_compartilhamento, lk_rede_social, eh_ativo, cd_usuario_cadastro, dt_cadastro, lk_seo) ".
             "VALUES ".
             "(\"$nm_rede_social\", \"$ds_arquivo_logo_compartilhamento\", \"$lk_rede_social\", \"$eh_ativo\", \"$cd_usuario_cadastro\", \"$dt_cadastro\", \"$lk_seo\")";
      require_once 'includes/utilitarios.php';                                  $util= new Utilitario();
      $util->gerarLog($sql, 'redes_sociais');            
      mysql_query($sql) or die ("Erro no banco de dados - TABELA REDES SOCIAIS!");
      $saida = mysql_affected_rows();
      return $saida;     
    }

    public function alteraRedeSocial($cd_rede_social, $nm_rede_social, $ds_arquivo_logo_compartilhamento, $lk_rede_social, $eh_ativo, $lk_seo) {
      $cd_usuario_ultima_atualizacao = $_SESSION['life_codigo'];
      $dt_ultima_atualizacao = date('Y-m-d');
      $sql = "UPDATE life_redes_sociais SET ".
             "nm_rede_social = \"$nm_rede_social\", ".
             "ds_arquivo_logo_compartilhamento = \"$ds_arquivo_logo_compartilhamento\", ".
             "lk_rede_social = \"$lk_rede_social\", ".
             "eh_ativo = \"$eh_ativo\", ".
             "lk_seo = \"$lk_seo\", ".
             "cd_usuario_ultima_atualizacao = \"$cd_usuario_ultima_atualizacao\", ".
             "dt_ultima_atualizacao = \"$dt_ultima_atualizacao\" ".             
             "WHERE cd_rede_social= '$cd_rede_social' ";
      require_once 'includes/utilitarios.php';                                  $util= new Utilitario();
      $util->gerarLog($sql, 'redes_sociais');            
      mysql_query($sql) or die ("Erro no banco de dados - TABELA REDES SOCIAIS!");
      $saida = mysql_affected_rows();
      return $saida;     
    }    
    
  }
?>