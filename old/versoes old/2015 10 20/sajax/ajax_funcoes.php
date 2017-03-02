<?php

  function incluirAreaConhecimentoRelacao($cd_area_conhecimento) {
    require_once 'conteudos/areas_conhecimento.php';                            $are_con = new AreaConhecimento();
    require_once 'includes/configuracoes.php';                                  $conf = new Configuracao();

    if (!isset($_SESSION['life_codigo_areas_conhecimento_capa'])) {
      $_SESSION['life_codigo_areas_conhecimento_capa'] = array();
    }
    $achou = false;
    $termo = "<p class=\"fontChamadaRelacaoAreaConhecimento\">";
    $tamanho = 0;
    $limite = $conf->retornaNumeroLimiteCaracteresRelacaoChamadaAreasConhecimentoCapaSite();
    $concatenar = true;
    foreach ($_SESSION['life_codigo_areas_conhecimento_capa'] as $it) {
      if ($it == $cd_area_conhecimento) {
        $achou = true;
      } 
    }

    if (!$achou) {
      $itens = $_SESSION['life_codigo_areas_conhecimento_capa'];
      unset($_SESSION['life_codigo_areas_conhecimento_capa']);
      $_SESSION['life_codigo_areas_conhecimento_capa'] = array();
      $_SESSION['life_codigo_areas_conhecimento_capa'][] = $cd_area_conhecimento;
      foreach ($itens as $it) {
        $_SESSION['life_codigo_areas_conhecimento_capa'][] = $it;
      }
    }
    
    foreach ($_SESSION['life_codigo_areas_conhecimento_capa'] as $it) {
      $dados = $are_con->selectDadosAreaConhecimento($it);
      $tamanho_nome = strlen($dados['nm_area_conhecimento']);
      $tamanho += $tamanho_nome + 6;
      if ($tamanho < $limite) {
        $termo.= $dados['nm_area_conhecimento']." <a href=\"#\" onClick=\"retirarRelacao('".$it."');\" class=\"fontChamadaRelacaoAreaConhecimentoLink\" alt=\"Retirar a �rea de Conhecimento ".$dados['nm_area_conhecimento']." da Rela��o de Itens � serem Pesquisados\" title=\"Retirar a �rea de Conhecimento ".$dados['nm_area_conhecimento']." da Rela��o de Itens � serem Pesquisados\">[x]</a>&nbsp;&nbsp;";
      } else {     
        if ($concatenar) {
          $termo.= "&nbsp;&nbsp;<a href=\"".$_SESSION['life_link_completo']."areas-conhecimento\" class=\"fontChamadaRelacaoAreaConhecimentoLink\" alt=\"Exibir Rela��o Completa de Itens � serem Pesquisados\" title=\"Exibir Rela��o Completa de Itens � serem Pesquisados\">[+]</a> ";
          $concatenar = false;
        }
      }
    }                 
    $termo.= "</p>";
    
    return utf8_encode($termo);
  }
  
  function retirarRelacao($cd_area_conhecimento) {
    require_once 'conteudos/areas_conhecimento.php';                            $are_con = new AreaConhecimento();
    require_once 'includes/configuracoes.php';                                  $conf = new Configuracao();

    if (!isset($_SESSION['life_codigo_areas_conhecimento_capa'])) {
      $_SESSION['life_codigo_areas_conhecimento_capa'] = array();
    }
    $termo = "<p class=\"fontChamadaRelacaoAreaConhecimento\">";
    $tamanho = 0;
    $limite = $conf->retornaNumeroLimiteCaracteresRelacaoChamadaAreasConhecimentoCapaSite();
    $concatenar = true;
    $itens = $_SESSION['life_codigo_areas_conhecimento_capa'];
    unset($_SESSION['life_codigo_areas_conhecimento_capa']);
    $_SESSION['life_codigo_areas_conhecimento_capa'] = array();
    foreach ($itens as $it) {
      if ($it != $cd_area_conhecimento) {
        $_SESSION['life_codigo_areas_conhecimento_capa'][] = $it;
        $dados = $are_con->selectDadosAreaConhecimento($it);
        $tamanho_nome = strlen($dados['nm_area_conhecimento']);
        $tamanho += $tamanho_nome + 6;
        if ($tamanho < $limite) {
          $termo.= $dados['nm_area_conhecimento']." <a href=\"#\" onClick=\"retirarRelacao('".$it."');\" class=\"fontChamadaRelacaoAreaConhecimentoLink\" alt=\"Retirar a �rea de Conhecimento ".$dados['nm_area_conhecimento']." da Rela��o de Itens � serem Pesquisados\" title=\"Retirar a �rea de Conhecimento ".$dados['nm_area_conhecimento']." da Rela��o de Itens � serem Pesquisados\">[x]</a>&nbsp;&nbsp;";
        } else {     
          if ($concatenar) {
            $termo.= "&nbsp;&nbsp;<a href=\"".$_SESSION['life_link_completo']."areas-conhecimento\" class=\"fontChamadaRelacaoAreaConhecimentoLink\" alt=\"Exibir Rela��o Completa de Itens � serem Pesquisados\" title=\"Exibir Rela��o Completa de Itens � serem Pesquisados\">[+]</a> ";
            $concatenar = false;
          }
        }
      }
    }
    $termo.= "</p>";
    
    return utf8_encode($termo);
  }
  
  function atualizarCampoTipoArquivo($cd_formato, $cd_formato_antigo, $eh_setado, $ds_location) {
    require_once 'includes/configuracoes.php';                                  $conf = new Configuracao();

    $ajuda = "";
    $retorno = "";
    switch ($cd_formato) {
      case "1":
        $ds_sugestao_locais_audio = $conf->retornaSugestaoLocaisAudio();
        if ($eh_setado == '1') {   
          $ajuda .= "Para alterar o Link de arquivo de �udio atual, informe novo link de �udio!<br />";
        } else {
          $ajuda .= "Informe link de �udio!<br />";
        }
        $ajuda.= nl2br($ds_sugestao_locais_audio)."<br />".
                 "Este campo � de preenchimento obrigat�rio!<br />";              
      break;   

      case "2":   
        $ds_sugestao_locais_video = $conf->retornaSugestaoLocaisVideo();
        if ($eh_setado == '1') {   
          $ajuda .= "Para alterar o Link de arquivo de V�deo atual, informe novo link de V�deo!<br />";
        } else {
          $ajuda .= "Informe link de V�deo!<br />";
        }
        $ajuda .= nl2br($ds_sugestao_locais_video)."<br />".
                  "Este campo � de preenchimento obrigat�rio!<br />";
      break;
            
      case "3":                     
        require_once 'conteudos/arquivos_extensao.php';                         $arq_ext = new ArquivoExtensao();
        $ds_limite_tamanho_arquivos = $conf->retornaTamanhoLimiteUploadArquivos();
        $ds_extensoes_arquivos = $arq_ext->retornaExtensoesArquivosObjetoAprendizagem($cd_formato);
        if ($eh_setado == '1') {   
          $ajuda .= "Para alterar o arquivo de Texto, selecione novo Arquivo, ou deixe este campo em branco para manter o arquivo atual!<br />";
        } else {
          $ajuda .= "Selecione Arquivo de Texto!<br />";
        }
        $ajuda .= "S�o aceitos arquivos nos formatos abaixo descritos, considerando limite de tamanho de cada arquivo em ".$ds_limite_tamanho_arquivos." MB.<br />".
                  "Formatos: <br />".$ds_extensoes_arquivos."<br />".
                  "Este campo � de preenchimento obrigat�rio!<br />";
      break;
              
      case "4":
        require_once 'conteudos/arquivos_extensao.php';                         $arq_ext = new ArquivoExtensao();
        $ds_limite_tamanho_arquivos = $conf->retornaTamanhoLimiteUploadArquivos();
        $ds_extensoes_arquivos = $arq_ext->retornaExtensoesArquivosObjetoAprendizagem($cd_formato);
        if ($eh_setado == '1') {
          $ajuda .= "Para alterar o arquivo de Imagem, selecione novo Arquivo, ou deixe este campo em branco para manter o arquivo atual!<br />";
        } else {
          $ajuda .= "Selecione Arquivo de Imagem!<br />";
        }
        $ajuda .= "S�o aceitos arquivos nos formatos abaixo descritos, considerando limite de tamanho de cada arquivo em ".$ds_limite_tamanho_arquivos." MB.<br />".
                  "Formatos: <br />".$ds_extensoes_arquivos."<br />".
                  "Este campo � de preenchimento obrigat�rio!<br />";
      break;
      

      case "5":
        require_once 'conteudos/arquivos_extensao.php';                         $arq_ext = new ArquivoExtensao();
        $ds_limite_tamanho_arquivos = $conf->retornaTamanhoLimiteUploadArquivos();
        $ds_extensoes_arquivos = $arq_ext->retornaExtensoesArquivosObjetoAprendizagem($cd_formato);
        if ($eh_setado == '1') {
          $ajuda .= "Para alterar o arquivo do Aplicativo, selecione novo Arquivo, ou deixe este campo em branco para manter o arquivo atual!<br />";
        } else {
          $ajuda .= "Selecione Arquivo de Aplicativo!<br />";
        }
        $ajuda .= "S�o aceitos arquivos nos formatos abaixo descritos, considerando limite de tamanho de cada arquivo em ".$ds_limite_tamanho_arquivos." MB.<br />".
                  "Formatos: <br />".$ds_extensoes_arquivos."<br />".
                  "Este campo � de preenchimento obrigat�rio!<br />";
      break;
            
      case "6":   
        if ($eh_setado == '1') {
          $ajuda .= "Para alterar o Link Externo atual, informe novo link externo!<br />";
        } else {
          $ajuda .= "Informe Link Externo!<br />";
        }
        $ajuda .= "Este campo � de preenchimento obrigat�rio!<br />";
      break;
            
      case "7":
        require_once 'conteudos/arquivos_extensao.php';                         $arq_ext = new ArquivoExtensao();
        $ds_limite_tamanho_arquivos = $conf->retornaTamanhoLimiteUploadArquivos();
        $ds_extensoes_arquivos = $arq_ext->retornaExtensoesArquivosObjetoAprendizagem($cd_formato);
        if ($eh_setado == '1') {
          $ajuda .= "Para alterar o arquivo de Apresenta��o, selecione novo Arquivo, ou deixe este campo em branco para manter o arquivo atual!<br />";
        } else {
          $ajuda .= "Selecione o arquivo de Apresenta��o!<br />";
        }
        $ajuda .= "S�o aceitos arquivos nos formatos abaixo descritos, considerando limite de tamanho de cada arquivo em ".$ds_limite_tamanho_arquivos." MB.<br />".
                  "Formatos: <br />".$ds_extensoes_arquivos."<br />".
                  "Este campo � de preenchimento obrigat�rio!<br />";
      break;                                                                         
    }
      
    $retorno.= "          <input type=\"hidden\" name=\"eh_setado\" id=\"eh_setado\" value=\"eh_setado\" />\n";
      
    $substituicao = "";
    if ($cd_formato == '') {
      $retorno.= "          <input type=\"text\" maxlength=\"0\" name=\"ds_technical_location\" id=\"ds_technical_location\" value=\"\" style=\"width:840px;\" alt=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" title=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" class=\"fontConteudoCampoTextHint\" placeholder=\"Localiza��o\" tabindex=\"1\"/>\n";
    } elseif (($cd_formato == '1') || ($cd_formato == '2') || ($cd_formato == '6')) {
      $retorno.= "          <input type=\"text\" maxlength=\"250\" name=\"ds_technical_location\" id=\"ds_technical_location\" value=\"".$ds_location."\" style=\"width:840px;\" alt=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" title=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" class=\"fontConteudoCampoTextHintObrigatorio\" placeholder=\"Localiza��o\" tabindex=\"1\"/>\n";
      $ajuda.= 'Campo do Tipo Texto com capacidade para at� 250 caracteres';
    } elseif (($cd_formato == '3') || ($cd_formato == '4') || ($cd_formato == '5') || ($cd_formato == '7')) {
      $retorno.= "          <input type=\"file\" maxlength=\"150\" name=\"ds_technical_location\" id=\"ds_technical_location\" value=\"\" style=\"width:840px;\" alt=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" title=\"Localiza��o do Arquivo contendo o Objeto de Aprendizagem\" class=\"fontConteudoCampoTextHintObrigatorio\" placeholder=\"Localiza��o\" tabindex=\"1\"/>\n";
      if ($cd_formato == $cd_formato_antigo) {
        if ($ds_location != '') {
          $substituicao = "<br />Para alterar o arquivo atual, selecione novo Arquivo, ou deixe este campo em branco mantendo o existente!\n";
        }
        $retorno.= "          <input type=\"hidden\" name=\"ds_technical_location_antigo\" id=\"ds_technical_location_antigo\" value=\"".$ds_location."\" />\n";
      } else {
        $retorno.= "          <input type=\"hidden\" name=\"ds_technical_location_antigo\" id=\"ds_technical_location_antigo\" value=\"\" />\n";
      }
      $retorno.= $arq_ext->retornaRelacaoExtensoesHidden($cd_formato);
      $ajuda.= 'Campo do Tipo Arquivo';
    }
    $retorno.= "          <a href=\"#\" class=\"dcontexto\">\n";
    $retorno.= "            <img src=\"".$_SESSION['life_link_completo']."icones/help.png\"border=\"0\">\n";
    $retorno.= "            <span class=\"fontdDetalhar\">\n";
    $retorno.= "              ".$ajuda."\n";
    $retorno.= "            </span>\n";
    $retorno.= "          </a>\n";  
    if ($substituicao != '') {
      $retorno.= $substituicao;
    }
  
      
    return utf8_encode($retorno);  
  }
  
  function atualizarCampoSubAreasConhecimento($cd_general, $cd_area_conhecimento) {
    require_once 'conteudos/sub_areas_conhecimento.php';                        $sac = new SubAreaConhecimento();
    
    $retorno = $sac->retornaCadastroSubAreasConhecimentoObjetoAprendizagem($cd_area_conhecimento, '840', $cd_general);
   
    return utf8_encode($retorno);
  }
  
  function detalharDadosObjetoAprendizagem($cd_objeto_aprendizagem) {
    require_once 'conteudos/objetos_aprendizagem.php';                          $oa = new ObjetoAprendizagem();
    
    $retorno = $oa->retornaInformacoesCompletasObjetoAprendizagem($cd_objeto_aprendizagem);
    
    return utf8_encode($retorno);
  }
      
  sajax_init();
	sajax_export("incluirAreaConhecimentoRelacao");
	sajax_export("retirarRelacao");
  sajax_export("atualizarCampoTipoArquivo");
  sajax_export("atualizarCampoSubAreasConhecimento");
  sajax_export("detalharDadosObjetoAprendizagem");
	sajax_handle_client_request();
?>