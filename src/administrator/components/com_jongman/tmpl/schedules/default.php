<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.tabstate');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=schedules'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div id="j-sidebar-container" class="col-md-2">
            <?php echo $this->sidebar; ?>
        </div>
        <div class="col-md-10">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-warning alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="scheduleList">
                        <thead>
                            <tr>
                                <th style="width:1%" class="text-center">
                                    <?php echo JHtml::_('grid.checkall'); ?>
                                </th>
                                <th style="width:1%" class="nowrap text-center">
                                    <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                </th>
                                <th>
                                    <?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="12">
                                    <?php echo $this->pagination->getListFooter(); ?>
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'schedules.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                                    </div>
                                </td>
                                <td class="nowrap has-context">
                                    <div>
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'schedules.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo JRoute::_('index.php?option=com_jongman&task=schedule.edit&id=' . (int) $item->id); ?>">
                                                <?php echo $this->escape($item->name); ?></a>
                                        <?php else : ?>
                                            <?php echo $this->escape($item->name); ?>
                                        <?php endif; ?>
                                        <span class="small">
												<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
											</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif;?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
