<?php
/**
 * @package      mod_jintegrity
 *
 * @copyright    © Robert N. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

$jinput = JFactory::getApplication()->input;
$crc_dir = JPATH_ROOT . '/administrator/modules/mod_jintegrity/crc32/';
$version = str_replace('.', '-', JVERSION);
$extension =  '.json';
$package = $crc_dir . $version . $extension;
$input_name = !is_file($package) ? 'jintegrity_download' : 'jintegrity_check';
$submit_value = !is_file($package) ? 'MOD_JINTEGRITY_DOWNLOAD_SUBMIT' : 'MOD_JINTEGRITY_CHECK_SUBMIT';
$use_ajax = $params->get('ajax', 0);
?>
<div class="jintegrity">

    <div class="info-page">
		<?php
		$text = '';

		if (!is_file($package))
		{
			$text = JText::_('MOD_JINTEGRITY_NO_PACKAGE_HASHES');
		}

        if ($jinput->get('jintegrity_download'))
		{
			$url = 'https://downloads.joomla.org/cms/joomla3/' . $version . '/Joomla_' . $version . '-Stable-Full_Package.zip?format=zip';
			$tmp_zip = $crc_dir . 'tmp.zip';
			$tmp_dir = $crc_dir . 'tmp_dir';

			if (ModJintegrityHelper::saveHash($url, $package, $tmp_zip, $tmp_dir))
			{
				$text = JText::_('MOD_JINTEGRITY_PACKAGE_READY');
                $input_name = 'jintegrity_check';
                $submit_value = 'MOD_JINTEGRITY_CHECK_SUBMIT';
			}
		}

        if ($jinput->get('jintegrity_check'))
		{
			$text = ModJintegrityHelper::getResult(JPATH_ROOT, $package);
		}

		echo $text;
		?>
    </div>

	<?php
	if ($use_ajax)
	{
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('var jintegrity_url = "' . JUri::current() . '"');
		$doc->addScript(JUri::root(true) . '/administrator/modules/mod_jintegrity/assets/js/mod_jintegrity.js');
		?>
        <div class="ajax-loader" style="display: none;">
            <img src="<?php echo JUri::root(true) . '/administrator/modules/mod_jintegrity/assets/css/loading.gif'; ?>"/>
            <span><?php echo JText::_('MOD_JINTEGRITY_PLEASE_WAIT'); ?></span>
        </div>
		<?php
	}
	?>

    <form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="jintegrity-check">
        <input type="hidden" name="<?php echo $input_name; ?>" value="1"/>
        <input type="hidden" name="jintegrity_ajax" value="<?php echo $use_ajax ? '1' : '0';?>"/>
        <input type="submit" id="jintegrity-submit" value="<?php echo JText::_($submit_value); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
    </form>

</div>