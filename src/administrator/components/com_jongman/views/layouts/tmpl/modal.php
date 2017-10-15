<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
$function  = JRequest::getCmd('function', 'jmSelectLayout');

$user       = JFactory::getUser();
$uid        = $user->get('id');
$list_order = $this->escape($this->state->get('list.ordering'));
$list_dir   = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=layouts&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!$this->is_j25) : ?>
    	<?php // Search tools ba
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
    <?php else: ?>
    <?php echo $this->loadTemplate('filter_j25')?>
    <?php endif;?>
    <div class="clr clearfix"></div>
    <table class="adminlist table table-stripped">
        <thead>
            <tr>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $list_dir, $list_order); ?>
                </th>
                
                <th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_TIMEZONE', 'a.timezone', $list_dir, $list_order); ?>
				</th>
				
                <th width="30%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_RESERVANLE_SLOTS')?>
                </th>
                
                <th width="15%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_BLOCKED_SLOTS')?>
                </th>	
                <th width="1%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $list_dir, $list_order); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td>
               		<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">
                        <?php echo $this->escape($item->title); ?></a>
                </td>
                
                <td>
                	<?php echo $this->escape($item->timezone)?>
                </td>
                <td>
                
                </td>
                
                <td>
                
                </td>
                
                <td class="center hidden-phone small">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if ($this->is_j25) :?>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <?php endif ?>
    </table>
	<?php if (!$this->is_j25) : echo $this->pagination->getListFooter(); endif; ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $list_dir; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>