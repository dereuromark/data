<?php echo $this->Html->link($this->Format->yesNo($ajaxToggle['MimeTypeImage']['active'], ['onTitle' => 'Active', 'offTitle' => 'Inactive']), ['action' => 'toggleActive', $ajaxToggle['MimeTypeImage']['id']], ['escape' => false]);?>