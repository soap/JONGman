<?php 
defined('_JEXEC') or die;
?>
<form action="index.php?option=com_jongman?option=com_jongman" method="post" name="adminForm">
	<div id="j-sidebar-container" class="span2">
    	<?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<<<<<<< HEAD
	<div class="adminform">
=======
	<div class="adminform row-fluid">
>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
    	<div class="cpanel-left pull-left span6">
	   		<div id="cpanel">
<?php	
		$link = "index.php?option=com_jongman&view=schedules";
<<<<<<< HEAD
		echo JongmanHelper::quickIconButton( $link, 'jongman_scheduleadmin_48.png', JText::_("COM_JONGMAN_SCHEDULES") );
						
		$link = 'index.php?option=com_jongman&view=resources';
		echo JongmanHelper::quickIconButton( $link, 'jongman_resourceadmin_48.png', JText::_( 'COM_JONGMAN_RESOURCES' ) );
		
		$link = 'index.php?option=com_jongman&view=reservations';
		echo JongmanHelper::quickIconButton( $link, 'jongman_reservationadmin_48.png', JText::_( 'COM_JONGMAN_RESERVATIONS' ) );

		$link = 'index.php?option=com_jongman&view=quotas';
		echo JongmanHelper::quickIconButton( $link, 'jongman_quotaadmin_48.png', JText::_( 'COM_JONGMAN_QUOTAS' ) );
=======
		echo JongmanHelper::quickIcon( $link, 'jongman_scheduleadmin_48.png', JText::_("COM_JONGMAN_SCHEDULES") );
						
		$link = 'index.php?option=com_jongman&view=resources';
		echo JongmanHelper::quickIcon( $link, 'jongman_resourceadmin_48.png', JText::_( 'COM_JONGMAN_RESOURCES' ) );
		
		$link = 'index.php?option=com_jongman&view=reservations';
		echo JongmanHelper::quickIcon( $link, 'jongman_reservationadmin_48.png', JText::_( 'COM_JONGMAN_RESERVATIONS' ) );

		$link = 'index.php?option=com_jongman&view=quotas';
		echo JongmanHelper::quickIcon( $link, 'jongman_quotaadmin_48.png', JText::_( 'COM_JONGMAN_QUOTAS' ) );
>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
?>
	   		</div>
    	</div>
		
    	<div class="cpanel-right pull-right span6">
<<<<<<< HEAD
	   		<div style="border:1px solid #ccc;background:#fff;margin:5px;padding:5px">
=======
	   		<div style="border:1px solid #ccc;background:#fff;margin:15px;padding:15px">
>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
            	<div style="float:right;margin:10px;">
	           		<?php echo JHtml::image(JURI::root().'media/com_jongman/images/logo-jongman.png', 'joomlant.org');?>
            	</div>
<?php
		echo '<h3>'.  JText::_('COM_JONGMAN_VERSION').'</h3>'
		.'<p>'.  $this->version .' (JONGman Library : '.$this->libVersion.')</p>';

		echo '<h3>'.  JText::_('COM_JONGMAN_COPYRIGHT').'</h3>'
		.'<p>© 2009 - '.  date("Y"). ' Prasit Gebsaap</p>'
		.'<p><a href="http://www.joomlant.org/" target="_blank">http://www.joomlant.org</a></p>';

		echo '<h3>'.  JText::_('COM_JONGMAN_LICENSE').'</h3>'
		.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';
		
		echo '<h3>'.  JText::_('COM_JONGMAN_TRANSLATION').': '. JText::_('COM_JONGMAN_TRANSLATION_LANGUAGE_TAG').'</h3>'
        .'<p>© 2009 - '.  date("Y"). ' '. JText::_('COM_JONGMAN_TRANSLATER'). '</p>'
        .'<p>'.JText::_('COM_JONGMAN_TRANSLATION_SUPPORT_URL').'</p>';
?>
	   </div>
    </div>
</div>
</div>
</div>
<input type="hidden" name="<?php echo JFactory::getSession()->getToken(); ?>" value="1" />
</form>
