<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('jquery.framework');
JHtml::_('behavior.formvalidator');
//JHtml::_('behavior.tabstate');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
    <div>
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JONGMAN_SCHEDULE_DETAILS', true)); ?>
        <div class="row">
            <div class="col-md-9">
                <?php echo $this->form->renderField('default'); ?>
                <?php echo $this->form->renderField('layout_id'); ?>
                <?php echo $this->form->renderField('weekday_start'); ?>
                <?php echo $this->form->renderField('view_days'); ?>
                <?php echo $this->form->renderField('timezone'); ?>
                <?php echo $this->form->renderField('time_format'); ?>
                <?php echo $this->form->renderField('admin_email'); ?>
                <?php echo $this->form->renderField('notify_admin'); ?>
            </div>
            <div class="col-md-3">
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>