<?php
if (!defined('PLX_ROOT')) exit;

/**
 * Plugin MyPluX_MembersContent
 *
 * @author	Yannic H.
 **/

class MyPluX_MembersContent extends plxPlugin {

  /**
   * Constructeur de la classe
   **/
  public function __construct($default_lang) {

    # Appel du constructeur
    parent::__construct($default_lang);

    # Droits d'accès à la configuration du plugin
    $this->setConfigProfil(PROFIL_ADMIN);

    # Initialisation des données
    $this->by_default_articles = $this->getParam('by_default_articles')!='' ? $this->getParam('by_default_articles') : 'Public';
    $this->by_default_pages = $this->getParam('by_default_pages')!='' ? $this->getParam('by_default_pages') : 'Public';
    $this->replace_article = $this->getParam('replace_article')!='' ? $this->getParam('replace_article') : base64_encode('Le contenu de cet article est réservé à nos membres.');
    $this->replace_page = $this->getParam('replace_page')!='' ? $this->getParam('replace_page') : base64_encode('Le contenu de cette page est réservé à nos membres.');
		$this->add_to_chapo_before = ($this->getParam('add_to_chapo_before')?$this->getParam('add_to_chapo_before'):'never');
		$this->txt_to_chapo_before = ($this->getParam('txt_to_chapo_before')?$this->getParam('txt_to_chapo_before'):base64_encode(''));
		$this->add_to_chapo_after = ($this->getParam('add_to_chapo_after')?$this->getParam('add_to_chapo_after'):'never');
		$this->txt_to_chapo_after = ($this->getParam('txt_to_chapo_after')?$this->getParam('txt_to_chapo_after'):base64_encode(''));
    $this->can_view = false;

    # Ajouts des hooks
    $this->addHook('plxMotorParseArticle','plxMotorParseArticle');
    $this->addHook('AdminArticleInitData','AdminArticleInitData');
    $this->addHook('AdminArticlePostData','AdminArticlePostData');
    $this->addHook('AdminArticleParseData','AdminArticleParseData');
    $this->addHook('AdminArticleTop','AdminArticleTop');
    $this->addHook('plxAdminEditArticleXml','plxAdminEditArticleXml');
    $this->addHook('AdminArticlePreview','AdminArticlePreview');    
  }

  public function plxMotorParseArticle(){
    echo "<?php
		\$art['can_view'] = isset(\$iTags['can_view']) ? plxUtils::getValue(\$values[\$iTags['can_view'][0]]['value']) : 'default';
		if( (!defined('PLX_ADMIN') || PLX_ADMIN!==true) && ( (\$art['can_view']=='default' && '".$this->by_default_articles."'!='Public') || \$art['can_view']=='Members') && (!isset(\$_SESSION['user']) || intval(\$_SESSION['user'])<1) )
		{
			\$art['content']='<div class=\"art_content_replaced\">".base64_decode($this->replace_article)."</div>';
			if('".$this->add_to_chapo_before."' != 'never')
				\$art['chapo'] = '".base64_decode($this->txt_to_chapo_before)."'.\$art['chapo'];
			if('".$this->add_to_chapo_after."' != 'never')
				\$art['chapo'] .= '".base64_decode($this->txt_to_chapo_after)."';
		}
		elseif( (!defined('PLX_ADMIN') || PLX_ADMIN!==true) && ((\$art['can_view']=='default' && '".$this->by_default_articles."'!='Public') || \$art['can_view']=='Members'))
		{
			if('".$this->add_to_chapo_before."' == 'always')
				\$art['chapo'] = '".base64_decode($this->txt_to_chapo_before)."'.\$art['chapo'];
			if('".$this->add_to_chapo_after."' == 'always')
				\$art['chapo'] .= '".base64_decode($this->txt_to_chapo_after)."';			
		}
    ?>";
  }

  public function AdminArticleInitData(){
    echo "<?php
    \$can_view = '".$this->by_default_articles."';
    ?>";
  }
  
  public function AdminArticlePostData(){
    echo "<?php
    \$art['can_view'] = (isset(\$result) && isset(\$result[can_view])?\$result['can_view']:'default');
    ?>";
  }
  
  public function AdminArticleParseData(){
    echo "
    \$can_view = \$result['can_view'];
    ";
  }

  public function AdminArticlePreview(){
    echo "<?php
    \$art['can_view'] = \$_POST['can_view'];
    ?>";
  } 
  
 /**
   * Choix
   **/
  public function AdminArticleTop() {
    echo "<p>
    <?php 
    \$view = 'Private';    
    if(isset(\$result) && @isset(\$result[can_view])) \$view = \$result['can_view']; else \$view = 'default';
		  plxUtils::printSelect('can_view', array('default'=>'Par défaut','Public'=>'Publique','Members'=>'Membres'), \$view);
		?>
    </p>
";
  }

  /**
   * Sauvegarde
   **/
  public function plxAdminEditArticleXml() {    
    echo "<?php
    \$can_view = plxUtils::getValue(\$content['can_view']);
    \$xml .= \"\t\".'<can_view><![CDATA['.plxUtils::cdataCheck(trim(\$can_view)).']]></can_view>'.\"\n\";
    ?>";
  }
}