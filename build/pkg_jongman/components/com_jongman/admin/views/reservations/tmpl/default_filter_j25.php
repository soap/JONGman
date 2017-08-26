<?php ?>
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search">
				<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>:</label>
			<input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo JText::_('COM_JONGMAN_RESERVATIONS_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

		</div>
		<div class="filter-select fltrt">
			<select name="filter_schedule_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_SCHEDULE');?></option>
				<?php echo JHtml::_('select.options', JongmanHelper::getScheduleOptions(),
					'value', 'text', $this->state->get('filter.schedule_id'));?>
			</select>
			
			<select name="filter_resource_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_RESOURCE');?></option>
				<?php echo JHtml::_('select.options', JongmanHelper::getResourceOptions($this->state->get('filter.schedule_id')),
					'value', 'text', $this->state->get('filter.resource_id'));?>
			</select>
			
			<select name="filter_reservation_category" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_RESERVATION_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', 
					JongmanHelper::getReservationCategoryOptions(), 	
					'value', 'text', $this->state->get('filter.reservation_category'));?>
			</select>
			
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JongmanHelper::getReservationOptions(),
					'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'),
					'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>
	</fieldset>
