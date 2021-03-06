<?php
/**
 * @package      pkg_projectfork
 * @subpackage   com_pfrepo
 *
 * @author       Tobias Kuhn (eaxs)
 * @copyright    Copyright (C) 2006-2013 Tobias Kuhn. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


$user     = JFactory::getUser();
$uid      = $user->get('id');
$x        = count($this->items['directories']);

$this_dir  = $this->items['directory'];
$this_path = (empty($this_dir) ? '' : $this_dir->path);

$filter_search  = $this->state->get('filter.search');
$filter_project = (int) $this->state->get('filter.project');
$is_search      = empty($filter_search) ? false : true;

foreach ($this->items['notes'] as $i => $item) :
    $link   = PFrepoHelperRoute::getNoteRoute($item->slug, $item->project_slug, $item->dir_slug, $item->path);
    $access = PFrepoHelper::getActions('note', $item->id);

    $can_create   = $access->get('core.create');
    $can_edit     = $access->get('core.edit');
    $can_checkin  = ($user->authorise('core.manage', 'com_checkin') || $item->checked_out == $uid || $item->checked_out == 0);
    $can_edit_own = ($access->get('core.edit.own') && $item->created_by == $uid);
    $can_change   = ($access->get('core.edit.state') && $can_checkin);
    $date_opts    = array('past-class' => '', 'past-icon' => 'calendar');
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <?php if ($this_dir->parent_id >= 1) : ?>
        <td>
            <label for="cb<?php echo $x; ?>" class="checkbox">
                <?php echo JHtml::_('pf.html.id', $x, $item->id, false, 'nid'); ?>
            </label>
        </td>
        <?php endif; ?>
        <td>
        	<span class="item-title pull-left">
	            <?php if ($item->checked_out) : ?><span aria-hidden="true" class="icon-lock"></span> <?php endif; ?>
	            <a href="<?php echo JRoute::_($link);?>"  class="hasPopover" rel="popover" title="<?php echo JText::_($this->escape($item->title)); ?>" data-content="<?php echo $this->escape($item->description); ?>" data-placement="right">
	            	<span aria-hidden="true" class="icon-pencil-2 text-warning"></span> 
	                <?php echo JText::_($this->escape($item->title)); ?>
	            </a>
        	</span>
        	
        	<span class="dropdown pull-left">
	        	<?php
	                $this->menu->start(array('class' => 'btn-mini btn-link'));
	                $this->menu->itemEdit('noteform', $item->id, ($can_edit || $can_edit_own));
	                $this->menu->itemDelete('repository', $x, ($can_edit || $can_edit_own));
	                $this->menu->end();
	
	                echo $this->menu->render(array('class' => 'btn-mini'));
	            ?>
        	</span>

            <?php if ($filter_project && $is_search): ?>
                <div class="small">
                    <?php echo str_replace($this_path, '.', $item->path) . '/'; ?>
                </div>
            <?php endif; ?>
        </td>
        <td>
        	<?php echo JText::_('JGRID_HEADING_NOTE'); ?>
        </td>
        <td>
            <?php echo $item->author_name; ?>
        </td>
        <td>
            <?php echo JHtml::_('date', $item->created, JText::_('M d')); ?>
        </td>
    </tr>
<?php $x++; endforeach; ?>
