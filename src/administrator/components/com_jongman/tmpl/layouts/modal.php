<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

\JHtml::_('behavior.tooltip');

$app = JFactory::getApplication();
$function  = $app->input->getCmd('function', 'jSelectLayout');

$user       = \JFactory::getUser();
$uid        = $user->get('id');
$list_order = $this->escape($this->state->get('list.ordering'));
$list_dir   = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=layouts&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php // Search tools ba
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
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>
                            <?php if ($item->language && JLanguageMultilang::isEnabled())
                            {
                                $tag = strlen($item->language);
                                if ($tag == 5)
                                {
                                    $lang = substr($item->language, 0, 2);
                                }
                                elseif ($tag == 6)
                                {
                                    $lang = substr($item->language, 0, 3);
                                }
                                else
                                {
                                    $lang = '';
                                }
                            }
                            elseif (!JLanguageMultilang::isEnabled())
                            {
                                $lang = '';
                            }
						    ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td>
                                    <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', null, '<?php echo $this->escape('index.php?option=com_jongman&view=layout&id='.$item->id.'&lang='.$item->language); ?>', '<?php echo $this->escape($lang); ?>', null);">
                                        <?php echo $this->escape($item->title); ?>
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
                </table>
                <?php endif;?>
            </div>
        </div>
    </div>


        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
    </table>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $list_dir; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>