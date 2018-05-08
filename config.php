<?php
if(!defined('PLX_ROOT'))
	exit;

$PLUGIN_NAME = 'MyPluX_MembersContent';

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	
	$allowed_tags = '<p><a><br><span><em><b>';
	
  $plxPlugin->setParam('by_default_articles', plxUtils::strCheck($_POST['by_default_articles']), 'string');
  $plxPlugin->setParam('by_default_pages', plxUtils::strCheck($_POST['by_default_pages']), 'string');
  $plxPlugin->setParam('replace_article', plxUtils::strCheck(base64_encode(strip_tags(html_entity_decode($_POST['replace_article']),$allowed_tags))), 'string');
  $plxPlugin->setParam('replace_page', plxUtils::strCheck(base64_encode(strip_tags(html_entity_decode($_POST['replace_page']),$allowed_tags))), 'string');
  $plxPlugin->saveParams();

	header('Location: parametres_plugin.php?p='.$PLUGIN_NAME);
	exit;
}
$by_default_articles = ($plxPlugin->getParam('by_default_articles')?$plxPlugin->getParam('by_default_articles'):'Public');
$by_default_pages = ($plxPlugin->getParam('by_default_pages')?$plxPlugin->getParam('by_default_pages'):'Public');
$replace_article = ($plxPlugin->getParam('replace_article')?$plxPlugin->getParam('replace_article'):base64_encode('Le contenu de cet article est réservé à nos membres.'));
$replace_page = ($plxPlugin->getParam('replace_page')?$plxPlugin->getParam('replace_page'):base64_encode('Le contenu de cette page est réservé à nos membres.'));
?>
<form id="form_config_plugin" action="parametres_plugin.php?p=<?php echo $PLUGIN_NAME; ?>" method="post">
	<fieldset>
		<p class="field">
			<table>
				<tr>
					<td><label for="id_by_default_articles"><?php $plxPlugin->lang('L_BY_DEFAULT_ARTICLES') ?> :</label></td>
					<td><?php plxUtils::printSelect('by_default_articles', array('Public'=>$plxPlugin->getLang('L_PUBLIC'),'Members'=>$plxPlugin->getLang('L_MEMBERS')), $by_default_articles); ?></td>
				</tr>
				<tr>
					<td><label for="id_text_articles"><?php $plxPlugin->lang('L_ARTICLE_TXT_REPLACE') ?> :</label></td>
					<td><?php plxUtils::printArea('replace_article',  htmlentities(base64_decode($replace_article)), 80, 5, $readonly=false, $class='') ?></td>
				</tr>
				<tr>
					<td><label for="id_by_default_pages"><?php $plxPlugin->lang('L_BY_DEFAULT_PAGES') ?> :</label></td>
					<td><?php plxUtils::printSelect('by_default_pages', array('Public'=>$plxPlugin->getLang('L_PUBLIC'),'Members'=>$plxPlugin->getLang('L_MEMBERS')), $by_default_pages, '50-255', true); ?></td>
				</tr>
				<tr>
					<td><label for="id_text_pages"><?php $plxPlugin->lang('L_PAGE_TXT_REPLACE') ?> :</label></td>
					<td><?php plxUtils::printArea('replace_page',  htmlentities(base64_decode($replace_page)), 80, 5, true) ?></td>
				</tr>
			</table>
		</p>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>