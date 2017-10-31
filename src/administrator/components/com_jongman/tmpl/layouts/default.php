<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */


defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

$user       = JFactory::getUser();
$uid        = $user->get('id');
$list_order = $this->escape($this->state->get('list.ordering'));
$list_dir   = $this->escape($this->state->get('list.direction'));

$saveOrder	= $list_order=='a.ordering';
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=layouts&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div id="j-sidebar-container" class="col-md-2">
            <?php echo $this->sidebar; ?>
        </div>
        <div class="<?php if (!empty($this->sidebar)) { echo 'col-md-10'; } else { echo 'col-md-12'; } ?>">
            <div id="j-main-container" class="j-main-container">
                <?php // Search tools
                    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-warning alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th style="width:1%" class="text-center">
                                    <?php echo JHtml::_('grid.checkall'); ?>
                                </th>

                                <th style="width:1%;min-width:55px" class="nowrap center">
                                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:20%">
                                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:10%">
                                    <?php echo JHtml::_('grid.sort', 'COM_JONGMAN_DEFAULT_LAYOUT', 'a.default', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:10%">
                                    <?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_USED_COUNT', 'a.used_count', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:10%">
                                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:30%">
                                    <?php echo JText::_('COM_JONGMAN_HEADING_RESERVABLE_SLOTS')?>
                                </th>

                                <th style="width:15%">
                                    <?php echo JText::_('COM_JONGMAN_HEADING_BLOCKED_SLOTS')?>
                                </th>

                                <th style="width:10%" class="nowrap hidden-sm-down text-center">
                                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $list_dir, $list_order); ?>
                                </th>

                                <th style="width:1%" class="nowrap hidden-phone">
                                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $list_dir, $list_order); ?>
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="3">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>
                            <?php
                            $ordering	= ($list_order == 'ordering');
                            $canCreate	= $user->authorise('core.create', 'com_jongman');
                            $canEdit	= $user->authorise('core.edit', 'com_jongman');
                            $canCheckin	= $user->authorise('core.manage',	'com_checkin') || $item->checked_out == $uid || $item->checked_out == 0;
                            $canChange	= $user->authorise('core.edit.state', 'com_jongman') && $canCheckin;
                            ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'banners.', $canChange, 'cb'); ?>
                                    </div>
                                </td>
                                <td class="nowrap has-contetxt">
                                    <div class="pull-left">
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'layouts.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canEdit || $canEditOwn) : ?>
                                            <a href="<?php echo JRoute::_('index.php?option=com_jongman&task=layout.edit&id='.(int) $item->id); ?>">
                                                <?php echo $this->escape($item->title); ?></a>
                                        <?php else : ?>
                                            <?php echo $this->escape($item->title); ?>
                                        <?php endif; ?>
                                        <span class="small">
								            <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							            </span>
                                        <div class="small">
                                            <?php echo $item->timezone; ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'layouts.', $canChange)?>
                                </td>

                                <td class="text-center">
                                    <?php echo $item->used_count?>
                                </td>

                                <td class="text-center">
                                    <?php echo $item->access_level?>
                                </td>

                                <td>

                                </td>

                                <td>

                                </td>

                                <td class="small nowrap hidden-sm-down text-center">
                                    <?php echo JLayoutHelper::render('joomla.content.language', $item); ?>
                                </td>

                                <td class="center hidden-phone small">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif;?>
            </div>
        </div>
    </div>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $list_dir; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>